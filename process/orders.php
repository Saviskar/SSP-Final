<?php
session_start();
require_once '../src/includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in to place orders';
    http_response_code(401);
    echo json_encode($response);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_SESSION['user_id'];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            handleGetOrders($db, $userId, $response);
            break;
            
        case 'POST':
            handlePlaceOrder($db, $userId, $response);
            break;
            
        default:
            $response['message'] = 'Method not allowed';
            http_response_code(405);
            break;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Internal server error';
    error_log("Orders API error: " . $e->getMessage());
}

echo json_encode($response);

function handleGetOrders($db, $userId, &$response) {
    // Check if user is admin (can see all orders)
    $isAdmin = isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'ADMIN';
    
    if ($isAdmin) {
        // Get all orders with user information
        $stmt = $db->prepare("
            SELECT 
                o.OrderID,
                o.UserID,
                u.FullName as CustomerName,
                o.PlacedAt,
                o.Status,
                COUNT(oi.OrderItemID) as ItemCount,
                SUM(oi.Quantity * oi.UnitPriceAtOrder) as OrderTotal,
                ua.AddressLine,
                c.CityName,
                p.ProvinceName
            FROM `Order` o
            JOIN User u ON o.UserID = u.UserID
            JOIN OrderItem oi ON o.OrderID = oi.OrderID
            LEFT JOIN UserAddress ua ON u.UserID = ua.UserID
            LEFT JOIN City c ON ua.CityID = c.CityID
            LEFT JOIN Province p ON c.ProvinceID = p.ProvinceID
            GROUP BY o.OrderID
            ORDER BY o.PlacedAt DESC
        ");
        $stmt->execute();
    } else {
        // Get user's orders only
        $stmt = $db->prepare("
            SELECT 
                o.OrderID,
                o.PlacedAt,
                o.Status,
                COUNT(oi.OrderItemID) as ItemCount,
                SUM(oi.Quantity * oi.UnitPriceAtOrder) as OrderTotal
            FROM `Order` o
            JOIN OrderItem oi ON o.OrderID = oi.OrderID
            WHERE o.UserID = ?
            GROUP BY o.OrderID
            ORDER BY o.PlacedAt DESC
        ");
        $stmt->execute([$userId]);
    }
    
    $orders = $stmt->fetchAll();
    
    // Get order items for each order
    foreach ($orders as &$order) {
        $stmt = $db->prepare("
            SELECT 
                oi.ProductID,
                oi.Quantity,
                oi.UnitPriceAtOrder,
                p.ProductName
            FROM OrderItem oi
            JOIN Product p ON oi.ProductID = p.ProductID
            WHERE oi.OrderID = ?
        ");
        $stmt->execute([$order['OrderID']]);
        $order['items'] = $stmt->fetchAll();
    }
    
    $response['success'] = true;
    $response['data'] = $orders;
}

function handlePlaceOrder($db, $userId, &$response) {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $address = trim($_POST['address'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = "Full name is required.";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email address is required.";
    }
    
    if (empty($mobile)) {
        $errors[] = "Mobile number is required.";
    }
    
    if (empty($address)) {
        $errors[] = "Delivery address is required.";
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(' ', $errors);
        return;
    }
    
    // Get user's cart items
    $stmt = $db->prepare("
        SELECT 
            ci.ProductID,
            ci.Quantity,
            p.Price,
            p.Stock,
            p.ProductName,
            COALESCE(pr.PromotionPercentage, 0) as DiscountPercentage,
            CASE 
                WHEN pr.PromotionPercentage IS NOT NULL 
                THEN p.Price * (1 - pr.PromotionPercentage / 100)
                ELSE p.Price 
            END as FinalPrice
        FROM CartItem ci
        JOIN Cart c ON ci.CartID = c.CartID
        JOIN Product p ON ci.ProductID = p.ProductID
        LEFT JOIN ProductPromotion pp ON p.ProductID = pp.ProductID
        LEFT JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
        WHERE c.UserID = ?
    ");
    $stmt->execute([$userId]);
    $cartItems = $stmt->fetchAll();
    
    if (empty($cartItems)) {
        $response['message'] = 'Your cart is empty';
        return;
    }
    
    // Check stock availability
    foreach ($cartItems as $item) {
        if ($item['Stock'] < $item['Quantity']) {
            $response['message'] = "Insufficient stock for {$item['ProductName']}";
            return;
        }
    }
    
    // Begin transaction
    $db->beginTransaction();
    
    try {
        // Create order
        $stmt = $db->prepare("INSERT INTO `Order` (UserID, Status) VALUES (?, 'processing')");
        $stmt->execute([$userId]);
        $orderId = $db->lastInsertId();
        
        $orderTotal = 0;
        
        // Create order items and update stock
        foreach ($cartItems as $item) {
            // Add order item
            $stmt = $db->prepare("
                INSERT INTO OrderItem (OrderID, ProductID, Quantity, UnitPriceAtOrder) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $orderId, 
                $item['ProductID'], 
                $item['Quantity'], 
                $item['FinalPrice']
            ]);
            
            // Update product stock
            $stmt = $db->prepare("
                UPDATE Product 
                SET Stock = Stock - ? 
                WHERE ProductID = ?
            ");
            $stmt->execute([$item['Quantity'], $item['ProductID']]);
            
            $orderTotal += ($item['Quantity'] * $item['FinalPrice']);
        }
        
        // Clear cart
        $stmt = $db->prepare("
            DELETE FROM CartItem 
            WHERE CartID = (SELECT CartID FROM Cart WHERE UserID = ?)
        ");
        $stmt->execute([$userId]);
        
        // Update user information if needed
        $stmt = $db->prepare("
            UPDATE User 
            SET FullName = ?, Email = ?, Mobile = ? 
            WHERE UserID = ?
        ");
        $stmt->execute([$fullName, $email, $mobile, $userId]);
        
        // Update or insert user address
        $stmt = $db->prepare("
            INSERT INTO UserAddress (UserID, AddressLine, CityID) 
            VALUES (?, ?, 1)
            ON DUPLICATE KEY UPDATE AddressLine = ?
        ");
        $stmt->execute([$userId, $address, $address]);
        
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = 'Order placed successfully!';
        $response['data'] = [
            'orderId' => $orderId,
            'orderTotal' => $orderTotal
        ];
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
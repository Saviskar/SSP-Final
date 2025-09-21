<?php
session_start();
require_once '../src/includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in to manage your cart';
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
            handleGetCart($db, $userId, $response);
            break;
            
        case 'POST':
            handleAddToCart($db, $userId, $response);
            break;
            
        case 'PUT':
            handleUpdateCart($db, $userId, $response);
            break;
            
        case 'DELETE':
            // >>> ADDED: Accept both body and query param for clearAll, plus optional id in query
            $raw = file_get_contents('php://input');
            $input = json_decode($raw, true);
            if (!is_array($input)) { $input = []; }

            // clearAll can come via body or query param (?clearAll=1 / true)
            $clearAll = null;
            if (isset($input['clearAll'])) {
                $clearAll = filter_var($input['clearAll'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
            if ($clearAll === null && isset($_GET['clearAll'])) {
                $clearAll = filter_var($_GET['clearAll'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            }
            $clearAll = (bool)($clearAll ?? false);

            if ($clearAll === true) {
                handleClearCart($db, $userId, $response);
            } else {
                // Allow id in body or query (?id=123) for single-item delete
                $cartItemId = 0;
                if (isset($input['cartItemId'])) {
                    $cartItemId = (int)$input['cartItemId'];
                } elseif (isset($_GET['cartItemId'])) {
                    $cartItemId = (int)$_GET['cartItemId'];
                } elseif (isset($_GET['id'])) { // common alias
                    $cartItemId = (int)$_GET['id'];
                }
                handleRemoveFromCart($db, $userId, $response, $cartItemId > 0 ? $cartItemId : null);
            }
            break;
            
        default:
            $response['message'] = 'Method not allowed';
            http_response_code(405);
            break;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Internal server error';
    error_log("Cart API error: " . $e->getMessage());
}

echo json_encode($response);

function handleGetCart($db, $userId, &$response) {
    // Get user's cart
    $stmt = $db->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
    $stmt->execute([$userId]);
    $cart = $stmt->fetch();
    
    if (!$cart) {
        // Create cart if doesn't exist
        $stmt = $db->prepare("INSERT INTO Cart (UserID) VALUES (?)");
        $stmt->execute([$userId]);
        $cartId = $db->lastInsertId();
    } else {
        $cartId = $cart['CartID'];
    }
    
    // Get cart items with product details
    $stmt = $db->prepare("
        SELECT 
            ci.CartItemID,
            ci.ProductID,
            ci.Quantity,
            p.ProductName,
            p.Price,
            p.ImageURL,
            p.Stock,
            (ci.Quantity * p.Price) as ItemTotal,
            COALESCE(pr.PromotionPercentage, 0) as DiscountPercentage,
            CASE 
                WHEN pr.PromotionPercentage IS NOT NULL 
                THEN p.Price * (1 - pr.PromotionPercentage / 100)
                ELSE p.Price 
            END as DiscountedPrice,
            CASE 
                WHEN pr.PromotionPercentage IS NOT NULL 
                THEN ROUND(ci.Quantity * p.Price * (1 - pr.PromotionPercentage / 100), 2)
                ELSE ROUND(ci.Quantity * p.Price, 2)
            END as DiscountedItemTotal
        FROM CartItem ci
        JOIN Product p ON ci.ProductID = p.ProductID
        LEFT JOIN ProductPromotion pp ON p.ProductID = pp.ProductID
        LEFT JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
        WHERE ci.CartID = ?
        ORDER BY ci.CreatedAt DESC
    ");
    $stmt->execute([$cartId]);
    $items = $stmt->fetchAll();
    
    // Calculate totals
    $subtotal = 0;
    $discountedSubtotal = 0;
    $totalItems = 0;
    
    foreach ($items as $item) {
        $subtotal += $item['ItemTotal'];
        $discountedSubtotal += $item['DiscountedItemTotal'];
        $totalItems += $item['Quantity'];
    }
    
    $shippingCost = 5.00; // Fixed shipping cost
    $total = $discountedSubtotal + $shippingCost;
    $savings = $subtotal - $discountedSubtotal;
    
    $response['success'] = true;
    $response['data'] = [
        'cartId' => $cartId,
        'items' => $items,
        'summary' => [
            'subtotal' => $subtotal,
            'discountedSubtotal' => $discountedSubtotal,
            'savings' => $savings,
            'shipping' => $shippingCost,
            'total' => $total,
            'totalItems' => $totalItems
        ]
    ];
}

function handleAddToCart($db, $userId, &$response) {
    $productId = (int)($_POST['productId'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);
    
    if ($productId <= 0 || $quantity <= 0) {
        $response['message'] = 'Invalid product or quantity';
        return;
    }
    
    // Check if product exists and has sufficient stock
    $stmt = $db->prepare("SELECT ProductID, ProductName, Stock FROM Product WHERE ProductID = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $response['message'] = 'Product not found';
        return;
    }
    
    if ($product['Stock'] < $quantity) {
        $response['message'] = 'Insufficient stock available';
        return;
    }
    
    // Get user's cart
    $stmt = $db->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
    $stmt->execute([$userId]);
    $cart = $stmt->fetch();
    
    if (!$cart) {
        // Create cart if doesn't exist
        $stmt = $db->prepare("INSERT INTO Cart (UserID) VALUES (?)");
        $stmt->execute([$userId]);
        $cartId = $db->lastInsertId();
    } else {
        $cartId = $cart['CartID'];
    }
    
    // Check if item already exists in cart
    $stmt = $db->prepare("SELECT CartItemID, Quantity FROM CartItem WHERE CartID = ? AND ProductID = ?");
    $stmt->execute([$cartId, $productId]);
    $existingItem = $stmt->fetch();
    
    if ($existingItem) {
        // Update quantity
        $newQuantity = $existingItem['Quantity'] + $quantity;
        
        if ($product['Stock'] < $newQuantity) {
            $response['message'] = 'Not enough stock to add more items';
            return;
        }
        
        $stmt = $db->prepare("UPDATE CartItem SET Quantity = ? WHERE CartItemID = ?");
        $stmt->execute([$newQuantity, $existingItem['CartItemID']]);
        
        $response['message'] = 'Cart updated successfully';
    } else {
        // Add new item
        $stmt = $db->prepare("
            INSERT INTO CartItem (CartID, ProductID, Quantity) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$cartId, $productId, $quantity]);
        
        $response['message'] = 'Item added to cart successfully';
    }
    
    $response['success'] = true;
}

function handleUpdateCart($db, $userId, &$response) {
    $input = json_decode(file_get_contents('php://input'), true);
    $cartItemId = (int)($input['cartItemId'] ?? 0);
    $quantity = (int)($input['quantity'] ?? 0);
    
    if ($cartItemId <= 0) {
        $response['message'] = 'Invalid cart item';
        return;
    }
    
    if ($quantity <= 0) {
        // Remove item if quantity is 0 or negative
        handleRemoveFromCart($db, $userId, $response, $cartItemId);
        return;
    }
    
    // Verify cart item belongs to user
    $stmt = $db->prepare("
        SELECT ci.CartItemID, ci.ProductID, p.Stock 
        FROM CartItem ci
        JOIN Cart c ON ci.CartID = c.CartID
        JOIN Product p ON ci.ProductID = p.ProductID
        WHERE ci.CartItemID = ? AND c.UserID = ?
    ");
    $stmt->execute([$cartItemId, $userId]);
    $item = $stmt->fetch();
    
    if (!$item) {
        $response['message'] = 'Cart item not found';
        return;
    }
    
    if ($item['Stock'] < $quantity) {
        $response['message'] = 'Insufficient stock available';
        return;
    }
    
    // Update quantity
    $stmt = $db->prepare("UPDATE CartItem SET Quantity = ? WHERE CartItemID = ?");
    $stmt->execute([$quantity, $cartItemId]);
    
    $response['success'] = true;
    $response['message'] = 'Cart updated successfully';
}

function handleRemoveFromCart($db, $userId, &$response, $cartItemId = null) {
    if ($cartItemId === null) {
        $input = json_decode(file_get_contents('php://input'), true);
        $cartItemId = (int)($input['cartItemId'] ?? 0);
    }
    
    if ($cartItemId <= 0) {
        $response['message'] = 'Invalid cart item';
        return;
    }
    
    // Verify cart item belongs to user
    $stmt = $db->prepare("
        SELECT ci.CartItemID 
        FROM CartItem ci
        JOIN Cart c ON ci.CartID = c.CartID
        WHERE ci.CartItemID = ? AND c.UserID = ?
    ");
    $stmt->execute([$cartItemId, $userId]);
    $item = $stmt->fetch();
    
    if (!$item) {
        $response['message'] = 'Cart item not found';
        return;
    }
    
    // Remove item
    $stmt = $db->prepare("DELETE FROM CartItem WHERE CartItemID = ?");
    $stmt->execute([$cartItemId]);
    
    $response['success'] = true;
    $response['message'] = 'Item removed from cart';
}

// >>> ADDED: clear-all handler for DELETE clearAll
function handleClearCart($db, $userId, &$response) {
    // Find the user's cart
    $stmt = $db->prepare("SELECT CartID FROM Cart WHERE UserID = ?");
    $stmt->execute([$userId]);
    $cart = $stmt->fetch();

    // If no cart, treat as idempotent success
    if (!$cart) {
        $response['success'] = true;
        $response['message'] = 'Cart cleared';
        return;
    }

    $cartId = (int)$cart['CartID'];

    // Delete all items for this cart
    $stmt = $db->prepare("DELETE FROM CartItem WHERE CartID = ?");
    $stmt->execute([$cartId]);

    $response['success'] = true;
    $response['message'] = 'Cart cleared';
}
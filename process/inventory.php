<?php
session_start();
require_once '../src/includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];

// Check if user is admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
    $response['message'] = 'Unauthorized access';
    http_response_code(401);
    echo json_encode($response);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            handleGetInventory($db, $response);
            break;
            
        case 'PUT':
            handleUpdateStock($db, $response);
            break;
            
        case 'DELETE':
            handleDeleteProduct($db, $response);
            break;
            
        default:
            $response['message'] = 'Method not allowed';
            http_response_code(405);
            break;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Internal server error';
    error_log("Inventory API error: " . $e->getMessage());
}

echo json_encode($response);

function handleGetInventory($db, &$response) {
    $lowStockThreshold = (int)($_GET['lowStockThreshold'] ?? 10);
    $category = $_GET['category'] ?? '';
    
    $sql = "
        SELECT 
            p.ProductID,
            p.ProductName,
            p.Price,
            p.Stock,
            c.CategoryName,
            COALESCE(SUM(oi.Quantity), 0) as TotalSold,
            CASE 
                WHEN p.Stock <= ? THEN 'low'
                WHEN p.Stock = 0 THEN 'out'
                ELSE 'normal'
            END as StockStatus
        FROM Product p
        JOIN Category c ON p.CategoryID = c.CategoryID
        LEFT JOIN OrderItem oi ON p.ProductID = oi.ProductID
        WHERE 1=1
    ";
    
    $params = [$lowStockThreshold];
    
    if ($category) {
        $sql .= " AND c.CategoryName = ?";
        $params[] = $category;
    }
    
    $sql .= " 
        GROUP BY p.ProductID, p.ProductName, p.Price, p.Stock, c.CategoryName
        ORDER BY p.Stock ASC, p.ProductName
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $inventory = $stmt->fetchAll();
    
    // Get stock statistics
    $stmt = $db->prepare("
        SELECT 
            COUNT(*) as TotalProducts,
            SUM(CASE WHEN Stock = 0 THEN 1 ELSE 0 END) as OutOfStock,
            SUM(CASE WHEN Stock <= ? AND Stock > 0 THEN 1 ELSE 0 END) as LowStock,
            SUM(Stock * Price) as TotalInventoryValue
        FROM Product
    ");
    $stmt->execute([$lowStockThreshold]);
    $stats = $stmt->fetch();
    
    $response['success'] = true;
    $response['data'] = [
        'inventory' => $inventory,
        'statistics' => $stats
    ];
}

function handleUpdateStock($db, &$response) {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($input['productId'] ?? 0);
    $newStock = (int)($input['stock'] ?? 0);
    $action = $input['action'] ?? 'set'; // 'set', 'add', 'subtract'
    
    if ($productId <= 0) {
        $response['message'] = 'Valid product ID is required';
        return;
    }
    
    if ($newStock < 0) {
        $response['message'] = 'Stock cannot be negative';
        return;
    }
    
    // Check if product exists
    $stmt = $db->prepare("SELECT ProductID, Stock, ProductName FROM Product WHERE ProductID = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $response['message'] = 'Product not found';
        return;
    }
    
    $finalStock = $newStock;
    
    if ($action === 'add') {
        $finalStock = $product['Stock'] + $newStock;
    } elseif ($action === 'subtract') {
        $finalStock = max(0, $product['Stock'] - $newStock);
    }
    
    // Update stock
    $stmt = $db->prepare("UPDATE Product SET Stock = ? WHERE ProductID = ?");
    $stmt->execute([$finalStock, $productId]);
    
    $response['success'] = true;
    $response['message'] = "Stock updated successfully for {$product['ProductName']}";
    $response['data'] = [
        'productId' => $productId,
        'oldStock' => $product['Stock'],
        'newStock' => $finalStock
    ];
}

function handleDeleteProduct($db, &$response) {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($input['productId'] ?? 0);
    
    if ($productId <= 0) {
        $response['message'] = 'Valid product ID is required';
        return;
    }
    
    // Check if product exists and get name
    $stmt = $db->prepare("SELECT ProductName FROM Product WHERE ProductID = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        $response['message'] = 'Product not found';
        return;
    }
    
    // Check if product has been ordered
    $stmt = $db->prepare("SELECT COUNT(*) as OrderCount FROM OrderItem WHERE ProductID = ?");
    $stmt->execute([$productId]);
    $orderCount = $stmt->fetch()['OrderCount'];
    
    if ($orderCount > 0) {
        $response['message'] = 'Cannot delete product that has been ordered. Consider setting stock to 0 instead.';
        return;
    }
    
    $db->beginTransaction();
    
    try {
        // Delete from cart items first (foreign key constraint)
        $stmt = $db->prepare("DELETE FROM CartItem WHERE ProductID = ?");
        $stmt->execute([$productId]);
        
        // Delete promotions
        $stmt = $db->prepare("DELETE FROM ProductPromotion WHERE ProductID = ?");
        $stmt->execute([$productId]);
        
        // Delete product
        $stmt = $db->prepare("DELETE FROM Product WHERE ProductID = ?");
        $stmt->execute([$productId]);
        
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = "Product '{$product['ProductName']}' deleted successfully";
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
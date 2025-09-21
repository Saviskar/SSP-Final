<?php
session_start();
require_once '../src/includes/db.php';

header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => ''];
$method = $_SERVER['REQUEST_METHOD'];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            handleGetPromotions($db, $response);
            break;
            
        case 'POST':
            // Admin only
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
                $response['message'] = 'Unauthorized access';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            handleAddPromotion($db, $response);
            break;
            
        case 'PUT':
            // Admin only
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
                $response['message'] = 'Unauthorized access';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            handleUpdatePromotion($db, $response);
            break;
            
        case 'DELETE':
            // Admin only
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
                $response['message'] = 'Unauthorized access';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            handleDeletePromotion($db, $response);
            break;
            
        default:
            $response['message'] = 'Method not allowed';
            http_response_code(405);
            break;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Internal server error';
    error_log("Promotions API error: " . $e->getMessage());
}

echo json_encode($response);

function handleGetPromotions($db, &$response) {
    // Get all promotions with product details
    $stmt = $db->prepare("
        SELECT 
            pp.ProductID,
            pp.PromotionID,
            p.ProductName,
            pr.PromotionPercentage
        FROM ProductPromotion pp
        JOIN Product p ON pp.ProductID = p.ProductID
        JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
        ORDER BY pp.ProductID
    ");
    $stmt->execute();
    $promotions = $stmt->fetchAll();
    
    // Get all available products for admin interface
    $stmt = $db->prepare("
        SELECT ProductID, ProductName 
        FROM Product 
        ORDER BY ProductName
    ");
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    $response['success'] = true;
    $response['data'] = [
        'promotions' => $promotions,
        'products' => $products
    ];
}

function handleAddPromotion($db, &$response) {
    $productId = (int)($_POST['productId'] ?? 0);
    $discountPercentage = (float)($_POST['discountPercentage'] ?? 0);
    
    // Validation
    if ($productId <= 0) {
        $response['message'] = 'Valid product is required';
        return;
    }
    
    if ($discountPercentage <= 0 || $discountPercentage > 100) {
        $response['message'] = 'Discount percentage must be between 0 and 100';
        return;
    }
    
    // Check if product exists
    $stmt = $db->prepare("SELECT ProductID FROM Product WHERE ProductID = ?");
    $stmt->execute([$productId]);
    if (!$stmt->fetch()) {
        $response['message'] = 'Product not found';
        return;
    }
    
    // Check if promotion already exists for this product
    $stmt = $db->prepare("SELECT ProductID FROM ProductPromotion WHERE ProductID = ?");
    $stmt->execute([$productId]);
    if ($stmt->fetch()) {
        $response['message'] = 'Promotion already exists for this product';
        return;
    }
    
    $db->beginTransaction();
    
    try {
        // Create promotion
        $stmt = $db->prepare("INSERT INTO Promotion (PromotionPercentage) VALUES (?)");
        $stmt->execute([$discountPercentage]);
        $promotionId = $db->lastInsertId();
        
        // Link product to promotion
        $stmt = $db->prepare("INSERT INTO ProductPromotion (ProductID, PromotionID) VALUES (?, ?)");
        $stmt->execute([$productId, $promotionId]);
        
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = 'Promotion added successfully';
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}

function handleUpdatePromotion($db, &$response) {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($input['productId'] ?? 0);
    $discountPercentage = (float)($input['discountPercentage'] ?? 0);
    
    // Validation
    if ($productId <= 0) {
        $response['message'] = 'Valid product is required';
        return;
    }
    
    if ($discountPercentage <= 0 || $discountPercentage > 100) {
        $response['message'] = 'Discount percentage must be between 0 and 100';
        return;
    }
    
    // Get existing promotion
    $stmt = $db->prepare("
        SELECT pp.PromotionID 
        FROM ProductPromotion pp 
        WHERE pp.ProductID = ?
    ");
    $stmt->execute([$productId]);
    $promotion = $stmt->fetch();
    
    if (!$promotion) {
        $response['message'] = 'Promotion not found';
        return;
    }
    
    // Update promotion percentage
    $stmt = $db->prepare("UPDATE Promotion SET PromotionPercentage = ? WHERE PromotionID = ?");
    $stmt->execute([$discountPercentage, $promotion['PromotionID']]);
    
    $response['success'] = true;
    $response['message'] = 'Promotion updated successfully';
}

function handleDeletePromotion($db, &$response) {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($input['productId'] ?? 0);
    
    if ($productId <= 0) {
        $response['message'] = 'Valid product is required';
        return;
    }
    
    // Get promotion ID
    $stmt = $db->prepare("SELECT PromotionID FROM ProductPromotion WHERE ProductID = ?");
    $stmt->execute([$productId]);
    $promotion = $stmt->fetch();
    
    if (!$promotion) {
        $response['message'] = 'Promotion not found';
        return;
    }
    
    $db->beginTransaction();
    
    try {
        // Delete product promotion link
        $stmt = $db->prepare("DELETE FROM ProductPromotion WHERE ProductID = ?");
        $stmt->execute([$productId]);
        
        // Delete promotion
        $stmt = $db->prepare("DELETE FROM Promotion WHERE PromotionID = ?");
        $stmt->execute([$promotion['PromotionID']]);
        
        $db->commit();
        
        $response['success'] = true;
        $response['message'] = 'Promotion deleted successfully';
        
    } catch (Exception $e) {
        $db->rollBack();
        throw $e;
    }
}
?>
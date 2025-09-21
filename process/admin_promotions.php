<?php
require_once '../src/includes/config.php';
require_once '../src/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $db = getDB();
    
    switch($method) {
        case 'GET':
            // Get all promotions with product associations
            $stmt = $db->query("
                SELECT pp.ProductID, pr.PromotionID, pr.PromotionPercentage as Discount,
                       p.ProductName
                FROM ProductPromotion pp
                JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
                JOIN Product p ON pp.ProductID = p.ProductID
                ORDER BY pp.ProductID
            ");
            $result = $stmt->fetchAll();
            echo json_encode($result);
            break;
            
        case 'POST':
            // Add new promotion
            $db->beginTransaction();
            
            // First create the promotion
            $stmt = $db->prepare("INSERT INTO Promotion (PromotionPercentage) VALUES (?)");
            $stmt->execute([$input['discount']]);
            $promotionId = $db->lastInsertId();
            
            // Then associate with product
            $stmt = $db->prepare("INSERT INTO ProductPromotion (ProductID, PromotionID) VALUES (?, ?)");
            $stmt->execute([$input['productId'], $promotionId]);
            
            $db->commit();
            echo json_encode(['success' => true, 'id' => $promotionId]);
            break;
            
        case 'PUT':
            // Update promotion
            $stmt = $db->prepare("UPDATE Promotion SET PromotionPercentage = ? WHERE PromotionID = ?");
            $stmt->execute([$input['discount'], $input['promotionId']]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            // Delete promotion
            $promotionId = $input['promotionId'] ?? $_GET['id'];
            $db->beginTransaction();
            
            // Remove product associations first
            $stmt = $db->prepare("DELETE FROM ProductPromotion WHERE PromotionID = ?");
            $stmt->execute([$promotionId]);
            
            // Then remove the promotion itself
            $stmt = $db->prepare("DELETE FROM Promotion WHERE PromotionID = ?");
            $stmt->execute([$promotionId]);
            
            $db->commit();
            echo json_encode(['success' => true]);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

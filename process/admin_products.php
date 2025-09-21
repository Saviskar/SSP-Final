<?php
require_once '../src/includes/config.php';
require_once '../src/includes/db.php';

header('Content-Type: application/json');

// Handle different HTTP methods
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $db = getDB();
    
    switch($method) {
        case 'GET':
            if (isset($_GET['id'])) {
                // Get single product
                $stmt = $db->prepare("
                    SELECT p.*, c.CategoryName 
                    FROM Product p 
                    LEFT JOIN Category c ON p.CategoryID = c.CategoryID 
                    WHERE p.ProductID = ?
                ");
                $stmt->execute([$_GET['id']]);
                $result = $stmt->fetch();
                echo json_encode($result);
            } else {
                // Get all products
                $stmt = $db->query("
                    SELECT p.ProductID, p.ProductName, p.Stock, p.Price, c.CategoryName,
                           COALESCE(pr.PromotionPercentage, 0) as Discount
                    FROM Product p 
                    LEFT JOIN Category c ON p.CategoryID = c.CategoryID
                    LEFT JOIN ProductPromotion pp ON p.ProductID = pp.ProductID
                    LEFT JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
                    ORDER BY p.ProductID
                ");
                $result = $stmt->fetchAll();
                echo json_encode($result);
            }
            break;
            
        case 'POST':
            // Add new product
            $stmt = $db->prepare("
                INSERT INTO Product (CategoryID, ProductName, Description, Price, Stock, ImageURL) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $input['categoryId'],
                $input['productName'],
                $input['description'],
                $input['price'],
                $input['stock'],
                $input['imageUrl'] ?? null
            ]);
            
            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
            break;
            
        case 'PUT':
            // Update product
            $stmt = $db->prepare("
                UPDATE Product 
                SET CategoryID = ?, ProductName = ?, Description = ?, Price = ?, Stock = ?, ImageURL = ?
                WHERE ProductID = ?
            ");
            $stmt->execute([
                $input['categoryId'],
                $input['productName'],
                $input['description'],
                $input['price'],
                $input['stock'],
                $input['imageUrl'] ?? null,
                $input['productId']
            ]);
            
            echo json_encode(['success' => true]);
            break;
            
        case 'DELETE':
            // Delete product
            $productId = $input['productId'] ?? $_GET['id'];
            $stmt = $db->prepare("DELETE FROM Product WHERE ProductID = ?");
            $stmt->execute([$productId]);
            
            echo json_encode(['success' => true]);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

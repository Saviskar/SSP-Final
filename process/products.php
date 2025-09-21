<?php
require_once '../src/includes/db.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = ['success' => false, 'data' => null, 'message' => ''];

try {
    $db = getDB();
    
    switch ($method) {
        case 'GET':
            handleGetProducts($db, $response);
            break;
            
        case 'POST':
            session_start();
            if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'ADMIN') {
                $response['message'] = 'Unauthorized access';
                http_response_code(401);
                echo json_encode($response);
                exit();
            }
            handleAddProduct($db, $response);
            break;
            
        default:
            $response['message'] = 'Method not allowed';
            http_response_code(405);
            break;
    }
    
} catch (Exception $e) {
    $response['message'] = 'Internal server error';
    error_log("Products API error: " . $e->getMessage());
}

echo json_encode($response);

function handleGetProducts($db, &$response) {
    $category = $_GET['category'] ?? '';
    $search = $_GET['search'] ?? '';
    $limit = (int)($_GET['limit'] ?? 50);
    $offset = (int)($_GET['offset'] ?? 0);
    
    $sql = "
        SELECT 
            p.ProductID, 
            p.ProductName, 
            p.Description, 
            p.Price, 
            p.Stock, 
            p.ImageURL,
            c.CategoryName,
            COALESCE(pr.PromotionPercentage, 0) as DiscountPercentage,
            CASE 
                WHEN pr.PromotionPercentage IS NOT NULL 
                THEN ROUND(p.Price * (1 - pr.PromotionPercentage / 100), 2)
                ELSE ROUND(p.Price, 2)
            END as DiscountedPrice
        FROM Product p
        JOIN Category c ON p.CategoryID = c.CategoryID
        LEFT JOIN ProductPromotion pp ON p.ProductID = pp.ProductID
        LEFT JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
        WHERE 1=1
    ";
    
    $params = [];
    
    if ($category) {
        $sql .= " AND c.CategoryName = ?";
        $params[] = $category;
    }
    
    if ($search) {
        $sql .= " AND (p.ProductName LIKE ? OR p.Description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    $sql .= " ORDER BY p.ProductID DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll();
    
    // Get categories for filtering
    $stmt = $db->prepare("SELECT CategoryID, CategoryName FROM Category ORDER BY CategoryName");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    $response['success'] = true;
    $response['data'] = [
        'products' => $products,
        'categories' => $categories
    ];
}

function handleAddProduct($db, &$response) {
    $productName = trim($_POST['productName'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock'] ?? 0);
    $categoryId = (int)($_POST['categoryId'] ?? 0);
    $imageUrl = trim($_POST['imageUrl'] ?? '');
    
    // Validation
    $errors = [];
    
    if (empty($productName)) {
        $errors[] = "Product name is required.";
    }
    
    if ($price <= 0) {
        $errors[] = "Price must be greater than 0.";
    }
    
    if ($stock < 0) {
        $errors[] = "Stock cannot be negative.";
    }
    
    if ($categoryId <= 0) {
        $errors[] = "Valid category is required.";
    }
    
    // Check if category exists
    if ($categoryId > 0) {
        $stmt = $db->prepare("SELECT CategoryID FROM Category WHERE CategoryID = ?");
        $stmt->execute([$categoryId]);
        if (!$stmt->fetch()) {
            $errors[] = "Selected category does not exist.";
        }
    }
    
    if (!empty($errors)) {
        $response['message'] = implode(' ', $errors);
        return;
    }
    
    // Insert product
    $stmt = $db->prepare("
        INSERT INTO Product (CategoryID, ProductName, Description, Price, Stock, ImageURL) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$categoryId, $productName, $description, $price, $stock, $imageUrl]);
    
    $productId = $db->lastInsertId();
    
    $response['success'] = true;
    $response['message'] = "Product added successfully!";
    $response['data'] = ['productId' => $productId];
}
?>
<?php
require_once '../src/includes/config.php';
require_once '../src/includes/db.php';


header('Content-Type: application/json');

try {
    $db = getDB();
    
    // Get all categories
    $stmt = $db->query("SELECT CategoryID, CategoryName FROM Category ORDER BY CategoryName");
    $result = $stmt->fetchAll();
    
    echo json_encode($result);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

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
            // [CHANGED] Removed ProductsIDxQuantity column
            $stmt = $db->query("
                SELECT 
                    o.OrderID,
                    u.FullName AS Customer,
                    o.PlacedAt AS OrderPlacedDate,
                    SUM(oi.Quantity * oi.UnitPriceAtOrder) AS Total,
                    o.Status,
                    CONCAT(ua.AddressLine, ', ', c.CityName, ', ', pr.ProvinceName) AS DeliveryAddress
                FROM `Order` o
                JOIN User u ON o.UserID = u.UserID
                LEFT JOIN OrderItem oi ON o.OrderID = oi.OrderID
                LEFT JOIN Product p ON oi.ProductID = p.ProductID
                LEFT JOIN UserAddress ua ON u.UserID = ua.UserID
                LEFT JOIN City c ON ua.CityID = c.CityID
                LEFT JOIN Province pr ON c.ProvinceID = pr.ProvinceID
                GROUP BY 
                    o.OrderID, u.FullName, o.PlacedAt, o.Status, ua.AddressLine, c.CityName, pr.ProvinceName
                ORDER BY o.OrderID
            ");
            $result = $stmt->fetchAll();

            // Format for UI
            foreach($result as &$order) {
                $order['OrderPlacedDate'] = date('d/m/Y', strtotime($order['OrderPlacedDate']));
                $order['Total'] = '$' . number_format($order['Total'], 0);
                // [CHANGED] This field no longer exists; ensure it’s not referenced by UI
                // unset($order['ProductsIDxQuantity']);
            }
            
            echo json_encode($result);
            break;
            
        case 'PUT':
            // Update order status
            $stmt = $db->prepare("UPDATE `Order` SET Status = ? WHERE OrderID = ?");
            $stmt->execute([$input['status'], $input['orderId']]);
            echo json_encode(['success' => true]);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}

<?php
require_once '../src/includes/config.php';
require_once '../src/includes/db.php';

header('Content-Type: application/json');

try {
    $db = getDB();

    // Normalize method + body and allow method override
    $method = $_SERVER['REQUEST_METHOD'];
    $raw    = file_get_contents('php://input');
    $json   = $raw ? json_decode($raw, true) : null;
    $input  = is_array($json) ? $json : $_POST;

    if ($method === 'POST' && isset($input['_method'])) {
        $method = strtoupper($input['_method']);
    }

    switch ($method) {
        case 'GET': {
            // Orders list
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
            $rows = $stmt->fetchAll();

            foreach ($rows as &$order) {
                $order['OrderPlacedDate'] = date('d/m/Y', strtotime($order['OrderPlacedDate']));
                $order['Total'] = '$' . number_format((float)$order['Total'], 0);
            }

            echo json_encode($rows);
            break;
        }

        case 'PUT': {
            // Validate and update status
            $orderId = isset($input['orderId']) ? (int)$input['orderId'] : 0;
            $status  = isset($input['status']) ? strtolower(trim($input['status'])) : '';

            // Allowed states (adjust if you have different ones)
            $allowed = ['pending','processing','shipped','delivered','cancelled'];

            if ($orderId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid orderId']);
                exit;
            }
            if (!in_array($status, $allowed, true)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid status']);
                exit;
            }

            $stmt = $db->prepare("UPDATE `Order` SET Status = ? WHERE OrderID = ?");
            $stmt->execute([$status, $orderId]);

            echo json_encode(['success' => true]);
            break;
        }

        default: {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

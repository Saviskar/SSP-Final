<?php
require_once '../src/includes/config.php';
require_once '../src/includes/db.php';

header('Content-Type: application/json');

try {
    $db = getDB();

    // Normalize method + body (supports JSON and form-encoded with _method override)
    $method = $_SERVER['REQUEST_METHOD'];
    $raw    = file_get_contents('php://input');
    $json   = $raw ? json_decode($raw, true) : null;

    // Prefer JSON body if valid, else fall back to $_POST (form-encoded)
    $input = is_array($json) ? $json : $_POST;

    // Allow method override: POST + _method=PUT/DELETE
    if ($method === 'POST' && isset($input['_method'])) {
        $method = strtoupper($input['_method']);
    }

    switch ($method) {
        case 'GET': {
            // All promotions with product associations
            $stmt = $db->query("
                SELECT 
                    pp.ProductID,
                    pr.PromotionID,
                    pr.PromotionPercentage AS Discount,
                    p.ProductName
                FROM ProductPromotion pp
                JOIN Promotion pr ON pp.PromotionID = pr.PromotionID
                JOIN Product p    ON pp.ProductID    = p.ProductID
                ORDER BY pp.ProductID
            ");
            echo json_encode($stmt->fetchAll());
            break;
        }

        case 'POST': {
            // Create new promotion and link to a product
            $discount  = isset($input['discount']) ? filter_var($input['discount'], FILTER_VALIDATE_FLOAT) : null;
            $productId = isset($input['productId']) ? (int)$input['productId'] : 0;

            if ($discount === false || $discount === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid discount']);
                exit;
            }
            if ($discount < 0 || $discount > 90) { // keep consistent with your UI rule
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Discount must be between 0 and 90']);
                exit;
            }
            if ($productId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid productId']);
                exit;
            }

            $db->beginTransaction();

            $stmt = $db->prepare("INSERT INTO Promotion (PromotionPercentage) VALUES (?)");
            $stmt->execute([$discount]);
            $promotionId = (int)$db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO ProductPromotion (ProductID, PromotionID) VALUES (?, ?)");
            $stmt->execute([$productId, $promotionId]);

            $db->commit();
            echo json_encode(['success' => true, 'id' => $promotionId]);
            break;
        }

        case 'PUT': {
            // Update promotion percentage; optionally re-assign to a different product
            $promotionId = isset($input['promotionId']) ? (int)$input['promotionId'] : 0;
            $discount    = isset($input['discount']) ? filter_var($input['discount'], FILTER_VALIDATE_FLOAT) : null;
            $newProduct  = isset($input['productId']) && $input['productId'] !== '' ? (int)$input['productId'] : null;

            if ($promotionId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid promotionId']);
                exit;
            }
            if ($discount === false || $discount === null) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid discount']);
                exit;
            }
            if ($discount < 0 || $discount > 90) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Discount must be between 0 and 90']);
                exit;
            }

            $db->beginTransaction();

            // Update discount
            $stmt = $db->prepare("UPDATE Promotion SET PromotionPercentage = ? WHERE PromotionID = ?");
            $stmt->execute([$discount, $promotionId]);

            // Optional: re-assign the product link for this promotion
            if ($newProduct !== null) {
                if ($newProduct <= 0) {
                    $db->rollBack();
                    http_response_code(400);
                    echo json_encode(['success' => false, 'error' => 'Invalid productId']);
                    exit;
                }
                // If a promotion links to multiple products, this will move ALL its links.
                $stmt = $db->prepare("UPDATE ProductPromotion SET ProductID = ? WHERE PromotionID = ?");
                $stmt->execute([$newProduct, $promotionId]);
            }

            $db->commit();
            echo json_encode(['success' => true]);
            break;
        }

        case 'DELETE': {
            // Delete promotion (and its product links)
            $promotionId = 0;
            if (isset($input['promotionId'])) {
                $promotionId = (int)$input['promotionId'];
            } elseif (isset($_GET['id'])) {
                $promotionId = (int)$_GET['id'];
            }

            if ($promotionId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Invalid promotionId']);
                exit;
            }

            $db->beginTransaction();

            $stmt = $db->prepare("DELETE FROM ProductPromotion WHERE PromotionID = ?");
            $stmt->execute([$promotionId]);

            $stmt = $db->prepare("DELETE FROM Promotion WHERE PromotionID = ?");
            $stmt->execute([$promotionId]);

            $db->commit();
            echo json_encode(['success' => true]);
            break;
        }

        default: {
            http_response_code(405);
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        }
    }
} catch (Exception $e) {
    if (isset($db) && $db instanceof PDO && $db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

<?php
/**
 * Admin Products API
 * Handles product updates (price, stock) and deletions
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Check authentication
$is_authenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;

if (!$is_authenticated) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Set JSON response header
header('Content-Type: application/json');

// Get request data
$input = json_decode(file_get_contents('php://input'), true);
$action = $input['action'] ?? '';
$product_id = intval($input['product_id'] ?? 0);

if (!$product_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

try {
    $pdo = Database::connect();

    switch ($action) {
        case 'update_price':
            $price = floatval($input['price'] ?? 0);
            if ($price <= 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid price']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE products SET price = :price WHERE id = :id");
            $stmt->execute([':price' => $price, ':id' => $product_id]);
            echo json_encode(['success' => true, 'message' => 'Price updated']);
            break;

        case 'update_stock':
            $stock = intval($input['stock'] ?? 0);
            if ($stock < 0) {
                echo json_encode(['success' => false, 'message' => 'Invalid stock quantity']);
                exit;
            }
            
            $stmt = $pdo->prepare("UPDATE products SET stock = :stock WHERE id = :id");
            $stmt->execute([':stock' => $stock, ':id' => $product_id]);
            echo json_encode(['success' => true, 'message' => 'Stock updated']);
            break;

        case 'delete':
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute([':id' => $product_id]);
            echo json_encode(['success' => true, 'message' => 'Product deleted']);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

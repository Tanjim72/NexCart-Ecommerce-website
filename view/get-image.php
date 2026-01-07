<?php
/**
 * Image Retrieval Script
 * Serves product images stored as LONGBLOB in database
 * 
 * Usage: <img src="/admin/get-image.php?id=1" alt="Product">
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Get product ID
$product_id = intval($_GET['id'] ?? 0);

if (!$product_id) {
    http_response_code(400);
    die('Invalid product ID');
}

try {
    $pdo = Database::connect();
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product || empty($product['image'])) {
        http_response_code(404);
        // Return a placeholder image (1x1 transparent GIF)
        header('Content-Type: image/gif');
        echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        exit;
    }

    // Detect image type from BLOB data
    $image_data = $product['image'];
    $image_type = 'image/jpeg'; // Default
    
    // Check for JPEG signature
    if (substr($image_data, 0, 3) === "\xFF\xD8\xFF") {
        $image_type = 'image/jpeg';
    }
    // Check for PNG signature
    elseif (substr($image_data, 0, 8) === "\x89PNG\r\n\x1a\n") {
        $image_type = 'image/png';
    }
    // Check for GIF signature
    elseif (substr($image_data, 0, 3) === 'GIF') {
        $image_type = 'image/gif';
    }
    // Check for WebP signature
    elseif (strpos($image_data, 'WEBP') !== false && substr($image_data, 0, 4) === 'RIFF') {
        $image_type = 'image/webp';
    }

    header('Content-Type: ' . $image_type);
    header('Cache-Control: public, max-age=86400'); // Cache for 24 hours
    header('Content-Length: ' . strlen($image_data));

    // Output the image binary data
    echo $image_data;
    exit;

} catch (Exception $e) {
    http_response_code(500);
    die('Error retrieving image: ' . htmlspecialchars($e->getMessage()));
}

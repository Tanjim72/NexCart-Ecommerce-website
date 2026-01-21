<?php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    require_once __DIR__ . '/../includes/functions.php';

    // Get all products
    $products = getProducts();
    
    // Get cart for display
    $cart = getCart();
    $cart_totals = calculateCartTotals();
    
    $page_title = 'Shop';
    
    include __DIR__ . '/../includes/header.php';
    include __DIR__ . '/../includes/nav.php';

    // Include page-specific HTML templates
    include __DIR__ . '/templates/products.php';
    include __DIR__ . '/templates/checkout.php';
    include __DIR__ . '/templates/payment.php';
    include __DIR__ . '/templates/tracking.php';
    include __DIR__ . '/templates/invoice.php';

    include __DIR__ . '/../includes/footer.php';
    
} catch (Throwable $e) {
    echo '<pre style="background:#fee; padding:20px; margin:20px; border:1px solid #c00;">';
    echo 'ERROR: ' . htmlspecialchars($e->getMessage()) . "\n";
    echo 'File: ' . htmlspecialchars($e->getFile()) . "\n";
    echo 'Line: ' . $e->getLine() . "\n";
    echo htmlspecialchars($e->getTraceAsString());
    echo '</pre>';
}


?>

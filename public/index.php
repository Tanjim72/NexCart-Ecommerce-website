<?php
/**
 * Main Product Listing Page
 */

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/functions.php';

// Get all products
$products = getProducts();

// Get cart for display
$cart = getCart();
$cart_totals = calculateCartTotals();

$page_title = 'Shop';

// Page-specific CSS files
$page_css = [
    'product-page.css',
    'cart-page.css'
];

// Page-specific JS files (in order of dependencies)
$page_js = [
    'product-page.js',
    'cart-page.js',
    'checkout-page.js',
    'payment-page.js'
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';

// Include page-specific HTML templates
include __DIR__ . '/templates/products.php';
include __DIR__ . '/templates/checkout.php';
include __DIR__ . '/templates/payment.php';
include __DIR__ . '/templates/tracking.php';
include __DIR__ . '/templates/invoice.php';

include __DIR__ . '/../includes/footer.php';
?>

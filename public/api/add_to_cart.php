<?php
/**
 * API: Add Item to Cart
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['error' => 'Method not allowed'], 405);
}

// Verify CSRF token
$csrf_token = $_POST['csrf_token'] ?? '';
if (!verifyCsrfToken($csrf_token)) {
    jsonResponse(['error' => 'CSRF token invalid'], 403);
}

// Get and validate input
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
$quantity = isset($_POST['quantity']) ? max(1, (int)$_POST['quantity']) : 1;

if (!$product_id) {
    jsonResponse(['error' => 'Invalid product ID'], 400);
}

// Get product from database
$product = getProductById($product_id);
if (!$product) {
    jsonResponse(['error' => 'Product not found'], 404);
}

// Add to session cart
addToCartSession($product_id, $product['name'], $product['price'], $quantity);

// Get updated cart totals
$cart = getCart();
$totals = calculateCartTotals(isset($_SESSION['shipping_cost']) ? $_SESSION['shipping_cost'] : 0);

jsonResponse([
    'success' => true,
    'message' => 'Product added to cart',
    'cart_count' => $totals['item_count'],
    'cart_count_text' => $totals['item_count_text'],
    'subtotal' => $totals['subtotal'],
    'total' => $totals['total'],
    'cart' => $cart,
]);

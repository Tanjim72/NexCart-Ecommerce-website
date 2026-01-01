<?php
/**
 * API: Remove Item from Cart
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

if (!$product_id) {
    jsonResponse(['error' => 'Invalid product ID'], 400);
}

// Remove from cart
removeFromCartSession($product_id);

// Get updated cart totals
$cart = getCart();
$totals = calculateCartTotals(isset($_SESSION['shipping_cost']) ? $_SESSION['shipping_cost'] : 0);

jsonResponse([
    'success' => true,
    'message' => 'Item removed from cart',
    'cart_count' => $totals['item_count'],
    'cart_count_text' => $totals['item_count_text'],
    'subtotal' => $totals['subtotal'],
    'total' => $totals['total'],
    'cart' => $cart,
]);

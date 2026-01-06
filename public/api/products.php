<?php
/**
 * API: Get All Products as JSON
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Get all products
$products = getProducts();

jsonResponse([
    'success' => true,
    'products' => $products,
]);

<?php
/**
 * Cart API Handler
 * ============================================
 * Unified handler for all cart operations:
 * - add_to_cart
 * - remove_from_cart
 * - update_cart
 * - process_checkout
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

// Determine action
$action = $_POST['action'] ?? '';

try {
    switch ($action) {
        case 'add':
            handleAddToCart();
            break;
        case 'remove':
            handleRemoveFromCart();
            break;
        case 'update':
            handleUpdateCart();
            break;
        case 'checkout':
            handleCheckout();
            break;
        default:
            jsonResponse(['error' => 'Invalid action'], 400);
    }
} catch (Exception $e) {
    jsonResponse(['error' => 'An error occurred: ' . $e->getMessage()], 500);
}

/**
 * Handle adding item to cart
 */
function handleAddToCart() {
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
}

/**
 * Handle removing item from cart
 */
function handleRemoveFromCart() {
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
}

/**
 * Handle updating cart item quantity
 */
function handleUpdateCart() {
    // Get and validate input
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;

    if (!$product_id || $quantity === null) {
        jsonResponse(['error' => 'Invalid request'], 400);
    }

    // Update quantity
    updateCartItemQuantity($product_id, $quantity);

    // Get updated cart totals
    $cart = getCart();
    $totals = calculateCartTotals(isset($_SESSION['shipping_cost']) ? $_SESSION['shipping_cost'] : 0);

    jsonResponse([
        'success' => true,
        'message' => 'Cart updated',
        'cart_count' => $totals['item_count'],
        'cart_count_text' => $totals['item_count_text'],
        'subtotal' => $totals['subtotal'],
        'total' => $totals['total'],
        'cart' => $cart,
    ]);
}

/**
 * Handle checkout
 */
function handleCheckout() {
    // Get cart
    $cart = getCart();
    if (empty($cart)) {
        jsonResponse(['error' => 'Cart is empty'], 400);
    }

    // Validate form data
    $errors = validateCheckoutForm($_POST);
    if (!empty($errors)) {
        jsonResponse(['error' => 'Validation failed', 'errors' => $errors], 422);
    }

    // Sanitize inputs
    $form_data = [
        'firstName' => sanitize($_POST['firstName'] ?? ''),
        'lastName' => sanitize($_POST['lastName'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'address' => sanitize($_POST['address'] ?? ''),
        'city' => sanitize($_POST['city'] ?? ''),
        'state' => sanitize($_POST['state'] ?? ''),
        'postalCode' => sanitize($_POST['postalCode'] ?? ''),
        'country' => sanitize($_POST['country'] ?? ''),
        'shippingMethod' => sanitize($_POST['shippingMethod'] ?? '0'),
        'orderNotes' => sanitize($_POST['orderNotes'] ?? ''),
        'paymentMethod' => sanitize($_POST['paymentMethod'] ?? 'cash_on_delivery'),
    ];

    // Calculate totals
    $shipping_cost = getShippingCost($form_data['shippingMethod']);
    $totals = calculateCartTotals($shipping_cost);

    // Store shipping cost in session for cart
    $_SESSION['shipping_cost'] = $shipping_cost;

    // Create order in database
    try {
        $db = Database::connect();
        
        // Begin transaction
        $db->beginTransaction();

        // Insert order
        $order_sql = 'INSERT INTO orders (first_name, last_name, email, phone, address, city, state, postal_code, country, order_notes, subtotal, shipping_cost, order_total, shipping_method, payment_method, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        
        $order_id = insert($order_sql, [
            $form_data['firstName'],
            $form_data['lastName'],
            $form_data['email'],
            $form_data['phone'],
            $form_data['address'],
            $form_data['city'],
            $form_data['state'],
            $form_data['postalCode'],
            $form_data['country'],
            $form_data['orderNotes'],
            $totals['subtotal'],
            $totals['shipping'],
            $totals['total'],
            $form_data['shippingMethod'],
            $form_data['paymentMethod'],
            'pending'
        ]);

        // Insert order items
        foreach ($cart as $item) {
            $item_sql = 'INSERT INTO order_items (order_id, product_id, quantity, unit_price) VALUES (?, ?, ?, ?)';
            executeQuery($item_sql, [
                $order_id,
                $item['product_id'],
                $item['quantity'],
                $item['price']
            ]);
        }

        // Commit transaction
        $db->commit();

        // Clear cart
        clearCart();
        unset($_SESSION['shipping_cost']);

        // Return success with redirect
        jsonResponse([
            'success' => true,
            'message' => 'Order placed successfully',
            'order_id' => $order_id,
            'redirect_url' => APP_URL . '/order_confirmation.php?order_id=' . $order_id,
        ]);

    } catch (Exception $e) {
        if (isset($db) && $db->inTransaction()) {
            $db->rollBack();
        }
        jsonResponse(['error' => 'Failed to process order: ' . $e->getMessage()], 500);
    }
}

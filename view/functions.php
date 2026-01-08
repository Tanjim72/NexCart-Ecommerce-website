<?php
/**
 * Database Connection and Helper Functions
 */

// Load configuration
if (!file_exists(__DIR__ . '/../config/config.php')) {
    die('Error: config/config.php not found. Please copy config/config.sample.php and configure it.');
}
require_once __DIR__ . '/../config/config.php';

// PDO Database Connection
class Database {
    private static $pdo = null;

    /**
     * Get or create PDO connection
     */
    public static function connect() {
        if (self::$pdo === null) {
            try {
                $dsn = sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                    DB_HOST,
                    DB_PORT,
                    DB_NAME
                );
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
            }
        }
        return self::$pdo;
    }
}

/**
 * Execute a prepared query with parameters
 */
function executeQuery($sql, $params = []) {
    $db = Database::connect();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/**
 * Fetch one row
 */
function fetchOne($sql, $params = []) {
    return executeQuery($sql, $params)->fetch();
}

/**
 * Fetch all rows
 */
function fetchAll($sql, $params = []) {
    return executeQuery($sql, $params)->fetchAll();
}

/**
 * Insert and return last insert ID
 */
function insert($sql, $params = []) {
    $db = Database::connect();
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    return $db->lastInsertId();
}

/**
 * Get CSRF token
 */
function getCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCsrfToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Escape HTML output
 */
function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize input
 */
function sanitize($input) {
    if (is_array($input)) {
        return array_map('sanitize', $input);
    }
    return trim(stripslashes($input ?? ''));
}

/**
 * Validate email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone (basic)
 */
function isValidPhone($phone) {
    // Extract only digits
    $digitsOnly = preg_replace('/[^0-9]/', '', $phone);
    // Check if at least 10 digits
    return strlen($digitsOnly) >= 10;
}

/**
 * Get all products from database
 */
function getProducts() {
    return fetchAll('SELECT * FROM products ORDER BY created_at DESC');
}

/**
 * Get single product by ID
 */
function getProductById($id) {
    return fetchOne('SELECT * FROM products WHERE id = ?', [$id]);
}

/**
 * Get cart from session
 */
function getCart() {
    return $_SESSION['cart'] ?? [];
}

/**
 * Add item to cart session
 */
function addToCartSession($product_id, $name, $price, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if item already in cart
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            $item['quantity'] += $quantity;
            return true;
        }
    }

    // Add new item
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'name' => $name,
        'price' => (float)$price,
        'quantity' => (int)$quantity,
    ];
    return true;
}

/**
 * Update cart item quantity
 */
function updateCartItemQuantity($product_id, $quantity) {
    if (!isset($_SESSION['cart'])) {
        return false;
    }

    foreach ($_SESSION['cart'] as &$item) {
        if ($item['product_id'] == $product_id) {
            if ($quantity <= 0) {
                // Remove item
                removeFromCartSession($product_id);
                return true;
            }
            $item['quantity'] = (int)$quantity;
            return true;
        }
    }
    return false;
}

/**
 * Remove item from cart
 */
function removeFromCartSession($product_id) {
    if (!isset($_SESSION['cart'])) {
        return false;
    }

    $_SESSION['cart'] = array_filter(
        $_SESSION['cart'],
        fn($item) => $item['product_id'] != $product_id
    );

    // Reindex array
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    return true;
}

/**
 * Clear cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
}

/**
 * Calculate cart totals
 */
function calculateCartTotals($shippingCost = 0) {
    $subtotal = 0;
    $itemCount = 0;
    $cart = getCart();

    foreach ($cart as $item) {
        $subtotal += $item['price'] * $item['quantity'];
        $itemCount += $item['quantity'];
    }

    $total = $subtotal + $shippingCost;

    return [
        'subtotal' => round($subtotal, 2),
        'shipping' => round($shippingCost, 2),
        'total' => round($total, 2),
        'item_count' => $itemCount,
        'item_count_text' => $itemCount . ' ' . ($itemCount === 1 ? 'item' : 'items'),
    ];
}

/**
 * Get shipping cost by method
 */
function getShippingCost($method) {
    $methods = [
        '0' => 0,
        '200' => 200,
        '500' => 500,
    ];
    return isset($methods[$method]) ? $methods[$method] : 0;
}

/**
 * Return JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Validate checkout form data
 */
function validateCheckoutForm($data) {
    $errors = [];

    // Required fields (excluding optional orderNotes)
    $required_fields = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
    foreach ($required_fields as $field) {
        if (empty($data[$field] ?? '')) {
            $errors[$field] = ucfirst($field) . ' is required';
        }
    }

    // Validate email if provided
    if (!empty($data['email']) && !isValidEmail($data['email'])) {
        $errors['email'] = 'Invalid email address';
    }

    // Validate phone if provided  
    if (!empty($data['phone']) && !isValidPhone($data['phone'])) {
        $errors['phone'] = 'Invalid phone number';
    }

    return $errors;
}

/**
 * Redirect helper
 */
function redirect($path) {
    header('Location: ' . $path);
    exit;
}

/**
 * Get orders for admin
 */
function getOrders($limit = 20, $offset = 0) {
    $sql = 'SELECT * FROM orders ORDER BY created_at DESC LIMIT ? OFFSET ?';
    return fetchAll($sql, [$limit, $offset]);
}

/**
 * Get single order with items
 */
function getOrderWithItems($order_id) {
    $order = fetchOne('SELECT * FROM orders WHERE id = ?', [$order_id]);
    if ($order) {
        $order['items'] = fetchAll(
            'SELECT oi.*, p.name FROM order_items oi 
            LEFT JOIN products p ON oi.product_id = p.id 
            WHERE oi.order_id = ?',
            [$order_id]
        );
    }
    return $order;
}

/**
 * Count total orders
 */
function countOrders() {
    $result = fetchOne('SELECT COUNT(*) as count FROM orders');
    return $result['count'] ?? 0;
}

/**
 * Update order status
 */
function updateOrderStatus($order_id, $status) {
    $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
    if (!in_array($status, $valid_statuses)) {
        return false;
    }
    $stmt = executeQuery('UPDATE orders SET status = ? WHERE id = ?', [$status, $order_id]);
    return $stmt->rowCount() > 0;
}

<?php
/**
 * Admin Order Details Page
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../includes/functions.php';

// Check authentication
if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
    redirect(APP_URL . '/admin/index.php');
}

// Get order ID
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$order_id) {
    redirect(APP_URL . '/admin/index.php');
}

// Get order with items
$order = getOrderWithItems($order_id);

if (!$order) {
    die('Order not found.');
}

// Page-specific CSS files (for admin pages) - Now consolidated in common.css
// $page_css = ['admin-page.css'];
// Page-specific JS files (for admin pages) - Now consolidated in utilities.js
// $page_js = ['admin-page.js'];

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $new_status = sanitize($_POST['new_status'] ?? '');
    if ($new_status) {
        updateOrderStatus($order_id, $new_status);
        // Refresh order data
        $order = getOrderWithItems($order_id);
        $message = 'Order status updated successfully';
    }
}

$page_title = 'Order #' . $order_id;
include __DIR__ . '/../../includes/header.php';
?>

<section class="content active" style="padding: 2rem 1rem;">
    <div style="max-width: 900px; margin: 0 auto;">
        <div style="margin-bottom: 2rem;">
            <a href="<?php echo APP_URL; ?>/admin/index.php" style="color: #667eea; text-decoration: none; font-weight: 600;">
                ← Back to Orders
            </a>
        </div>

        <?php if (isset($message)): ?>
        <div style="background: #f0fff4; color: #48bb78; padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #48bb78;">
            <?php echo escape($message); ?>
        </div>
        <?php endif; ?>

        <div class="invoice" style="margin-top: 0;">
            <div class="invoice-header">
                <h1 class="invoice-title">Order Details</h1>
                <div class="invoice-meta">
                    <div class="invoice-brand">
                        <h2 style="margin: 0; color: #667eea; font-size: 1.5rem;">NextCart Store</h2>
                        <p class="invoice-company">Quality Electronics & Gadgets</p>
                    </div>
                    <div class="invoice-info">
                        <p><strong>Order ID:</strong> <span class="order-id">#<?php echo escape(str_pad($order['id'], 6, '0', STR_PAD_LEFT)); ?></span></p>
                        <p><strong>Order Date:</strong> <?php echo escape(date('M d, Y \a\t h:i A', strtotime($order['created_at']))); ?></p>
                    </div>
                </div>
            </div>

            <div style="background: #f7fafc; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                    <div>
                        <h3 style="margin-top: 0; color: #667eea; font-weight: 600;">Customer Information</h3>
                        <p style="margin: 0.5rem 0;">
                            <strong><?php echo escape($order['first_name'] . ' ' . $order['last_name']); ?></strong><br>
                            Email: <?php echo escape($order['email']); ?><br>
                            Phone: <?php echo escape($order['phone']); ?>
                        </p>
                    </div>
                    <div>
                        <h3 style="margin-top: 0; color: #667eea; font-weight: 600;">Delivery Address</h3>
                        <p style="margin: 0.5rem 0;">
                            <?php echo escape($order['address']); ?><br>
                            <?php echo escape($order['city'] . ', ' . $order['state']); ?><br>
                            <?php echo escape($order['postal_code'] . ', ' . $order['country']); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="invoice-items">
                <h3 class="invoice-section-title">Order Items</h3>
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th style="text-align: center; width: 15%;">Quantity</th>
                            <th class="amount">Unit Price</th>
                            <th class="amount" style="text-align: right; width: 20%;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?php echo escape($item['name']); ?></td>
                            <td style="text-align: center;"><?php echo escape($item['quantity']); ?></td>
                            <td class="amount">৳ <?php echo number_format($item['unit_price'], 2); ?></td>
                            <td class="amount">৳ <?php echo number_format($item['unit_price'] * $item['quantity'], 2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="invoice-total">
                <div style="margin-bottom: 1rem;">
                    <span>Subtotal:</span>
                    <span class="amount" style="margin-left: 1rem;">৳ <?php echo number_format($order['subtotal'], 2); ?></span>
                </div>
                <div style="margin-bottom: 1rem;">
                    <span>Shipping Cost:</span>
                    <span class="amount" style="margin-left: 1rem;">৳ <?php echo number_format($order['shipping_cost'], 2); ?></span>
                </div>
                <div style="font-size: 1.3rem; font-weight: 700;">
                    <span>Total Amount:</span>
                    <span class="invoice-total-amount">৳ <?php echo number_format($order['order_total'], 2); ?></span>
                </div>
            </div>

            <div class="invoice-payment-info">
                <h3 class="invoice-section-title">Order Status & Actions</h3>
                <form method="POST" style="margin-top: 1rem;">
                    <div style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
                        <div>
                            <label for="new_status" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #2d3748; font-size: 0.9rem;">Update Status:</label>
                            <select id="new_status" name="new_status" style="padding: 0.5rem; border: 2px solid #e2e8f0; border-radius: 6px; font-family: inherit; font-size: 0.95rem;">
                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="processing" <?php echo $order['status'] === 'processing' ? 'selected' : ''; ?>>Processing</option>
                                <option value="shipped" <?php echo $order['status'] === 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                        </div>
                        <button type="submit" name="update_status" value="1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 0.5rem 1.5rem; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 0.95rem;">
                            Update Status
                        </button>
                    </div>
                </form>

                <p style="margin-top: 1rem;">
                    <strong>Payment Method:</strong> <?php echo escape(ucfirst($order['payment_method'] ?? 'Not specified')); ?><br>
                    <strong>Shipping Method:</strong> <?php echo escape(ucfirst($order['shipping_method'] ?? 'Not specified')); ?><br>
                    <strong>Current Status:</strong> <span class="status-badge <?php echo escape(strtolower($order['status'])); ?>"><?php echo escape(ucfirst($order['status'])); ?></span>
                </p>
            </div>

            <?php if (!empty($order['order_notes'])): ?>
            <div style="background: #fffaf0; padding: 1rem; border-radius: 8px; margin-top: 1rem; border-left: 4px solid #ed8936;">
                <h3 style="margin-top: 0; color: #ed8936;">Customer Notes</h3>
                <p style="margin: 0;"><?php echo escape($order['order_notes']); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <div style="margin-top: 2rem;">
            <a href="<?php echo APP_URL; ?>/admin/index.php" class="btn btn-secondary">
                Back to Orders
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

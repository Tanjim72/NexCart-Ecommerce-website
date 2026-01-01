<?php
/**
 * Order Confirmation / Invoice Page
 */

// Initialize session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../includes/functions.php';

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : null;

if (!$order_id) {
    redirect(APP_URL . '/index.php');
}

// Get order with items
$order = getOrderWithItems($order_id);

if (!$order) {
    die('Order not found.');
}

$page_title = 'Order Confirmation';

// Page-specific CSS files
$page_css = [
    'order-page.css'
];

// Page-specific JS files
$page_js = [
    'order-page.js'
];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/nav.php';
?>

<section id="invoice" class="content active" role="tabpanel" aria-labelledby="invoice-tab">
    <div class="section-header">
        <h2>Order Confirmation</h2>
        <p class="section-subtitle">Thank you for your order!</p>
    </div>

    <div class="invoice">
        <div class="invoice-header">
            <h1 class="invoice-title">Invoice</h1>
            <div class="invoice-meta">
                <div class="invoice-brand">
                    <h2 style="margin: 0; color: #667eea; font-size: 1.5rem;">NextCart Store</h2>
                    <p class="invoice-company">Quality Electronics & Gadgets</p>
                    <p>Dhaka, Bangladesh</p>
                    <p>Email: support@nextcart.local</p>
                </div>
                <div class="invoice-info">
                    <p><strong>Order ID:</strong> <span class="order-id">#<?php echo escape(str_pad($order['id'], 6, '0', STR_PAD_LEFT)); ?></span></p>
                    <p><strong>Order Date:</strong> <?php echo escape(date('M d, Y \a\t h:i A', strtotime($order['created_at']))); ?></p>
                    <p><strong>Status:</strong> <span class="status-badge <?php echo escape(strtolower($order['status'])); ?>"><?php echo escape(ucfirst($order['status'])); ?></span></p>
                </div>
            </div>
        </div>

        <div class="invoice-meta" style="margin-bottom: 2rem; border-top: 1px solid #e2e8f0; padding-top: 1rem;">
            <div>
                <h3 class="invoice-section-title">Bill To</h3>
                <p style="margin: 0.5rem 0;">
                    <strong><?php echo escape($order['first_name'] . ' ' . $order['last_name']); ?></strong><br>
                    <?php echo escape($order['address']); ?><br>
                    <?php echo escape($order['city'] . ', ' . $order['state'] . ' ' . $order['postal_code']); ?><br>
                    <?php echo escape($order['country']); ?><br>
                    <strong>Email:</strong> <?php echo escape($order['email']); ?><br>
                    <strong>Phone:</strong> <?php echo escape($order['phone']); ?>
                </p>
            </div>
            <div>
                <h3 class="invoice-section-title">Order Summary</h3>
                <p style="margin: 0.5rem 0;">
                    <strong>Subtotal:</strong> ৳ <?php echo number_format($order['subtotal'], 2); ?><br>
                    <strong>Shipping:</strong> ৳ <?php echo number_format($order['shipping_cost'], 2); ?><br>
                    <strong>Order Total:</strong> <span style="font-size: 1.2rem; color: #667eea;">৳ <?php echo number_format($order['order_total'], 2); ?></span>
                </p>
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
            <h3 class="invoice-section-title">Payment Information</h3>
            <p style="margin: 0.5rem 0;">
                <strong>Payment Method:</strong> <?php echo escape(ucfirst($order['payment_method'] ?? 'Not specified')); ?><br>
                <strong>Shipping Method:</strong> <?php echo escape(ucfirst($order['shipping_method'] ?? 'Not specified')); ?>
            </p>
        </div>

        <div class="invoice-footer">
            <p>Thank you for your purchase! We appreciate your business.</p>
            <p>For support, please contact us at support@nextcart.local</p>
            <p style="margin-top: 2rem; font-size: 0.85rem;">
                <button class="btn btn-secondary" onclick="window.print()" style="margin-right: 0.5rem;">
                    Print Invoice
                </button>
                <a href="<?php echo APP_URL; ?>/index.php" class="btn btn-primary" style="text-decoration: none;">
                    Continue Shopping
                </a>
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>

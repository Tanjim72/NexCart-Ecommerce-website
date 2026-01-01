<?php
/**
 * CONSOLIDATED ORDER COMPONENT
 * Merges: order-item.php + order-summary.php + order-table.php
 * Displays order items, summary, and table views
 * 
 * Usage: 
 * - Items: <?php include 'components/order.php'; ?> (with $items array)
 * - Summary: <?php include 'components/order.php'; ?> (with $subtotal, $shipping, $total, $payment_method)
 * - Table: <?php include 'components/order.php'; ?> (with $orders array, optional $show_actions)
 */

// Order Items List (for invoice/order confirmation)
if (isset($items) && !empty($items)):
?>
<table class="order-items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item): ?>
        <tr class="invoice-row">
            <td class="item-name">
                <?php echo htmlspecialchars($item['product_name']); ?>
            </td>
            <td class="item-qty">
                <?php echo htmlspecialchars($item['quantity']); ?>
            </td>
            <td class="item-price">
                ৳ <?php echo number_format($item['price']); ?>
            </td>
            <td class="item-total">
                ৳ <?php echo number_format($item['quantity'] * $item['price']); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- Order Summary (for invoice/order confirmation) -->
<?php if (isset($subtotal) && isset($total)): ?>
<div class="order-summary">
    <div class="summary-section">
        <h4>Order Summary</h4>
        
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>৳ <?php echo number_format($subtotal); ?></span>
        </div>
        
        <div class="summary-row">
            <span>Shipping:</span>
            <span>৳ <?php echo number_format($shipping ?? 0); ?></span>
        </div>
        
        <div class="summary-row total">
            <span><strong>Total:</strong></span>
            <span><strong>৳ <?php echo number_format($total); ?></strong></span>
        </div>
    </div>
    
    <?php if (isset($payment_method)): ?>
    <div class="summary-section">
        <h4>Payment Information</h4>
        <p>
            <strong>Payment Method:</strong> 
            <?php 
            $methods = [
                'cash_on_delivery' => 'Cash on Delivery',
                'bank_transfer' => 'Bank Transfer',
                'mobile_banking' => 'Mobile Banking'
            ];
            echo htmlspecialchars($methods[$payment_method] ?? $payment_method);
            ?>
        </p>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<!-- Order Table (for admin dashboard) -->
<?php 
if (isset($orders) && !empty($orders)):
    if (!isset($show_actions)) {
        $show_actions = true;
    }
?>
<div class="table-responsive">
    <table class="order-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Total</th>
                <th>Status</th>
                <th>Date</th>
                <?php if ($show_actions): ?>
                <th>Action</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orders as $order): ?>
            <tr>
                <td class="order-id">#<?php echo htmlspecialchars($order['order_id']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($order['customer_email']); ?></td>
                <td class="amount">৳ <?php echo number_format($order['total_amount']); ?></td>
                <td>
                    <span class="status-badge status-<?php echo htmlspecialchars($order['status']); ?>">
                        <?php echo ucfirst(htmlspecialchars($order['status'])); ?>
                    </span>
                </td>
                <td class="date">
                    <?php 
                    $date = new DateTime($order['created_at']);
                    echo $date->format('M d, Y');
                    ?>
                </td>
                <?php if ($show_actions): ?>
                <td class="actions">
                    <a href="order-details.php?id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn-small">View</a>
                    <a href="edit-order.php?id=<?php echo htmlspecialchars($order['order_id']); ?>" class="btn-small">Edit</a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

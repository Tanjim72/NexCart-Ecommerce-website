<?php
/**
 * Order Table Component
 * Displays table of orders with status badges and action links
 * Used in admin dashboard
 * 
 * Usage: <?php include 'components/order-table.php'; ?>
 * 
 * Parameters:
 * - $orders (array) - Array of order records
 * - $show_actions (boolean) - Show action links (default: true)
 */

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
                    <a href="orders.php?order_id=<?php echo $order['order_id']; ?>" class="btn-link">
                        View Details
                    </a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

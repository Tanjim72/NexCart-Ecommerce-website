<?php
/**
 * Status Badge Component
 * Displays order status with color coding
 * 
 * Usage: <?php include 'components/status-badge.php'; ?>
 * 
 * Parameters:
 * - $status (string) - Order status (pending, processing, shipped, delivered, cancelled)
 */

$status_classes = [
    'pending' => 'pending',
    'processing' => 'processing',
    'shipped' => 'shipped',
    'delivered' => 'delivered',
    'cancelled' => 'cancelled'
];

$status_icons = [
    'pending' => '⏳',
    'processing' => '⚙️',
    'shipped' => '📦',
    'delivered' => '✅',
    'cancelled' => '❌'
];
?>

<span class="status-badge status-<?php echo htmlspecialchars($status_classes[$status] ?? 'pending'); ?>">
    <span class="status-icon">
        <?php echo $status_icons[$status] ?? '❓'; ?>
    </span>
    <span class="status-text">
        <?php echo ucfirst(htmlspecialchars($status)); ?>
    </span>
</span>

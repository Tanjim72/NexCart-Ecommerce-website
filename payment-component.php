<?php
/**
 * CONSOLIDATED PAYMENT COMPONENT
 * Merges: payment-options.php + status-badge.php
 * Displays payment methods and order status
 * 
 * Usage:
 * - Payment: <?php include 'components/payment.php'; ?> (displays payment options)
 * - Status: <?php include 'components/payment.php'; ?> (with $status variable)
 */

// Payment Options Display
if (!isset($status)):
?>
<div class="payment-form-section">
    <h3>Select Payment Method</h3>
    <div class="payment-options">
        <label class="payment-option">
            <input type="radio" name="paymentMethod" value="cash_on_delivery" checked>
            <span class="payment-label">
                <div class="payment-icon">💵</div>
                <div class="payment-text">Cash on Delivery</div>
            </span>
        </label>
        
        <label class="payment-option">
            <input type="radio" name="paymentMethod" value="bank_transfer">
            <span class="payment-label">
                <div class="payment-icon">🏦</div>
                <div class="payment-text">Bank Transfer</div>
            </span>
        </label>
        
        <label class="payment-option">
            <input type="radio" name="paymentMethod" value="mobile_banking">
            <span class="payment-label">
                <div class="payment-icon">📱</div>
                <div class="payment-text">Mobile Banking</div>
            </span>
        </label>
    </div>
</div>

<!-- Status Badge Display -->
<?php else: 
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
<?php endif; ?>

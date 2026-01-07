<?php
/**
 * Payment Options Component
 * Displays payment method selection radio buttons
 * 
 * Usage: <?php include 'components/payment-options.php'; ?>
 */
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

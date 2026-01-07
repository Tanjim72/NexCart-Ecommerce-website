<?php
/**
 * Order Summary Component (for invoice/order confirmation)
 * Displays order totals and payment information
 * 
 * Usage: <?php include 'components/order-summary.php'; ?>
 * 
 * Parameters:
 * - $subtotal (float) - Order subtotal
 * - $shipping (float) - Shipping cost
 * - $total (float) - Order total
 * - $payment_method (string) - Payment method used
 */
?>

<div class="order-summary">
    <div class="summary-section">
        <h4>Order Summary</h4>
        
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>৳ <?php echo number_format($subtotal); ?></span>
        </div>
        
        <div class="summary-row">
            <span>Shipping:</span>
            <span>৳ <?php echo number_format($shipping); ?></span>
        </div>
        
        <div class="summary-row total">
            <span><strong>Total:</strong></span>
            <span><strong>৳ <?php echo number_format($total); ?></strong></span>
        </div>
    </div>
    
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
</div>

<?php


if (!isset($shipping_cost)) {
    $shipping_cost = 0;
}
?>

<div id="cartSummary" class="cart-summary">
    <div class="summary-row">
        <span>Subtotal:</span>
        <span>৳ <?php echo number_format($subtotal); ?></span>
    </div>
    
    <div class="summary-row">
        <span>Shipping:</span>
        <span id="shippingCostDisplay">
            ৳ <?php echo number_format($shipping_cost); ?>
        </span>
    </div>
    
    <div class="summary-row total">
        <span>Total:</span>
        <span>৳ <span id="totalAmount"><?php echo number_format($total); ?></span></span>
    </div>
</div>

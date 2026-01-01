<?php
/**
 * CONSOLIDATED CART COMPONENT
 * Merges: cart-item.php + cart-summary.php
 * Displays complete shopping cart with items and totals
 * 
 * Usage: <?php include 'components/cart.php'; ?>
 * 
 * Parameters:
 * - $cart (array) - Array of cart items with product_id, name, price, quantity
 * - $subtotal (float) - Cart subtotal
 * - $shipping_cost (float) - Shipping cost (optional, default: 0)
 * - $total (float) - Cart total
 */

if (!isset($shipping_cost)) {
    $shipping_cost = 0;
}
?>

<div id="cartContainer" class="cart-container">
    <!-- Cart Items List -->
    <div id="cartItems" class="cart-items-list">
        <?php if (!empty($cart)): ?>
            <?php foreach ($cart as $item): ?>
            <div class="cart-item" data-product-id="<?php echo htmlspecialchars($item['product_id']); ?>">
                <div class="item-info">
                    <div class="item-name">
                        <?php echo htmlspecialchars($item['name']); ?>
                    </div>
                    <div class="item-price">
                        ৳ <?php echo number_format($item['price']); ?>
                    </div>
                </div>
                
                <div class="quantity-control">
                    <button class="qty-btn" 
                            onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantity'] - 1; ?>)">
                        −
                    </button>
                    <input type="number" class="qty-input" 
                           value="<?php echo htmlspecialchars($item['quantity']); ?>" 
                           min="1" 
                           onchange="updateQuantity(<?php echo $item['product_id']; ?>, this.value)">
                    <button class="qty-btn" 
                            onclick="updateQuantity(<?php echo $item['product_id']; ?>, <?php echo $item['quantity'] + 1; ?>)">
                        +
                    </button>
                </div>
                
                <div class="item-total">
                    ৳ <?php echo number_format($item['price'] * $item['quantity']); ?>
                </div>
                
                <button class="remove-btn" 
                        onclick="removeFromCart(<?php echo $item['product_id']; ?>)">
                    Remove
                </button>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty-cart-message">Your cart is empty</p>
        <?php endif; ?>
    </div>
    
    <!-- Cart Summary -->
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
</div>

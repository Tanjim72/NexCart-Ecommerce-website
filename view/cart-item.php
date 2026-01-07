<?php

?>

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

<!-- Products Section / Shopping Tab -->
<!-- public/templates/products.php -->
<!-- 
  Displays the product listing grid with add-to-cart functionality
  
  Required Variables:
  - $products (array): Array of product objects with id, name, description, image, price
-->

<section id="shopping" class="content active" role="tabpanel" aria-labelledby="shopping-tab">
    <div class="section-header">
        <h2>Our Products</h2>
        <p class="section-subtitle">Browse our selection of quality electronics</p>
    </div>
    
    <div class="products-grid">
        <?php foreach ($products as $product): ?>
        <article class="product-card" data-product-id="<?php echo escape($product['id']); ?>">
            <div class="product-image">
                <img src="<?php echo APP_URL; ?>/admin/get-image.php?id=<?php echo escape($product['id']); ?>" 
                     alt="<?php echo escape($product['name']); ?>" 
                     style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
            </div>
            <div class="product-details">
                <h3 class="product-name"><?php echo escape($product['name']); ?></h3>
                <p class="product-desc"><?php echo escape($product['description']); ?></p>
                <div class="product-price">৳ <?php echo number_format($product['price'], 0, '.', ','); ?></div>
                <button class="btn btn-primary" onclick="addToCart(<?php echo escape($product['id']); ?>, '<?php echo escape(addslashes($product['name'])); ?>', <?php echo escape($product['price']); ?>)">
                    <span class="btn-icon">+</span> Add to Cart
                </button>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <div id="cart-display" class="cart-section">
        <div class="cart-header">
            <h3>🛒 Your Shopping Cart</h3>
            <span class="cart-count" id="cartCount"><?php echo escape($cart_totals['item_count_text']); ?></span>
        </div>
        <div class="cart-items" id="cartItems">
            <?php if (empty($cart)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">🛒</div>
                    <p>Your cart is empty. Add some products to get started!</p>
                </div>
            <?php else: ?>
                <?php foreach ($cart as $item): ?>
                <div class="cart-item" data-product-id="<?php echo escape($item['product_id']); ?>">
                    <div class="item-info">
                        <div class="item-name"><?php echo escape($item['name']); ?></div>
                        <div class="item-price">৳ <?php echo number_format($item['price'], 0, '.', ','); ?></div>
                    </div>
                    <div class="quantity-control">
                        <button class="qty-btn" onclick="updateQuantity(<?php echo escape($item['product_id']); ?>, this.nextElementSibling.value - 1)">−</button>
                        <input type="number" class="qty-input" value="<?php echo escape($item['quantity']); ?>" min="1" onchange="updateQuantity(<?php echo escape($item['product_id']); ?>, this.value)">
                        <button class="qty-btn" onclick="updateQuantity(<?php echo escape($item['product_id']); ?>, parseInt(this.previousElementSibling.value) + 1)">+</button>
                    </div>
                    <div class="item-total">৳ <?php echo number_format($item['price'] * $item['quantity'], 0, '.', ','); ?></div>
                    <button class="remove-btn" onclick="removeFromCart(<?php echo escape($item['product_id']); ?>)">Remove</button>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="cart-summary" id="cartSummary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>৳ <?php echo number_format($cart_totals['subtotal'], 0, '.', ','); ?></span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span id="shippingCostDisplay">৳ 0</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>৳ <span id="totalAmount"><?php echo number_format($cart_totals['total'], 0, '.', ','); ?></span></span>
            </div>
        </div>
        <button class="btn btn-primary btn-large" onclick="switchTab('checkout')" id="proceedBtn" <?php echo empty($cart) ? 'disabled' : ''; ?>>
            Proceed to Checkout →
        </button>
    </div>
</section>

<?php
/**
 * Product Card Component
 * Displays a single product with image, name, description, price, and add-to-cart button
 * 
 * Usage: <?php include 'components/product-card.php'; ?>
 * 
 * Parameters (pass before include or set in $product):
 * - $product_id (int) - Product ID
 * - $product_name (string) - Product name
 * - $product_desc (string) - Product description
 * - $product_price (float) - Product price
 * - $product_emoji (string) - Product emoji icon
 */
?>

<article class="product-card" data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>">
    <div class="product-image">
        <?php echo htmlspecialchars($product['emoji_icon']); ?>
    </div>
    <div class="product-details">
        <h3 class="product-name">
            <?php echo htmlspecialchars($product['name']); ?>
        </h3>
        <p class="product-desc">
            <?php echo htmlspecialchars($product['description']); ?>
        </p>
        <div class="product-price">
            ৳ <?php echo number_format($product['price']); ?>
        </div>
        <button class="btn btn-primary" 
                onclick="addToCart(<?php echo $product['product_id']; ?>, 
                         '<?php echo addslashes($product['name']); ?>', 
                         <?php echo $product['price']; ?>)">
            <span class="btn-icon">+</span> Add to Cart
        </button>
    </div>
</article>

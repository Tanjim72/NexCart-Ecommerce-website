<?php
/**
 * CONSOLIDATED ORDER FLOW TEMPLATE
 * Unified template for all order process sections:
 * - Checkout
 * - Payment
 * - Invoice
 * - Tracking
 * 
 * Usage: <?php include 'order-flow-template.php'; ?>
 * Set $section variable to: 'checkout', 'payment', 'invoice', or 'tracking'
 * 
 * Required Variables by Section:
 * - checkout: none (form is self-contained)
 * - payment: none (payment methods displayed)
 * - invoice: $items, $subtotal, $shipping, $total, $payment_method, $order_id
 * - tracking: $order_id, $status
 */

// Default to checkout if not specified
if (!isset($section)) {
    $section = 'checkout';
}
?>

<!-- ============================================
     CHECKOUT SECTION
     ============================================ -->
<?php if ($section === 'checkout'): ?>
<section id="checkout" class="content" role="tabpanel" aria-labelledby="checkout-tab">
    <div class="section-header">
        <h2>Checkout</h2>
        <p class="section-subtitle">Enter delivery information and choose payment</p>
    </div>
    
    <form id="checkoutForm" class="checkout-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo escape(getCsrfToken()); ?>">
        
        <!-- Personal Information -->
        <fieldset class="form-fieldset">
            <legend>Personal Information</legend>
            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name <span class="required">*</span></label>
                    <input type="text" id="firstName" name="firstName" required autocomplete="given-name" placeholder="Enter your first name">
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name <span class="required">*</span></label>
                    <input type="text" id="lastName" name="lastName" required autocomplete="family-name" placeholder="Enter your last name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email Address <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required autocomplete="email" placeholder="your@email.com">
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" required autocomplete="tel" placeholder="01XXXXXXXXX">
                </div>
            </div>
        </fieldset>

        <!-- Delivery Address -->
        <fieldset class="form-fieldset">
            <legend>Delivery Address</legend>
            <div class="form-group">
                <label for="address">Street Address <span class="required">*</span></label>
                <input type="text" id="address" name="address" required autocomplete="street-address" placeholder="House No, Road, Area">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="city">City / District <span class="required">*</span></label>
                    <input type="text" id="city" name="city" required autocomplete="address-level2" placeholder="e.g., Dhaka">
                </div>
                <div class="form-group">
                    <label for="state">Division / State <span class="required">*</span></label>
                    <input type="text" id="state" name="state" required autocomplete="address-level1" placeholder="e.g., Dhaka Division">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="postalCode">Postal Code <span class="required">*</span></label>
                    <input type="text" id="postalCode" name="postalCode" required autocomplete="postal-code" placeholder="1234">
                </div>
                <div class="form-group">
                    <label for="country">Country <span class="required">*</span></label>
                    <select id="country" name="country" required autocomplete="country">
                        <option value="Bangladesh" selected>Bangladesh</option>
                    </select>
                </div>
            </div>
        </fieldset>

        <!-- Shipping Options -->
        <fieldset class="form-fieldset">
            <legend>Shipping Options</legend>
            <div class="form-group">
                <label for="shippingMethod">Delivery Method <span class="required">*</span></label>
                <select id="shippingMethod" name="shippingMethod" onchange="updateShippingCost()">
                    <option value="0">📦 Standard Delivery (Free) - 5-7 days</option>
                    <option value="200">🚚 Express Delivery (+৳ 200) - 2-3 days</option>
                    <option value="500">⚡ Overnight Delivery (+৳ 500) - Next day</option>
                </select>
            </div>

            <div class="form-group">
                <label for="orderNotes">Order Notes <span class="optional">(Optional)</span></label>
                <textarea id="orderNotes" name="orderNotes" rows="3" placeholder="Any special delivery instructions..."></textarea>
            </div>
        </fieldset>

        <!-- Payment Method (integrated) -->
        <fieldset class="form-fieldset">
            <legend>Payment Method</legend>
            <?php include __DIR__ . '/payment-component.php'; ?>
        </fieldset>

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="switchTab('shopping')">← Back to Cart</button>
            <button type="submit" class="btn btn-primary btn-large">Complete Order →</button>
        </div>
    </form>
</section>

<!-- ============================================
     PAYMENT SECTION
     ============================================ -->
<?php elseif ($section === 'payment'): ?>
<section id="payment" class="content" role="tabpanel" aria-labelledby="payment-tab">
    <div class="section-header">
        <h2>Payment</h2>
        <p class="section-subtitle">Choose your preferred payment method</p>
    </div>
    
    <div id="paymentContent">
        <?php 
        // Display payment options - status variable not set, so shows payment methods
        include __DIR__ . '/payment-component.php'; 
        ?>
    </div>
    
    <div class="form-actions" style="margin-top: 2rem;">
        <button type="button" class="btn btn-secondary" onclick="switchTab('checkout')">← Back to Checkout</button>
        <button type="submit" form="checkoutForm" class="btn btn-primary btn-large">Place Order →</button>
    </div>
</section>

<!-- ============================================
     INVOICE SECTION
     ============================================ -->
<?php elseif ($section === 'invoice'): ?>
<section id="invoice" class="content" role="tabpanel" aria-labelledby="invoice-tab">
    <div class="section-header">
        <h2>Invoice</h2>
        <p class="section-subtitle">Your order summary and receipt</p>
    </div>
    
    <div id="invoiceContent">
        <?php 
        // Display order items, summary, and status
        include __DIR__ . '/order-component.php'; 
        ?>
    </div>
    
    <div class="invoice-actions" style="margin-top: 2rem;">
        <button class="btn btn-primary" onclick="printInvoice()">🖨️ Print Invoice</button>
        <button class="btn btn-secondary" onclick="downloadInvoice()">📥 Download PDF</button>
        <a href="<?php echo APP_URL; ?>" class="btn btn-secondary">← Continue Shopping</a>
    </div>
</section>

<!-- ============================================
     TRACKING SECTION
     ============================================ -->
<?php elseif ($section === 'tracking'): ?>
<section id="tracking" class="content" role="tabpanel" aria-labelledby="tracking-tab">
    <div class="section-header">
        <h2>Order Tracking</h2>
        <p class="section-subtitle">Track your order status in real-time</p>
    </div>
    
    <div id="trackingContent">
        <?php if (isset($order_id)): ?>
        <div class="tracking-container">
            <div class="tracking-info">
                <p><strong>Order #<?php echo htmlspecialchars($order_id); ?></strong></p>
            </div>
            
            <div class="tracking-status">
                <?php 
                // Display current order status with icon
                include __DIR__ . '/payment-component.php'; 
                ?>
            </div>
            
            <div class="tracking-timeline">
                <h3>Status Timeline</h3>
                <ul class="timeline">
                    <li class="timeline-item<?php echo ($status === 'pending' || $status === 'processing' || $status === 'shipped' || $status === 'delivered') ? ' completed' : ''; ?>">
                        <span class="timeline-marker">✓</span>
                        <span>Order Confirmed</span>
                    </li>
                    <li class="timeline-item<?php echo ($status === 'processing' || $status === 'shipped' || $status === 'delivered') ? ' completed' : ''; ?>">
                        <span class="timeline-marker">✓</span>
                        <span>Processing</span>
                    </li>
                    <li class="timeline-item<?php echo ($status === 'shipped' || $status === 'delivered') ? ' completed' : ''; ?>">
                        <span class="timeline-marker">✓</span>
                        <span>Shipped</span>
                    </li>
                    <li class="timeline-item<?php echo ($status === 'delivered') ? ' completed' : ''; ?>">
                        <span class="timeline-marker">✓</span>
                        <span>Delivered</span>
                    </li>
                </ul>
            </div>
        </div>
        <?php else: ?>
        <p class="message">No order found. Please check your order ID.</p>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================
     DEFAULT/UNKNOWN SECTION
     ============================================ -->
<?php else: ?>
<div class="error-message">
    <p>Invalid section: <?php echo htmlspecialchars($section); ?></p>
    <p>Available sections: checkout, payment, invoice, tracking</p>
</div>
<?php endif; ?>

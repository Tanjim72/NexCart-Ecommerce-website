<!-- Checkout Form Section / Checkout Tab -->
<!-- public/templates/checkout.php -->
<!-- 
  Displays the checkout form with customer information and delivery options
  
  Required Variables:
  - None (CSRF token generated automatically)
-->

<section id="checkout" class="content" role="tabpanel" aria-labelledby="checkout-tab">
    <div class="section-header">
        <h2>Checkout</h2>
        <p class="section-subtitle">Enter your delivery information</p>
    </div>
    
    <form id="checkoutForm" class="checkout-form" novalidate>
        <input type="hidden" name="csrf_token" value="<?php echo escape(getCsrfToken()); ?>">
        <input type="hidden" name="paymentMethod" value="cash_on_delivery" id="paymentMethodField">
        
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

        <div class="form-actions">
            <button type="button" class="btn btn-secondary" onclick="switchTab('shopping')">← Back to Cart</button>
            <button type="button" class="btn btn-primary btn-large" onclick="validateAndProceed()">Continue to Payment →</button>
        </div>
    </form>
</section>

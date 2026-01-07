<?php
/**
 * Checkout Form Component
 * Displays the customer information form with all required fields
 * 
 * Usage: <?php include 'components/checkout-form.php'; ?>
 * 
 * Parameters:
 * - CSRF token from $csrf_token (set by including page)
 */
?>

<form id="checkoutForm" class="checkout-form">
    <!-- CSRF Token (Required) -->
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
    
    <!-- Personal Information Section -->
    <fieldset class="form-section">
        <legend>Personal Information</legend>
        
        <div class="form-row">
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
            <div class="form-group">
                <label for="phone">Phone Number *</label>
                <input type="tel" id="phone" name="phone" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
        </div>
    </fieldset>
    
    <!-- Shipping Address Section -->
    <fieldset class="form-section">
        <legend>Shipping Address</legend>
        
        <div class="form-group">
            <label for="address">Street Address *</label>
            <input type="text" id="address" name="address" required 
                   onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="city">City *</label>
                <input type="text" id="city" name="city" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
            <div class="form-group">
                <label for="state">State/Province *</label>
                <input type="text" id="state" name="state" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="postalCode">Postal Code *</label>
                <input type="text" id="postalCode" name="postalCode" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
            <div class="form-group">
                <label for="country">Country *</label>
                <input type="text" id="country" name="country" value="Bangladesh" required 
                       onchange="validateCheckoutForm()" oninput="validateCheckoutForm()">
            </div>
        </div>
    </fieldset>
    
    <!-- Shipping Method Section -->
    <fieldset class="form-section">
        <legend>Shipping Method</legend>
        
        <div class="form-group">
            <label for="shippingMethod">Select Shipping Option *</label>
            <select id="shippingMethod" name="shippingMethod" required 
                    onchange="updateShippingCost(); validateCheckoutForm()">
                <option value="0">Standard Shipping - Free (৳ 0)</option>
                <option value="200">Express Shipping (৳ 200)</option>
                <option value="500">Overnight Shipping (৳ 500)</option>
            </select>
        </div>
    </fieldset>
    
    <!-- Additional Information Section -->
    <fieldset class="form-section">
        <legend>Order Notes (Optional)</legend>
        
        <div class="form-group">
            <label for="orderNotes">Special Instructions</label>
            <textarea id="orderNotes" name="orderNotes" rows="4" 
                      placeholder="Add any special instructions for your order"></textarea>
        </div>
    </fieldset>
</form>

/**
 * Checkout Page JavaScript
 * ============================================
 * Handles checkout form validation and checkout flow
 * Used on: public/index.php (Checkout tab)
 * 
 * Dependencies: utilities.js (CONFIG, Notifications, Utils, Cart), cart-page.js (Shipping)
 */

'use strict';

// ============================================
// CHECKOUT FORM MODULE
// ============================================

const CheckoutForm = {
    validate() {
        const form = document.getElementById('checkoutForm');
        if (!form) return false;

        const proceedBtn = document.getElementById('proceedBtn');
        if (!proceedBtn) return false;

        const required = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
        const allFilled = required.every(field => {
            const input = form[field];
            return input && input.value.trim();
        });

        const email = form.email?.value.trim() || '';
        const phone = form.phone?.value.trim() || '';

        const isValid = allFilled && (email === '' || Utils.isValidEmail(email)) && (phone === '' || Utils.isValidPhone(phone));
        proceedBtn.disabled = !isValid || (document.getElementById('cartItems')?.children.length === 0);

        return true;
    },

    markFieldsEmpty() {
        const form = document.getElementById('checkoutForm');
        if (!form) return;

        const required = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
        required.forEach(field => {
            const input = form[field];
            if (input && !input.value.trim()) {
                input.classList.add('error');
            }
        });
    }
};

function validateCheckoutForm() { CheckoutForm.validate(); }

// ============================================
// VALIDATION & CHECKOUT HANDLER
// ============================================

function validateAndProceed() {
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    const required = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
    for (let field of required) {
        if (!form[field] || !form[field].value.trim()) {
            Notifications.error('Please fill in all required fields');
            return;
        }
    }

    const email = form.email.value.trim();
    if (!Utils.isValidEmail(email)) {
        Notifications.error('Please enter a valid email address');
        return;
    }

    const phone = form.phone.value.trim();
    if (!Utils.isValidPhone(phone)) {
        Notifications.error('Please enter a valid phone number');
        return;
    }

    window.scrollTo(0, 0);
    TabNav.switchTo('payment');
    Payment.renderOptions();
}

// ============================================
// APP INITIALIZATION (CHECKOUT PAGE)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Form validation on input
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        const inputs = checkoutForm.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.classList.contains('error') && this.value.trim()) {
                    this.classList.remove('error');
                }
            });
            input.addEventListener('input', function() {
                CheckoutForm.validate();
            });
        });
    }

    // Initialize proceed button handler
    const proceedBtn = document.getElementById('proceedBtn');
    if (proceedBtn) {
        proceedBtn.addEventListener('click', validateAndProceed);
    }
});

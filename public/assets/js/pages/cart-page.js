/**
 * Cart Page JavaScript
 * ============================================
 * Handles shopping cart display and cart management
 * Used on: public/index.php (Cart tab)
 * 
 * Dependencies: utilities.js (CONFIG, Notifications, Utils, Cart), product-page.js (Cart module)
 */

'use strict';

// ============================================
// SHIPPING MODULE
// ============================================

const Shipping = {
    getDisplay(method) {
        const displays = { '0': '৳ 0', '200': '+৳ 200', '500': '+৳ 500' };
        return displays[method] || '৳ 0';
    },

    updateCost() {
        const method = document.getElementById('shippingMethod')?.value || '0';
        const display = this.getDisplay(method);
        const displayEl = document.getElementById('shippingCostDisplay');
        if (displayEl) displayEl.textContent = display;

        const total = document.getElementById('totalAmount');
        if (total) {
            const subtotalText = document.querySelector('.summary-row span:last-child');
            const subtotal = subtotalText ? parseInt(subtotalText.textContent.replace(/[^0-9]/g, '')) : 0;
            const shipping = parseInt(method) || 0;
            total.textContent = Utils.formatCurrency(subtotal + shipping);
        }
    }
};

function updateShippingCost() { Shipping.updateCost(); }

// ============================================
// APP INITIALIZATION (CART PAGE)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tab navigation
    const tabButtons = document.querySelectorAll('.tab-btn');
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            TabNav.switchTo(tabName);
        });
    });

    // Initialize shipping cost updater
    const shippingMethod = document.getElementById('shippingMethod');
    if (shippingMethod) {
        shippingMethod.addEventListener('change', function() {
            Shipping.updateCost();
        });
    }
});

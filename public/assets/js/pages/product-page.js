/**
 * Product Page JavaScript
 * ============================================
 * Handles product listing and add-to-cart functionality
 * Used on: public/index.php (Products tab)
 * 
 * Dependencies: utilities.js (CONFIG, Notifications, Utils)
 */

'use strict';

// ============================================
// SHOPPING CART MODULE
// ============================================

const Cart = {
    async addItem(productId, productName, price) {
        try {
            const data = await Utils.fetchAPI('add_to_cart.php', {
                product_id: productId,
                quantity: 1,
                csrf_token: Utils.getCsrfToken()
            });

            if (data.success) {
                Notifications.success(`${productName} added to cart!`);
                this.updateDisplay(data);
            }
        } catch (error) {
            Notifications.error(error.message);
        }
    },

    async updateQuantity(productId, quantity) {
        if (quantity < 1) return this.removeItem(productId);

        try {
            const data = await Utils.fetchAPI('update_cart.php', {
                product_id: productId,
                quantity: quantity,
                csrf_token: Utils.getCsrfToken()
            });

            if (data.success) {
                this.updateDisplay(data);
            }
        } catch (error) {
            Notifications.error(error.message);
        }
    },

    async removeItem(productId) {
        try {
            const data = await Utils.fetchAPI('remove_from_cart.php', {
                product_id: productId,
                csrf_token: Utils.getCsrfToken()
            });

            if (data.success) {
                Notifications.warning('Item removed from cart');
                this.updateDisplay(data);
            }
        } catch (error) {
            Notifications.error(error.message);
        }
    },

    updateDisplay(cartData) {
        const cartCount = document.getElementById('cartCount');
        const proceedBtn = document.getElementById('proceedBtn');

        if (cartCount) cartCount.textContent = cartData.cart_count_text;
        if (proceedBtn) proceedBtn.disabled = cartData.cart_count === 0;

        this.renderItems(cartData.cart);
        this.updateTotals(cartData.subtotal, cartData.total);
    },

    renderItems(items) {
        const container = document.getElementById('cartItems');
        if (!container) return;

        if (!items || items.length === 0) {
            container.innerHTML = `
                <div class="empty-state">
                    <div class="empty-state-icon">🛒</div>
                    <p>Your cart is empty. Add some products to get started!</p>
                </div>
            `;
            return;
        }

        container.innerHTML = items.map(item => `
            <div class="cart-item" data-product-id="${item.product_id}">
                <div class="item-info">
                    <div class="item-name">${Utils.escapeHtml(item.name)}</div>
                    <div class="item-price">৳ ${Utils.formatCurrency(item.price)}</div>
                </div>
                <div class="quantity-control">
                    <button class="qty-btn" onclick="Cart.updateQuantity(${item.product_id}, ${item.quantity - 1})">−</button>
                    <input type="number" class="qty-input" value="${item.quantity}" min="1" onchange="Cart.updateQuantity(${item.product_id}, this.value)">
                    <button class="qty-btn" onclick="Cart.updateQuantity(${item.product_id}, ${item.quantity + 1})">+</button>
                </div>
                <div class="item-total">৳ ${Utils.formatCurrency(item.price * item.quantity)}</div>
                <button class="remove-btn" onclick="Cart.removeItem(${item.product_id})">Remove</button>
            </div>
        `).join('');
    },

    updateTotals(subtotal, total) {
        const summary = document.getElementById('cartSummary');
        if (!summary) return;

        const shippingMethod = document.getElementById('shippingMethod')?.value || '0';
        const shippingDisplay = Shipping.getDisplay(shippingMethod);

        summary.innerHTML = `
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>৳ ${Utils.formatCurrency(subtotal)}</span>
            </div>
            <div class="summary-row">
                <span>Shipping:</span>
                <span id="shippingCostDisplay">${shippingDisplay}</span>
            </div>
            <div class="summary-row total">
                <span>Total:</span>
                <span>৳ <span id="totalAmount">${Utils.formatCurrency(total)}</span></span>
            </div>
        `;
    }
};

// Global shortcuts for HTML onclick
function addToCart(productId, name, price) { Cart.addItem(productId, name, price); }
function updateQuantity(productId, qty) { Cart.updateQuantity(productId, qty); }
function removeFromCart(productId) { Cart.removeItem(productId); }

// ============================================
// APP INITIALIZATION (PRODUCT PAGE)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Tab navigation is handled by utilities.js
    // Product page just needs tab initialization
    TabNav.init();
});

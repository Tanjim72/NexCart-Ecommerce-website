/**
 * NextCart - Shared Utilities & Configuration
 * ============================================
 * Global utilities and configuration shared across all pages
 * Used by: All pages
 */

'use strict';

// ============================================
// CONFIGURATION MODULE
// ============================================

const CONFIG = {
    apiUrl: document.currentScript?.dataset.apiUrl || '/Prii_ecomerce/public/api',
    currency: '৳',
    locale: 'en-BD',
    toastDuration: 3000,
    animationDuration: 300
};

// ============================================
// NOTIFICATIONS MODULE
// ============================================

const Notifications = {
    show(message, type = 'success') {
        const container = document.getElementById('toastContainer');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;

        container.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, CONFIG.toastDuration);
    },

    success(msg) { this.show(msg, 'success'); },
    error(msg) { this.show(msg, 'error'); },
    warning(msg) { this.show(msg, 'warning'); }
};

// Global shorthand
function showToast(message, type = 'success') {
    Notifications.show(message, type);
}

// ============================================
// TAB NAVIGATION MODULE
// ============================================

const TabNav = {
    init() {
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const tabName = e.currentTarget.dataset.tab;
                if (tabName) this.switchTo(tabName);
            });
        });
    },

    switchTo(tabName) {
        document.querySelectorAll('.content').forEach(c => {
            c.classList.remove('active');
            c.setAttribute('aria-hidden', 'true');
        });

        document.querySelectorAll('.tab-btn').forEach(b => {
            b.classList.remove('active');
            b.setAttribute('aria-selected', 'false');
        });

        const content = document.getElementById(tabName);
        const btn = document.querySelector(`[data-tab="${tabName}"]`);

        if (content) {
            content.classList.add('active');
            content.setAttribute('aria-hidden', 'false');
        }

        if (btn) {
            btn.classList.add('active');
            btn.setAttribute('aria-selected', 'true');
        }

        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

// Global shorthand
function switchTab(tabName) {
    TabNav.switchTo(tabName);
}

// ============================================
// UTILITIES MODULE
// ============================================

const Utils = {
    getCsrfToken() {
        return document.querySelector('input[name="csrf_token"]')?.value || '';
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    },

    isValidPhone(phone) {
        // More lenient validation - just check for at least 10 digits
        const digitsOnly = phone.replace(/[^0-9]/g, '');
        return digitsOnly.length >= 10;
    },

    formatCurrency(amount) {
        return Math.round(amount).toLocaleString(CONFIG.locale);
    },

    async fetchAPI(endpoint, body) {
        const response = await fetch(`${CONFIG.apiUrl}/${endpoint}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(body)
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || `Request failed`);
        }

        return data;
    }
};

// ============================================
// SHOPPING CART MODULE
// ============================================
// Used on: public/index.php (Products tab)

const Cart = {
    async addItem(productId, productName, price) {
        try {
            const data = await Utils.fetchAPI('cart.php', {
                action: 'add',
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
            const data = await Utils.fetchAPI('cart.php', {
                action: 'update',
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
            const data = await Utils.fetchAPI('cart.php', {
                action: 'remove',
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
// SHIPPING MODULE
// ============================================
// Used on: public/index.php (Cart tab)

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
// CHECKOUT FORM MODULE
// ============================================
// Used on: public/index.php (Checkout tab)

const CheckoutForm = {
    validate() {
        const proceedBtn = document.getElementById('proceedBtn');
        if (!proceedBtn) return false;

        const required = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
        
        let allFilled = true;
        required.forEach(fieldId => {
            const input = document.getElementById(fieldId);
            if (!input || !input.value.trim()) {
                allFilled = false;
            }
        });

        const emailEl = document.getElementById('email');
        const phoneEl = document.getElementById('phone');
        const email = emailEl?.value.trim() || '';
        const phone = phoneEl?.value.trim() || '';

        const isValid = allFilled && 
                        (email === '' || Utils.isValidEmail(email)) && 
                        (phone === '' || Utils.isValidPhone(phone));
        
        const cartItems = document.getElementById('cartItems');
        const hasItems = cartItems && cartItems.children.length > 0 && !cartItems.querySelector('.empty-state');
        
        proceedBtn.disabled = !isValid || !hasItems;

        return true;
    },

    markFieldsEmpty() {
        const required = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
        required.forEach(fieldId => {
            const input = document.getElementById(fieldId);
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
    const required = ['firstName', 'lastName', 'email', 'phone', 'address', 'city', 'state', 'postalCode', 'country'];
    
    // Check all required fields
    for (let fieldId of required) {
        const input = document.getElementById(fieldId);
        if (!input || !input.value.trim()) {
            Notifications.error(`Please fill in all required fields`);
            return;
        }
    }

    // Validate email
    const emailEl = document.getElementById('email');
    const email = emailEl?.value.trim() || '';
    if (email && !Utils.isValidEmail(email)) {
        Notifications.error('Please enter a valid email address (e.g., user@example.com)');
        return;
    }

    // Validate phone
    const phoneEl = document.getElementById('phone');
    const phone = phoneEl?.value.trim() || '';
    if (phone && !Utils.isValidPhone(phone)) {
        Notifications.error('Please enter a valid phone number (at least 10 digits)');
        return;
    }

    // Check if cart has items
    const cartItems = document.getElementById('cartItems');
    if (!cartItems || cartItems.children.length === 0 || cartItems.querySelector('.empty-state')) {
        Notifications.error('Your cart is empty. Please add items before checkout.');
        return;
    }

    window.scrollTo(0, 0);
    TabNav.switchTo('payment');
    Payment.renderOptions();
}

// ============================================
// PAYMENT MODULE
// ============================================
// Used on: public/index.php (Payment tab)

const Payment = {
    currentMethod: 'cash_on_delivery',

    renderOptions() {
        const paymentContent = document.getElementById('paymentContent');
        if (!paymentContent) return;

        // Ensure the payment method field is synced before rendering
        const methodField = document.getElementById('paymentMethodField');
        if (methodField && methodField.value) {
            this.currentMethod = methodField.value;
        } else {
            this.currentMethod = 'cash_on_delivery';
            if (methodField) methodField.value = this.currentMethod;
        }

        paymentContent.innerHTML = `
            <div style="max-width: 900px; margin: 0 auto;">
                <div class="payment-form-section">
                    <h3>Select Payment Method</h3>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" id="cod" name="paymentMethod" value="cash_on_delivery" ${this.currentMethod === 'cash_on_delivery' ? 'checked' : ''} onchange="Payment.selectMethod('cash_on_delivery')">
                            <label for="cod" class="payment-label">
                                <div>💵</div>
                                <div>Cash on Delivery</div>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="bank" name="paymentMethod" value="bank_transfer" ${this.currentMethod === 'bank_transfer' ? 'checked' : ''} onchange="Payment.selectMethod('bank_transfer')">
                            <label for="bank" class="payment-label">
                                <div>🏦</div>
                                <div>Bank Transfer</div>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="mobile" name="paymentMethod" value="mobile_banking" ${this.currentMethod === 'mobile_banking' ? 'checked' : ''} onchange="Payment.selectMethod('mobile_banking')">
                            <label for="mobile" class="payment-label">
                                <div>📱</div>
                                <div>Mobile Banking</div>
                            </label>
                        </div>
                    </div>
                </div>
                
                <div id="methodDetails" class="payment-form-section" style="margin-top: 1.5rem;"></div>
                
                <div class="payment-form-section">
                    <h3>Order Summary</h3>
                    <div id="paymentSummary" style="margin-top: 1rem;"></div>
                </div>
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="button" class="btn btn-secondary" onclick="switchTab('checkout')" style="margin-right: 1rem;">← Back</button>
                    <button type="button" class="btn btn-primary btn-large" id="completeOrderBtn" onclick="Payment.submit()">Complete Order →</button>
                </div>
            </div>
        `;

        this.updateSummary();
        this.showMethodDetails(this.currentMethod);
    },

    selectMethod(method) {
        this.currentMethod = method;
        // Update the hidden field in the checkout form
        const methodField = document.getElementById('paymentMethodField');
        if (methodField) {
            methodField.value = method;
        }
        this.showMethodDetails(method);
    },

    showMethodDetails(method) {
        const detailsDiv = document.getElementById('methodDetails');
        if (!detailsDiv) return;

        switch(method) {
            case 'cash_on_delivery':
                detailsDiv.innerHTML = `
                    <div style="padding: 1rem; background: #f0f4ff; border-radius: 8px; border-left: 4px solid #667eea;">
                        <h4 style="margin-top: 0;">💵 Cash on Delivery</h4>
                        <p style="margin: 0.5rem 0;">Payment will be collected when your order is delivered.</p>
                        <p style="margin: 0.5rem 0; color: #666;">No payment information required.</p>
                    </div>
                `;
                break;
            case 'bank_transfer':
                detailsDiv.innerHTML = `
                    <div style="padding: 1rem; background: #f0f4ff; border-radius: 8px; border-left: 4px solid #667eea;">
                        <h4 style="margin-top: 0;">🏦 Bank Transfer</h4>
                        <p style="margin: 0.5rem 0;"><strong>Account Holder:</strong> NextCart Ltd.</p>
                        <p style="margin: 0.5rem 0;"><strong>Bank:</strong> Bangladesh Bank</p>
                        <p style="margin: 0.5rem 0;"><strong>Account Number:</strong> 1234567890</p>
                        <p style="margin: 0.5rem 0;"><strong>Routing Number:</strong> 987654321</p>
                        <p style="margin: 0.5rem 0; color: #d9534f;"><strong>Please note:</strong> We will verify your payment and ship your order within 24 hours.</p>
                    </div>
                `;
                break;
            case 'mobile_banking':
                detailsDiv.innerHTML = `
                    <div style="padding: 1rem; background: #f0f4ff; border-radius: 8px; border-left: 4px solid #667eea;">
                        <h4 style="margin-top: 0;">📱 Mobile Banking</h4>
                        <div style="margin-top: 1rem;">
                            <p style="margin: 0.5rem 0;"><strong>bKash:</strong> 01XXXXXXXXX</p>
                            <p style="margin: 0.5rem 0;"><strong>Nagad:</strong> 01YYYYYYYYY</p>
                            <p style="margin: 0.5rem 0;"><strong>Rocket:</strong> 01ZZZZZZZZZ</p>
                        </div>
                        <p style="margin-top: 1rem; color: #d9534f;"><strong>Payment Instructions:</strong></p>
                        <ol style="margin: 0.5rem 0; padding-left: 1.5rem;">
                            <li>Send money using your mobile banking app</li>
                            <li>Screenshot the confirmation</li>
                            <li>You'll be prompted to upload it after order confirmation</li>
                        </ol>
                    </div>
                `;
                break;
        }
    },

    updateSummary() {
        const summaryDiv = document.getElementById('paymentSummary');
        if (!summaryDiv) return;

        const subtotalText = document.querySelector('.summary-row span:last-child');
        const subtotal = subtotalText ? parseInt(subtotalText.textContent.replace(/[^0-9]/g, '')) : 0;
        const shippingMethod = document.getElementById('shippingMethod')?.value || '0';
        const shippingCost = parseInt(shippingMethod) || 0;
        const total = subtotal + shippingCost;

        summaryDiv.innerHTML = `
            <div style="text-align: right;">
                <p style="margin: 0.5rem 0;"><strong>Subtotal:</strong> ৳ ${Utils.formatCurrency(subtotal)}</p>
                <p style="margin: 0.5rem 0;"><strong>Shipping:</strong> ৳ ${Utils.formatCurrency(shippingCost)}</p>
                <p style="margin: 0.5rem 0; font-size: 1.2rem; font-weight: 700; color: #667eea;"><strong>Total:</strong> ৳ ${Utils.formatCurrency(total)}</p>
            </div>
        `;
    },

    async submit() {
        const csrfToken = Utils.getCsrfToken();
        if (!csrfToken) {
            Notifications.error('CSRF token not found');
            return;
        }

        // Get form elements safely
        const firstNameEl = document.getElementById('firstName');
        const lastNameEl = document.getElementById('lastName');
        const emailEl = document.getElementById('email');
        const phoneEl = document.getElementById('phone');
        const addressEl = document.getElementById('address');
        const cityEl = document.getElementById('city');
        const stateEl = document.getElementById('state');
        const postalCodeEl = document.getElementById('postalCode');
        const countryEl = document.getElementById('country');
        const shippingMethodEl = document.getElementById('shippingMethod');
        const orderNotesEl = document.getElementById('orderNotes');
        const paymentMethodEl = document.getElementById('paymentMethodField');

        if (!firstNameEl || !lastNameEl || !emailEl || !phoneEl) {
            Notifications.error('Form fields not found');
            return;
        }

        // Build form data with all fields
        const formData = {
            action: 'checkout',
            csrf_token: csrfToken,
            firstName: firstNameEl.value.trim(),
            lastName: lastNameEl.value.trim(),
            email: emailEl.value.trim(),
            phone: phoneEl.value.trim(),
            address: addressEl ? addressEl.value.trim() : '',
            city: cityEl ? cityEl.value.trim() : '',
            state: stateEl ? stateEl.value.trim() : '',
            postalCode: postalCodeEl ? postalCodeEl.value.trim() : '',
            country: countryEl ? countryEl.value.trim() : 'Bangladesh',
            shippingMethod: shippingMethodEl ? shippingMethodEl.value : '0',
            orderNotes: orderNotesEl ? orderNotesEl.value.trim() : '',
            paymentMethod: paymentMethodEl ? paymentMethodEl.value : this.currentMethod
        };

        try {
            const proceedBtn = document.getElementById('completeOrderBtn');
            if (proceedBtn) proceedBtn.disabled = true;

            const data = await Utils.fetchAPI('cart.php', formData);

            if (data.success) {
                Notifications.success('Order placed successfully!');
                setTimeout(() => { 
                    window.location.href = data.redirect_url; 
                }, 1000);
            } else {
                Notifications.error(data.error || 'Order failed');
                if (proceedBtn) proceedBtn.disabled = false;
            }
        } catch (error) {
            Notifications.error(error.message);
            const proceedBtn = document.getElementById('completeOrderBtn');
            if (proceedBtn) proceedBtn.disabled = false;
        }
    }
};

// ============================================
// ORDER DISPLAY MODULE
// ============================================
// Used on: public/order_confirmation.php

const OrderDisplay = {
    /**
     * Initialize order page - can enhance with dynamic loading later
     */
    init() {
        // Timeline animation on load
        this.animateTimeline();
        
        // Print button functionality if exists
        const printBtn = document.getElementById('printOrder');
        if (printBtn) {
            printBtn.addEventListener('click', () => {
                window.print();
            });
        }
    },

    /**
     * Animate timeline steps as they come into view
     */
    animateTimeline() {
        const timelineSteps = document.querySelectorAll('.timeline-step');
        timelineSteps.forEach((step, index) => {
            setTimeout(() => {
                step.style.opacity = '1';
                step.style.transition = 'opacity 0.5s ease-in';
            }, index * 200);
        });
    },

    /**
     * Copy order ID to clipboard
     */
    copyOrderId() {
        const orderIdEl = document.querySelector('.order-id');
        if (!orderIdEl) return;

        const orderId = orderIdEl.textContent;
        navigator.clipboard.writeText(orderId).then(() => {
            Notifications.success('Order ID copied to clipboard!');
        }).catch(() => {
            Notifications.error('Failed to copy order ID');
        });
    }
};

// Global shortcuts
function copyOrderId() { OrderDisplay.copyOrderId(); }

// ============================================
// ADMIN MODULE
// ============================================
// Used on: public/admin/index.php & public/admin/orders.php

const Admin = {
    /**
     * Initialize admin page features
     */
    init() {
        this.attachFilterHandlers();
        this.attachActionHandlers();
    },

    /**
     * Handle status filter buttons
     */
    attachFilterHandlers() {
        const filterBtns = document.querySelectorAll('.status-filter');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active from all buttons
                filterBtns.forEach(b => b.classList.remove('active'));
                
                // Add active to clicked button
                this.classList.add('active');
                
                // Get filter value and apply
                const status = this.getAttribute('data-status');
                Admin.filterOrders(status);
            });
        });
    },

    /**
     * Handle action buttons (view, edit, delete)
     */
    attachActionHandlers() {
        // View order button
        const viewBtns = document.querySelectorAll('.action-btn-view');
        viewBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const orderId = btn.getAttribute('data-order-id');
                if (orderId) {
                    window.location.href = `?action=view&order_id=${orderId}`;
                }
            });
        });

        // Edit order button
        const editBtns = document.querySelectorAll('.action-btn-edit');
        editBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const orderId = btn.getAttribute('data-order-id');
                if (orderId) {
                    window.location.href = `?action=edit&order_id=${orderId}`;
                }
            });
        });

        // Delete order button
        const deleteBtns = document.querySelectorAll('.action-btn-delete');
        deleteBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const orderId = btn.getAttribute('data-order-id');
                if (orderId && confirm('Are you sure you want to delete this order?')) {
                    this.deleteOrder(orderId);
                }
            });
        });
    },

    /**
     * Filter orders by status (client-side demo)
     * In production, this would trigger a server-side filter
     */
    filterOrders(status) {
        const rows = document.querySelectorAll('.orders-table tbody tr');
        let visibleCount = 0;

        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
                visibleCount++;
            } else {
                const rowStatus = row.getAttribute('data-status');
                if (rowStatus === status) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });

        if (visibleCount === 0) {
            const tbody = document.querySelector('.orders-table tbody');
            if (tbody) {
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; padding: 2rem;">No orders found</td></tr>';
            }
        }
    },

    /**
     * Delete order via API
     */
    async deleteOrder(orderId) {
        try {
            const data = await Utils.fetchAPI('delete_order.php', {
                order_id: orderId,
                csrf_token: Utils.getCsrfToken()
            });

            if (data.success) {
                Notifications.success('Order deleted successfully');
                // Reload page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                Notifications.error(data.error || 'Failed to delete order');
            }
        } catch (error) {
            Notifications.error(error.message);
        }
    },

    /**
     * Export orders to CSV
     */
    exportToCSV() {
        const table = document.querySelector('.orders-table');
        if (!table) return;

        let csv = [];
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const cells = row.querySelectorAll('td, th');
            const rowData = [];
            cells.forEach(cell => {
                rowData.push('"' + cell.textContent.trim() + '"');
            });
            csv.push(rowData.join(','));
        });

        const csvContent = 'data:text/csv;charset=utf-8,' + csv.join('\n');
        const link = document.createElement('a');
        link.setAttribute('href', encodeURI(csvContent));
        link.setAttribute('download', 'orders.csv');
        link.click();

        Notifications.success('Orders exported to CSV');
    }
};

// Global shortcuts
function filterOrders(status) { Admin.filterOrders(status); }
function exportToCSV() { Admin.exportToCSV(); }

// ============================================
// APP INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tab navigation
    TabNav.init();
    
    // Initialize checkout form validation
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

    // Initialize shipping cost updater
    const shippingMethod = document.getElementById('shippingMethod');
    if (shippingMethod) {
        shippingMethod.addEventListener('change', function() {
            Shipping.updateCost();
        });
    }

    // Initialize admin features
    if (document.querySelector('.admin-container')) {
        Admin.init();
    }

    // Initialize order display features
    if (document.querySelector('.invoice')) {
        OrderDisplay.init();
    }
});

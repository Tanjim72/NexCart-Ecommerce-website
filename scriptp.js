// CONFIGURATION & CONSTANTS
// ============================================

const CONFIG = {
    currency: '৳',
    locale: 'en-BD',
    orderPrefix: 'ORDER',
    animationDuration: 300,
    toastDuration: 3000
};

const PAYMENT_METHODS = {
    bkash: { name: 'bKash', icon: '📱' },
    nagad: { name: 'Nagad', icon: '📞' },
    rocket: { name: 'Rocket', icon: '🚀' },
    card: { name: 'Credit Card', icon: '💳' }
};

const TRACKING_STEPS = [
    { key: 'pending', title: 'Order Confirmed', desc: 'Your order has been received' },
    { key: 'processing', title: 'Processing', desc: 'Your order is being prepared' },
    { key: 'shipped', title: 'Shipped', desc: 'Your package is on its way' },
    { key: 'delivery', title: 'Out for Delivery', desc: 'Package arriving today' },
    { key: 'delivered', title: 'Delivered', desc: 'Order completed successfully' }
];

// ============================================
// APPLICATION STATE
// ============================================

const state = {
    cart: [],
    shipping: {
        cost: 0,
        method: 'standard'
    },
    order: null,
    trackingStatus: 'pending'
};

// ============================================
// UTILITY FUNCTIONS
// ============================================

/**
 * Format currency with locale
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return `${CONFIG.currency} ${amount.toLocaleString(CONFIG.locale)}`;
}

/**
 * Generate unique order ID
 * @returns {string} Unique order ID
 */
function generateOrderId() {
    const timestamp = Date.now();
    const random = Math.random().toString(36).substring(2, 6).toUpperCase();
    return `${CONFIG.orderPrefix}-${timestamp}-${random}`;
}

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of toast (success, error, warning)
 */
function showToast(message, type = 'success') {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${type === 'success' ? '✓' : type === 'error' ? '✕' : '⚠'}</span>
        <span class="toast-message">${message}</span>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), CONFIG.animationDuration);
    }, CONFIG.toastDuration);
}

/**
 * Validate email format
 * @param {string} email - Email to validate
 * @returns {boolean} Is valid email
 */
function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

/**
 * Validate phone number (Bangladesh format)
 * @param {string} phone - Phone number to validate
 * @returns {boolean} Is valid phone
 */
function isValidPhone(phone) {
    return /^01[3-9]\d{8}$/.test(phone.replace(/\s/g, ''));
}

/**
 * Escape HTML to prevent XSS
 * @param {string} str - String to escape
 * @returns {string} Escaped string
 */
function escapeHtml(str) {
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

// ============================================
// TAB NAVIGATION
// ============================================

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        const tabName = e.currentTarget.dataset.tab;
        if (tabName) switchTab(tabName);
    });
});

/**
 * Switch to specified tab
 * @param {string} tabName - Name of tab to switch to
 */
function switchTab(tabName) {
    // Update content visibility
    document.querySelectorAll('.content').forEach(c => {
        c.classList.remove('active');
        c.setAttribute('aria-hidden', 'true');
    });
    
    // Update tab buttons
    document.querySelectorAll('.tab-btn').forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
    });
    
    // Activate selected tab
    const activeContent = document.getElementById(tabName);
    const activeBtn = document.querySelector(`[data-tab="${tabName}"]`);
    
    if (activeContent) {
        activeContent.classList.add('active');
        activeContent.setAttribute('aria-hidden', 'false');
    }
    
    if (activeBtn) {
        activeBtn.classList.add('active');
        activeBtn.setAttribute('aria-selected', 'true');
    }
    
    // Load tab-specific content
    switch (tabName) {
        case 'payment':
            updatePaymentView();
            break;
        case 'tracking':
            updateTrackingView();
            break;
        case 'invoice':
            updateInvoiceView();
            break;
    }
    
    // Scroll to top of content
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ============================================
// SHOPPING CART FUNCTIONS
// ============================================

/**
 * Add item to cart
 * @param {string} name - Product name
 * @param {number} price - Product price
 */
function addToCart(name, price) {
    const existingItem = state.cart.find(item => item.name === name);
    
    if (existingItem) {
        existingItem.quantity++;
        showToast(`Added another ${name} to cart`, 'success');
    } else {
        state.cart.push({ 
            id: Date.now(),
            name, 
            price, 
            quantity: 1 
        });
        showToast(`${name} added to cart`, 'success');
    }
    
    updateCartDisplay();
}

/**
 * Update cart display
 */
function updateCartDisplay() {
    const cartContainer = document.getElementById('cartItems');
    const summaryContainer = document.getElementById('cartSummary');
    const proceedBtn = document.getElementById('proceedBtn');
    const cartCount = document.getElementById('cartCount');

    // Update cart count
    const totalItems = state.cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartCount) {
        cartCount.textContent = `${totalItems} item${totalItems !== 1 ? 's' : ''}`;
    }

    // Empty cart state
    if (state.cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">🛒</div>
                <p>Your cart is empty</p>
                <p class="text-muted">Add some products to get started!</p>
            </div>
        `;
        summaryContainer.innerHTML = '';
        proceedBtn.disabled = true;
        return;
    }

    // Render cart items
    cartContainer.innerHTML = state.cart.map((item, idx) => `
        <div class="cart-item" data-item-id="${item.id}">
            <div class="item-info">
                <div class="item-name">${escapeHtml(item.name)}</div>
                <div class="item-price">${formatCurrency(item.price)} each</div>
            </div>
            <div class="quantity-control">
                <button class="qty-btn" onclick="updateQuantity(${idx}, -1)" aria-label="Decrease quantity">−</button>
                <input type="number" 
                       class="qty-input" 
                       value="${item.quantity}" 
                       min="1" 
                       max="99"
                       onchange="setQuantity(${idx}, this.value)"
                       aria-label="Quantity">
                <button class="qty-btn" onclick="updateQuantity(${idx}, 1)" aria-label="Increase quantity">+</button>
            </div>
            <div class="item-total">${formatCurrency(item.price * item.quantity)}</div>
            <button class="remove-btn" onclick="removeFromCart(${idx})" aria-label="Remove item">
                Remove
            </button>
        </div>
    `).join('');

    // Calculate totals
    const subtotal = state.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const total = subtotal + state.shipping.cost;

    // Render summary
    summaryContainer.innerHTML = `
        <div class="summary-row">
            <span>Subtotal:</span>
            <span>${formatCurrency(subtotal)}</span>
        </div>
        <div class="summary-row">
            <span>Delivery Charge:</span>
            <span>${state.shipping.cost > 0 ? formatCurrency(state.shipping.cost) : 'Free'}</span>
        </div>
        <div class="summary-row total">
            <span>Grand Total:</span>
            <span>${formatCurrency(total)}</span>
        </div>
    `;

    proceedBtn.disabled = false;
}

/**
 * Update item quantity
 * @param {number} idx - Item index
 * @param {number} change - Quantity change (+1 or -1)
 */
function updateQuantity(idx, change) {
    if (!state.cart[idx]) return;
    
    const newQty = state.cart[idx].quantity + change;
    
    if (newQty > 0 && newQty <= 99) {
        state.cart[idx].quantity = newQty;
        updateCartDisplay();
    } else if (newQty <= 0) {
        removeFromCart(idx);
    }
}

/**
 * Set specific quantity for item
 * @param {number} idx - Item index
 * @param {string|number} value - New quantity
 */
function setQuantity(idx, value) {
    const qty = parseInt(value, 10);
    
    if (isNaN(qty) || qty < 1) {
        updateCartDisplay(); // Reset to current value
        return;
    }
    
    if (qty > 99) {
        state.cart[idx].quantity = 99;
    } else {
        state.cart[idx].quantity = qty;
    }
    
    updateCartDisplay();
}

/**
 * Remove item from cart
 * @param {number} idx - Item index
 */
function removeFromCart(idx) {
    const item = state.cart[idx];
    if (item) {
        showToast(`${item.name} removed from cart`, 'warning');
        state.cart.splice(idx, 1);
        updateCartDisplay();
    }
}

// ============================================
// CHECKOUT FUNCTIONS
// ============================================

/**
 * Validate form and proceed to payment
 */
function validateAndProceed() {
    const form = document.getElementById('checkoutForm');
    const fields = {
        firstName: document.getElementById('firstName'),
        lastName: document.getElementById('lastName'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        address: document.getElementById('address'),
        city: document.getElementById('city'),
        state: document.getElementById('state'),
        postalCode: document.getElementById('postalCode'),
        country: document.getElementById('country')
    };

    // Clear previous error states
    Object.values(fields).forEach(field => {
        field.classList.remove('error');
    });

    // Validate required fields
    let isValid = true;
    let firstError = null;

    for (const [key, field] of Object.entries(fields)) {
        if (!field.value.trim()) {
            field.classList.add('error');
            isValid = false;
            if (!firstError) firstError = field;
        }
    }

    // Validate email format
    if (fields.email.value && !isValidEmail(fields.email.value)) {
        fields.email.classList.add('error');
        isValid = false;
        showToast('Please enter a valid email address', 'error');
        if (!firstError) firstError = fields.email;
    }

    // Validate phone format
    if (fields.phone.value && !isValidPhone(fields.phone.value)) {
        fields.phone.classList.add('error');
        isValid = false;
        showToast('Please enter a valid Bangladesh phone number', 'error');
        if (!firstError) firstError = fields.phone;
    }

    if (!isValid) {
        if (firstError) firstError.focus();
        showToast('Please fill in all required fields correctly', 'error');
        return;
    }

    // Create order object
    state.order = {
        id: generateOrderId(),
        firstName: escapeHtml(fields.firstName.value.trim()),
        lastName: escapeHtml(fields.lastName.value.trim()),
        email: escapeHtml(fields.email.value.trim()),
        phone: escapeHtml(fields.phone.value.trim()),
        address: escapeHtml(fields.address.value.trim()),
        city: escapeHtml(fields.city.value.trim()),
        state: escapeHtml(fields.state.value.trim()),
        postalCode: escapeHtml(fields.postalCode.value.trim()),
        country: escapeHtml(fields.country.value),
        notes: escapeHtml(document.getElementById('orderNotes').value.trim()),
        items: JSON.parse(JSON.stringify(state.cart)),
        date: new Date(),
        subtotal: state.cart.reduce((sum, item) => sum + item.price * item.quantity, 0),
        shippingCost: state.shipping.cost
    };

    showToast('Delivery information saved!', 'success');
    switchTab('payment');
}

/**
 * Update shipping cost based on selection
 */
function updateShippingCost() {
    const shippingMethod = document.getElementById('shippingMethod');
    state.shipping.cost = parseFloat(shippingMethod.value) || 0;
    updateCartDisplay();
}

// ============================================
// PAYMENT FUNCTIONS
// ============================================

/**
 * Update payment view with options
 */
function updatePaymentView() {
    const content = document.getElementById('paymentContent');
    
    if (!state.order) {
        content.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">⚠️</div>
                <p>Please complete checkout first</p>
                <button class="btn btn-primary mt-2" onclick="switchTab('checkout')">Go to Checkout</button>
            </div>
        `;
        return;
    }

    const total = state.order.subtotal + state.shipping.cost;

    content.innerHTML = `
        <div class="cart-summary mb-3">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>${formatCurrency(state.order.subtotal)}</span>
            </div>
            <div class="summary-row">
                <span>Delivery:</span>
                <span>${state.shipping.cost > 0 ? formatCurrency(state.shipping.cost) : 'Free'}</span>
            </div>
            <div class="summary-row total">
                <span>Total to Pay:</span>
                <span>${formatCurrency(total)}</span>
            </div>
        </div>

        <h3 class="mb-2">Select Payment Method</h3>
        <div class="payment-options">
            ${Object.entries(PAYMENT_METHODS).map(([key, method]) => `
                <div class="payment-option">
                    <input type="radio" id="${key}" name="payment" value="${key}" ${key === 'bkash' ? 'checked' : ''}>
                    <label for="${key}" class="payment-label">
                        <div>${method.icon}</div>
                        ${method.name}
                    </label>
                </div>
            `).join('')}
        </div>

        <div id="paymentForms">
            ${renderPaymentForm('bkash', true)}
            ${renderPaymentForm('nagad', false)}
            ${renderPaymentForm('rocket', false)}
            ${renderCardForm(false)}
        </div>

        <div class="form-actions mt-3">
            <button class="btn btn-secondary" onclick="switchTab('checkout')">← Back</button>
            <button class="btn btn-primary btn-large" onclick="processPayment()">
                💳 Complete Payment - ${formatCurrency(total)}
            </button>
        </div>
    `;

    // Add payment method toggle listeners
    document.querySelectorAll('input[name="payment"]').forEach(radio => {
        radio.addEventListener('change', (e) => {
            document.querySelectorAll('[id$="Form"]').forEach(form => {
                if (form.id.startsWith('payment')) return;
                form.style.display = 'none';
            });
            const formId = e.target.value === 'card' ? 'cardForm' : `${e.target.value}Form`;
            const form = document.getElementById(formId);
            if (form) form.style.display = 'block';
        });
    });
}

/**
 * Render mobile payment form
 * @param {string} provider - Payment provider name
 * @param {boolean} show - Whether to show initially
 * @returns {string} HTML string
 */
function renderPaymentForm(provider, show) {
    const providerName = PAYMENT_METHODS[provider]?.name || provider;
    return `
        <div id="${provider}Form" class="payment-form-section" style="display: ${show ? 'block' : 'none'};">
            <h3>${providerName} Payment</h3>
            <div class="form-group">
                <label for="${provider}Phone">${providerName} Account Number</label>
                <input type="tel" 
                       id="${provider}Phone" 
                       placeholder="01XXXXXXXXX"
                       pattern="01[3-9][0-9]{8}"
                       maxlength="11">
            </div>
            <div class="form-group">
                <label for="${provider}Pin">PIN</label>
                <input type="password" 
                       id="${provider}Pin" 
                       placeholder="Enter your ${providerName} PIN" 
                       maxlength="5">
            </div>
        </div>
    `;
}

/**
 * Render credit card form
 * @param {boolean} show - Whether to show initially
 * @returns {string} HTML string
 */
function renderCardForm(show) {
    return `
        <div id="cardForm" class="payment-form-section" style="display: ${show ? 'block' : 'none'};">
            <h3>Card Information</h3>
            <div class="form-group">
                <label for="cardName">Cardholder Name</label>
                <input type="text" id="cardName" placeholder="Name on card" autocomplete="cc-name">
            </div>
            <div class="form-group">
                <label for="cardNumber">Card Number</label>
                <input type="text" 
                       id="cardNumber" 
                       placeholder="1234 5678 9012 3456" 
                       maxlength="19"
                       autocomplete="cc-number"
                       oninput="formatCardNumber(this)">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="expiryDate">Expiry Date</label>
                    <input type="text" 
                           id="expiryDate" 
                           placeholder="MM/YY" 
                           maxlength="5"
                           autocomplete="cc-exp"
                           oninput="formatExpiry(this)">
                </div>
                <div class="form-group">
                    <label for="cvv">CVV</label>
                    <input type="password" 
                           id="cvv" 
                           placeholder="123" 
                           maxlength="4"
                           autocomplete="cc-csc">
                </div>
            </div>
        </div>
    `;
}

/**
 * Format card number with spaces
 * @param {HTMLInputElement} input - Card number input
 */
function formatCardNumber(input) {
    let value = input.value.replace(/\s/g, '').replace(/\D/g, '');
    value = value.match(/.{1,4}/g)?.join(' ') || value;
    input.value = value;
}

/**
 * Format expiry date
 * @param {HTMLInputElement} input - Expiry input
 */
function formatExpiry(input) {
    let value = input.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2);
    }
    input.value = value;
}

/**
 * Process payment
 */
function processPayment() {
    const method = document.querySelector('input[name="payment"]:checked')?.value;
    
    if (!method) {
        showToast('Please select a payment method', 'error');
        return;
    }

    // Validate mobile payment
    if (['bkash', 'nagad', 'rocket'].includes(method)) {
        const phone = document.getElementById(`${method}Phone`)?.value.trim();
        const pin = document.getElementById(`${method}Pin`)?.value.trim();
        
        if (!phone || !isValidPhone(phone)) {
            showToast(`Please enter a valid ${PAYMENT_METHODS[method].name} number`, 'error');
            return;
        }
        if (!pin || pin.length < 4) {
            showToast('Please enter your PIN', 'error');
            return;
        }
    }
    
    // Validate card payment
    if (method === 'card') {
        const cardNumber = document.getElementById('cardNumber')?.value.replace(/\s/g, '');
        const expiry = document.getElementById('expiryDate')?.value;
        const cvv = document.getElementById('cvv')?.value;
        const cardName = document.getElementById('cardName')?.value.trim();
        
        if (!cardName) {
            showToast('Please enter cardholder name', 'error');
            return;
        }
        if (!cardNumber || cardNumber.length < 13) {
            showToast('Please enter a valid card number', 'error');
            return;
        }
        if (!expiry || !/^\d{2}\/\d{2}$/.test(expiry)) {
            showToast('Please enter a valid expiry date (MM/YY)', 'error');
            return;
        }
        if (!cvv || cvv.length < 3) {
            showToast('Please enter CVV', 'error');
            return;
        }
    }

    // Update order with payment info
    state.order.paymentMethod = method;
    state.order.paymentDate = new Date();
    state.trackingStatus = 'processing';

    showToast('Payment successful! Order confirmed.', 'success');
    switchTab('tracking');
}

// ============================================
// ORDER TRACKING FUNCTIONS
// ============================================

/**
 * Update tracking view
 */
function updateTrackingView() {
    const content = document.getElementById('trackingContent');
    
    if (!state.order) {
        content.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📦</div>
                <p>No order to track</p>
                <button class="btn btn-primary mt-2" onclick="switchTab('shopping')">Start Shopping</button>
            </div>
        `;
        return;
    }

    const currentStep = getCurrentStep();
    const orderDate = new Date(state.order.date);
    const estimatedDelivery = new Date(orderDate);
    estimatedDelivery.setDate(estimatedDelivery.getDate() + 5);

    content.innerHTML = `
        <div class="tracking-info">
            <h3>Order Details</h3>
            <div class="tracking-details-grid">
                <p><strong>Order ID:</strong> <span class="order-id">${state.order.id}</span></p>
                <p><strong>Order Date:</strong> ${orderDate.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                })}</p>
                <p><strong>Customer:</strong> ${state.order.firstName} ${state.order.lastName}</p>
                <p><strong>Email:</strong> ${state.order.email}</p>
                <p><strong>Phone:</strong> ${state.order.phone}</p>
                <p><strong>Delivery Address:</strong> ${state.order.address}, ${state.order.city}, ${state.order.state} ${state.order.postalCode}</p>
                <p><strong>Estimated Delivery:</strong> ${estimatedDelivery.toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    month: 'long', 
                    day: 'numeric' 
                })}</p>
            </div>
        </div>

        <h3 class="mt-3 mb-2">Tracking Status</h3>
        <div class="tracking-timeline">
            ${TRACKING_STEPS.map((step, idx) => `
                <div class="timeline-step ${idx <= currentStep ? 'completed' : ''}">
                    <div class="timeline-marker">
                        ${idx <= currentStep ? '✓' : idx + 1}
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">${step.title}</div>
                        <div class="timeline-time">${step.desc}</div>
                        ${idx <= currentStep ? `
                            <div class="timeline-status">
                                <span class="status-badge success">Completed</span>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `).join('')}
        </div>

        <div class="form-actions mt-3">
            <button class="btn btn-secondary" onclick="simulateProgress()">
                🚚 Simulate Delivery Progress
            </button>
            <button class="btn btn-primary btn-large" onclick="switchTab('invoice')">
                View Invoice →
            </button>
        </div>
    `;
}

/**
 * Get current tracking step index
 * @returns {number} Current step index
 */
function getCurrentStep() {
    const stepMap = { 
        pending: 0, 
        processing: 1, 
        shipped: 2, 
        delivery: 3, 
        delivered: 4 
    };
    return stepMap[state.trackingStatus] ?? 0;
}

/**
 * Simulate order progress (for demo purposes)
 */
function simulateProgress() {
    const steps = ['pending', 'processing', 'shipped', 'delivery', 'delivered'];
    const currentIdx = steps.indexOf(state.trackingStatus);
    
    if (currentIdx < steps.length - 1) {
        state.trackingStatus = steps[currentIdx + 1];
        showToast(`Order status updated: ${TRACKING_STEPS[currentIdx + 1].title}`, 'success');
        updateTrackingView();
    } else {
        showToast('Order already delivered!', 'warning');
    }
}

// ============================================
// INVOICE FUNCTIONS
// ============================================

/**
 * Update invoice view
 */
function updateInvoiceView() {
    const content = document.getElementById('invoiceContent');
    
    if (!state.order) {
        content.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">📄</div>
                <p>No invoice available</p>
                <button class="btn btn-primary mt-2" onclick="switchTab('shopping')">Start Shopping</button>
            </div>
        `;
        return;
    }

    const subtotal = state.order.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const total = subtotal + state.shipping.cost;
    const orderDate = new Date(state.order.date);

    content.innerHTML = `
        <div class="invoice">
            <div class="invoice-header">
                <div class="invoice-brand">
                    <div class="invoice-title">INVOICE</div>
                    <p class="invoice-company">Bangladesh Store</p>
                </div>
                <div class="invoice-info">
                    <p><strong>Invoice #:</strong> ${state.order.id}</p>
                    <p><strong>Date:</strong> ${orderDate.toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    })}</p>
                    <p><strong>Status:</strong> <span class="status-badge success">Paid</span></p>
                </div>
            </div>

            <div class="invoice-meta">
                <div class="invoice-section">
                    <div class="invoice-section-title">Bill To:</div>
                    <p><strong>${state.order.firstName} ${state.order.lastName}</strong></p>
                    <p>${state.order.address}</p>
                    <p>${state.order.city}, ${state.order.state} ${state.order.postalCode}</p>
                    <p>${state.order.country}</p>
                    <p>📧 ${state.order.email}</p>
                    <p>📱 ${state.order.phone}</p>
                </div>
                <div class="invoice-section">
                    <div class="invoice-section-title">Ship To:</div>
                    <p><strong>${state.order.firstName} ${state.order.lastName}</strong></p>
                    <p>${state.order.address}</p>
                    <p>${state.order.city}, ${state.order.state} ${state.order.postalCode}</p>
                    <p>${state.order.country}</p>
                </div>
            </div>

            <div class="invoice-items">
                <table class="invoice-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${state.order.items.map((item, idx) => `
                            <tr>
                                <td>${idx + 1}</td>
                                <td>${escapeHtml(item.name)}</td>
                                <td>${item.quantity}</td>
                                <td class="amount">${formatCurrency(item.price)}</td>
                                <td class="amount">${formatCurrency(item.price * item.quantity)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>

            <div class="invoice-total">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>${formatCurrency(subtotal)}</span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>${state.shipping.cost > 0 ? formatCurrency(state.shipping.cost) : 'Free'}</span>
                </div>
                <div class="summary-row">
                    <span>Tax:</span>
                    <span>Included</span>
                </div>
                <div class="summary-row total">
                    <span>Total Amount:</span>
                    <span class="invoice-total-amount">${formatCurrency(total)}</span>
                </div>
            </div>

            <div class="invoice-payment-info">
                <p><strong>Payment Method:</strong> ${PAYMENT_METHODS[state.order.paymentMethod]?.name || 'N/A'}</p>
                <p><strong>Payment Date:</strong> ${state.order.paymentDate ? new Date(state.order.paymentDate).toLocaleDateString('en-US') : 'N/A'}</p>
                ${state.order.notes ? `<p><strong>Notes:</strong> ${state.order.notes}</p>` : ''}
            </div>

            <div class="invoice-footer">
                <p>Thank you for shopping with Bangladesh Store!</p>
                <p class="text-muted">This is a computer-generated invoice and does not require a signature.</p>
            </div>
        </div>

        <div class="form-actions mt-3">
            <button class="btn btn-secondary" onclick="printInvoice()">
                🖨️ Print Invoice
            </button>
            <button class="btn btn-primary" onclick="downloadInvoice()">
                📥 Download Invoice
            </button>
            <button class="btn btn-secondary" onclick="newOrder()">
                🛒 New Order
            </button>
        </div>
    `;
}

/**
 * Print invoice
 */
function printInvoice() {
    window.print();
}

/**
 * Download invoice as text file
 */
function downloadInvoice() {
    if (!state.order) return;

    const subtotal = state.order.items.reduce((sum, item) => sum + item.price * item.quantity, 0);
    const total = subtotal + state.shipping.cost;
    const orderDate = new Date(state.order.date);
    
    let invoiceText = `
════════════════════════════════════════════════════════
                    BANGLADESH STORE
                       INVOICE
════════════════════════════════════════════════════════

Invoice #: ${state.order.id}
Date: ${orderDate.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}

────────────────────────────────────────────────────────
BILL TO:
────────────────────────────────────────────────────────
${state.order.firstName} ${state.order.lastName}
${state.order.address}
${state.order.city}, ${state.order.state} ${state.order.postalCode}
${state.order.country}
Email: ${state.order.email}
Phone: ${state.order.phone}

────────────────────────────────────────────────────────
ORDER ITEMS:
────────────────────────────────────────────────────────
`;

    state.order.items.forEach((item, idx) => {
        const itemTotal = item.price * item.quantity;
        invoiceText += `${(idx + 1).toString().padStart(2)}. ${item.name.padEnd(25)} x${item.quantity.toString().padStart(2)}  ${formatCurrency(itemTotal).padStart(15)}\n`;
    });

    invoiceText += `
────────────────────────────────────────────────────────
                              Subtotal: ${formatCurrency(subtotal).padStart(15)}
                              Shipping: ${(state.shipping.cost > 0 ? formatCurrency(state.shipping.cost) : 'Free').padStart(15)}
                              ─────────────────────────
                              TOTAL:    ${formatCurrency(total).padStart(15)}

────────────────────────────────────────────────────────
PAYMENT INFORMATION:
────────────────────────────────────────────────────────
Payment Method: ${PAYMENT_METHODS[state.order.paymentMethod]?.name || 'N/A'}
Payment Status: Paid

════════════════════════════════════════════════════════
        Thank you for shopping with Bangladesh Store!
        
   This is a computer-generated invoice and does not
              require a signature.
════════════════════════════════════════════════════════
`;

    // Create and download file
    const blob = new Blob([invoiceText], { type: 'text/plain;charset=utf-8' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `Invoice_${state.order.id}.txt`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);

    showToast('Invoice downloaded successfully!', 'success');
}

/**
 * Start a new order
 */
function newOrder() {
    // Reset state
    state.cart = [];
    state.shipping = { cost: 0, method: 'standard' };
    state.order = null;
    state.trackingStatus = 'pending';
    
    // Reset form
    const form = document.getElementById('checkoutForm');
    if (form) form.reset();
    
    // Update display and switch to shopping
    updateCartDisplay();
    switchTab('shopping');
    
    showToast('Ready for a new order!', 'success');
}

// ============================================
// INITIALIZATION
// ============================================

/**
 * Initialize application
 */
function init() {
    updateCartDisplay();
    
    // Add keyboard navigation for tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                btn.click();
            }
        });
    });

    console.log('🛍️ Bangladesh Store initialized successfully!');
}

// Run initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
/**
 * Payment Page JavaScript
 * ============================================
 * Handles payment method selection and order submission
 * Used on: public/index.php (Payment tab)
 * 
 * Dependencies: utilities.js (CONFIG, Notifications, Utils), checkout-page.js (form data)
 */

'use strict';

// ============================================
// PAYMENT MODULE
// ============================================

const Payment = {
    currentMethod: 'cash_on_delivery',

    renderOptions() {
        const paymentContent = document.getElementById('paymentContent');
        if (!paymentContent) return;

        paymentContent.innerHTML = `
            <div style="max-width: 900px; margin: 0 auto;">
                <div class="payment-form-section">
                    <h3>Select Payment Method</h3>
                    <div class="payment-options">
                        <div class="payment-option">
                            <input type="radio" id="cod" name="paymentMethod" value="cash_on_delivery" checked onchange="Payment.selectMethod('cash_on_delivery')">
                            <label for="cod" class="payment-label">
                                <div>💵</div>
                                <div>Cash on Delivery</div>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="bank" name="paymentMethod" value="bank_transfer" onchange="Payment.selectMethod('bank_transfer')">
                            <label for="bank" class="payment-label">
                                <div>🏦</div>
                                <div>Bank Transfer</div>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" id="mobile" name="paymentMethod" value="mobile_banking" onchange="Payment.selectMethod('mobile_banking')">
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
                    <button type="button" class="btn btn-primary btn-large" onclick="Payment.submit()">Complete Order →</button>
                </div>
            </div>
        `;

        this.updateSummary();
        this.showMethodDetails('cash_on_delivery');
    },

    selectMethod(method) {
        this.currentMethod = method;
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

        const form = document.getElementById('checkoutForm');
        const formData = new FormData(form);
        formData.append('csrf_token', csrfToken);

        try {
            const proceedBtn = document.querySelector('button[onclick="Payment.submit()"]');
            if (proceedBtn) proceedBtn.disabled = true;

            const data = await Utils.fetchAPI('process_checkout.php', Object.fromEntries(formData));

            if (data.success) {
                Notifications.success('Order placed successfully!');
                setTimeout(() => { window.location.href = data.redirect_url; }, 1500);
            } else {
                Notifications.error(data.error || 'Order failed');
                if (proceedBtn) proceedBtn.disabled = false;
            }
        } catch (error) {
            Notifications.error(error.message);
            const proceedBtn = document.querySelector('button[onclick="Payment.submit()"]');
            if (proceedBtn) proceedBtn.disabled = false;
        }
    }
};

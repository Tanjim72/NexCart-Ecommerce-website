/**
 * Order Confirmation Page JavaScript
 * ============================================
 * Handles order display, tracking timeline, and invoice
 * Used on: public/order_confirmation.php
 * 
 * Dependencies: utilities.js (CONFIG, Notifications, Utils)
 */

'use strict';

// ============================================
// ORDER DISPLAY MODULE
// ============================================

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
// APP INITIALIZATION (ORDER PAGE)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize order display features
    OrderDisplay.init();
});

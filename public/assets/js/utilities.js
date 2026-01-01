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
        return /^[0-9\s\-\+]+$/.test(phone) && phone.replace(/[^0-9]/g, '').length >= 10;
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

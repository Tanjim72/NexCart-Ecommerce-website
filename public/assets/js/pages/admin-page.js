/**
 * Admin Page JavaScript
 * ============================================
 * Handles admin dashboard interactions and order management
 * Used on: public/admin/index.php & public/admin/orders.php
 * 
 * Dependencies: utilities.js (CONFIG, Notifications, Utils)
 */

'use strict';

// ============================================
// ADMIN MODULE
// ============================================

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
// APP INITIALIZATION (ADMIN PAGE)
// ============================================

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin features
    Admin.init();
});

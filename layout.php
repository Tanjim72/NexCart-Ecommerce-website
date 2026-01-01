<?php
/**
 * UNIFIED LAYOUT TEMPLATE
 * Combines header, navigation, and footer
 * Wraps all order flow sections with consistent HTML structure
 * 
 * Usage: 
 * $section = 'checkout'; // or 'payment', 'invoice', 'tracking'
 * $page_title = 'Checkout'; // optional
 * include 'layout.php';
 */

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Require functions
require_once __DIR__ . '/functions.php';

// Default section
if (!isset($section)) {
    $section = 'checkout';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="NextCart - Your trusted e-commerce platform for electronics and gadgets">
    <meta name="keywords" content="e-commerce, electronics, gadgets, Bangladesh, online shopping">
    <meta name="author" content="NextCart">
    <meta name="theme-color" content="#667eea">
    <title><?php echo isset($page_title) ? escape($page_title) . ' | NextCart' : 'NextCart | E-commerce Order & Payment System'; ?></title>
    <!-- Common Styles (all pages) -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/common.css">
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <header class="site-header">
            <div class="header-content">
                <h1 class="site-title">
                    <span class="logo-icon">🛍️</span>
                    NextCart
                </h1>
                <p class="site-tagline">Your Trusted E-commerce Platform</p>
            </div>
        </header>

        <!-- Navigation Tabs -->
        <nav class="tabs" role="tablist" aria-label="Order Process Navigation">
            <button class="tab-btn<?php echo $section === 'checkout' ? ' active' : ''; ?>" data-tab="checkout" role="tab" aria-selected="<?php echo $section === 'checkout' ? 'true' : 'false'; ?>" aria-controls="checkout">
                <span class="tab-icon">📝</span>
                <span class="tab-text">Checkout</span>
            </button>
            <button class="tab-btn<?php echo $section === 'payment' ? ' active' : ''; ?>" data-tab="payment" role="tab" aria-selected="<?php echo $section === 'payment' ? 'true' : 'false'; ?>" aria-controls="payment">
                <span class="tab-icon">💳</span>
                <span class="tab-text">Payment</span>
            </button>
            <button class="tab-btn<?php echo $section === 'invoice' ? ' active' : ''; ?>" data-tab="invoice" role="tab" aria-selected="<?php echo $section === 'invoice' ? 'true' : 'false'; ?>" aria-controls="invoice">
                <span class="tab-icon">📄</span>
                <span class="tab-text">Invoice</span>
            </button>
            <button class="tab-btn<?php echo $section === 'tracking' ? ' active' : ''; ?>" data-tab="tracking" role="tab" aria-selected="<?php echo $section === 'tracking' ? 'true' : 'false'; ?>" aria-controls="tracking">
                <span class="tab-icon">📦</span>
                <span class="tab-text">Tracking</span>
            </button>
        </nav>

        <!-- Main Content Area -->
        <main class="main-content">
            <?php
            // Include order flow template with specified section
            include __DIR__ . '/order-flow-template.php';
            ?>
        </main>

        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-content">
                <p>&copy; 2025 NextCart. All rights reserved.</p>
                <p class="footer-links">
                    <a href="#privacy">Privacy Policy</a> |
                    <a href="#terms">Terms of Service</a> |
                    <a href="#contact">Contact Us</a>
                </p>
            </div>
        </footer>
    </div>

    <!-- Toast Notification Container -->
    <div id="toastContainer" class="toast-container"></div>

    <!-- Shared Utilities (all pages) -->
    <script src="<?php echo APP_URL; ?>/utilities.js" data-api-url="<?php echo APP_URL; ?>/api"></script>
</body>
</html>

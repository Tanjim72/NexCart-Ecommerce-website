<?php
/**
 * Header Template
 */

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/assets/css/common.css">
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

<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Management - NexCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>NexCart</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="product_catalog.php">Catalog</a>
            <a href="search_filter.php">Search</a>
            <a href="stock_management.php" class="active">Stock</a>
            <a href="admin_panel.php">Admin</a>
        </nav>
    </header>

    <main>
        <h2>Stock Management</h2>
        <div id="stock-alerts"></div>
        <input type="text" id="product-search" placeholder="Search product...">
        <div id="stock-update-form" style="display:none;">
            <input type="number" id="stock-quantity" placeholder="Quantity">
            <button onclick="updateStock()">Update</button>
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
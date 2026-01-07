<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog - NexCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>NexCart</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="product_catalog.php" class="active">Catalog</a>
            <a href="search_filter.php">Search</a>
            <a href="stock_management.php">Stock</a>
            <a href="admin_panel.php">Admin</a>
        </nav>
    </header>

    <main>
        <h2>Product Catalog</h2>
        <select id="category-filter">
            <option value="">All Categories</option>
            <option value="electronics">Electronics</option>
            <option value="clothing">Clothing</option>
        </select>
        <div id="product-grid">
            <!-- Products loaded by JavaScript -->
        </div>
    </main>

    <script src="script.js"></script>
</body>
</html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Products - NexCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>NexCart</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="product_catalog.php">Catalog</a>
            <a href="search_filter.php" class="active">Search</a>
            <a href="stock_management.php">Stock</a>
            <a href="admin_panel.php">Admin</a>
        </nav>
    </header>

    <main>
        <h2>Search Products</h2>
        <input type="text" id="search-box" placeholder="Search...">
        <input type="number" id="min-price" placeholder="Min Price">
        <input type="number" id="max-price" placeholder="Max Price">
        <button onclick="searchProducts()">Search</button>
        <div id="search-results"></div>
    </main>

    <script src="script.js"></script>
</body>
</html>
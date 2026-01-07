<?php
session_start();
if (!isset($_SESSION['admin'])) {
    $_SESSION['admin'] = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - NexCart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>NexCart</h1>
        <nav>
            <a href="index.php">Dashboard</a>
            <a href="product_catalog.php">Catalog</a>
            <a href="search_filter.php">Search</a>
            <a href="stock_management.php">Stock</a>
            <a href="admin_panel.php" class="active">Admin</a>
        </nav>
    </header>

    <main>
        <h2>Admin Panel</h2>
        <form id="product-form" onsubmit="return addProduct()">
            <input type="text" id="product-name" placeholder="Product Name" required>
            <input type="number" id="product-price" placeholder="Price" required>
            <input type="number" id="product-stock" placeholder="Stock" required>
            <button type="submit">Add Product</button>
        </form>
    </main>

    <script src="script.js"></script>
</body>
</html>
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NexCart Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>NexCart</h1>
        <nav>
            <a href="index.php" class="active">Dashboard</a>
            <a href="product_catalog.php">Catalog</a>
            <a href="search_filter.php">Search</a>
            <a href="stock_management.php">Stock</a>
            <a href="admin_panel.php">Admin</a>
        </nav>
    </header>

    <main>
        <h2>Dashboard</h2>
        <div class="stats">
            <div class="stat-box">
                <h3>Total Products</h3>
                <p id="total-products">Loading...</p>
            </div>
            <div class="stat-box">
                <h3>Low Stock</h3>
                <p id="low-stock">Loading...</p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2023 NexCart</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>
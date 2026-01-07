<?php
session_start();
$product_id = $_GET['id'] ?? '1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - NexCart</title>
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
            <a href="admin_panel.php">Admin</a>
        </nav>
    </header>

    <main>
        <h2 id="product-name">Product Name</h2>
        <img id="product-image" src="https://via.placeholder.com/300" alt="Product">
        <p id="product-price">Price: $0.00</p>
        <p id="product-stock">Stock: 0</p>
        <div id="product-specs"></div>
    </main>

    <script>
        const productId = "<?php echo $product_id; ?>";
        document.addEventListener('DOMContentLoaded', () => loadProductDetails(productId));
    </script>
    <script src="script.js"></script>
</body>
</html>
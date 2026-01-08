<?php
// Array of products
$products = [
    ["name" => "Wireless Headphone", "price" => 1199, "img" => "wireless headphone.webp", "extra" => false],
    ["name" => "Smart Watch", "price" => 4199, "img" => "Smart watch.jpg", "extra" => false],
    ["name" => "Sports Shoes", "price" => 3199, "img" => "Sport shoes.webp", "extra" => false],
    ["name" => "Bluetooth Speaker", "price" => 2399, "img" => "Bluetooth speaker.jpg", "extra" => true],
    ["name" => "Backpack", "price" => 1999, "img" => "images (6).jpg", "extra" => true],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Featured Products</title>

    <!--  MERGED CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="product-page">

<h2>Featured Products</h2>

<div id="productBox">
    <?php foreach($products as $product): ?>
        <div class="product <?= $product['extra'] ? 'extra' : '' ?>">
            <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <p><?= htmlspecialchars($product['name']) ?> - <?= htmlspecialchars($product['price']) ?> BDT</p>
        </div>
    <?php endforeach; ?>
</div>

<button id="showBtn">Show More</button>
<p id="msg"></p>

<!--  MERGED JS -->
<script src="script.js"></script>

</body>
</html>

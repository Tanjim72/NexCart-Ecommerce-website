<?php
$products = json_decode(file_get_contents('data/products.json'), true);

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';

$results = array_filter($products, function($p) use ($search, $category, $brand) {
    if ($search && stripos($p['name'], $search) === false) return false;
    if ($category && $p['category'] !== $category) return false;
    if ($brand && $p['brand'] !== $brand) return false;
    return true;
});
?>

<!DOCTYPE html>
<html>
<head>
    <title>NexCart - Search Products</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Search Products</h1>

<form method="GET" class="search-form">
    <input type="text" name="search" placeholder="Search product name">
    
    <select name="category">
        <option value="">All Categories</option>
        <option value="Electronics">Electronics</option>
        <option value="Fashion">Fashion</option>
    </select>

    <select name="brand">
        <option value="">All Brands</option>
        <option value="Samsung">Samsung</option>
        <option value="Dell">Dell</option>
        <option value="Nike">Nike</option>
        <option value="Fossil">Fossil</option>
        <option value="Sony">Sony</option>
    </select>

    <button type="submit">Search</button>
</form>

<div class="product-grid">
<?php if(count($results) > 0): ?>
    <?php foreach($results as $p): ?>
        <div class="product-card">
            <img src="<?= $p['image'] ?>" alt="<?= $p['name'] ?>">
            <h3><?= $p['name'] ?></h3>
            <p><?= $p['brand'] ?></p>
            <p>Price: <?= number_format($p['price']) ?> BDT</p>
            <p>Stock: <?= $p['stock'] ?></p>
            <a href="product-details.php?id=<?= $p['id'] ?>">View Details</a>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No products found.</p>
<?php endif; ?>
</div>

<a href="product-catalog.php">← Back to Catalog</a>

</body>
</html>

<?php
session_start();
$productsFile = 'data/products.json';
$products = json_decode(file_get_contents($productsFile), true);

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$brand = $_GET['brand'] ?? '';

$filtered = array_filter($products, function($p) use($search,$category,$brand){
    if($search && stripos($p['name'],$search) === false) return false;
    if($category && $p['category'] !== $category) return false;
    if($brand && $p['brand'] !== $brand) return false;
    return true;
});

if(isset($_GET['order'])){
    $id = $_GET['order'];
    foreach($products as &$p){
        if($p['id'] == $id && $p['stock'] > 0){
            $p['stock']--;
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
            file_put_contents($productsFile, json_encode($products, JSON_PRETTY_PRINT));
            $msg = "Order placed successfully!";
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>NexCart - Catalog</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>NexCart Product Catalog</h1>

<form method="GET">
<input type="text" name="search" placeholder="Search product">
<select name="category">
<option value="">All Categories</option>
<option>Electronics</option>
<option>Fashion</option>
</select>
<select name="brand">
<option value="">All Brands</option>
<option>Samsung</option>
<option>Dell</option>
<option>Nike</option>
<option>Fossil</option>
<option>Sony</option>
</select>
<button>Search</button>
</form>

<?php if(isset($msg)) echo "<p style='color:green'>$msg</p>"; ?>

<div class="product-grid">
<?php foreach($filtered as $p): ?>
<div class="product-card">
<img src="<?= $p['image'] ?>">
<h3><?= $p['name'] ?></h3>
<p><?= $p['brand'] ?></p>
<p>Price: <?= number_format($p['price']) ?> BDT</p>
<p>Stock: <?= $p['stock'] ?></p>

<a href="product-details.php?id=<?= $p['id'] ?>">Details</a>

<?php if($p['stock']>0): ?>
<a href="?order=<?= $p['id'] ?>">Order</a>
<?php else: ?>
<p style="color:red">Out of Stock</p>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>

</body>
</html>

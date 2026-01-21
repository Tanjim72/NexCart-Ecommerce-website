<?php
session_start();
$productsFile = 'data/products.json';
$products = json_decode(file_get_contents($productsFile), true);

$id = $_GET['id'] ?? 0;
foreach($products as $p){
    if($p['id']==$id){ $product=$p; break; }
}
if(!isset($product)) die("Product not found");

if(isset($_POST['order']) && $product['stock']>0){
    foreach($products as &$p){
        if($p['id']==$id){ $p['stock']--; }
    }
    file_put_contents($productsFile,json_encode($products,JSON_PRETTY_PRINT));
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id]??0)+1;
    $msg="Order successful!";
}
?>
<!DOCTYPE html>
<html>
<head>
<title><?= $product['name'] ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<a href="product-catalog.php">← Back</a>

<img src="<?= $product['image'] ?>" width="300">
<h1><?= $product['name'] ?></h1>
<p>Brand: <?= $product['brand'] ?></p>
<p>Category: <?= $product['category'] ?></p>
<p>Price: <?= number_format($product['price']) ?> BDT</p>
<p>Stock: <?= $product['stock'] ?></p>

<?php if(isset($msg)) echo "<p style='color:green'>$msg</p>"; ?>

<?php if($product['stock']>0): ?>
<form method="POST">
<button name="order">Order Now</button>
</form>
<?php else: ?>
<p style="color:red">Out of Stock</p>
<?php endif; ?>

</body>
</html>

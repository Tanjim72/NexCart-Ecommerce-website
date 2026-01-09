<?php
$productsFile = 'data/products.json';
$products = json_decode(file_get_contents($productsFile), true);

if($_SERVER['REQUEST_METHOD']=="POST"){
    $products[]=[
        "id"=>count($products)+1,
        "name"=>$_POST['name'],
        "category"=>$_POST['category'],
        "brand"=>$_POST['brand'],
        "price"=>intval($_POST['price']), 
        "stock"=>intval($_POST['stock']),
        "rating"=>floatval($_POST['rating']),
        "image"=>"images/".$_FILES['image']['name']
    ];
    move_uploaded_file($_FILES['image']['tmp_name'], "images/".$_FILES['image']['name']);
    file_put_contents($productsFile,json_encode($products,JSON_PRETTY_PRINT));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Product</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Add Product</h1>
<form method="POST" enctype="multipart/form-data">
<input name="name" placeholder="Name" required>
<input name="category" placeholder="Category" required>
<input name="brand" placeholder="Brand" required>
<input type="number" name="price" placeholder="Price (BDT)" required>
<input type="number" name="stock" placeholder="Stock" required>
<input type="number" step="0.1" name="rating" placeholder="Rating" required>
<input type="file" name="image" required>
<button>Add</button>
</form>

</body>
</html>

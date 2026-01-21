<?php
$products = json_decode(file_get_contents('data/products.json'), true);
?>
<!DOCTYPE html>
<html>
<head>
<title>Stock Management</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<h1>Stock Management</h1>
<table border="1" cellpadding="10">
<tr><th>Name</th><th>Price (BDT)</th><th>Stock</th></tr>
<?php foreach($products as $p): ?>
<tr>
<td><?= $p['name'] ?></td>
<td><?= number_format($p['price']) ?> BDT</td>
<td><?= $p['stock'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>

<?php
session_start();


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}


if (!isset($_SESSION['wishlist'])) {
    $_SESSION['wishlist'] = [];
}


if (isset($_GET['add'])) {
    $_SESSION['wishlist'][] = $_GET['add'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Wishlist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Your Wishlist</h2>

    <ul>
        <?php if (empty($_SESSION['wishlist'])): ?>
            <li>No items in wishlist</li>
        <?php else: ?>
            <?php foreach ($_SESSION['wishlist'] as $item): ?>
                <li><?= htmlspecialchars($item) ?></li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>

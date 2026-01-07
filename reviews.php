<?php
session_start();


if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}


if (!isset($_SESSION['reviews'])) {
    $_SESSION['reviews'] = [];
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $text = trim($_POST['reviewText'] ?? '');
    $rating = $_POST['rating'] ?? '';

    if ($text !== "" && $rating !== "") {
        $_SESSION['reviews'][] = [
            'user' => $_SESSION['user']['name'],
            'text' => $text,
            'rating' => $rating
        ];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reviews</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h2>Write a Review</h2>

    
    <form method="POST">

       
        <input type="text" name="reviewText" placeholder="Write your review..." required>

        <select name="rating" required>
            <option value="1">⭐</option>
            <option value="2">⭐⭐</option>
            <option value="3">⭐⭐⭐</option>
            <option value="4">⭐⭐⭐⭐</option>
            <option value="5">⭐⭐⭐⭐⭐</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <h3>All Reviews</h3>

    <ul>
        <?php foreach ($_SESSION['reviews'] as $review): ?>
            <li>
                <strong><?= htmlspecialchars($review['user']) ?></strong>
                (<?= $review['rating'] ?>⭐)<br>
                <?= htmlspecialchars($review['text']) ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

</body>
</html>

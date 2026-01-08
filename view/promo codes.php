<?php

$promoMsg = "";
$promoColor = "red";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $code = trim($_POST["promoCode"] ?? "");
    $validCode = "SALE20";

    if ($code === "") {
        $promoMsg = "Please enter a promo code.";
    } elseif (strtoupper($code) === $validCode) {
        $promoMsg = "Promo applied! You get 20% discount!";
        $promoColor = "green";
    } else {
        $promoMsg = "Invalid promo code. Try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Promo Code Validation</title>

    <!--  MERGED CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="promo-page">

<h2>Promo Code Validation</h2>

<form id="promoForm" method="post" action="">
    <label for="promoInput">Enter Promo Code:</label><br>
    <input type="text" name="promoCode" id="promoInput" placeholder="Enter code"><br><br>
    <button type="submit">Apply Code</button>
</form>

<p id="promoMsg" style="color: <?= htmlspecialchars($promoColor) ?>;">
    <?= htmlspecialchars($promoMsg) ?>
</p>

<!--  MERGED JS -->
<script src="script.js"></script>

</body>
</html>

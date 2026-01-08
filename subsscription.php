<?php
$email = "";
$messageText = "";
$messageColor = "red";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["emailInput"] ?? "");

    // Simple server-side validation
    if ($email === "") {
        $messageText = "Email field cannot be empty!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messageText = "Please enter a valid email address.";
    } else {
        $messageText = "Subscribed successfully!";
        $messageColor = "green";

        // Clear email field
        $email = "";

        // Optional: save to database here
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NexCart Subscription</title>

    <!-- MERGED CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="subscription-page">

<h2>NexCart Subscription</h2>

<form id="subscribeForm" method="post" action="">
    <label for="emailInput">Enter your Email:</label><br>
    <input type="text" name="emailInput" id="emailInput" placeholder="your@email.com" value="<?= htmlspecialchars($email) ?>">

    <br><br>

    <button type="submit" id="subscribeBtn">Subscribe</button>

    <p id="message" style="color: <?= htmlspecialchars($messageColor) ?>; font-weight: bold;">
        <?= htmlspecialchars($messageText) ?>
    </p>
</form>

<!--   MERGED JS -->
<script src="script.js"></script>

</body>
</html>

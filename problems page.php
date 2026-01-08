<?php
$name = $email = $message = "";
$feedback = "";
$feedbackColor = "red";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");

    // Server-side validation
    if ($name === "") {
        $feedback = "Please enter your name.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback = "Please enter a valid email address.";
    } elseif (strlen($message) < 10) {
        $feedback = "Message must be at least 10 characters.";
    } else {
        $feedback = "Message sent successfully!";
        $feedbackColor = "green";

        // Clear form values
        $name = $email = $message = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Drop Your Problem</title>

    <!--  MERGED CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body class="contact-page">

<h2>DROP YOUR PROBLEM</h2>

<form id="contactForm" method="post" action="">
    <label for="name">Name:</label><br>
    <input type="text" name="name" id="name" placeholder="Enter your name" value="<?= htmlspecialchars($name) ?>"><br><br>

    <label for="email">Email:</label><br>
    <input type="text" name="email" id="email" placeholder="Enter your email" value="<?= htmlspecialchars($email) ?>"><br><br>

    <label for="message">Message:</label><br>
    <textarea name="message" id="message" placeholder="Write your message"><?= htmlspecialchars($message) ?></textarea><br><br>

    <button type="submit" id="contactBtn">Submit</button>
</form>

<?php if ($feedback !== ""): ?>
    <p id="contactMessage" style="color: <?= $feedbackColor ?>; font-weight:bold;">
        <?= $feedback ?>
    </p>
<?php endif; ?>

<!--  MERGED JS -->
<script src="script.js"></script>
</body>
</html>

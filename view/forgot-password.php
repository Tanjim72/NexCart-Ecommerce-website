<?php
session_start();

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');

    if ($email === "") {
        $error = "Email is required!";
    }
    else {
       
        $message = "If this email exists, a reset link has been sent.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

<div class="container">
    <div class="right-panel">
        <div class="form-box">
            <h2>Reset Password</h2>

            
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($message): ?>
                <p class="success"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

           
            <form method="POST">

                <div class="input-group">
                    <label>Email</label>
                 
                    <input type="email" name="email" placeholder="Enter your email" required>
                </div>

                <button class="btn" type="submit">Send Reset Link</button>
            </form>

            <p class="switch">
                <a href="login.php">Back to Login</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>

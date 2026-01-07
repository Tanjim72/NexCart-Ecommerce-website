<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === "" || $password === "") {
        $error = "All fields are required!";
    }
    elseif ($email === "user@example.com" && $password === "123456") {

        $_SESSION['user'] = [
            'name' => 'Demo User',
            'email' => $email
        ];

        header("Location: profile.php");
        exit();
    }
    else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login | NexCart</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

    <div class="left-panel">
        <img src="shopping.jpg" alt="Shopping">
        <div class="overlay">
            <h1 class="brand">NexCart-Ecommerce-website</h1>
            <p>Welcome back! Login to continue shopping.</p>
        </div>
    </div>

    <div class="right-panel">
       
       <form class="form-box" id="loginForm" method="POST">

            <h2>Welcome Back</h2>

          
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label>Email</label>
               
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group password-group">
                <label>Password</label>
               
                <input type="password" name="password" id="loginPass" placeholder="Enter password" required>
                <span class="toggle" onclick="toggleLoginPass()">👁️</span>
            </div>

            <div class="options">
                <label>
                    <input type="checkbox"> Remember me
                </label>
                <a href="#">Forgot password?</a>
            </div>

            <button type="submit" class="btn">Login</button>

            <p class="switch">
                Don’t have an account?
                <a href="register.php">Register Now</a>
            </p>
        </form>
    </div>

</div>

<script src="script.js"></script>
</body>
</html>

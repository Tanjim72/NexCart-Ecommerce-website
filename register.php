<?php
session_start();

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if ($name === "" || $email === "" || $pass === "") {
        $error = "All fields are required!";
    }
    elseif (strlen($pass) < 6) {
        $error = "Password must be at least 6 characters!";
    }
    else {
        
        $_SESSION['registered_user'] = [
            'name' => $name,
            'email' => $email
        ];

        $success = "Registration successful! You can login now.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   
    <title>Register | E-Commerce</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
<div class="container">

    <div class="left-panel">
        <img src="shopping.jpg" alt="Shopping">
        <div class="overlay">
            <h1 class="brand">NexCart-Ecommerce-website</h1>
            <p>Create your account and start shopping today!</p>
        </div>
    </div>

    <div class="right-panel">
       
       <form class="form-box" id="registerForm" method="POST">

            <h2>Create Account</h2>

           
            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label>Full Name</label>
                
                <input type="text" name="name" placeholder="Enter your name" required>
            </div>

            <div class="input-group">
                <label>Email</label>
                
                <input type="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group password-group">
                <label>Password</label>
            
                <input type="password" name="password" id="regPass" placeholder="Create a password" required>
                <span class="toggle" onclick="togglePass()">👁️</span>
            </div>

            <button type="submit" class="btn">Register</button>

            <p class="switch">Already have an account?
                <a href="login.php">Login</a>
            </p>
        </form>
    </div>

</div>

<script src="script.js"></script>
</body>
</html>

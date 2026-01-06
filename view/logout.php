<?php
session_start();

$_SESSION = [];
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logging Out</title>
    <link rel="stylesheet" href="style.css">
    
    <meta http-equiv="refresh" content="2;url=login.php">
</head>
<body>

<div class="container">
    <h2>You have been logged out</h2>
    <p>Redirecting to login page...</p>

    <p class="switch">
        <a href="login.php">Click here if not redirected</a>
    </p>
</div>

</body>
</html>

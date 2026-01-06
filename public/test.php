<?php
echo "PHP is working!<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current Directory: " . __DIR__ . "<br>";
echo "<br>Checking files exist:<br>";
echo "functions.php exists: " . (file_exists(__DIR__ . '/../includes/functions.php') ? 'YES' : 'NO') . "<br>";
echo "header.php exists: " . (file_exists(__DIR__ . '/../includes/header.php') ? 'YES' : 'NO') . "<br>";
echo "footer.php exists: " . (file_exists(__DIR__ . '/../includes/footer.php') ? 'YES' : 'NO') . "<br>";
echo "config.php exists: " . (file_exists(__DIR__ . '/../config/config.php') ? 'YES' : 'NO') . "<br>";

echo "<br>Testing require functions.php:<br>";
try {
    require_once __DIR__ . '/../includes/functions.php';
    echo "functions.php loaded successfully!<br>";
} catch (Throwable $e) {
    echo "Error loading functions.php:<br>";
    echo $e->getMessage();
}
?>

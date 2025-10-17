<?php
echo "<h1>PHP Test</h1>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>Current Time: " . date('Y-m-d H:i:s') . "</p>";

echo "<h2>Environment Variables:</h2>";
echo "<pre>";
echo "DB_HOST: " . (getenv('DB_HOST') ?: 'NOT SET') . "\n";
echo "DB_NAME: " . (getenv('DB_NAME') ?: 'NOT SET') . "\n";
echo "DB_USER: " . (getenv('DB_USER') ?: 'NOT SET') . "\n";
echo "DB_PASS: " . (getenv('DB_PASS') ?: 'NOT SET') . "\n";
echo "</pre>";

echo "<h2>File System:</h2>";
echo "<p>Current Directory: " . getcwd() . "</p>";
echo "<p>Index.php exists: " . (file_exists('index.php') ? 'YES' : 'NO') . "</p>";
echo "<p>Setup.php exists: " . (file_exists('setup.php') ? 'YES' : 'NO') . "</p>";

echo "<h2>Database Test:</h2>";
try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database error: " . $e->getMessage() . "</p>";
}
?>
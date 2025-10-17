<?php
define('_ISVALID', true);

try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Update main_url to localhost:8083
    $stmt = $pdo->prepare("UPDATE db_settings SET cfg_value = ? WHERE cfg_name = 'main_url'");
    $stmt->execute(['http://localhost:8083']);
    
    echo "<h1>✅ Main URL Fixed!</h1>";
    echo "<p>Updated main_url to: http://localhost:8083</p>";
    echo "<p>Now try accessing:</p>";
    echo "<ul>";
    echo "<li><a href='/'>Main Site</a></li>";
    echo "<li><a href='/admin'>Admin Panel</a></li>";
    echo "<li><a href='/working_index.php'>Working Index</a></li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h1>❌ Error: " . $e->getMessage() . "</h1>";
}
?>
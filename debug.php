<?php
echo "<h1>EasyStream Debug</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

echo "<h2>1. File System Check</h2>";
$files_to_check = [
    'index.php',
    'setup.php', 
    'parser.php',
    'f_core/config.core.php',
    'f_core/config.database.php',
    'f_modules/m_backend/signin.php',
    'f_modules/m_backend/parser.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        echo "<p class='ok'>✓ {$file} exists</p>";
    } else {
        echo "<p class='error'>✗ {$file} MISSING</p>";
    }
}

echo "<h2>2. Directory Permissions</h2>";
$dirs_to_check = ['f_core', 'f_modules', 'f_modules/m_backend'];
foreach ($dirs_to_check as $dir) {
    if (is_dir($dir)) {
        $readable = is_readable($dir) ? 'readable' : 'NOT readable';
        echo "<p class='ok'>✓ {$dir} exists and is {$readable}</p>";
    } else {
        echo "<p class='error'>✗ {$dir} directory missing</p>";
    }
}

echo "<h2>3. PHP Include Test</h2>";
try {
    if (file_exists('f_core/config.core.php')) {
        echo "<p class='info'>Attempting to include config.core.php...</p>";
        define('_ISVALID', true);
        include_once 'f_core/config.core.php';
        echo "<p class='ok'>✓ config.core.php included successfully</p>";
    } else {
        echo "<p class='error'>✗ config.core.php not found</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Error including config.core.php: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p class='error'>✗ Fatal error including config.core.php: " . $e->getMessage() . "</p>";
}

echo "<h2>4. Database Connection Test</h2>";
try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    echo "<p class='ok'>✓ Direct database connection works</p>";
    
    // Test if settings table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'db_settings'");
    if ($stmt->rowCount() > 0) {
        echo "<p class='ok'>✓ db_settings table exists</p>";
        
        // Check setup_complete setting
        $stmt = $pdo->prepare("SELECT cfg_value FROM db_settings WHERE cfg_name = 'setup_complete'");
        $stmt->execute();
        $setup_complete = $stmt->fetchColumn();
        echo "<p class='info'>setup_complete = " . ($setup_complete ?: 'NOT SET') . "</p>";
    } else {
        echo "<p class='error'>✗ db_settings table missing</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>✗ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>5. URL Routing Test</h2>";
echo "<p class='info'>Current REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'NOT SET') . "</p>";
echo "<p class='info'>Current SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'NOT SET') . "</p>";
echo "<p class='info'>Current PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'NOT SET') . "</p>";

echo "<h2>6. Direct File Access Test</h2>";
echo "<p><a href='/test.php'>test.php</a> - Should work</p>";
echo "<p><a href='/debug.php'>debug.php</a> - This page</p>";
echo "<p><a href='/index.php'>index.php</a> - Main page</p>";
echo "<p><a href='/setup.php'>setup.php</a> - Setup page</p>";
echo "<p><a href='/parser.php'>parser.php</a> - URL parser</p>";

echo "<h2>7. Environment Variables</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($env_vars as $var) {
    $value = getenv($var);
    if ($value) {
        echo "<p class='ok'>✓ {$var} = {$value}</p>";
    } else {
        echo "<p class='error'>✗ {$var} not set</p>";
    }
}

echo "<h2>8. Next Steps</h2>";
echo "<p>Based on the results above, we can identify the exact issue:</p>";
echo "<ul>";
echo "<li>If files are missing → File system issue</li>";
echo "<li>If includes fail → PHP configuration issue</li>";
echo "<li>If database fails → Database connection issue</li>";
echo "<li>If routing is wrong → Caddy configuration issue</li>";
echo "</ul>";
?>
<?php
// Comprehensive EasyStream Diagnostic
echo "<h1>🔍 EasyStream Full Diagnostic</h1>";

// Test 1: Environment Variables
echo "<h2>1. Environment Variables</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($env_vars as $var) {
    $value = getenv($var);
    echo "$var: " . ($value ? "✅ Set (length: " . strlen($value) . ")" : "❌ Not set") . "<br>";
}

// Test 2: Core Files
echo "<h2>2. Core Files</h2>";
$core_files = [
    'f_core/config.core.php',
    'f_core/config.database.php',
    'f_core/config.backend.php', 
    'f_core/config.href.php',
    'index.php',
    'parser.php',
    'error.php',
    '.htaccess'
];

foreach ($core_files as $file) {
    echo "$file: " . (file_exists($file) ? '✅ EXISTS' : '❌ MISSING') . "<br>";
}

// Test 3: Module Files
echo "<h2>3. Module Files</h2>";
$module_files = [
    'f_modules/m_frontend/m_file/browse.php',
    'f_modules/m_frontend/m_file/view.php',
    'f_modules/m_backend/parser.php',
    'f_modules/m_backend/signin.php',
    'f_modules/m_backend/dashboard.php'
];

foreach ($module_files as $file) {
    echo "$file: " . (file_exists($file) ? '✅ EXISTS' : '❌ MISSING') . "<br>";
}

// Test 4: Database Connection
echo "<h2>4. Database Connection Test</h2>";
try {
    define('_ISVALID', true);
    
    $cfg_dbtype = 'mysqli';
    $cfg_dbhost = getenv('DB_HOST') ?: 'localhost';
    $cfg_dbname = getenv('DB_NAME') ?: '';
    $cfg_dbuser = getenv('DB_USER') ?: '';
    $cfg_dbpass = getenv('DB_PASS') ?: '';
    
    echo "DB Type: $cfg_dbtype<br>";
    echo "DB Host: " . ($cfg_dbhost ?: 'Not set') . "<br>";
    echo "DB Name: " . ($cfg_dbname ?: 'Not set') . "<br>";
    echo "DB User: " . ($cfg_dbuser ?: 'Not set') . "<br>";
    echo "DB Pass: " . ($cfg_dbpass ? 'Set (length: ' . strlen($cfg_dbpass) . ')' : 'Not set') . "<br>";
    
    if ($cfg_dbhost && $cfg_dbname && $cfg_dbuser) {
        $connection = @new mysqli($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname);
        if ($connection->connect_error) {
            echo "❌ Database connection failed: " . $connection->connect_error . "<br>";
        } else {
            echo "✅ Database connection successful<br>";
            $connection->close();
        }
    } else {
        echo "❌ Database credentials not properly configured<br>";
    }
} catch (Exception $e) {
    echo "❌ Database test error: " . $e->getMessage() . "<br>";
}

// Test 5: Configuration Loading
echo "<h2>5. Configuration Loading</h2>";
try {
    if (file_exists('f_core/config.backend.php') && file_exists('f_core/config.href.php')) {
        define('_INCLUDE', true);
        require 'f_core/config.backend.php';
        require 'f_core/config.href.php';
        
        echo "✅ Configuration files loaded successfully<br>";
        echo "Backend access URL: <strong>$backend_access_url</strong><br>";
        echo "Index href: <strong>'" . $href["index"] . "'</strong><br>";
        echo "Error href: <strong>'" . $href["error"] . "'</strong><br>";
    } else {
        echo "❌ Configuration files missing<br>";
    }
} catch (Exception $e) {
    echo "❌ Configuration loading error: " . $e->getMessage() . "<br>";
}

// Test 6: Parser Logic
echo "<h2>6. Parser Logic Test</h2>";
if (defined('_INCLUDE')) {
    function keyCheck($k, $a)
    {
        foreach ($k as $v) {
            if ($v == '@') {
                $v = 'channel';
            }
            if (in_array($v, $a)) {
                return $v;
            }
        }
        if (empty($k) || (count($k) == 1 && $k[0] === '')) {
            return '';
        }
        return null;
    }
    
    $sections = array(
        $backend_access_url     => 'f_modules/m_backend/parser',
        $href["index"]          => 'index',
        $href["error"]          => 'error',
        $href["browse"]         => 'f_modules/m_frontend/m_file/browse',
        $href["videos"]         => 'f_modules/m_frontend/m_file/browse',
        $href["watch"]          => 'f_modules/m_frontend/m_file/view',
    );
    
    $test_urls = ['/', '/admin', '/videos', '/browse', '/watch/test'];
    
    foreach ($test_urls as $test_uri) {
        $section_array = explode('/', trim($test_uri, '/'));
        
        if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
            $section = $backend_access_url;
        } else {
            $section = keyCheck($section_array, $href);
        }
        
        $include = isset($sections[$section]) ? $sections[$section] : 'error';
        $file_exists = file_exists($include . '.php');
        
        echo "URL: <strong>$test_uri</strong> → Section: <strong>'$section'</strong> → File: <strong>$include.php</strong> " . 
             ($file_exists ? '✅' : '❌') . "<br>";
    }
}

// Test 7: Server Info
echo "<h2>7. Server Information</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "<br>";
echo "Script Name: " . ($_SERVER['SCRIPT_NAME'] ?? 'Unknown') . "<br>";

// Test 8: Apache Modules
echo "<h2>8. Apache Modules</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? '✅ Loaded' : '❌ Not loaded') . "<br>";
    echo "mod_headers: " . (in_array('mod_headers', $modules) ? '✅ Loaded' : '❌ Not loaded') . "<br>";
} else {
    echo "Cannot check Apache modules (not running under Apache or function not available)<br>";
}

echo "<h2>9. Quick Fix Suggestions</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba;'>";
echo "<h3>If you're getting 404 errors:</h3>";
echo "<ol>";
echo "<li>Check if environment variables are set (DB_HOST, DB_NAME, DB_USER, DB_PASS)</li>";
echo "<li>Verify .htaccess file exists and mod_rewrite is enabled</li>";
echo "<li>Ensure all module files exist</li>";
echo "<li>Try accessing <a href='/working_index.php'>working_index.php</a> directly</li>";
echo "<li>Check <a href='/admin_direct.php'>admin_direct.php</a> for admin access</li>";
echo "</ol>";
echo "</div>";
?>
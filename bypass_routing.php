<?php
// Bypass routing completely - direct access to core functionality
echo "<h1>🚀 EasyStream Bypass Access</h1>";

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 Direct Access Links</h2>";
echo "<p>These links bypass the routing system entirely:</p>";
echo "<ul style='list-style: none; padding: 0;'>";
echo "<li style='margin: 10px 0;'><a href='/working_index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🏠 Working Home Page</a></li>";
echo "<li style='margin: 10px 0;'><a href='/admin_direct.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>🔧 Direct Admin Access</a></li>";
echo "<li style='margin: 10px 0;'><a href='/f_modules/m_backend/signin.php' style='background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>👤 Backend Login</a></li>";
echo "<li style='margin: 10px 0;'><a href='/f_modules/m_frontend/m_file/browse.php' style='background: #fd7e14; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📁 Browse Files</a></li>";
echo "</ul>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🔍 Quick Diagnostics</h2>";

// Environment check
$env_ok = true;
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($env_vars as $var) {
    if (!getenv($var)) {
        $env_ok = false;
        break;
    }
}

if ($env_ok) {
    echo "<p>✅ Environment variables are set</p>";
} else {
    echo "<p>❌ Environment variables missing - Docker not running?</p>";
    echo "<p><strong>Run:</strong> <code>docker-compose up -d</code></p>";
}

// Database check
try {
    $cfg_dbhost = getenv('DB_HOST') ?: 'localhost';
    $cfg_dbname = getenv('DB_NAME') ?: '';
    $cfg_dbuser = getenv('DB_USER') ?: '';
    $cfg_dbpass = getenv('DB_PASS') ?: '';
    
    if ($cfg_dbhost && $cfg_dbname && $cfg_dbuser) {
        $connection = @new mysqli($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname);
        if ($connection->connect_error) {
            echo "<p>❌ Database connection failed</p>";
        } else {
            echo "<p>✅ Database connection working</p>";
            $connection->close();
        }
    }
} catch (Exception $e) {
    echo "<p>❌ Database error: " . $e->getMessage() . "</p>";
}

// File check
$files = ['index.php', 'parser.php', '.htaccess'];
$all_files_exist = true;
foreach ($files as $file) {
    if (!file_exists($file)) {
        $all_files_exist = false;
        echo "<p>❌ Missing: $file</p>";
    }
}
if ($all_files_exist) {
    echo "<p>✅ Core files exist</p>";
}

echo "</div>";

echo "<div style='background: #f8d7da; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🚨 If You're Still Getting 404s</h2>";
echo "<p>The most common cause is that <strong>Docker containers are not running</strong>.</p>";
echo "<h3>Quick Fix:</h3>";
echo "<ol>";
echo "<li>Open terminal/command prompt</li>";
echo "<li>Navigate to your EasyStream directory</li>";
echo "<li>Run: <code>docker-compose up -d</code></li>";
echo "<li>Wait 2-3 minutes for services to start</li>";
echo "<li>Try accessing the site again</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #d4edda; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>✅ Alternative Access Methods</h2>";
echo "<p>If the main routing isn't working, you can still use EasyStream:</p>";
echo "<ul>";
echo "<li><strong>Admin Panel:</strong> Use <a href='/admin_direct.php'>admin_direct.php</a></li>";
echo "<li><strong>File Management:</strong> Direct access to modules</li>";
echo "<li><strong>User Interface:</strong> Use <a href='/working_index.php'>working_index.php</a></li>";
echo "</ul>";
echo "</div>";

// Create a simple form to test core functionality
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🧪 Test Core System</h2>";
echo "<form method='POST'>";
echo "<button type='submit' name='test_core' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>Test Core System</button>";
echo "</form>";

if (isset($_POST['test_core'])) {
    echo "<h3>Core System Test Results:</h3>";
    try {
        define('_ISVALID', true);
        
        if (file_exists('f_core/config.core.php')) {
            ob_start();
            include_once 'f_core/config.core.php';
            $output = ob_get_clean();
            
            if (empty($output)) {
                echo "<p style='color: green;'>✅ Core system loads successfully!</p>";
                echo "<p>The routing issue is likely with URL rewriting or environment variables.</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Core system loads with warnings:</p>";
                echo "<pre style='background: white; padding: 10px;'>" . htmlspecialchars($output) . "</pre>";
            }
        } else {
            echo "<p style='color: red;'>❌ Core configuration file missing</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Core system error: " . $e->getMessage() . "</p>";
    }
}

echo "</div>";
?>
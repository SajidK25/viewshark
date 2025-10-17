<?php
// Final 404 Debug - Step by step diagnosis
echo "<h1>🔍 Final 404 Debug - Step by Step</h1>";

echo "<h2>Step 1: Basic PHP Test</h2>";
echo "✅ PHP is working (you can see this page)<br>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Current time: " . date('Y-m-d H:i:s') . "<br>";

echo "<h2>Step 2: File System Check</h2>";
$critical_files = [
    'index.php' => 'Main entry point',
    'parser.php' => 'URL router',
    'error.php' => 'Error handler',
    '.htaccess' => 'URL rewriting',
    'f_core/config.core.php' => 'Core config',
    'f_core/config.database.php' => 'Database config',
    'f_core/config.backend.php' => 'Backend config',
    'f_core/config.href.php' => 'URL config'
];

$missing_files = [];
foreach ($critical_files as $file => $desc) {
    if (file_exists($file)) {
        echo "✅ $file ($desc)<br>";
    } else {
        echo "❌ $file ($desc) - MISSING<br>";
        $missing_files[] = $file;
    }
}

echo "<h2>Step 3: Environment Variables</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'MAIN_URL'];
$missing_env = [];

foreach ($env_vars as $var) {
    $value = getenv($var);
    if ($value) {
        echo "✅ $var: " . ($var === 'DB_PASS' ? '[HIDDEN]' : $value) . "<br>";
    } else {
        echo "❌ $var: NOT SET<br>";
        $missing_env[] = $var;
    }
}

echo "<h2>Step 4: Database Connection Test</h2>";
$db_working = false;
try {
    $cfg_dbhost = getenv('DB_HOST') ?: 'localhost';
    $cfg_dbname = getenv('DB_NAME') ?: '';
    $cfg_dbuser = getenv('DB_USER') ?: '';
    $cfg_dbpass = getenv('DB_PASS') ?: '';
    
    if ($cfg_dbhost && $cfg_dbname && $cfg_dbuser) {
        $connection = @new mysqli($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname);
        if ($connection->connect_error) {
            echo "❌ Database connection failed: " . $connection->connect_error . "<br>";
        } else {
            echo "✅ Database connection successful<br>";
            $db_working = true;
            $connection->close();
        }
    } else {
        echo "❌ Database credentials incomplete<br>";
    }
} catch (Exception $e) {
    echo "❌ Database test error: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 5: Core System Test</h2>";
$core_working = false;
try {
    if (file_exists('f_core/config.core.php')) {
        define('_ISVALID', true);
        
        // Capture any output/errors
        ob_start();
        $error_handler = set_error_handler(function($severity, $message, $file, $line) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        });
        
        include_once 'f_core/config.core.php';
        
        restore_error_handler();
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "✅ Core system loads successfully<br>";
            $core_working = true;
        } else {
            echo "⚠️ Core system loads with warnings:<br>";
            echo "<pre style='background: #fff3cd; padding: 10px; border-radius: 5px;'>" . htmlspecialchars($output) . "</pre>";
        }
    } else {
        echo "❌ Core configuration file missing<br>";
    }
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Core system error: " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "<br>";
}

echo "<h2>Step 6: URL Routing Test</h2>";
if (defined('_ISVALID')) {
    try {
        require_once 'f_core/config.backend.php';
        require_once 'f_core/config.href.php';
        
        echo "✅ Configuration files loaded<br>";
        if (isset($backend_access_url)) {
            echo "Backend URL: <strong>$backend_access_url</strong><br>";
        } else {
            echo "❌ Backend access URL not defined<br>";
        }
        if (isset($href["index"])) {
            echo "Index href: <strong>'" . $href["index"] . "'</strong><br>";
        } else {
            echo "❌ Index href not defined<br>";
        }
        
        // Test the parser logic
        function keyCheck($k, $a) {
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
        
        if (isset($backend_access_url) && isset($href)) {
            $test_urls = ['/', '/admin', '/videos'];
            foreach ($test_urls as $test_url) {
                $section_array = explode('/', trim($test_url, '/'));
                if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
                    $section = $backend_access_url;
                } else {
                    $section = keyCheck($section_array, $href);
                }
                echo "URL: <strong>$test_url</strong> → Section: <strong>'$section'</strong><br>";
            }
        } else {
            echo "❌ Cannot test routing - configuration variables not loaded properly<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Routing test error: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>Step 7: Docker Status Check</h2>";
if (function_exists('exec')) {
    $docker_output = [];
    $docker_return = 0;
    @exec('docker-compose ps 2>&1', $docker_output, $docker_return);
    
    if ($docker_return === 0 && !empty($docker_output)) {
        echo "✅ Docker Compose is available<br>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
        echo htmlspecialchars(implode("\n", $docker_output));
        echo "</pre>";
    } else {
        echo "⚠️ Docker Compose not available or not running<br>";
    }
} else {
    echo "⚠️ Cannot check Docker status (exec function disabled)<br>";
}

echo "<h2>🎯 DIAGNOSIS RESULTS</h2>";

if (!empty($missing_files)) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ CRITICAL: Missing Files</h3>";
    echo "<p>These essential files are missing:</p>";
    echo "<ul>";
    foreach ($missing_files as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul>";
    echo "<p><strong>Solution:</strong> Restore these files from your EasyStream installation.</p>";
    echo "</div>";
}

if (!empty($missing_env)) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚠️ LIKELY CAUSE: Missing Environment Variables</h3>";
    echo "<p>These environment variables are not set:</p>";
    echo "<ul>";
    foreach ($missing_env as $var) {
        echo "<li>$var</li>";
    }
    echo "</ul>";
    echo "<p><strong>Solution:</strong> Start Docker containers: <code>docker-compose up -d</code></p>";
    echo "</div>";
}

if (!$db_working) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ DATABASE ISSUE</h3>";
    echo "<p>Database connection is not working. This is likely the main cause of 404 errors.</p>";
    echo "<p><strong>Solutions:</strong></p>";
    echo "<ol>";
    echo "<li>Start Docker: <code>docker-compose up -d</code></li>";
    echo "<li>Wait 2-3 minutes for database initialization</li>";
    echo "<li>Check status: <code>docker-compose ps</code></li>";
    echo "<li>Check logs: <code>docker-compose logs db</code></li>";
    echo "</ol>";
    echo "</div>";
}

if (!$core_working) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ CORE SYSTEM ISSUE</h3>";
    echo "<p>The core system is not loading properly.</p>";
    echo "<p><strong>This will cause 404 errors on all pages.</strong></p>";
    echo "</div>";
}

// If everything looks good but still 404
if (empty($missing_files) && empty($missing_env) && $db_working && $core_working) {
    echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>🤔 SYSTEM LOOKS GOOD</h3>";
    echo "<p>All checks passed, but you're still getting 404s. Try these:</p>";
    echo "<ol>";
    echo "<li>Clear browser cache and try again</li>";
    echo "<li>Try accessing <a href='/working_index.php'>/working_index.php</a></li>";
    echo "<li>Try accessing <a href='/admin_direct.php'>/admin_direct.php</a></li>";
    echo "<li>Check if mod_rewrite is enabled in Apache</li>";
    echo "<li>Restart Docker containers: <code>docker-compose restart</code></li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>🚀 IMMEDIATE ACTIONS</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
echo "<h3>Try These Links Right Now:</h3>";
echo "<ul>";
echo "<li><a href='/working_index.php' target='_blank'>Working Index Page</a> (bypasses routing)</li>";
echo "<li><a href='/admin_direct.php' target='_blank'>Direct Admin Access</a> (bypasses routing)</li>";
echo "<li><a href='/test_core.php' target='_blank'>Core System Test</a></li>";
echo "<li><a href='/simple_test.php' target='_blank'>Simple Test</a></li>";
echo "</ul>";
echo "</div>";

echo "<h2>📋 NEXT STEPS</h2>";
echo "<ol>";
echo "<li><strong>If environment variables are missing:</strong> Run <code>docker-compose up -d</code></li>";
echo "<li><strong>If database is not working:</strong> Wait 2-3 minutes after starting Docker</li>";
echo "<li><strong>If files are missing:</strong> Re-upload the missing files</li>";
echo "<li><strong>If everything looks good:</strong> Try the direct access links above</li>";
echo "<li><strong>Still not working:</strong> Check Docker logs with <code>docker-compose logs</code></li>";
echo "</ol>";
?>
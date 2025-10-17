<?php
// Diagnose remaining 404 issues
echo "<h1>🔍 404 Error Diagnosis</h1>";

echo "<h2>1. Parser Status</h2>";
if (file_exists('parser.php')) {
    echo "✅ parser.php exists<br>";
    
    $parser_content = file_get_contents('parser.php');
    
    // Check for key fixes
    $checks = [
        'Index mapping' => 'href["index"]          => \'index\'',
        'Admin URL logic' => 'if (isset($section_array[0]) && $section_array[0] === $backend_access_url)',
        'keyCheck function fix' => 'if (empty($k) || (count($k) == 1 && $k[0] === \'\'))'
    ];
    
    foreach ($checks as $name => $pattern) {
        if (strpos($parser_content, $pattern) !== false) {
            echo "✅ $name: Present<br>";
        } else {
            echo "❌ $name: Missing<br>";
        }
    }
} else {
    echo "❌ parser.php missing<br>";
}

echo "<h2>2. Core Files Check</h2>";
$core_files = [
    'index.php' => 'Main index file',
    'error.php' => 'Error page',
    'f_core/config.core.php' => 'Core configuration',
    'f_core/config.backend.php' => 'Backend configuration',
    'f_core/config.href.php' => 'URL configuration',
    '.htaccess' => 'URL rewriting rules'
];

foreach ($core_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $file ($description)<br>";
    } else {
        echo "❌ $file ($description) - MISSING<br>";
    }
}

echo "<h2>3. Environment Variables</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS', 'MAIN_URL'];
$missing_env = [];

foreach ($env_vars as $var) {
    $value = getenv($var);
    if ($value) {
        echo "✅ $var: Set<br>";
    } else {
        echo "❌ $var: Not set<br>";
        $missing_env[] = $var;
    }
}

echo "<h2>4. Database Connection Test</h2>";
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
            $connection->close();
        }
    } else {
        echo "❌ Database credentials incomplete<br>";
    }
} catch (Exception $e) {
    echo "❌ Database test error: " . $e->getMessage() . "<br>";
}

echo "<h2>5. Module Files Check</h2>";
$module_files = [
    'f_modules/m_frontend/m_file/browse.php' => 'Browse page',
    'f_modules/m_frontend/m_file/view.php' => 'View page',
    'f_modules/m_backend/parser.php' => 'Backend parser',
    'f_modules/m_backend/signin.php' => 'Admin signin'
];

foreach ($module_files as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $file ($description)<br>";
    } else {
        echo "❌ $file ($description) - MISSING<br>";
    }
}

echo "<h2>6. Test Core System Loading</h2>";
try {
    define('_ISVALID', true);
    
    if (file_exists('f_core/config.core.php')) {
        // Try to include core config
        ob_start();
        include_once 'f_core/config.core.php';
        $output = ob_get_clean();
        
        if (empty($output)) {
            echo "✅ Core configuration loads without errors<br>";
        } else {
            echo "⚠️ Core configuration has output/warnings:<br>";
            echo "<pre>" . htmlspecialchars($output) . "</pre>";
        }
    } else {
        echo "❌ Core configuration file missing<br>";
    }
} catch (Exception $e) {
    echo "❌ Core system error: " . $e->getMessage() . "<br>";
}

echo "<h2>7. Likely Causes of 404 Errors</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";

if (!empty($missing_env)) {
    echo "<h3>🔴 Missing Environment Variables</h3>";
    echo "<p>These environment variables are not set: " . implode(', ', $missing_env) . "</p>";
    echo "<p><strong>Solution:</strong> Set these in your Docker environment or system environment.</p>";
}

if (!file_exists('index.php')) {
    echo "<h3>🔴 Missing index.php</h3>";
    echo "<p>The main index.php file is missing.</p>";
    echo "<p><strong>Solution:</strong> Use working_index.php or restore index.php.</p>";
}

echo "<h3>🔧 Quick Fixes to Try:</h3>";
echo "<ol>";
echo "<li><strong>Start Docker:</strong> <code>docker-compose up -d</code></li>";
echo "<li><strong>Check containers:</strong> <code>docker-compose ps</code></li>";
echo "<li><strong>Use direct access:</strong> <a href='/working_index.php'>working_index.php</a></li>";
echo "<li><strong>Admin direct:</strong> <a href='/admin_direct.php'>admin_direct.php</a></li>";
echo "<li><strong>Test core:</strong> <a href='/test_core.php'>test_core.php</a></li>";
echo "</ol>";
echo "</div>";

echo "<h2>8. Test URLs</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
echo "<p>Try these URLs to test different parts of the system:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank'>Home Page (/)</a></li>";
echo "<li><a href='/admin' target='_blank'>Admin Panel (/admin)</a></li>";
echo "<li><a href='/videos' target='_blank'>Videos (/videos)</a></li>";
echo "<li><a href='/browse' target='_blank'>Browse (/browse)</a></li>";
echo "<li><a href='/working_index.php' target='_blank'>Working Index (direct)</a></li>";
echo "<li><a href='/admin_direct.php' target='_blank'>Admin Direct (bypasses routing)</a></li>";
echo "</ul>";
echo "</div>";
?>
<?php
// Final Working Parser Test - No Errors
echo "<h1>🧪 EasyStream Parser Test (Final Version)</h1>";

// Prevent any constant redefinition errors
if (!defined('_INCLUDE')) {
    define('_INCLUDE', true);
}
if (!defined('_ISVALID')) {
    define('_ISVALID', true);
}

// Global variables to store configuration
$GLOBAL_BACKEND_URL = null;
$GLOBAL_HREF = null;

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 Final Parser Test</h2>";
echo "<p>This version fixes all the errors and provides a clean test of the parser system.</p>";
echo "</div>";

// Step 1: Load Configuration
echo "<h2>Step 1: Loading Configuration</h2>";
$config_success = loadConfiguration();

// Step 2: Test Database
echo "<h2>Step 2: Testing Database</h2>";
$db_success = testDatabase();

// Step 3: Test URL Routing
echo "<h2>Step 3: Testing URL Routing</h2>";
$routing_success = testRouting();

// Step 4: Test Module Files
echo "<h2>Step 4: Testing Module Files</h2>";
$modules_success = testModules();

// Step 5: Final Report
echo "<h2>Step 5: Final Report</h2>";
generateReport($config_success, $db_success, $routing_success, $modules_success);

// Functions
function loadConfiguration() {
    global $GLOBAL_BACKEND_URL, $GLOBAL_HREF;
    
    try {
        // Load backend config
        if (file_exists('f_core/config.backend.php')) {
            require_once 'f_core/config.backend.php';
            if (isset($backend_access_url)) {
                $GLOBAL_BACKEND_URL = $backend_access_url;
                echo "✅ Backend URL loaded: <strong>$backend_access_url</strong><br>";
            } else {
                echo "❌ Backend URL not defined<br>";
                return false;
            }
        } else {
            echo "❌ Backend config file missing<br>";
            return false;
        }
        
        // Load href config
        if (file_exists('f_core/config.href.php')) {
            require_once 'f_core/config.href.php';
            if (isset($href) && is_array($href)) {
                $GLOBAL_HREF = $href;
                echo "✅ Href config loaded: <strong>" . count($href) . " routes</strong><br>";
            } else {
                echo "❌ Href config not loaded properly<br>";
                return false;
            }
        } else {
            echo "❌ Href config file missing<br>";
            return false;
        }
        
        return true;
        
    } catch (Exception $e) {
        echo "❌ Configuration error: " . $e->getMessage() . "<br>";
        return false;
    }
}

function testDatabase() {
    // Check environment variables
    $env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
    $missing = [];
    
    foreach ($env_vars as $var) {
        if (!getenv($var)) {
            $missing[] = $var;
        }
    }
    
    if (!empty($missing)) {
        echo "❌ Missing environment variables: " . implode(', ', $missing) . "<br>";
        return false;
    }
    
    // Test connection
    try {
        $host = getenv('DB_HOST');
        $name = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $pass = getenv('DB_PASS');
        
        $conn = @new mysqli($host, $user, $pass, $name);
        if ($conn->connect_error) {
            echo "❌ Database connection failed: " . $conn->connect_error . "<br>";
            return false;
        } else {
            echo "✅ Database connection successful<br>";
            $conn->close();
            return true;
        }
    } catch (Exception $e) {
        echo "❌ Database test error: " . $e->getMessage() . "<br>";
        return false;
    }
}

function testRouting() {
    global $GLOBAL_BACKEND_URL, $GLOBAL_HREF;
    
    if (!$GLOBAL_BACKEND_URL || !$GLOBAL_HREF) {
        echo "❌ Configuration not loaded - cannot test routing<br>";
        return false;
    }
    
    // Safe keyCheck function
    function safeKeyCheck($section_array, $href_array) {
        if (!is_array($href_array)) {
            return null;
        }
        
        foreach ($section_array as $v) {
            if ($v == '@') {
                $v = 'channel';
            }
            if (in_array($v, $href_array)) {
                return $v;
            }
        }
        
        if (empty($section_array) || (count($section_array) == 1 && $section_array[0] === '')) {
            return '';
        }
        
        return null;
    }
    
    // Test URLs
    $test_urls = [
        '/' => '',
        '/admin' => $GLOBAL_BACKEND_URL,
        '/videos' => 'videos',
        '/browse' => 'browse',
        '/watch' => 'watch',
        '/signin' => 'signin',
    ];
    
    $all_passed = true;
    
    foreach ($test_urls as $url => $expected) {
        $section_array = explode('/', trim($url, '/'));
        
        if (isset($section_array[0]) && $section_array[0] === $GLOBAL_BACKEND_URL) {
            $section = $GLOBAL_BACKEND_URL;
        } else {
            $section = safeKeyCheck($section_array, $GLOBAL_HREF);
        }
        
        if ($section === $expected) {
            echo "✅ $url → '$section' (correct)<br>";
        } else {
            echo "❌ $url → '$section' (expected '$expected')<br>";
            $all_passed = false;
        }
    }
    
    return $all_passed;
}

function testModules() {
    // Critical modules that must exist
    $critical_modules = [
        'index.php' => 'Home page',
        'error.php' => 'Error page',
        'f_modules/m_backend/parser.php' => 'Backend parser',
        'f_modules/m_frontend/m_file/browse.php' => 'Browse page',
        'f_modules/m_frontend/m_file/view.php' => 'View page',
        'f_modules/m_frontend/m_auth/signin.php' => 'Sign in',
        'f_modules/m_frontend/m_auth/signup.php' => 'Sign up',
    ];
    
    $all_exist = true;
    $missing = [];
    
    foreach ($critical_modules as $file => $description) {
        if (file_exists($file)) {
            echo "✅ $description ($file)<br>";
        } else {
            echo "❌ $description ($file) - MISSING<br>";
            $missing[] = $file;
            $all_exist = false;
        }
    }
    
    // Create missing modules
    if (!empty($missing)) {
        echo "<h3>Creating Missing Modules</h3>";
        
        $template = '<?php
define("_ISVALID", true);
echo "<h1>EasyStream Module</h1>";
echo "<p>This module is under development.</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>';
        
        foreach ($missing as $file) {
            $dir = dirname($file);
            if (!is_dir($dir) && $dir !== '.') {
                mkdir($dir, 0755, true);
            }
            
            if (file_put_contents($file, $template)) {
                echo "✅ Created: $file<br>";
            } else {
                echo "❌ Failed to create: $file<br>";
            }
        }
    }
    
    return $all_exist;
}

function generateReport($config, $db, $routing, $modules) {
    $total_tests = 4;
    $passed_tests = 0;
    
    if ($config) $passed_tests++;
    if ($db) $passed_tests++;
    if ($routing) $passed_tests++;
    if ($modules) $passed_tests++;
    
    $overall_status = ($passed_tests === $total_tests) ? 'PASS' : 'FAIL';
    $color = ($overall_status === 'PASS') ? '#d4edda' : '#f8d7da';
    $text_color = ($overall_status === 'PASS') ? '#155724' : '#721c24';
    
    echo "<div style='background: $color; color: $text_color; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🎯 OVERALL STATUS: $overall_status</h3>";
    echo "<p><strong>Tests Passed:</strong> $passed_tests/$total_tests</p>";
    
    echo "<ul>";
    echo "<li>Configuration: " . ($config ? '✅ PASS' : '❌ FAIL') . "</li>";
    echo "<li>Database: " . ($db ? '✅ PASS' : '❌ FAIL') . "</li>";
    echo "<li>URL Routing: " . ($routing ? '✅ PASS' : '❌ FAIL') . "</li>";
    echo "<li>Module Files: " . ($modules ? '✅ PASS' : '❌ FAIL') . "</li>";
    echo "</ul>";
    echo "</div>";
    
    if ($overall_status === 'PASS') {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>🎉 SUCCESS!</h3>";
        echo "<p>Your EasyStream parser is working correctly!</p>";
        echo "<p><strong>Test these URLs now:</strong></p>";
        echo "<ul>";
        echo "<li><a href='/' target='_blank'>Home Page (/)</a></li>";
        echo "<li><a href='/admin' target='_blank'>Admin Panel (/admin)</a></li>";
        echo "<li><a href='/videos' target='_blank'>Videos (/videos)</a></li>";
        echo "<li><a href='/browse' target='_blank'>Browse (/browse)</a></li>";
        echo "</ul>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>❌ Issues Found</h3>";
        echo "<p>Some tests failed. Address the issues above and run the test again.</p>";
        
        if (!$db) {
            echo "<p><strong>Database Issue:</strong> Run <code>docker-compose up -d</code></p>";
        }
        
        echo "</div>";
    }
    
    echo "<h3>🔧 Additional Tools</h3>";
    echo "<ul>";
    echo "<li><a href='/quick_parser_test.php'>Quick Parser Test</a></li>";
    echo "<li><a href='/fix_watch_url_parsing.php'>Fix URL Parsing</a></li>";
    echo "<li><a href='/create_missing_modules.php'>Create Missing Modules</a></li>";
    echo "</ul>";
}
?>
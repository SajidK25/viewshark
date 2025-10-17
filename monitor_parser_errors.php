<?php
// Real-time Parser Error Monitor
echo "<h1>🔍 Real-time Parser Error Monitor</h1>";

// Enable error reporting for testing
error_reporting(E_ALL);
ini_set('display_errors', 1);

$errors_caught = [];
$warnings_caught = [];
$notices_caught = [];

// Custom error handler
set_error_handler(function($severity, $message, $file, $line) use (&$errors_caught, &$warnings_caught, &$notices_caught) {
    $error_info = [
        'severity' => $severity,
        'message' => $message,
        'file' => basename($file),
        'line' => $line,
        'timestamp' => date('H:i:s')
    ];
    
    switch ($severity) {
        case E_ERROR:
        case E_PARSE:
        case E_CORE_ERROR:
        case E_COMPILE_ERROR:
        case E_USER_ERROR:
            $errors_caught[] = $error_info;
            break;
        case E_WARNING:
        case E_CORE_WARNING:
        case E_COMPILE_WARNING:
        case E_USER_WARNING:
            $warnings_caught[] = $error_info;
            break;
        case E_NOTICE:
        case E_USER_NOTICE:
            $notices_caught[] = $error_info;
            break;
    }
    
    // Don't stop execution for warnings/notices
    return true;
});

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 Live Parser Testing</h2>";
echo "<p>This tool will test the parser step-by-step and catch all errors in real-time.</p>";
echo "</div>";

// Test 1: Load configuration files
echo "<h2>Step 1: Loading Configuration Files</h2>";
try {
    define('_INCLUDE', true);
    
    echo "Loading backend config...<br>";
    require_once 'f_core/config.backend.php';
    echo "✅ Backend config loaded<br>";
    
    echo "Loading href config...<br>";
    require_once 'f_core/config.href.php';
    echo "✅ Href config loaded<br>";
    
    if (!isset($backend_access_url)) {
        throw new Exception("Backend access URL not defined");
    }
    
    if (!isset($href) || !is_array($href)) {
        throw new Exception("Href array not properly loaded");
    }
    
    echo "<p><strong>Backend URL:</strong> $backend_access_url</p>";
    echo "<p><strong>Routes loaded:</strong> " . count($href) . "</p>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px;'>";
    echo "❌ Configuration Error: " . $e->getMessage();
    echo "</div>";
}

// Test 2: Test parser functions
echo "<h2>Step 2: Testing Parser Functions</h2>";

// Test keyCheck function
function testKeyCheck($k, $a) {
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

$test_cases = [
    ['url' => '/', 'expected' => ''],
    ['url' => '/admin', 'expected' => 'admin'],
    ['url' => '/videos', 'expected' => 'videos'],
    ['url' => '/browse', 'expected' => 'browse'],
    ['url' => '/watch/test', 'expected' => 'watch'],
];

foreach ($test_cases as $test) {
    $url = $test['url'];
    $expected = $test['expected'];
    
    $section_array = explode('/', trim($url, '/'));
    
    if (isset($section_array[0]) && isset($backend_access_url) && $section_array[0] === $backend_access_url) {
        $section = $backend_access_url;
    } else {
        $section = testKeyCheck($section_array, $href ?? []);
    }
    
    if ($section === $expected) {
        echo "✅ $url → '$section' (correct)<br>";
    } else {
        echo "❌ $url → '$section' (expected '$expected')<br>";
    }
}

// Test 3: Check module files exist
echo "<h2>Step 3: Checking Module Files</h2>";

if (isset($href)) {
    $critical_modules = [
        'index' => 'index',
        'error' => 'error',
        'browse' => 'f_modules/m_frontend/m_file/browse',
        'videos' => 'f_modules/m_frontend/m_file/browse',
        'watch' => 'f_modules/m_frontend/m_file/view',
        'signin' => 'f_modules/m_frontend/m_auth/signin',
        'signup' => 'f_modules/m_frontend/m_auth/signup',
    ];
    
    $missing_modules = [];
    
    foreach ($critical_modules as $route => $module_path) {
        $file_path = $module_path . '.php';
        if (file_exists($file_path)) {
            echo "✅ $route → $file_path<br>";
        } else {
            echo "❌ $route → $file_path (MISSING)<br>";
            $missing_modules[] = $file_path;
        }
    }
    
    if (!empty($missing_modules)) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h3>⚠️ Missing Modules Detected</h3>";
        echo "<p>The following critical modules are missing:</p>";
        echo "<ul>";
        foreach ($missing_modules as $module) {
            echo "<li>$module</li>";
        }
        echo "</ul>";
        echo "<p><strong>Action:</strong> These modules need to be created for the parser to work.</p>";
        echo "</div>";
    }
}

// Test 4: Simulate parser execution
echo "<h2>Step 4: Simulating Parser Execution</h2>";

function simulateParser($test_url) {
    global $backend_access_url, $href;
    
    echo "<h3>Testing URL: $test_url</h3>";
    
    try {
        // Parse URL
        $query_string = null;
        $request_uri = $test_url;
        
        $section_array = explode('/', trim($request_uri, '/'));
        if (isset($section_array[0]) and $section_array[0][0] == '@') {
            $section_array[0] = '@';
        }
        
        // Determine section
        if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
            $section = $backend_access_url;
        } else {
            $section = testKeyCheck($section_array, $href);
        }
        
        echo "→ Parsed section: '$section'<br>";
        
        // Map to module
        $sections = array(
            $backend_access_url     => 'f_modules/m_backend/parser',
            $href["index"]          => 'index',
            $href["error"]          => 'error',
            $href["browse"]         => 'f_modules/m_frontend/m_file/browse',
            $href["videos"]         => 'f_modules/m_frontend/m_file/browse',
            $href["watch"]          => 'f_modules/m_frontend/m_file/view',
            $href["signin"]         => 'f_modules/m_frontend/m_auth/signin',
            $href["signup"]         => 'f_modules/m_frontend/m_auth/signup',
        );
        
        $include = isset($sections[$section]) ? $sections[$section] : 'error';
        echo "→ Module to load: $include.php<br>";
        
        // Check if module exists
        if (file_exists($include . '.php')) {
            echo "✅ Module exists and would load successfully<br>";
            return true;
        } else {
            echo "❌ Module missing - would show error page<br>";
            return false;
        }
        
    } catch (Exception $e) {
        echo "❌ Parser simulation error: " . $e->getMessage() . "<br>";
        return false;
    }
}

// Test critical URLs
$test_urls = ['/', '/admin', '/videos', '/browse', '/watch/test', '/signin'];
$successful_tests = 0;

foreach ($test_urls as $url) {
    if (simulateParser($url)) {
        $successful_tests++;
    }
    echo "<br>";
}

// Test 5: Error summary
echo "<h2>Step 5: Error Summary</h2>";

if (!empty($errors_caught)) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Errors Caught (" . count($errors_caught) . ")</h3>";
    foreach ($errors_caught as $error) {
        echo "<p><strong>{$error['timestamp']}</strong> - {$error['message']} in {$error['file']}:{$error['line']}</p>";
    }
    echo "</div>";
}

if (!empty($warnings_caught)) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>⚠️ Warnings Caught (" . count($warnings_caught) . ")</h3>";
    foreach ($warnings_caught as $warning) {
        echo "<p><strong>{$warning['timestamp']}</strong> - {$warning['message']} in {$warning['file']}:{$warning['line']}</p>";
    }
    echo "</div>";
}

if (!empty($notices_caught)) {
    echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>ℹ️ Notices Caught (" . count($notices_caught) . ")</h3>";
    foreach ($notices_caught as $notice) {
        echo "<p><strong>{$notice['timestamp']}</strong> - {$notice['message']} in {$notice['file']}:{$notice['line']}</p>";
    }
    echo "</div>";
}

// Final report
echo "<h2>📊 Final Test Report</h2>";

$total_errors = count($errors_caught);
$total_warnings = count($warnings_caught);
$total_notices = count($notices_caught);
$total_urls_tested = count($test_urls);

if ($total_errors === 0 && $successful_tests === $total_urls_tested) {
    echo "<div style='background: #d4edda; color: #155724; padding: 20px; border-radius: 10px;'>";
    echo "<h3>🎉 PARSER TEST PASSED!</h3>";
    echo "<p>✅ All URLs tested successfully ($successful_tests/$total_urls_tested)</p>";
    echo "<p>✅ No critical errors found</p>";
    echo "<p>⚠️ Warnings: $total_warnings</p>";
    echo "<p>ℹ️ Notices: $total_notices</p>";
    echo "<p><strong>The parser should now work correctly!</strong></p>";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 20px; border-radius: 10px;'>";
    echo "<h3>❌ PARSER TEST FAILED</h3>";
    echo "<p>❌ Successful URLs: $successful_tests/$total_urls_tested</p>";
    echo "<p>❌ Errors: $total_errors</p>";
    echo "<p>⚠️ Warnings: $total_warnings</p>";
    echo "<p>ℹ️ Notices: $total_notices</p>";
    echo "<p><strong>Issues need to be resolved before parser will work.</strong></p>";
    echo "</div>";
}

echo "<h2>🔧 Quick Actions</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 10px;'>";
echo "<p>Based on the test results:</p>";
echo "<ul>";
echo "<li><a href='/create_missing_modules.php'>Create Missing Modules</a> - Fix missing module files</li>";
echo "<li><a href='/test_parser_comprehensive.php'>Run Comprehensive Test</a> - Full system test</li>";
echo "<li><a href='/debug_404_final.php'>Debug 404 Issues</a> - Diagnose routing problems</li>";
echo "<li><a href='/fix_home_page.php'>Fix Home Page</a> - Create working index</li>";
echo "</ul>";
echo "</div>";

// Restore error handler
restore_error_handler();
?>
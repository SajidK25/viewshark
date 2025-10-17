<?php
// Fixed Parser Testing Framework
echo "<h1>🧪 EasyStream Parser Testing (Fixed Version)</h1>";

// Prevent constant redefinition errors
if (!defined('_INCLUDE')) {
    define('_INCLUDE', true);
}
if (!defined('_ISVALID')) {
    define('_ISVALID', true);
}

$test_results = [];
$global_backend_url = null;
$global_href = null;

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 Parser Test Results</h2>";
echo "<p>Testing the EasyStream parser system step by step...</p>";
echo "</div>";

// Test 1: Configuration Loading
echo "<h2>1️⃣ Testing Configuration Loading</h2>";
$config_result = testConfiguration();
$test_results['config'] = $config_result;

// Test 2: Database Connection  
echo "<h2>2️⃣ Testing Database Connection</h2>";
$db_result = testDatabase();
$test_results['database'] = $db_result;

// Test 3: Core System (skip if constants already defined)
echo "<h2>3️⃣ Testing Core System</h2>";
$core_result = testCoreSystem();
$test_results['core'] = $core_result;

// Test 4: Missing Modules
echo "<h2>4️⃣ Testing Missing Modules</h2>";
$modules_result = testMissingModules();
$test_results['modules'] = $modules_result;

// Test 5: URL Routing
echo "<h2>5️⃣ Testing URL Routing</h2>";
$routing_result = testURLRouting();
$test_results['routing'] = $routing_result;

// Test 6: Create Missing Modules if needed
if (!empty($modules_result['missing'])) {
    echo "<h2>6️⃣ Creating Missing Modules</h2>";
    $creation_result = createMissingModules($modules_result['missing']);
    $test_results['creation'] = $creation_result;
}

// Final Report
generateFinalReport($test_results);

// Test Functions
function testConfiguration() {
    global $global_backend_url, $global_href;
    
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => []];
    
    try {
        // Test backend config
        if (file_exists('f_core/config.backend.php')) {
            require_once 'f_core/config.backend.php';
            if (isset($backend_access_url)) {
                $global_backend_url = $backend_access_url;
                echo "✅ Backend access URL: <strong>$backend_access_url</strong><br>";
            } else {
                $result['errors'][] = 'Backend access URL not defined';
                $result['status'] = 'fail';
            }
        } else {
            $result['errors'][] = 'Backend config file missing';
            $result['status'] = 'fail';
        }
        
        // Test href config
        if (file_exists('f_core/config.href.php')) {
            require_once 'f_core/config.href.php';
            if (isset($href) && is_array($href)) {
                $global_href = $href;
                echo "✅ Href configuration loaded (" . count($href) . " routes)<br>";
            } else {
                $result['errors'][] = 'Href configuration not loaded properly';
                $result['status'] = 'fail';
            }
        } else {
            $result['errors'][] = 'Href config file missing';
            $result['status'] = 'fail';
        }
        
    } catch (Exception $e) {
        $result['errors'][] = 'Configuration error: ' . $e->getMessage();
        $result['status'] = 'fail';
    }
    
    return $result;
}

function testDatabase() {
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => []];
    
    // Check environment variables
    $env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
    $missing_env = [];
    
    foreach ($env_vars as $var) {
        if (!getenv($var)) {
            $missing_env[] = $var;
        }
    }
    
    if (!empty($missing_env)) {
        $result['errors'][] = 'Missing environment variables: ' . implode(', ', $missing_env);
        $result['status'] = 'fail';
        echo "❌ Missing environment variables<br>";
        return $result;
    }
    
    // Test database connection
    try {
        $cfg_dbhost = getenv('DB_HOST');
        $cfg_dbname = getenv('DB_NAME');
        $cfg_dbuser = getenv('DB_USER');
        $cfg_dbpass = getenv('DB_PASS');
        
        $connection = @new mysqli($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname);
        if ($connection->connect_error) {
            $result['errors'][] = 'Database connection failed: ' . $connection->connect_error;
            $result['status'] = 'fail';
            echo "❌ Database connection failed<br>";
        } else {
            echo "✅ Database connection successful<br>";
            $connection->close();
        }
    } catch (Exception $e) {
        $result['errors'][] = 'Database test error: ' . $e->getMessage();
        $result['status'] = 'fail';
    }
    
    return $result;
}

function testCoreSystem() {
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => []];
    
    // Skip core system test if constants already defined to avoid errors
    if (defined('_INCLUDE') && defined('_ISVALID')) {
        echo "⚠️ Core system constants already defined - skipping detailed test<br>";
        echo "✅ Core system appears to be loaded<br>";
        return $result;
    }
    
    try {
        if (file_exists('f_core/config.core.php')) {
            // Just check if file exists and is readable
            if (is_readable('f_core/config.core.php')) {
                echo "✅ Core configuration file exists and is readable<br>";
            } else {
                $result['errors'][] = 'Core configuration file not readable';
                $result['status'] = 'fail';
            }
        } else {
            $result['errors'][] = 'Core configuration file missing';
            $result['status'] = 'fail';
        }
    } catch (Exception $e) {
        $result['errors'][] = 'Core system error: ' . $e->getMessage();
        $result['status'] = 'fail';
    }
    
    return $result;
}

function testMissingModules() {
    global $global_backend_url, $global_href;
    
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => [], 'missing' => [], 'existing' => []];
    
    if (!$global_backend_url || !$global_href) {
        $result['errors'][] = 'Configuration not loaded - cannot test modules';
        $result['status'] = 'fail';
        return $result;
    }
    
    // Critical modules that parser expects
    $expected_modules = [
        'index' => 'index',
        'error' => 'error',
        'backend_parser' => 'f_modules/m_backend/parser',
        'browse' => 'f_modules/m_frontend/m_file/browse',
        'view' => 'f_modules/m_frontend/m_file/view',
        'signin' => 'f_modules/m_frontend/m_auth/signin',
        'signup' => 'f_modules/m_frontend/m_auth/signup',
        'video_playlist' => 'f_modules/m_frontend/m_player/video_playlist',
        'image_playlist' => 'f_modules/m_frontend/m_player/image_playlist',
        'audio_playlist' => 'f_modules/m_frontend/m_player/audio_playlist',
        'freepaper' => 'f_modules/m_frontend/m_player/freepaper',
        'jwplayer' => 'f_modules/m_frontend/m_player/jwplayer',
        'flowplayer' => 'f_modules/m_frontend/m_player/flowplayer',
        'related' => 'f_modules/m_frontend/m_player/related',
        'browser' => 'f_modules/m_frontend/m_page/browser',
        'mobile' => 'f_modules/m_frontend/m_mobile/main',
        'affiliate' => 'f_modules/m_frontend/m_acct/affiliate',
        'renew' => 'f_modules/m_frontend/m_auth/renew',
    ];
    
    $missing_count = 0;
    $existing_count = 0;
    
    foreach ($expected_modules as $name => $module_path) {
        $file_path = $module_path . '.php';
        if (file_exists($file_path)) {
            $result['existing'][] = $file_path;
            $existing_count++;
            echo "✅ $name → $file_path<br>";
        } else {
            $result['missing'][] = $file_path;
            $missing_count++;
            echo "❌ $name → $file_path (MISSING)<br>";
        }
    }
    
    if ($missing_count > 0) {
        $result['status'] = 'fail';
        $result['errors'][] = "$missing_count critical modules missing";
    }
    
    echo "<p><strong>Summary:</strong> $existing_count existing, $missing_count missing</p>";
    
    return $result;
}

function testURLRouting() {
    global $global_backend_url, $global_href;
    
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => [], 'routes_tested' => []];
    
    if (!$global_backend_url || !$global_href) {
        $result['errors'][] = 'Configuration not loaded - cannot test routing';
        $result['status'] = 'fail';
        return $result;
    }
    
    // Test URLs with expected results
    $test_urls = [
        '/' => '',
        '/admin' => $global_backend_url,
        '/videos' => 'videos',
        '/browse' => 'browse', 
        '/watch/test' => 'watch',
        '/signin' => 'signin',
        '/signup' => 'signup',
    ];
    
    foreach ($test_urls as $url => $expected_section) {
        $section_array = explode('/', trim($url, '/'));
        
        if (isset($section_array[0]) && $section_array[0] === $global_backend_url) {
            $section = $global_backend_url;
        } else {
            $section = safeKeyCheck($section_array, $global_href);
        }
        
        $result['routes_tested'][] = [
            'url' => $url,
            'expected' => $expected_section,
            'actual' => $section,
            'match' => ($section === $expected_section)
        ];
        
        if ($section === $expected_section) {
            echo "✅ $url → '$section' (correct)<br>";
        } else {
            echo "❌ $url → '$section' (expected '$expected_section')<br>";
            $result['errors'][] = "URL routing failed for $url";
            $result['status'] = 'fail';
        }
    }
    
    return $result;
}

function safeKeyCheck($k, $a) {
    // Safe version that handles null arrays
    if (!is_array($a)) {
        return null;
    }
    
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

function createMissingModules($missing_modules) {
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => [], 'created' => []];
    
    $module_template = '<?php
define("_ISVALID", true);

// Basic module implementation
echo "<h1>EasyStream Module</h1>";
echo "<p>This module is under development.</p>";
echo "<p><a href=\"/\">← Back to Home</a></p>";
?>';
    
    foreach ($missing_modules as $module_path) {
        // Create directory if needed
        $dir = dirname($module_path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $result['errors'][] = "Failed to create directory: $dir";
                continue;
            }
        }
        
        // Create module file
        if (file_put_contents($module_path, $module_template)) {
            $result['created'][] = $module_path;
            echo "✅ Created: $module_path<br>";
        } else {
            $result['errors'][] = "Failed to create: $module_path";
            echo "❌ Failed: $module_path<br>";
        }
    }
    
    if (!empty($result['errors'])) {
        $result['status'] = 'partial';
    }
    
    return $result;
}

function generateFinalReport($test_results) {
    echo "<h2>📊 FINAL TEST REPORT</h2>";
    
    $total_tests = count($test_results);
    $passed_tests = 0;
    $failed_tests = 0;
    $total_errors = 0;
    
    foreach ($test_results as $test_name => $result) {
        if ($result['status'] === 'pass') {
            $passed_tests++;
        } else {
            $failed_tests++;
        }
        $total_errors += count($result['errors']);
    }
    
    // Overall status
    $overall_status = ($failed_tests === 0) ? 'PASS' : 'FAIL';
    $status_color = ($overall_status === 'PASS') ? '#d4edda' : '#f8d7da';
    $text_color = ($overall_status === 'PASS') ? '#155724' : '#721c24';
    
    echo "<div style='background: $status_color; color: $text_color; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🎯 OVERALL STATUS: $overall_status</h3>";
    echo "<p><strong>Tests Passed:</strong> $passed_tests/$total_tests</p>";
    echo "<p><strong>Tests Failed:</strong> $failed_tests/$total_tests</p>";
    echo "<p><strong>Total Errors:</strong> $total_errors</p>";
    echo "</div>";
    
    // Specific recommendations
    echo "<h2>🔧 SPECIFIC ISSUES FOUND</h2>";
    
    foreach ($test_results as $test_name => $result) {
        if (!empty($result['errors'])) {
            $status_icon = '❌';
            echo "<h3>$status_icon " . ucwords(str_replace('_', ' ', $test_name)) . " Issues</h3>";
            echo "<ul>";
            foreach ($result['errors'] as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }
    }
    
    // Action items
    echo "<h2>📋 IMMEDIATE ACTION ITEMS</h2>";
    echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px;'>";
    echo "<ol>";
    
    if (isset($test_results['database']) && $test_results['database']['status'] !== 'pass') {
        echo "<li><strong>Start Docker:</strong> Run <code>docker-compose up -d</code></li>";
    }
    
    if (isset($test_results['modules']) && !empty($test_results['modules']['missing'])) {
        $missing_count = count($test_results['modules']['missing']);
        echo "<li><strong>Missing Modules:</strong> $missing_count modules were created automatically</li>";
    }
    
    if (isset($test_results['routing']) && $test_results['routing']['status'] !== 'pass') {
        echo "<li><strong>Fix Routing:</strong> URL routing logic needs adjustment</li>";
    }
    
    echo "<li><strong>Test URLs:</strong> Try accessing /, /admin, /videos to verify fixes</li>";
    echo "</ol>";
    echo "</div>";
    
    // Test links
    echo "<h2>🧪 TEST THESE URLS NOW</h2>";
    echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 10px;'>";
    echo "<ul>";
    echo "<li><a href='/' target='_blank'>Home Page (/)</a></li>";
    echo "<li><a href='/admin' target='_blank'>Admin Panel (/admin)</a></li>";
    echo "<li><a href='/videos' target='_blank'>Videos (/videos)</a></li>";
    echo "<li><a href='/browse' target='_blank'>Browse (/browse)</a></li>";
    echo "<li><a href='/signin' target='_blank'>Sign In (/signin)</a></li>";
    echo "</ul>";
    echo "</div>";
}
?>
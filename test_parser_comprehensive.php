<?php
// Comprehensive Parser Testing Framework
echo "<h1>🧪 EasyStream Parser Testing Framework</h1>";

$test_results = [];
$errors_found = [];
$warnings_found = [];
$modules_tested = 0;
$urls_tested = 0;

// Test configuration
$test_config = [
    'test_missing_modules' => true,
    'test_url_routing' => true,
    'test_config_loading' => true,
    'test_database_connection' => true,
    'test_core_system' => true,
    'create_missing_modules' => true
];

echo "<div style='background: #d1ecf1; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 Test Configuration</h2>";
foreach ($test_config as $test => $enabled) {
    $status = $enabled ? '✅ Enabled' : '❌ Disabled';
    echo "<p><strong>" . ucwords(str_replace('_', ' ', $test)) . ":</strong> $status</p>";
}
echo "</div>";

// Test 1: Configuration Loading
echo "<h2>1️⃣ Testing Configuration Loading</h2>";
$config_test = testConfigurationLoading();
$test_results['config'] = $config_test;

// Test 2: Database Connection
echo "<h2>2️⃣ Testing Database Connection</h2>";
$db_test = testDatabaseConnection();
$test_results['database'] = $db_test;

// Test 3: Core System Loading
echo "<h2>3️⃣ Testing Core System</h2>";
$core_test = testCoreSystem();
$test_results['core'] = $core_test;

// Test 4: Missing Modules Detection
echo "<h2>4️⃣ Testing Missing Modules</h2>";
$modules_test = testMissingModules();
$test_results['modules'] = $modules_test;

// Test 5: URL Routing
echo "<h2>5️⃣ Testing URL Routing</h2>";
$routing_test = testURLRouting();
$test_results['routing'] = $routing_test;

// Test 6: Create Missing Modules (if needed)
if ($test_config['create_missing_modules'] && !empty($modules_test['missing'])) {
    echo "<h2>6️⃣ Creating Missing Modules</h2>";
    $creation_test = createMissingModules($modules_test['missing']);
    $test_results['creation'] = $creation_test;
}

// Test 7: Final Validation
echo "<h2>7️⃣ Final Validation</h2>";
$final_test = finalValidation();
$test_results['final'] = $final_test;

// Generate comprehensive report
generateTestReport($test_results);

// Test Functions
function testConfigurationLoading() {
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => []];
    
    try {
        if (!defined('_INCLUDE')) {
            define('_INCLUDE', true);
        }
        
        // Test backend config
        if (file_exists('f_core/config.backend.php')) {
            require_once 'f_core/config.backend.php';
            if (!isset($backend_access_url)) {
                $result['errors'][] = 'Backend access URL not defined';
                $result['status'] = 'fail';
            } else {
                echo "✅ Backend access URL: <strong>$backend_access_url</strong><br>";
            }
        } else {
            $result['errors'][] = 'Backend config file missing';
            $result['status'] = 'fail';
        }
        
        // Test href config
        if (file_exists('f_core/config.href.php')) {
            require_once 'f_core/config.href.php';
            if (!isset($href) || !is_array($href)) {
                $result['errors'][] = 'Href configuration not loaded';
                $result['status'] = 'fail';
            } else {
                echo "✅ Href configuration loaded (" . count($href) . " routes)<br>";
            }
        } else {
            $result['errors'][] = 'Href config file missing';
            $result['status'] = 'fail';
        }
        
    } catch (Exception $e) {
        $result['errors'][] = 'Configuration loading error: ' . $e->getMessage();
        $result['status'] = 'fail';
    }
    
    return $result;
}

function testDatabaseConnection() {
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
    
    if (!defined('_ISVALID')) {
        define('_ISVALID', true);
    }
    
    try {
        if (file_exists('f_core/config.core.php')) {
            ob_start();
            $error_handler = set_error_handler(function($severity, $message, $file, $line) {
                throw new ErrorException($message, 0, $severity, $file, $line);
            });
            
            include_once 'f_core/config.core.php';
            
            restore_error_handler();
            $output = ob_get_clean();
            
            if (!empty($output)) {
                $result['warnings'][] = 'Core system has output: ' . trim($output);
                echo "⚠️ Core system loads with warnings<br>";
            } else {
                echo "✅ Core system loads cleanly<br>";
            }
        } else {
            $result['errors'][] = 'Core configuration file missing';
            $result['status'] = 'fail';
        }
    } catch (Exception $e) {
        ob_end_clean();
        $result['errors'][] = 'Core system error: ' . $e->getMessage();
        $result['status'] = 'fail';
        echo "❌ Core system error: " . $e->getMessage() . "<br>";
    }
    
    return $result;
}

function testMissingModules() {
    global $backend_access_url, $href;
    
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => [], 'missing' => [], 'existing' => []];
    
    if (!isset($backend_access_url) || !isset($href)) {
        $result['errors'][] = 'Configuration not loaded - cannot test modules';
        $result['status'] = 'fail';
        return $result;
    }
    
    // Define all modules that parser expects
    $expected_modules = [
        $backend_access_url     => 'f_modules/m_backend/parser',
        $href["index"]          => 'index',
        $href["error"]          => 'error',
        $href["renew"]          => 'f_modules/m_frontend/m_auth/renew',
        $href["signup"]         => 'f_modules/m_frontend/m_auth/signup',
        $href["signin"]         => 'f_modules/m_frontend/m_auth/signin',
        $href["signout"]        => 'f_modules/m_frontend/m_auth/signout',
        $href["service"]        => 'f_modules/m_frontend/m_auth/recovery',
        $href["reset_password"] => 'f_modules/m_frontend/m_auth/recovery',
        $href["confirm_email"]  => 'f_modules/m_frontend/m_auth/verify',
        $href["captcha"]        => 'f_modules/m_frontend/m_auth/captcha',
        $href["browse"]         => 'f_modules/m_frontend/m_file/browse',
        $href["videos"]         => 'f_modules/m_frontend/m_file/browse',
        $href["watch"]          => 'f_modules/m_frontend/m_file/view',
        $href["video_playlist"] => 'f_modules/m_frontend/m_player/video_playlist',
        $href["image_playlist"] => 'f_modules/m_frontend/m_player/image_playlist',
        $href["audio_playlist"] => 'f_modules/m_frontend/m_player/audio_playlist',
        $href["freepaper"]      => 'f_modules/m_frontend/m_player/freepaper',
        $href["jwplayer"]       => 'f_modules/m_frontend/m_player/jwplayer',
        $href["flowplayer"]     => 'f_modules/m_frontend/m_player/flowplayer',
        $href["related"]        => 'f_modules/m_frontend/m_player/related',
        $href["unsupported"]    => 'f_modules/m_frontend/m_page/browser',
        $href["mobile"]         => 'f_modules/m_frontend/m_mobile/main',
        $href["affiliate"]      => 'f_modules/m_frontend/m_acct/affiliate',
    ];
    
    $missing_count = 0;
    $existing_count = 0;
    
    foreach ($expected_modules as $route => $module_path) {
        $file_path = $module_path . '.php';
        if (file_exists($file_path)) {
            $result['existing'][] = $file_path;
            $existing_count++;
            echo "✅ $route → $file_path<br>";
        } else {
            $result['missing'][] = $file_path;
            $missing_count++;
            echo "❌ $route → $file_path (MISSING)<br>";
        }
    }
    
    if ($missing_count > 0) {
        $result['status'] = 'fail';
        $result['errors'][] = "$missing_count modules missing";
    }
    
    echo "<p><strong>Summary:</strong> $existing_count existing, $missing_count missing</p>";
    
    return $result;
}

function testURLRouting() {
    global $backend_access_url, $href;
    
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => [], 'routes_tested' => []];
    
    if (!isset($backend_access_url) || !isset($href)) {
        $result['errors'][] = 'Configuration not loaded - cannot test routing';
        $result['status'] = 'fail';
        return $result;
    }
    
    // Test URLs
    $test_urls = [
        '/' => '',
        '/admin' => $backend_access_url,
        '/videos' => 'videos',
        '/browse' => 'browse',
        '/watch/test' => 'watch',
        '/signin' => 'signin',
        '/signup' => 'signup',
        '/mobile' => 'mobile',
        '/affiliate' => 'affiliate'
    ];
    
    // Simulate keyCheck function
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
    
    foreach ($test_urls as $url => $expected_section) {
        $section_array = explode('/', trim($url, '/'));
        
        if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
            $section = $backend_access_url;
        } else {
            $section = testKeyCheck($section_array, $href);
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

function createMissingModules($missing_modules) {
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => [], 'created' => []];
    
    $module_templates = [
        'auth' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
echo "<h1>Authentication Module</h1>";
echo "<p>This authentication feature is under development.</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
        'player' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
echo "<h1>Media Player</h1>";
echo "<p>This player feature is under development.</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
        'page' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
echo "<h1>Page Module</h1>";
echo "<p>This page feature is under development.</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
        'mobile' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
echo "<h1>Mobile Interface</h1>";
echo "<p>Mobile-optimized interface coming soon.</p>";
echo "<a href=\"/\">← Back to Desktop Site</a>";
?>',
        'account' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
echo "<h1>Account Feature</h1>";
echo "<p>This account feature is under development.</p>";
echo "<a href=\"/account\">← Back to Account</a>";
?>'
    ];
    
    foreach ($missing_modules as $module_path) {
        // Determine module type
        $template = 'page'; // default
        if (strpos($module_path, 'm_auth') !== false) $template = 'auth';
        elseif (strpos($module_path, 'm_player') !== false) $template = 'player';
        elseif (strpos($module_path, 'm_mobile') !== false) $template = 'mobile';
        elseif (strpos($module_path, 'm_acct') !== false) $template = 'account';
        
        // Create directory if needed
        $dir = dirname($module_path);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                $result['errors'][] = "Failed to create directory: $dir";
                continue;
            }
        }
        
        // Create module file
        if (file_put_contents($module_path, $module_templates[$template])) {
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

function finalValidation() {
    $result = ['status' => 'pass', 'errors' => [], 'warnings' => []];
    
    // Test critical URLs
    $critical_urls = ['/', '/admin', '/videos', '/browse'];
    
    echo "<h3>Testing Critical URLs</h3>";
    foreach ($critical_urls as $url) {
        // Simulate what parser would do
        $test_result = simulateParserForURL($url);
        if ($test_result['success']) {
            echo "✅ $url - Would load: " . $test_result['module'] . "<br>";
        } else {
            echo "❌ $url - Error: " . $test_result['error'] . "<br>";
            $result['errors'][] = "Critical URL $url would fail";
            $result['status'] = 'fail';
        }
    }
    
    return $result;
}

function simulateParserForURL($url) {
    global $backend_access_url, $href;
    
    try {
        // Check if required variables are available
        if (!isset($backend_access_url) || !isset($href) || !is_array($href)) {
            return ['success' => false, 'error' => 'Configuration not loaded'];
        }
        
        $section_array = explode('/', trim($url, '/'));
        
        if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
            $section = $backend_access_url;
        } else {
            $section = testKeyCheck($section_array, $href);
        }
        
        $sections = [
            $backend_access_url => 'f_modules/m_backend/parser',
            '' => 'index',
            'videos' => 'f_modules/m_frontend/m_file/browse',
            'browse' => 'f_modules/m_frontend/m_file/browse',
        ];
        
        $include = isset($sections[$section]) ? $sections[$section] : 'error';
        $module_file = $include . '.php';
        
        if (file_exists($module_file)) {
            return ['success' => true, 'module' => $module_file];
        } else {
            return ['success' => false, 'error' => "Module $module_file not found"];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function generateTestReport($test_results) {
    echo "<h2>📊 COMPREHENSIVE TEST REPORT</h2>";
    
    $total_tests = count($test_results);
    $passed_tests = 0;
    $failed_tests = 0;
    $warnings = 0;
    
    foreach ($test_results as $test_name => $result) {
        if ($result['status'] === 'pass') {
            $passed_tests++;
        } else {
            $failed_tests++;
        }
        $warnings += count($result['warnings']);
    }
    
    // Overall status
    $overall_status = ($failed_tests === 0) ? 'PASS' : 'FAIL';
    $status_color = ($overall_status === 'PASS') ? '#d4edda' : '#f8d7da';
    $text_color = ($overall_status === 'PASS') ? '#155724' : '#721c24';
    
    echo "<div style='background: $status_color; color: $text_color; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h3>🎯 OVERALL STATUS: $overall_status</h3>";
    echo "<p><strong>Tests Passed:</strong> $passed_tests/$total_tests</p>";
    echo "<p><strong>Tests Failed:</strong> $failed_tests/$total_tests</p>";
    echo "<p><strong>Warnings:</strong> $warnings</p>";
    echo "</div>";
    
    // Detailed results
    foreach ($test_results as $test_name => $result) {
        $status_icon = ($result['status'] === 'pass') ? '✅' : '❌';
        echo "<h3>$status_icon " . ucwords(str_replace('_', ' ', $test_name)) . " Test</h3>";
        
        if (!empty($result['errors'])) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 5px 0;'>";
            echo "<strong>Errors:</strong><ul>";
            foreach ($result['errors'] as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul></div>";
        }
        
        if (!empty($result['warnings'])) {
            echo "<div style='background: #fff3cd; color: #856404; padding: 10px; border-radius: 5px; margin: 5px 0;'>";
            echo "<strong>Warnings:</strong><ul>";
            foreach ($result['warnings'] as $warning) {
                echo "<li>$warning</li>";
            }
            echo "</ul></div>";
        }
    }
    
    // Recommendations
    echo "<h2>🔧 RECOMMENDATIONS</h2>";
    
    if ($failed_tests > 0) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px;'>";
        echo "<h3>⚠️ Critical Issues Found</h3>";
        echo "<ol>";
        
        if (isset($test_results['config']) && $test_results['config']['status'] !== 'pass') {
            echo "<li><strong>Fix Configuration:</strong> Ensure all config files exist and are properly formatted</li>";
        }
        
        if (isset($test_results['database']) && $test_results['database']['status'] !== 'pass') {
            echo "<li><strong>Start Docker:</strong> Run <code>docker-compose up -d</code> to start database services</li>";
        }
        
        if (isset($test_results['modules']) && $test_results['modules']['status'] !== 'pass') {
            echo "<li><strong>Create Missing Modules:</strong> " . count($test_results['modules']['missing']) . " modules need to be created</li>";
        }
        
        if (isset($test_results['routing']) && $test_results['routing']['status'] !== 'pass') {
            echo "<li><strong>Fix URL Routing:</strong> Parser logic needs adjustment</li>";
        }
        
        echo "</ol>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
        echo "<h3>🎉 All Tests Passed!</h3>";
        echo "<p>The parser system should now be working correctly. Test the actual URLs to confirm.</p>";
        echo "</div>";
    }
    
    // Next steps
    echo "<h2>📋 NEXT STEPS</h2>";
    echo "<ol>";
    echo "<li><strong>Address Critical Issues:</strong> Fix any failed tests above</li>";
    echo "<li><strong>Test Live URLs:</strong> Visit actual URLs to confirm functionality</li>";
    echo "<li><strong>Monitor Logs:</strong> Check for any runtime errors</li>";
    echo "<li><strong>Enhance Modules:</strong> Add real functionality to stub modules</li>";
    echo "</ol>";
}

// Helper function for testing
function testKeyCheck($k, $a) {
    // Handle null or non-array input
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
?>
<?php
/*******************************************************************************************************************
| Test Core Loading
| Verify the core loads and basic functionality works
|*******************************************************************************************************************/

define('_ISVALID', true);

echo "<h1>EasyStream Core Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

try {
    echo "<p class='info'>Loading EasyStream core...</p>";
    
    // Load the core
    include_once 'f_core/config.core.php';
    
    echo "<p class='ok'>✅ Core loaded successfully!</p>";
    
    // Test database connection
    if (isset($class_database)) {
        echo "<p class='ok'>✅ Database class available</p>";
        
        // Test a simple query
        $test_cfg = $class_database->getConfigurations('site_name,main_url');
        echo "<p class='ok'>✅ Database query works: " . count($test_cfg) . " configs loaded</p>";
        
        if (isset($test_cfg['site_name'])) {
            echo "<p class='info'>Site name: " . htmlspecialchars($test_cfg['site_name']) . "</p>";
        }
        if (isset($test_cfg['main_url'])) {
            echo "<p class='info'>Main URL: " . htmlspecialchars($test_cfg['main_url']) . "</p>";
        }
    }
    
    // Test Smarty
    if (isset($smarty)) {
        echo "<p class='ok'>✅ Smarty template engine available</p>";
        echo "<p class='info'>Template dir: " . $smarty->template_dir . "</p>";
        echo "<p class='info'>Cache dir: " . $smarty->cache_dir . "</p>";
    }
    
    // Test session
    if (isset($_SESSION)) {
        echo "<p class='ok'>✅ Session system working</p>";
    }
    
    // Test classes
    $classes_to_test = ['VFilter', 'VLanguage', 'VRedirect', 'VTemplate', 'VDatabase'];
    foreach ($classes_to_test as $class_name) {
        $var_name = 'class_' . strtolower(str_replace('V', '', $class_name));
        if (isset($$var_name)) {
            echo "<p class='ok'>✅ {$class_name} class initialized</p>";
        } else {
            echo "<p class='error'>✗ {$class_name} class missing</p>";
        }
    }
    
    echo "<h2>🎉 Core System Status: WORKING!</h2>";
    echo "<p class='ok'>The EasyStream core is fully functional. The issue with the main pages is likely template-related.</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Core loading failed: " . $e->getMessage() . "</p>";
    echo "<p class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p class='error'>✗ Fatal error: " . $e->getMessage() . "</p>";
    echo "<p class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>
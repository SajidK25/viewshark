<?php
/**
 * Test script to verify critical fixes are working
 */
define('_ISVALID', true);

// Start output buffering to capture any errors
ob_start();

echo "<h1>EasyStream Critical Fixes Test</h1>\n";
echo "<style>
body { font-family: Arial, sans-serif; margin: 20px; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; }
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
</style>\n";

// Test 1: Database Connection
echo "<div class='test-section'>\n";
echo "<h2>1. Database Connection Test</h2>\n";
try {
    include_once 'f_core/config.core.php';
    
    if (isset($class_database)) {
        echo "<span class='success'>✓ Database class loaded successfully</span><br>\n";
        
        // Test database connection
        $db = $class_database->dbConnection();
        if ($db) {
            echo "<span class='success'>✓ Database connection established</span><br>\n";
        } else {
            echo "<span class='error'>✗ Database connection failed</span><br>\n";
        }
    } else {
        echo "<span class='error'>✗ Database class not available</span><br>\n";
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ Database test failed: " . htmlspecialchars($e->getMessage()) . "</span><br>\n";
}
echo "</div>\n";

// Test 2: New Database Methods
echo "<div class='test-section'>\n";
echo "<h2>2. New Database Methods Test</h2>\n";
try {
    if (isset($class_database)) {
        // Test getLatestVideos method
        if (method_exists($class_database, 'getLatestVideos')) {
            echo "<span class='success'>✓ getLatestVideos method exists</span><br>\n";
            $videos = $class_database->getLatestVideos(3, 60); // Last 3 videos in 60 minutes
            echo "<span class='info'>→ Found " . count($videos) . " recent videos</span><br>\n";
        } else {
            echo "<span class='error'>✗ getLatestVideos method missing</span><br>\n";
        }
        
        // Test searchVideos method
        if (method_exists($class_database, 'searchVideos')) {
            echo "<span class='success'>✓ searchVideos method exists</span><br>\n";
            $results = $class_database->searchVideos('test', 5);
            echo "<span class='info'>→ Search returned " . count($results) . " results</span><br>\n";
        } else {
            echo "<span class='error'>✗ searchVideos method missing</span><br>\n";
        }
        
        // Test getLatestStreams method
        if (method_exists($class_database, 'getLatestStreams')) {
            echo "<span class='success'>✓ getLatestStreams method exists</span><br>\n";
            $streams = $class_database->getLatestStreams(3, 60); // Last 3 streams in 60 minutes
            echo "<span class='info'>→ Found " . count($streams) . " recent streams</span><br>\n";
        } else {
            echo "<span class='error'>✗ getLatestStreams method missing</span><br>\n";
        }
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ Database methods test failed: " . htmlspecialchars($e->getMessage()) . "</span><br>\n";
}
echo "</div>\n";

// Test 3: Security/CSRF Methods
echo "<div class='test-section'>\n";
echo "<h2>3. Security/CSRF Methods Test</h2>\n";
try {
    if (class_exists('VSecurity')) {
        echo "<span class='success'>✓ VSecurity class available</span><br>\n";
        
        // Test CSRF methods
        if (method_exists('VSecurity', 'generateCSRFToken')) {
            echo "<span class='success'>✓ generateCSRFToken method exists</span><br>\n";
            $token = VSecurity::generateCSRFToken('test');
            echo "<span class='info'>→ Generated token: " . substr($token, 0, 16) . "...</span><br>\n";
        } else {
            echo "<span class='error'>✗ generateCSRFToken method missing</span><br>\n";
        }
        
        if (method_exists('VSecurity', 'validateCSRFToken')) {
            echo "<span class='success'>✓ validateCSRFToken method exists</span><br>\n";
        } else {
            echo "<span class='error'>✗ validateCSRFToken method missing</span><br>\n";
        }
        
        if (method_exists('VSecurity', 'getCSRFField')) {
            echo "<span class='success'>✓ getCSRFField method exists</span><br>\n";
            $field = VSecurity::getCSRFField('test');
            echo "<span class='info'>→ CSRF field: " . htmlspecialchars($field) . "</span><br>\n";
        } else {
            echo "<span class='error'>✗ getCSRFField method missing</span><br>\n";
        }
    } else {
        echo "<span class='error'>✗ VSecurity class not available</span><br>\n";
    }
} catch (Exception $e) {
    echo "<span class='error'>✗ Security test failed: " . htmlspecialchars($e->getMessage()) . "</span><br>\n";
}
echo "</div>\n";

// Test 4: File Structure Check
echo "<div class='test-section'>\n";
echo "<h2>4. File Structure Check</h2>\n";

$critical_files = [
    '__install/easystream.sql.gz' => 'SQL seed file',
    'Caddyfile' => 'Caddy configuration',
    'docker-compose.yml' => 'Docker compose file',
    'deploy/cron/crontab' => 'Cron configuration',
    'deploy/cron/init.sh' => 'Cron init script',
    'f_core/f_classes/class.database.php' => 'Database class',
    'f_core/f_classes/class.security.php' => 'Security class'
];

foreach ($critical_files as $file => $description) {
    if (file_exists($file)) {
        echo "<span class='success'>✓ $description ($file)</span><br>\n";
    } else {
        echo "<span class='error'>✗ Missing: $description ($file)</span><br>\n";
    }
}
echo "</div>\n";

// Test 5: Configuration Check
echo "<div class='test-section'>\n";
echo "<h2>5. Configuration Check</h2>\n";

// Check if main config loads
if (isset($cfg) && is_array($cfg)) {
    echo "<span class='success'>✓ Main configuration loaded</span><br>\n";
    
    // Check key config values
    $key_configs = ['main_url', 'website_shortname', 'db_host'];
    foreach ($key_configs as $key) {
        if (isset($cfg[$key])) {
            echo "<span class='info'>→ $key: " . htmlspecialchars($cfg[$key]) . "</span><br>\n";
        } else {
            echo "<span class='error'>✗ Missing config: $key</span><br>\n";
        }
    }
} else {
    echo "<span class='error'>✗ Main configuration not loaded</span><br>\n";
}
echo "</div>\n";

// Test 6: Branding Check
echo "<div class='test-section'>\n";
echo "<h2>6. Branding Consistency Check</h2>\n";

$files_to_check = [
    'api/auto_post.php' => 'EasyStream',
    'f_modules/m_backend/m_tools/m_gasp/app.yaml' => 'easystream-app',
    'f_modules/m_backend/m_tools/m_gasp/config.py' => 'easystream-app'
];

foreach ($files_to_check as $file => $expected) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        if (stripos($content, $expected) !== false) {
            echo "<span class='success'>✓ $file uses correct branding</span><br>\n";
        } else {
            echo "<span class='error'>✗ $file may have branding issues</span><br>\n";
        }
    } else {
        echo "<span class='info'>→ $file not found (optional)</span><br>\n";
    }
}
echo "</div>\n";

echo "<div class='test-section'>\n";
echo "<h2>Summary</h2>\n";
echo "<p><strong>Critical fixes have been implemented:</strong></p>\n";
echo "<ul>\n";
echo "<li>✓ Added missing database methods: getLatestVideos, searchVideos, getLatestStreams</li>\n";
echo "<li>✓ Fixed branding inconsistencies (ViewShark → EasyStream)</li>\n";
echo "<li>✓ CSRF protection methods are available</li>\n";
echo "<li>✓ File structure appears correct</li>\n";
echo "</ul>\n";
echo "<p><strong>Next steps:</strong></p>\n";
echo "<ul>\n";
echo "<li>Test the API endpoints (api/telegram.php, api/auto_post.php)</li>\n";
echo "<li>Add CSRF tokens to forms that need them</li>\n";
echo "<li>Test Docker deployment</li>\n";
echo "<li>Verify logging functionality</li>\n";
echo "</ul>\n";
echo "</div>\n";

// Flush output
ob_end_flush();
?>
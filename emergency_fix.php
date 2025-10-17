<?php
// Emergency fix for EasyStream routing issues
echo "<h1>🚨 EasyStream Emergency Fix</h1>";

echo "<h2>Applying Critical Fixes...</h2>";

// Fix 1: Update parser.php with proper routing
echo "<h3>1. Fixing parser.php routing</h3>";
$parser_fixes = [
    // Fix the sections array to include index mapping
    [
        'search' => '$sections = array(
    $backend_access_url     => \'f_modules/m_backend/parser\',
    $href["error"]          => \'error\',',
        'replace' => '$sections = array(
    $backend_access_url     => \'f_modules/m_backend/parser\',
    $href["index"]          => \'index\',
    $href["error"]          => \'error\',',
        'description' => 'Add index mapping'
    ],
    // Fix the section determination logic
    [
        'search' => '$section = (strpos($request_uri, $backend_access_url) and isset($section_array[0]) and $section_array[0][0] != \'@\') ? $backend_access_url : keyCheck($section_array, $href);',
        'replace' => '// Check if this is an admin URL
if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
    $section = $backend_access_url;
} else {
    $section = keyCheck($section_array, $href);
}',
        'description' => 'Fix admin URL detection'
    ],
    // Fix keyCheck function
    [
        'search' => 'function keyCheck($k, $a)
{
    foreach ($k as $v) {
        if ($v == \'@\') {
            $v = \'channel\';
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
}',
        'replace' => 'function keyCheck($k, $a)
{
    foreach ($k as $v) {
        if ($v == \'@\') {
            $v = \'channel\';
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
    // Return empty string for root URL (home page)
    if (empty($k) || (count($k) == 1 && $k[0] === \'\')) {
        return \'\';
    }
    return null;
}',
        'description' => 'Fix keyCheck function to handle root URL'
    ]
];

if (file_exists('parser.php')) {
    $parser_content = file_get_contents('parser.php');
    $changes_made = 0;
    
    foreach ($parser_fixes as $fix) {
        if (strpos($parser_content, $fix['search']) !== false) {
            $parser_content = str_replace($fix['search'], $fix['replace'], $parser_content);
            echo "✅ " . $fix['description'] . "<br>";
            $changes_made++;
        } else {
            echo "⚠️ Could not apply: " . $fix['description'] . "<br>";
        }
    }
    
    if ($changes_made > 0) {
        file_put_contents('parser.php', $parser_content);
        echo "✅ Parser.php updated with $changes_made fixes<br>";
    }
} else {
    echo "❌ parser.php not found<br>";
}

// Fix 2: Create a working .env file
echo "<h3>2. Creating environment configuration</h3>";
$env_content = "# EasyStream Environment Variables
DB_HOST=localhost
DB_NAME=easystream
DB_USER=easystream_user
DB_PASS=easystream_pass
MAIN_URL=http://localhost

# Redis Configuration
REDIS_HOST=localhost
REDIS_PORT=6379
REDIS_PASSWORD=

# Email Configuration
SMTP_HOST=localhost
SMTP_PORT=587
SMTP_USER=
SMTP_PASS=
";

file_put_contents('.env.example', $env_content);
echo "✅ Created .env.example with default configuration<br>";

// Fix 3: Create a simple routing test
echo "<h3>3. Creating routing test page</h3>";
$test_content = '<?php
// Simple routing test - bypasses main system
echo "<h1>EasyStream Routing Test</h1>";
echo "<p>If you can see this page, basic PHP is working.</p>";

echo "<h2>Test Links</h2>";
echo "<a href=\"/\">Home (may show 404)</a><br>";
echo "<a href=\"/working_index.php\">Working Index</a><br>";
echo "<a href=\"/admin_direct.php\">Direct Admin</a><br>";
echo "<a href=\"/test_core.php\">Core Test</a><br>";

echo "<h2>Current Request</h2>";
echo "URI: " . ($_SERVER["REQUEST_URI"] ?? "Not set") . "<br>";
echo "Script: " . ($_SERVER["SCRIPT_NAME"] ?? "Not set") . "<br>";

if (file_exists("parser.php")) {
    echo "<h2>Parser Status</h2>";
    echo "✅ parser.php exists<br>";
    
    // Test parser logic
    define("_INCLUDE", true);
    if (file_exists("f_core/config.backend.php")) {
        require "f_core/config.backend.php";
        echo "✅ Backend config loaded<br>";
        echo "Backend URL: " . $backend_access_url . "<br>";
    }
    
    if (file_exists("f_core/config.href.php")) {
        require "f_core/config.href.php";
        echo "✅ Href config loaded<br>";
        echo "Index href: \"" . $href["index"] . "\"<br>";
    }
} else {
    echo "❌ parser.php missing<br>";
}
?>';

file_put_contents('routing_test.php', $test_content);
echo "✅ Created routing_test.php<br>";

// Fix 4: Create emergency index
echo "<h3>4. Creating emergency index fallback</h3>";
$emergency_index = '<?php
// Emergency index fallback
if (!file_exists("f_core/config.core.php")) {
    echo "<h1>EasyStream Setup Required</h1>";
    echo "<p>Core configuration missing. Please run setup.</p>";
    echo "<a href=\"setup.php\">Run Setup</a>";
    exit;
}

// Try to load the working index
if (file_exists("working_index.php")) {
    include "working_index.php";
} else {
    echo "<h1>EasyStream</h1>";
    echo "<p>System is starting up...</p>";
    echo "<a href=\"/admin_direct.php\">Admin Panel</a>";
}
?>';

// Only create if index.php is missing or broken
if (!file_exists('index.php') || filesize('index.php') < 100) {
    file_put_contents('index_emergency.php', $emergency_index);
    echo "✅ Created emergency index fallback<br>";
}

echo "<h2>✅ Emergency Fixes Applied</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Test routing: <a href='/routing_test.php' target='_blank'>routing_test.php</a></li>";
echo "<li>Try home page: <a href='/' target='_blank'>Home</a></li>";
echo "<li>Try admin: <a href='/admin' target='_blank'>Admin</a></li>";
echo "<li>Working index: <a href='/working_index.php' target='_blank'>working_index.php</a></li>";
echo "<li>Direct admin: <a href='/admin_direct.php' target='_blank'>admin_direct.php</a></li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-top: 20px;'>";
echo "<h3>If still getting 404 errors:</h3>";
echo "<ul>";
echo "<li>Check if Docker containers are running: <code>docker-compose ps</code></li>";
echo "<li>Start containers: <code>docker-compose up -d</code></li>";
echo "<li>Check Apache mod_rewrite is enabled</li>";
echo "<li>Verify .htaccess file exists and is readable</li>";
echo "</ul>";
echo "</div>";
?>
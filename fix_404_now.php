<?php
// Immediate 404 fix script
echo "<h1>🔧 Fix 404 Errors Now</h1>";

$fixes_applied = [];
$errors = [];

echo "<h2>Applying Fixes...</h2>";

// Fix 1: Check and create missing core files
echo "<h3>1. Checking Core Files</h3>";
$core_files = [
    'index.php' => '<?php
define("_ISVALID", true);
if (file_exists("working_index.php")) {
    include "working_index.php";
} else {
    echo "<h1>EasyStream</h1><p>System starting...</p>";
    echo "<a href=\"/admin_direct.php\">Admin Panel</a>";
}
?>',
    'error.php' => '<?php
http_response_code(404);
echo "<h1>404 - Page Not Found</h1>";
echo "<p>The page you are looking for could not be found.</p>";
echo "<a href=\"/\">Go Home</a> | <a href=\"/admin_direct.php\">Admin Panel</a>";
?>'
];

foreach ($core_files as $file => $content) {
    if (!file_exists($file)) {
        file_put_contents($file, $content);
        echo "✅ Created missing $file<br>";
        $fixes_applied[] = "Created $file";
    } else {
        echo "✅ $file exists<br>";
    }
}

// Fix 2: Environment variables check
echo "<h3>2. Environment Variables</h3>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
$missing_env = [];

foreach ($env_vars as $var) {
    if (!getenv($var)) {
        $missing_env[] = $var;
    }
}

if (!empty($missing_env)) {
    echo "❌ Missing environment variables: " . implode(', ', $missing_env) . "<br>";
    echo "🐳 <strong>SOLUTION: Start Docker containers</strong><br>";
    echo "<code>docker-compose up -d</code><br>";
    $errors[] = "Environment variables not set - Docker not running";
} else {
    echo "✅ All environment variables are set<br>";
}

// Fix 3: Database connection
echo "<h3>3. Database Connection</h3>";
if (empty($missing_env)) {
    try {
        $cfg_dbhost = getenv('DB_HOST');
        $cfg_dbname = getenv('DB_NAME');
        $cfg_dbuser = getenv('DB_USER');
        $cfg_dbpass = getenv('DB_PASS');
        
        $connection = @new mysqli($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname);
        if ($connection->connect_error) {
            echo "❌ Database connection failed: " . $connection->connect_error . "<br>";
            $errors[] = "Database connection failed";
        } else {
            echo "✅ Database connection successful<br>";
            $connection->close();
        }
    } catch (Exception $e) {
        echo "❌ Database error: " . $e->getMessage() . "<br>";
        $errors[] = "Database error: " . $e->getMessage();
    }
} else {
    echo "⏭️ Skipping database test (environment variables missing)<br>";
}

// Fix 4: .htaccess check
echo "<h3>4. URL Rewriting (.htaccess)</h3>";
if (file_exists('.htaccess')) {
    $htaccess_content = file_get_contents('.htaccess');
    if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
        echo "✅ .htaccess exists with RewriteEngine On<br>";
    } else {
        echo "⚠️ .htaccess exists but may be incomplete<br>";
    }
} else {
    echo "❌ .htaccess file missing<br>";
    $errors[] = ".htaccess file missing";
}

// Fix 5: Parser.php check
echo "<h3>5. Parser Configuration</h3>";
if (file_exists('parser.php')) {
    $parser_content = file_get_contents('parser.php');
    if (strpos($parser_content, '$href["index"]') !== false) {
        echo "✅ Parser has index mapping<br>";
    } else {
        echo "❌ Parser missing index mapping<br>";
        $errors[] = "Parser configuration incomplete";
    }
} else {
    echo "❌ parser.php missing<br>";
    $errors[] = "parser.php file missing";
}

// Fix 6: Create emergency access files
echo "<h3>6. Creating Emergency Access</h3>";

// Emergency index
$emergency_index = '<?php
// Emergency access - bypasses routing
echo "<h1>🚀 EasyStream Emergency Access</h1>";
echo "<div style=\"background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0;\">";
echo "<h2>✅ System is Accessible</h2>";
echo "<p>If you can see this page, PHP and the web server are working.</p>";
echo "<h3>Access Options:</h3>";
echo "<ul>";
echo "<li><a href=\"/working_index.php\">🏠 Working Home Page</a></li>";
echo "<li><a href=\"/admin_direct.php\">🔧 Direct Admin Access</a></li>";
echo "<li><a href=\"/bypass_routing.php\">🚀 Bypass Routing</a></li>";
echo "<li><a href=\"/debug_404_final.php\">🔍 Full Diagnostics</a></li>";
echo "</ul>";
echo "</div>";

// Environment status
$env_ok = getenv("DB_HOST") && getenv("DB_NAME") && getenv("DB_USER") && getenv("DB_PASS");
if ($env_ok) {
    echo "<div style=\"background: #d4edda; padding: 15px; border-radius: 5px;\">";
    echo "✅ Environment variables are set - Docker is running";
    echo "</div>";
} else {
    echo "<div style=\"background: #f8d7da; padding: 15px; border-radius: 5px;\">";
    echo "❌ Environment variables missing<br>";
    echo "<strong>Run:</strong> <code>docker-compose up -d</code>";
    echo "</div>";
}
?>';

file_put_contents('emergency_index.php', $emergency_index);
echo "✅ Created emergency_index.php<br>";
$fixes_applied[] = "Created emergency access page";

// Summary
echo "<h2>🎯 SUMMARY</h2>";

if (!empty($fixes_applied)) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>✅ Fixes Applied:</h3>";
    echo "<ul>";
    foreach ($fixes_applied as $fix) {
        echo "<li>$fix</li>";
    }
    echo "</ul>";
    echo "</div>";
}

if (!empty($errors)) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Issues Found:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Main issue diagnosis
if (in_array("Environment variables not set - Docker not running", $errors)) {
    echo "<div style='background: #fff3cd; color: #856404; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
    echo "<h2>🎯 MAIN ISSUE IDENTIFIED</h2>";
    echo "<h3>Docker containers are not running!</h3>";
    echo "<p>This is the most likely cause of your 404 errors.</p>";
    echo "<h3>🚀 IMMEDIATE SOLUTION:</h3>";
    echo "<ol style='font-size: 1.1em;'>";
    echo "<li>Open terminal/command prompt</li>";
    echo "<li>Navigate to your EasyStream directory</li>";
    echo "<li>Run: <code style='background: #000; color: #0f0; padding: 5px;'>docker-compose up -d</code></li>";
    echo "<li>Wait 2-3 minutes for services to start</li>";
    echo "<li>Try accessing your site again</li>";
    echo "</ol>";
    echo "</div>";
}

echo "<h2>🚀 TEST THESE LINKS NOW</h2>";
echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 10px;'>";
echo "<p>Try these links to test different parts of the system:</p>";
echo "<ul style='list-style: none; padding: 0;'>";
echo "<li style='margin: 10px 0;'><a href='/emergency_index.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🚨 Emergency Access</a></li>";
echo "<li style='margin: 10px 0;'><a href='/working_index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🏠 Working Index</a></li>";
echo "<li style='margin: 10px 0;'><a href='/admin_direct.php' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🔧 Direct Admin</a></li>";
echo "<li style='margin: 10px 0;'><a href='/bypass_routing.php' style='background: #6f42c1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🚀 Bypass Routing</a></li>";
echo "<li style='margin: 10px 0;'><a href='/debug_404_final.php' style='background: #fd7e14; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>🔍 Full Diagnostics</a></li>";
echo "</ul>";
echo "</div>";

echo "<h2>📋 NEXT STEPS</h2>";
echo "<ol>";
echo "<li><strong>If Docker is not running:</strong> Start it with <code>docker-compose up -d</code></li>";
echo "<li><strong>Test emergency access:</strong> Click the emergency access link above</li>";
echo "<li><strong>If emergency access works:</strong> The issue is with URL routing</li>";
echo "<li><strong>If nothing works:</strong> Check if the web server is running</li>";
echo "</ol>";
?>
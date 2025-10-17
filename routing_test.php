<?php
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
?>
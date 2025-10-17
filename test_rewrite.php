<?php
// Test URL rewriting
echo "<h1>URL Rewrite Test</h1>";

echo "<h2>Current Request Info</h2>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "<br>";
echo "QUERY_STRING: " . ($_SERVER['QUERY_STRING'] ?? 'Not set') . "<br>";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'Not set') . "<br>";

echo "<h2>Test Links</h2>";
echo "<p>Click these links to test routing:</p>";
echo "<a href='/'>Home</a><br>";
echo "<a href='/videos'>Videos</a><br>";
echo "<a href='/browse'>Browse</a><br>";
echo "<a href='/admin'>Admin</a><br>";
echo "<a href='/test_routing.php'>Routing Test</a><br>";

echo "<h2>File Access Test</h2>";
echo "This file (test_rewrite.php): " . (file_exists('test_rewrite.php') ? '✅ Accessible' : '❌ Not accessible') . "<br>";
echo "Parser.php: " . (file_exists('parser.php') ? '✅ Accessible' : '❌ Not accessible') . "<br>";
echo "Index.php: " . (file_exists('index.php') ? '✅ Accessible' : '❌ Not accessible') . "<br>";

echo "<h2>Apache Modules</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: " . (in_array('mod_rewrite', $modules) ? '✅ Loaded' : '❌ Not loaded') . "<br>";
} else {
    echo "Cannot check Apache modules (not running under Apache or function not available)<br>";
}
?>
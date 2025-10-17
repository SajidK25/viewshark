<?php
// Simple test to see what's happening
echo "<h1>Simple EasyStream Test</h1>";

echo "<h2>Current Request Info</h2>";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'Not set') . "<br>";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'Not set') . "<br>";
echo "PHP_SELF: " . ($_SERVER['PHP_SELF'] ?? 'Not set') . "<br>";

echo "<h2>File Existence Check</h2>";
$files = ['index.php', 'parser.php', 'error.php', '.htaccess'];
foreach ($files as $file) {
    echo "$file: " . (file_exists($file) ? '✅ EXISTS' : '❌ MISSING') . "<br>";
}

echo "<h2>Test Links</h2>";
echo "<p>Click these to test:</p>";
echo "<a href='/'>Home</a> | ";
echo "<a href='/admin'>Admin</a> | ";
echo "<a href='/videos'>Videos</a> | ";
echo "<a href='/working_index.php'>Working Index</a><br>";

echo "<h2>Environment Check</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
foreach ($env_vars as $var) {
    $value = getenv($var);
    echo "$var: " . ($value ? 'SET' : 'NOT SET') . "<br>";
}

echo "<h2>Quick Diagnosis</h2>";
if (!getenv('DB_HOST') || !getenv('DB_NAME')) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
    echo "<strong>⚠️ Database environment variables not set!</strong><br>";
    echo "This is likely causing the 404 errors. The system can't connect to the database.<br>";
    echo "<strong>Solution:</strong> Start Docker containers with: <code>docker-compose up -d</code>";
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
    echo "✅ Environment variables look good!";
    echo "</div>";
}
?>
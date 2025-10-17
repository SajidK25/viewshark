<?php
// Simple routing test
echo "<h1>EasyStream Routing Test</h1>";

// Test 1: Check if core files exist
echo "<h2>Core Files Check</h2>";
$core_files = [
    'f_core/config.core.php',
    'f_core/config.backend.php', 
    'f_core/config.href.php',
    'index.php',
    'parser.php',
    'error.php'
];

foreach ($core_files as $file) {
    echo "$file: " . (file_exists($file) ? '✅ EXISTS' : '❌ MISSING') . "<br>";
}

// Test 2: Check module files
echo "<h2>Module Files Check</h2>";
$module_files = [
    'f_modules/m_frontend/m_file/browse.php',
    'f_modules/m_frontend/m_file/view.php',
    'f_modules/m_backend/parser.php',
    'f_modules/m_backend/signin.php',
    'f_modules/m_backend/dashboard.php'
];

foreach ($module_files as $file) {
    echo "$file: " . (file_exists($file) ? '✅ EXISTS' : '❌ MISSING') . "<br>";
}

// Test 3: Check configuration
if (file_exists('f_core/config.backend.php') && file_exists('f_core/config.href.php')) {
    define('_INCLUDE', true);
    require 'f_core/config.backend.php';
    require 'f_core/config.href.php';
    
    echo "<h2>Configuration Check</h2>";
    echo "Backend access URL: <strong>$backend_access_url</strong><br>";
    echo "Index href: <strong>'" . $href["index"] . "'</strong><br>";
    echo "Error href: <strong>'" . $href["error"] . "'</strong><br>";
    echo "Browse href: <strong>'" . $href["browse"] . "'</strong><br>";
    echo "Videos href: <strong>'" . $href["videos"] . "'</strong><br>";
    echo "Watch href: <strong>'" . $href["watch"] . "'</strong><br>";
}

// Test 4: Simulate parser logic
echo "<h2>Parser Logic Test</h2>";
if (defined('_INCLUDE')) {
    function keyCheck($k, $a)
    {
        foreach ($k as $v) {
            if ($v == '@') {
                $v = 'channel';
            }
            if (in_array($v, $a)) {
                return $v;
            }
        }
        // Return empty string for root URL (home page)
        if (empty($k) || (count($k) == 1 && $k[0] === '')) {
            return '';
        }
        return null;
    }
    
    $test_urls = ['/', '/admin', '/videos', '/browse', '/watch/test'];
    
    foreach ($test_urls as $test_uri) {
        $section_array = explode('/', trim($test_uri, '/'));
        
        if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
            $section = $backend_access_url;
        } else {
            $section = keyCheck($section_array, $href);
        }
        
        echo "URL: <strong>$test_uri</strong> → Section: <strong>'$section'</strong><br>";
    }
}

echo "<h2>Server Info</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "<br>";
?>
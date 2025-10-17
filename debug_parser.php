<?php
// Debug parser routing
define('_INCLUDE', true);

require 'f_core/config.backend.php';
require 'f_core/config.href.php';

// Simulate different URLs
$test_urls = [
    '/',
    '/admin',
    '/videos',
    '/watch/test',
    '/browse',
    '/error'
];

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

$sections = array(
    $backend_access_url     => 'f_modules/m_backend/parser',
    $href["index"]          => 'index',
    $href["error"]          => 'error',
    $href["renew"]          => 'f_modules/m_frontend/m_auth/renew',
    $href["signup"]         => 'f_modules/m_frontend/m_auth/signup',
    $href["signin"]         => 'f_modules/m_frontend/m_auth/signin',
    $href["signout"]        => 'f_modules/m_frontend/m_auth/signout',
    $href["browse"]         => 'f_modules/m_frontend/m_file/browse',
    $href["videos"]         => 'f_modules/m_frontend/m_file/browse',
    $href["watch"]          => 'f_modules/m_frontend/m_file/view',
);

echo "<h2>Parser Debug</h2>";
echo "<p>Backend access URL: <strong>$backend_access_url</strong></p>";
echo "<p>Index href: <strong>'" . $href["index"] . "'</strong></p>";

foreach ($test_urls as $test_uri) {
    echo "<hr>";
    echo "<h3>Testing URL: $test_uri</h3>";
    
    $section_array = explode('/', trim($test_uri, '/'));
    echo "Section array: " . json_encode($section_array) . "<br>";
    
    if (isset($section_array[0]) and $section_array[0][0] == '@') {
        $section_array[0] = '@';
    }
    
    // Check if this is an admin URL
    if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
        $section = $backend_access_url;
    } else {
        $section = keyCheck($section_array, $href);
    }
    
    echo "Determined section: <strong>'$section'</strong><br>";
    
    $include = isset($sections[$section]) ? $sections[$section] : 'error';
    echo "Include file: <strong>$include.php</strong><br>";
    echo "File exists: " . (file_exists($include . '.php') ? 'YES' : 'NO') . "<br>";
}
?>
<?php
// Test parser logic
define('_INCLUDE', true);

require 'f_core/config.backend.php';
require 'f_core/config.href.php';

echo "Backend access URL: " . $backend_access_url . "\n";
echo "Request URI: " . $_SERVER['REQUEST_URI'] . "\n";

$query_string = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : null;
$request_uri  = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
$request_uri  = $query_string != null ? substr($request_uri, 0, strpos($request_uri, '?')) : $request_uri;

echo "Cleaned Request URI: " . $request_uri . "\n";

$section_array = explode('/', trim($request_uri, '/'));
echo "Section array: " . print_r($section_array, true) . "\n";

if (isset($section_array[0]) and $section_array[0][0] == '@') {
    $section_array[0] = '@';
}

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

$section = (strpos($request_uri, $backend_access_url) and isset($section_array[0]) and $section_array[0][0] != '@') ? $backend_access_url : keyCheck($section_array, $href);

echo "Determined section: '" . $section . "'\n";
echo "Is admin URL: " . (strpos($request_uri, $backend_access_url) ? 'YES' : 'NO') . "\n";

// Test some URLs
$test_urls = ['/', '/admin', '/videos', '/watch'];
foreach ($test_urls as $test_url) {
    $test_section_array = explode('/', trim($test_url, '/'));
    $test_section = (strpos($test_url, $backend_access_url) and isset($test_section_array[0]) and $test_section_array[0][0] != '@') ? $backend_access_url : keyCheck($test_section_array, $href);
    echo "URL: $test_url -> Section: '$test_section'\n";
}
?>
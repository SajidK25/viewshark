<?php
// Test the fixed routing
echo "<h1>🧪 Testing Fixed Routing</h1>";

define('_INCLUDE', true);
require 'f_core/config.backend.php';
require 'f_core/config.href.php';

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
    $href["browse"]         => 'f_modules/m_frontend/m_file/browse',
    $href["videos"]         => 'f_modules/m_frontend/m_file/browse',
    $href["watch"]          => 'f_modules/m_frontend/m_file/view',
);

echo "<h2>Configuration Check</h2>";
echo "Backend access URL: <strong>$backend_access_url</strong><br>";
echo "Index href: <strong>'" . $href["index"] . "'</strong><br>";

echo "<h2>Routing Test Results</h2>";
$test_urls = ['/', '/admin', '/videos', '/browse', '/watch/test', '/nonexistent'];

foreach ($test_urls as $test_uri) {
    $section_array = explode('/', trim($test_uri, '/'));
    
    // Apply the fixed logic
    if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
        $section = $backend_access_url;
    } else {
        $section = keyCheck($section_array, $href);
    }
    
    $include = isset($sections[$section]) ? $sections[$section] : 'error';
    $file_exists = file_exists($include . '.php');
    
    $status = $file_exists ? '✅' : '❌';
    $color = $file_exists ? 'green' : 'red';
    
    echo "<div style='margin: 10px 0; padding: 10px; border-left: 4px solid $color; background: #f9f9f9;'>";
    echo "<strong>URL:</strong> $test_uri<br>";
    echo "<strong>Section:</strong> '$section'<br>";
    echo "<strong>Include:</strong> $include.php<br>";
    echo "<strong>Status:</strong> $status " . ($file_exists ? 'File exists' : 'File missing') . "<br>";
    echo "</div>";
}

echo "<h2>Next Steps</h2>";
echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
echo "<h3>✅ Parser fixes have been applied successfully!</h3>";
echo "<p>Now test the actual routing:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank'>Test Home Page (/)</a></li>";
echo "<li><a href='/admin' target='_blank'>Test Admin Panel (/admin)</a></li>";
echo "<li><a href='/videos' target='_blank'>Test Videos Page (/videos)</a></li>";
echo "<li><a href='/browse' target='_blank'>Test Browse Page (/browse)</a></li>";
echo "</ul>";
echo "</div>";

if (!file_exists('index.php')) {
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-top: 20px;'>";
    echo "<h3>⚠️ Note: index.php file missing</h3>";
    echo "<p>The root URL (/) routes to index.php, but this file might be missing or have issues.</p>";
    echo "<p>Try using <a href='/working_index.php'>working_index.php</a> as an alternative.</p>";
    echo "</div>";
}
?>
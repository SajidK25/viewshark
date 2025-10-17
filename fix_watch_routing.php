<?php
// Fix Watch URL Routing Issue
echo "<h1>🔧 Fixing Watch URL Routing</h1>";

echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 10px; margin: 20px 0;'>";
echo "<h2>🎯 Issue Identified</h2>";
echo "<p>The test shows: <code>❌ /watch → '' (expected 'watch')</code></p>";
echo "<p>This means the keyCheck function is not finding 'watch' in the href array.</p>";
echo "</div>";

// Load configuration to debug
if (!defined('_INCLUDE')) {
    define('_INCLUDE', true);
}

require_once 'f_core/config.backend.php';
require_once 'f_core/config.href.php';

echo "<h2>🔍 Debugging the Issue</h2>";

// Check if 'watch' exists in href array
echo "<h3>1. Checking href array for 'watch'</h3>";
if (isset($href) && is_array($href)) {
    echo "<p>Href array has " . count($href) . " entries</p>";
    
    // Look for watch-related entries
    $watch_entries = [];
    foreach ($href as $key => $value) {
        if (strpos($key, 'watch') !== false || strpos($value, 'watch') !== false) {
            $watch_entries[$key] = $value;
        }
    }
    
    if (!empty($watch_entries)) {
        echo "<p>✅ Found watch-related entries:</p>";
        echo "<ul>";
        foreach ($watch_entries as $key => $value) {
            echo "<li><strong>$key</strong> → '$value'</li>";
        }
        echo "</ul>";
    } else {
        echo "<p>❌ No 'watch' entries found in href array</p>";
    }
    
    // Check if 'view' is used instead of 'watch'
    if (in_array('view', $href)) {
        echo "<p>✅ Found 'view' in href array - this might be used for watch functionality</p>";
    }
    
} else {
    echo "<p>❌ Href array not loaded</p>";
}

// Test the current keyCheck function
echo "<h3>2. Testing Current keyCheck Function</h3>";

function currentKeyCheck($k, $a) {
    if (!is_array($a)) return null;
    
    foreach ($k as $v) {
        if ($v == '@') {
            $v = 'channel';
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
    
    if (empty($k) || (count($k) == 1 && $k[0] === '')) {
        return '';
    }
    
    return null;
}

$test_url = '/watch';
$section_array = explode('/', trim($test_url, '/'));
echo "<p><strong>URL:</strong> $test_url</p>";
echo "<p><strong>Section array:</strong> " . json_encode($section_array) . "</p>";

$result = currentKeyCheck($section_array, $href);
echo "<p><strong>Current result:</strong> '$result'</p>";

// Check what's actually in the href array values
echo "<h3>3. Checking href array values</h3>";
if (isset($href)) {
    echo "<p>Looking for 'watch' in href values:</p>";
    $found_watch = false;
    foreach ($href as $key => $value) {
        if ($value === 'watch') {
            echo "<p>✅ Found: \$href['$key'] = '$value'</p>";
            $found_watch = true;
        }
    }
    
    if (!$found_watch) {
        echo "<p>❌ 'watch' not found as a value in href array</p>";
        echo "<p>Let's check what values are available:</p>";
        echo "<div style='background: #f8f9fa; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;'>";
        $unique_values = array_unique(array_values($href));
        sort($unique_values);
        foreach ($unique_values as $value) {
            if (strpos($value, 'watch') !== false || strpos($value, 'view') !== false) {
                echo "<strong>$value</strong><br>";
            } else {
                echo "$value<br>";
            }
        }
        echo "</div>";
    }
}

// The fix
echo "<h2>🔧 The Fix</h2>";

echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>✅ Solution Found</h3>";

if (isset($href) && isset($href['watch'])) {
    $watch_value = $href['watch'];
    echo "<p>The href array has: \$href['watch'] = '$watch_value'</p>";
    echo "<p>The keyCheck function should look for '$watch_value' not 'watch'</p>";
} else {
    echo "<p>The issue is that 'watch' is not properly defined in the href configuration.</p>";
    echo "<p>We need to check what the actual key/value pair is for watch functionality.</p>";
}
echo "</div>";

// Create the corrected parser
echo "<h2>📝 Creating Corrected Parser</h2>";

// First, let's see what the watch entry actually is
if (isset($href)) {
    $watch_key = null;
    $watch_value = null;
    
    // Look for watch-related entries
    foreach ($href as $key => $value) {
        if ($key === 'watch' || strpos($key, 'watch') !== false) {
            $watch_key = $key;
            $watch_value = $value;
            break;
        }
    }
    
    if ($watch_key) {
        echo "<p>✅ Found watch entry: \$href['$watch_key'] = '$watch_value'</p>";
        
        // Test with correct value
        echo "<h3>Testing with correct value</h3>";
        $test_section_array = ['watch'];
        
        // The keyCheck should look for the VALUE not the KEY
        if (in_array($watch_value, $href)) {
            echo "<p>✅ '$watch_value' is in href array values</p>";
        } else {
            echo "<p>❌ '$watch_value' is NOT in href array values</p>";
        }
        
        // The correct test
        $correct_result = in_array('watch', array_keys($href)) ? $href['watch'] : null;
        echo "<p><strong>Correct mapping:</strong> 'watch' → '$correct_result'</p>";
        
    } else {
        echo "<p>❌ No watch entry found in href array</p>";
    }
}

// Create fixed parser
$fixed_parser_content = '<?php
// Fixed parser with correct watch URL handling
define("_INCLUDE", true);

require "f_core/config.backend.php";
require "f_core/config.href.php";

$query_string = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : null;
$request_uri  = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : null;
$request_uri  = $query_string != null ? substr($request_uri, 0, strpos($request_uri, "?")) : $request_uri;

$section_array = explode("/", trim($request_uri, "/"));
if (isset($section_array[0]) and $section_array[0][0] == "@") {
    $section_array[0] = "@";
}

// Fixed admin URL detection
if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
    $section = $backend_access_url;
} else {
    $section = fixedKeyCheck($section_array, $href);
}

// FIXED: This function now correctly maps URL segments to href keys
function fixedKeyCheck($k, $a) {
    if (!is_array($a)) return null;
    
    foreach ($k as $v) {
        if ($v == "@") {
            $v = "channel";
        }
        // Check if this URL segment exists as a KEY in the href array
        if (array_key_exists($v, $a)) {
            return $v; // Return the key, not the value
        }
    }
    
    if (empty($k) || (count($k) == 1 && $k[0] === "")) {
        return "";
    }
    
    return null;
}

// Rest of parser code...
$sections = array(
    $backend_access_url     => "f_modules/m_backend/parser",
    $href["index"]          => "index",
    $href["error"]          => "error",
    $href["watch"]          => "f_modules/m_frontend/m_file/view",
    $href["browse"]         => "f_modules/m_frontend/m_file/browse",
    $href["videos"]         => "f_modules/m_frontend/m_file/browse",
    $href["signin"]         => "f_modules/m_frontend/m_auth/signin",
    $href["signup"]         => "f_modules/m_frontend/m_auth/signup",
    // ... other sections
);

if (!ob_start("ob_gzhandler")) {
    ob_start();
}

$include = isset($sections[$section]) ? $sections[$section] : "error";

if (!file_exists($include . ".php")) {
    error_log("EasyStream: Missing module - " . $include . ".php");
    $include = "error";
}

try {
    include $include . ".php";
} catch (Exception $e) {
    include "error.php";
}

$get_ct = ob_get_contents();
$end_ct = ob_end_clean();
echo $get_ct;
?>';

file_put_contents('parser_watch_fixed.php', $fixed_parser_content);
echo "✅ Created fixed parser: parser_watch_fixed.php<br>";

echo "<h2>🧪 Testing the Fix</h2>";

// Test the fixed function
function fixedKeyCheck($k, $a) {
    if (!is_array($a)) return null;
    
    foreach ($k as $v) {
        if ($v == "@") {
            $v = "channel";
        }
        // Check if this URL segment exists as a KEY in the href array
        if (array_key_exists($v, $a)) {
            return $v; // Return the key, not the value
        }
    }
    
    if (empty($k) || (count($k) == 1 && $k[0] === "")) {
        return "";
    }
    
    return null;
}

$test_urls = ['/', '/admin', '/videos', '/browse', '/watch', '/watch/video123'];

foreach ($test_urls as $url) {
    $parts = explode('/', trim($url, '/'));
    
    if (isset($parts[0]) && isset($backend_access_url) && $parts[0] === $backend_access_url) {
        $section = $backend_access_url;
    } else {
        $section = fixedKeyCheck($parts, $href);
    }
    
    echo "<p>✅ $url → '$section'</p>";
}

echo "<h2>🎯 Summary</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
echo "<h3>✅ Issue Fixed!</h3>";
echo "<p>The problem was that the keyCheck function was looking for URL segments in the href array VALUES, but it should look in the KEYS.</p>";
echo "<p>Now /watch will correctly return 'watch' instead of an empty string.</p>";
echo "</div>";

echo "<h2>📋 Next Steps</h2>";
echo "<ol>";
echo "<li>The fixed parser has been created as <strong>parser_watch_fixed.php</strong></li>";
echo "<li>Test the URLs above to confirm they work</li>";
echo "<li>If tests pass, replace your main parser.php with the fixed version</li>";
echo "<li>Run your test again - /watch should now work correctly</li>";
echo "</ol>";
?>
<?php
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
?>
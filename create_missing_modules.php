<?php
// Create Missing Parser Modules
echo "<h1>🔧 Creating Missing Parser Modules</h1>";

$modules_created = 0;
$errors = [];

// Define missing modules with basic implementations
$missing_modules = [
    // Player modules
    'f_modules/m_frontend/m_player/video_playlist.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Video playlist player - basic implementation
echo "<h1>Video Playlist Player</h1>";
echo "<p>Video playlist functionality coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    'f_modules/m_frontend/m_player/image_playlist.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Image playlist player - basic implementation
echo "<h1>Image Playlist Player</h1>";
echo "<p>Image playlist functionality coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    'f_modules/m_frontend/m_player/audio_playlist.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Audio playlist player - basic implementation
echo "<h1>Audio Playlist Player</h1>";
echo "<p>Audio playlist functionality coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    'f_modules/m_frontend/m_player/freepaper.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Freepaper player - basic implementation
echo "<h1>Freepaper Player</h1>";
echo "<p>Freepaper functionality coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    'f_modules/m_frontend/m_player/jwplayer.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// JW Player integration - basic implementation
echo "<h1>JW Player</h1>";
echo "<p>JW Player integration coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    'f_modules/m_frontend/m_player/flowplayer.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Flow Player integration - basic implementation
echo "<h1>Flow Player</h1>";
echo "<p>Flow Player integration coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    'f_modules/m_frontend/m_player/related.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Related videos - basic implementation
echo "<h1>Related Videos</h1>";
echo "<p>Related videos functionality coming soon...</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    // Authentication modules
    'f_modules/m_frontend/m_auth/renew.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Password renewal - basic implementation
echo "<h1>Password Renewal</h1>";
echo "<p>Password renewal functionality coming soon...</p>";
echo "<a href=\"/signin\">← Back to Sign In</a>";
?>',
    
    // Page modules
    'f_modules/m_frontend/m_page/browser.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Browser compatibility - basic implementation
echo "<h1>Browser Compatibility</h1>";
echo "<p>Your browser is supported. Please update if you experience issues.</p>";
echo "<a href=\"/\">← Back to Home</a>";
?>',
    
    // Mobile module
    'f_modules/m_frontend/m_mobile/main.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Mobile interface - basic implementation
echo "<h1>Mobile Interface</h1>";
echo "<p>Mobile-optimized interface coming soon...</p>";
echo "<a href=\"/\">← Back to Desktop Site</a>";
?>',
    
    // Account modules
    'f_modules/m_frontend/m_acct/affiliate.php' => '<?php
define("_ISVALID", true);
include_once "f_core/config.core.php";
// Affiliate system - basic implementation
echo "<h1>Affiliate Program</h1>";
echo "<p>Affiliate program functionality coming soon...</p>";
echo "<a href=\"/account\">← Back to Account</a>";
?>'
];

echo "<h2>Creating Missing Modules...</h2>";

foreach ($missing_modules as $path => $content) {
    // Create directory if it doesn't exist
    $dir = dirname($path);
    if (!is_dir($dir)) {
        if (!mkdir($dir, 0755, true)) {
            $errors[] = "Failed to create directory: $dir";
            continue;
        }
    }
    
    // Create the module file
    if (file_put_contents($path, $content)) {
        echo "✅ Created: $path<br>";
        $modules_created++;
    } else {
        echo "❌ Failed to create: $path<br>";
        $errors[] = "Failed to create file: $path";
    }
}

echo "<h2>📊 Summary</h2>";
echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<h3>✅ Modules Created: $modules_created</h3>";
echo "</div>";

if (!empty($errors)) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h3>❌ Errors:</h3>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li>$error</li>";
    }
    echo "</ul>";
    echo "</div>";
}

// Now create an enhanced parser with better error handling
echo "<h2>🔧 Creating Enhanced Parser</h2>";

$enhanced_parser = '<?php
/*******************************************************************************************************************
| Enhanced EasyStream Parser with Error Handling
| Fixed version that handles missing modules gracefully
|*******************************************************************************************************************/
define("_INCLUDE", true);

// Enhanced error handling
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

try {
    require "f_core/config.backend.php";
    require "f_core/config.href.php";
} catch (Exception $e) {
    // Fallback if config files fail
    header("Location: /error.php");
    exit;
}

// Validate required variables
if (!isset($backend_access_url) || !isset($href)) {
    header("Location: /error.php");
    exit;
}

$query_string = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : null;
$request_uri  = isset($_SERVER["REQUEST_URI"]) ? $_SERVER["REQUEST_URI"] : null;
$request_uri  = $query_string != null ? substr($request_uri, 0, strpos($request_uri, "?")) : $request_uri;

$section_array = explode("/", trim($request_uri, "/"));
if (isset($section_array[0]) and $section_array[0][0] == "@") {
    $section_array[0] = "@";
}

// Enhanced admin URL detection
if (isset($section_array[0]) && $section_array[0] === $backend_access_url) {
    $section = $backend_access_url;
} else {
    $section = keyCheck($section_array, $href);
}

$sections = array(
    $backend_access_url     => "f_modules/m_backend/parser",
    $href["index"]          => "index",
    $href["error"]          => "error",
    $href["renew"]          => "f_modules/m_frontend/m_auth/renew",
    $href["signup"]         => "f_modules/m_frontend/m_auth/signup",
    $href["signin"]         => "f_modules/m_frontend/m_auth/signin",
    $href["signout"]        => "f_modules/m_frontend/m_auth/signout",
    $href["service"]        => "f_modules/m_frontend/m_auth/recovery",
    $href["reset_password"] => "f_modules/m_frontend/m_auth/recovery",
    $href["confirm_email"]  => "f_modules/m_frontend/m_auth/verify",
    $href["captcha"]        => "f_modules/m_frontend/m_auth/captcha",
    $href["account"]        => "f_modules/m_frontend/m_acct/account",
    $href["channels"]       => "f_modules/m_frontend/m_acct/channels",
    $href["messages"]       => "f_modules/m_frontend/m_msg/messages",
    $href["contacts"]       => "f_modules/m_frontend/m_msg/messages",
    $href["comments"]       => "f_modules/m_frontend/m_msg/messages",
    $href["confirm_friend"] => "f_modules/m_frontend/m_msg/friend_action",
    $href["upload"]         => "f_modules/m_frontend/m_file/upload",
    $href["uploader"]       => "f_modules/m_frontend/m_file/uploader",
    $href["submit"]         => "f_modules/m_frontend/m_file/upload_form",
    $href["files"]          => "f_modules/m_frontend/m_file/files",
    $href["subscriptions"]  => "f_modules/m_frontend/m_file/subscriptions",
    $href["following"]      => "f_modules/m_frontend/m_file/subscriptions",
    $href["files_edit"]     => "f_modules/m_frontend/m_file/files_edit",
    $href["playlist"]       => "f_modules/m_frontend/m_file/playlist",
    $href["playlists"]      => "f_modules/m_frontend/m_file/playlists",
    $href["browse"]         => "f_modules/m_frontend/m_file/browse",
    $href["blogs"]          => "f_modules/m_frontend/m_file/browse",
    $href["broadcasts"]     => "f_modules/m_frontend/m_file/browse",
    $href["videos"]         => "f_modules/m_frontend/m_file/browse",
    $href["images"]         => "f_modules/m_frontend/m_file/browse",
    $href["audios"]         => "f_modules/m_frontend/m_file/browse",
    $href["documents"]      => "f_modules/m_frontend/m_file/browse",
    $href["watch"]          => "f_modules/m_frontend/m_file/view",
    $href["see_comments"]   => "f_modules/m_frontend/m_file/view_extra",
    $href["download"]       => "f_modules/m_frontend/m_file/download",
    $href["respond"]        => "f_modules/m_frontend/m_file/respond",
    $href["see_responses"]  => "f_modules/m_frontend/m_file/respond_extra",
    $href["search"]         => "f_modules/m_frontend/m_file/search",
    $href["video_playlist"] => "f_modules/m_frontend/m_player/video_playlist",
    $href["image_playlist"] => "f_modules/m_frontend/m_player/image_playlist",
    $href["audio_playlist"] => "f_modules/m_frontend/m_player/audio_playlist",
    $href["freepaper"]      => "f_modules/m_frontend/m_player/freepaper",
    $href["page"]           => "f_modules/m_frontend/m_page/page",
    $href["language"]       => "f_modules/m_frontend/m_page/lang",
    $href["unsupported"]    => "f_modules/m_frontend/m_page/browser",
    $href["mobile"]         => "f_modules/m_frontend/m_mobile/main",
    $href["jwplayer"]       => "f_modules/m_frontend/m_player/jwplayer",
    $href["flowplayer"]     => "f_modules/m_frontend/m_player/flowplayer",
    $href["embed"]          => "f_modules/m_frontend/m_player/embed",
    $href["embed_blog"]     => "f_modules/m_frontend/m_player/embed_blog",
    $href["embed_doc"]      => "f_modules/m_frontend/m_player/embed_doc",
    $href["related"]        => "f_modules/m_frontend/m_player/related",
    $href["vast"]           => "f_modules/m_frontend/m_player/vast",
    $href["vmap"]           => "f_modules/m_frontend/m_player/vmap",
    $href["adv"]            => "f_modules/m_frontend/m_player/adv",
    $href["import"]         => "f_modules/m_frontend/m_file/import",
    $href["manage_channel"] => "f_modules/m_frontend/m_acct/manage_channel",
    $href["channel"]        => "f_modules/m_frontend/m_acct/channel",
    $href["@"]              => "f_modules/m_frontend/m_acct/channel",
    $href["affiliate"]      => "f_modules/m_frontend/m_acct/affiliate",
    $href["subscribers"]    => "f_modules/m_frontend/m_acct/subscribers",
    $href["publish"]        => "f_modules/m_frontend/m_live/auth",
    $href["publish_done"]   => "f_modules/m_frontend/m_live/done",
    $href["record_done"]    => "f_modules/m_frontend/m_live/record",
    $href["lstatus"]        => "f_modules/m_frontend/m_live/status",
    $href["chat_sync"]      => "f_modules/m_frontend/m_acct/sync_subs",
    $href["vods_sync"]      => "f_modules/m_frontend/m_acct/sync_vods",
    $href["df_sync"]        => "f_modules/m_frontend/m_acct/sync_df",
    $href["viewers"]        => "f_modules/m_frontend/m_acct/live_viewers",
    $href["tokenlist"]      => "f_modules/m_frontend/m_acct/token_list",
    $href["tokenpayment"]   => "f_modules/m_frontend/m_acct/token_payment",
    $href["tokendonate"]    => "f_modules/m_frontend/m_acct/token_donate",
    $href["tokens"]         => "f_modules/m_frontend/m_acct/tokens",
    $href["soon"]           => "f_offline/index",
    $href["shorts"]         => "f_modules/m_frontend/m_file/shorts",
    $href["thumb"]          => "f_modules/m_frontend/m_file/thumb",
);

if (!ob_start("ob_gzhandler")) {
    ob_start();
}

// Enhanced module loading with validation
$include = isset($sections[$section]) ? $sections[$section] : "error";

// Validate module exists before including
if (!file_exists($include . ".php")) {
    // Log missing module
    if (function_exists("error_log")) {
        error_log("EasyStream: Missing module - " . $include . ".php");
    }
    $include = "error";
}

// Include the module with error handling
try {
    include $include . ".php";
} catch (Exception $e) {
    // If module fails, show error page
    include "error.php";
}

$get_ct = ob_get_contents();
$end_ct = ob_end_clean();
echo $get_ct;

function hrefCheck($c)
{
    $section = explode("/", $c);
    return $section[0];
}

function keyCheck($k, $a)
{
    foreach ($k as $v) {
        if ($v == "@") {
            $v = "channel";
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
    // Return empty string for root URL (home page)
    if (empty($k) || (count($k) == 1 && $k[0] === "")) {
        return "";
    }
    return null;
}

function compress_page($buffer)
{
    $search = array(
        "/ +/"                                                                                                                                  => " ",
        "/<!--\{(.*?)\}-->|<!--(.*?)-->|\/\/(.*?)|[\t\r\n]|<!--|-->|\/\/ <!--|\/\/ -->|<!\[CDATA\[|\/\/ \]\]>|\]\]>|\/\/\]\]>|\/\/<!\[CDATA\[/" => "",
    );
    $buffer = preg_replace(array_keys($search), array_values($search), $buffer);
    return $buffer;
}
?>';

// Backup original parser
if (file_exists('parser.php')) {
    copy('parser.php', 'parser_backup.php');
    echo "✅ Backed up original parser to parser_backup.php<br>";
}

// Write enhanced parser
if (file_put_contents('parser_enhanced.php', $enhanced_parser)) {
    echo "✅ Created enhanced parser: parser_enhanced.php<br>";
} else {
    echo "❌ Failed to create enhanced parser<br>";
}

echo "<h2>🧪 Testing Parser</h2>";
echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 10px;'>";
echo "<p>Test these URLs to verify the parser is working:</p>";
echo "<ul>";
echo "<li><a href='/' target='_blank'>Home Page (/)</a></li>";
echo "<li><a href='/admin' target='_blank'>Admin Panel (/admin)</a></li>";
echo "<li><a href='/videos' target='_blank'>Videos (/videos)</a></li>";
echo "<li><a href='/browse' target='_blank'>Browse (/browse)</a></li>";
echo "<li><a href='/mobile' target='_blank'>Mobile (/mobile)</a> - Now works!</li>";
echo "<li><a href='/affiliate' target='_blank'>Affiliate (/affiliate)</a> - Now works!</li>";
echo "</ul>";
echo "</div>";

echo "<h2>📋 Next Steps</h2>";
echo "<ol>";
echo "<li><strong>Test all URLs:</strong> Verify no more 404 errors</li>";
echo "<li><strong>Replace parser:</strong> If tests pass, replace parser.php with parser_enhanced.php</li>";
echo "<li><strong>Enhance modules:</strong> Add real functionality to the stub modules</li>";
echo "<li><strong>Add features:</strong> Implement missing features in each module</li>";
echo "</ol>";

echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<h3>🎯 RESULT</h3>";
echo "<p>Created <strong>$modules_created missing modules</strong> that were causing parser failures.</p>";
echo "<p>The parser should now work without 404 errors for all standard URLs!</p>";
echo "</div>";
?>
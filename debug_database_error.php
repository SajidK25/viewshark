<?php
/*******************************************************************************************************************
| Database Error Debug
| Find the exact table/field causing the "Invalid table or field name" error
|*******************************************************************************************************************/

define('_ISVALID', true);

echo "<h1>Database Error Debug</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Database Connection: ✅ OK</h2>";
    
    // Check what tables exist
    echo "<h2>Existing Tables:</h2>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "<p class='ok'>✓ {$table}</p>";
    }
    
    // Check db_settings table structure specifically
    echo "<h2>db_settings Table Structure:</h2>";
    try {
        $stmt = $pdo->query("DESCRIBE db_settings");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($columns as $column) {
            echo "<p class='ok'>✓ Column: {$column['Field']} ({$column['Type']})</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ Error describing db_settings: " . $e->getMessage() . "</p>";
    }
    
    // Test the specific query that might be failing
    echo "<h2>Testing Common Queries:</h2>";
    
    // Test 1: Basic settings query
    try {
        $stmt = $pdo->prepare("SELECT cfg_value FROM db_settings WHERE cfg_name = ?");
        $stmt->execute(['main_url']);
        $result = $stmt->fetchColumn();
        echo "<p class='ok'>✓ Settings query works: main_url = " . ($result ?: 'NULL') . "</p>";
    } catch (Exception $e) {
        echo "<p class='error'>✗ Settings query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 2: getConfigurations query (this is likely where it fails)
    try {
        $config_keys = 'affiliate_module,live_module,live_server,live_uploads,live_cast,live_chat,live_chat_server,live_chat_salt,live_vod,live_vod_server,live_hls_server,live_del,thumbs_nr,mobile_module,mobile_detection,default_language,head_title,metaname_description,metaname_keywords,website_shortname,video_module,video_uploads,image_module,image_uploads,audio_module,audio_uploads,document_module,document_uploads,activity_logging,debug_mode,website_offline_mode,website_offline_message,internal_messaging,user_friends,user_blocking,channel_comments,file_comments,paid_memberships,user_subscriptions,file_playlists,public_channels,session_name,session_lifetime,date_timezone,google_analytics,google_webmaster,yahoo_explorer,bing_validate,backend_menu_toggle,benchmark_display,facebook_link,twitter_link,gplus_link,twitter_feed,blog_module,file_favorites,file_rating,file_history,file_watchlist,file_playlists,file_comments,file_responses,custom_tagline,user_follows,file_approval,video_player,audio_player,comment_emoji,social_media_links,user_tokens,import_yt,import_dm,import_vi,short_module,short_uploads,new_layout,channel_memberships,member_chat_only,member_badges';
        
        $keys = explode(',', $config_keys);
        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        
        $stmt = $pdo->prepare("SELECT cfg_name, cfg_value FROM db_settings WHERE cfg_name IN ({$placeholders})");
        $stmt->execute($keys);
        $results = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        echo "<p class='ok'>✓ getConfigurations query works: Found " . count($results) . " settings</p>";
        
        // Show missing configurations
        $missing = array_diff($keys, array_keys($results));
        if (!empty($missing)) {
            echo "<p class='info'>Missing configurations: " . implode(', ', array_slice($missing, 0, 10)) . (count($missing) > 10 ? '... and ' . (count($missing) - 10) . ' more' : '') . "</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ getConfigurations query failed: " . $e->getMessage() . "</p>";
    }
    
    // Test 3: Check if it's a different table
    echo "<h2>Testing Other Tables:</h2>";
    
    $test_tables = ['db_users', 'db_videofiles', 'db_banlist'];
    foreach ($test_tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "<p class='ok'>✓ {$table}: {$count} records</p>";
        } catch (Exception $e) {
            echo "<p class='error'>✗ {$table} failed: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Database connection failed: " . $e->getMessage() . "</p>";
}

// Now let's try to load the core step by step to pinpoint the exact error
echo "<h2>Step-by-Step Core Loading:</h2>";

try {
    echo "<p class='info'>Loading config.define.php...</p>";
    require_once 'f_core/config.define.php';
    echo "<p class='ok'>✓ config.define.php loaded</p>";
    
    echo "<p class='info'>Loading config.cache.php...</p>";
    require_once 'f_core/config.cache.php';
    echo "<p class='ok'>✓ config.cache.php loaded</p>";
    
    echo "<p class='info'>Loading config.set.php...</p>";
    require_once 'f_core/config.set.php';
    echo "<p class='ok'>✓ config.set.php loaded</p>";
    
    echo "<p class='info'>Loading config.href.php...</p>";
    require_once 'f_core/config.href.php';
    echo "<p class='ok'>✓ config.href.php loaded</p>";
    
    echo "<p class='info'>Loading config.folders.php...</p>";
    require_once 'f_core/config.folders.php';
    echo "<p class='ok'>✓ config.folders.php loaded</p>";
    
    echo "<p class='info'>Loading config.paging.php...</p>";
    require_once 'f_core/config.paging.php';
    echo "<p class='ok'>✓ config.paging.php loaded</p>";
    
    echo "<p class='info'>Loading config.footer.php...</p>";
    require_once 'f_core/config.footer.php';
    echo "<p class='ok'>✓ config.footer.php loaded</p>";
    
    echo "<p class='info'>Loading config.smarty.php...</p>";
    require_once 'f_core/config.smarty.php';
    echo "<p class='ok'>✓ config.smarty.php loaded</p>";
    
    echo "<p class='info'>Loading config.autoload.php...</p>";
    require_once 'f_core/config.autoload.php';
    echo "<p class='ok'>✓ config.autoload.php loaded</p>";
    
    echo "<p class='info'>Loading config.keys.php...</p>";
    require_once 'f_core/config.keys.php';
    echo "<p class='ok'>✓ config.keys.php loaded</p>";
    
    echo "<p class='info'>Loading config.logging.php...</p>";
    require_once 'f_core/config.logging.php';
    echo "<p class='ok'>✓ config.logging.php loaded</p>";
    
    echo "<p class='info'>Running VServer::var_check()...</p>";
    VServer::var_check();
    echo "<p class='ok'>✓ VServer::var_check() completed</p>";
    
    echo "<p class='info'>Loading database classes...</p>";
    require_once 'f_core/f_classes/class_adodb/adodb.inc.php';
    require_once 'f_core/f_classes/class_mobile/MobileDetect.php';
    require_once 'f_core/f_functions/functions.general.php';
    require_once 'f_core/f_functions/functions.security.php';
    require_once 'f_core/f_functions/functions.queue.php';
    echo "<p class='ok'>✓ Core functions loaded</p>";
    
    echo "<p class='info'>Initializing classes...</p>";
    $class_filter   = new VFilter;
    $class_language = new VLanguage;
    $class_redirect = new VRedirect;
    $class_smarty   = new VTemplate;
    $class_database = new VDatabase;
    echo "<p class='ok'>✓ Core classes initialized</p>";
    
    echo "<p class='info'>Connecting to database...</p>";
    $db = $class_database->dbConnection();
    echo "<p class='ok'>✓ Database connection established</p>";
    
    echo "<p class='info'>Loading configurations...</p>";
    $cfg = $class_database->getConfigurations('affiliate_module,live_module,video_module,session_name,session_lifetime,date_timezone');
    echo "<p class='ok'>✓ Configurations loaded: " . count($cfg) . " items</p>";
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Error at step: " . $e->getMessage() . "</p>";
    echo "<p class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
} catch (Error $e) {
    echo "<p class='error'>✗ Fatal error at step: " . $e->getMessage() . "</p>";
    echo "<p class='error'>File: " . $e->getFile() . " Line: " . $e->getLine() . "</p>";
}
?>
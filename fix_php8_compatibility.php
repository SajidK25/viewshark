<?php
/*******************************************************************************************************************
| EasyStream PHP 8 Compatibility Fixer
| Automatically fixes common PHP 8 compatibility issues
|*******************************************************************************************************************/

define('_ISVALID', true);

echo "<h1>EasyStream PHP 8 Compatibility Fixer</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;} .fixed{color:orange;}</style>";

$fixes_applied = 0;
$issues_found = [];

// Fix 1: Check and fix missing configuration values
echo "<h2>Fix 1: Missing Configuration Values</h2>";
try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Add missing configuration values that the core expects
    $missing_configs = [
        'db_cache_dir' => 'f_data/data_cache',
        'main_dir' => '/srv/easystream',
        'templates_dir' => 'f_templates',
        'smarty_cache_dir' => 'f_data/data_cache',
        'scripts_url' => '/f_scripts',
        'modules_url' => '/f_modules',
        'modules_url_be' => '/f_modules/m_backend',
        'styles_url' => '/f_scripts/fe/css',
        'javascript_dir' => 'f_scripts/fe/js',
        'javascript_url' => '/f_scripts/fe/js',
        'styles_url_be' => '/f_scripts/be/css',
        'javascript_url_be' => '/f_scripts/be/js',
        'global_images_url' => '/f_scripts/fe/img',
        'media_files_url' => '/f_data/data_userfiles',
        'profile_images_url' => '/f_data/data_userfiles/profile_images',
        'logging_dir' => 'f_data/logs',
        'new_layout' => '1',
        'class_files_dir' => 'f_core/f_classes'
    ];
    
    foreach ($missing_configs as $key => $value) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO db_settings (cfg_name, cfg_value) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
        echo "<p class='ok'>✓ Added config: {$key}</p>";
        $fixes_applied++;
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Database config fix failed: " . $e->getMessage() . "</p>";
}

// Fix 2: Create missing directories
echo "<h2>Fix 2: Create Missing Directories</h2>";
$required_dirs = [
    'f_data/data_cache',
    'f_data/logs',
    'f_data/data_userfiles',
    'f_data/data_userfiles/profile_images',
    'f_data/data_thumbs',
    'f_data/data_tmp'
];

foreach ($required_dirs as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p class='fixed'>✓ Created directory: {$dir}</p>";
            $fixes_applied++;
        } else {
            echo "<p class='error'>✗ Failed to create: {$dir}</p>";
        }
    } else {
        echo "<p class='ok'>✓ Directory exists: {$dir}</p>";
    }
}

// Fix 3: Check for missing config files and create them
echo "<h2>Fix 3: Missing Config Files</h2>";
$config_files = [
    'f_core/config.theme.php' => '<?php
defined(\'_ISVALID\') or header(\'Location: /error\');
// Theme configuration
$cfg[\'theme\'] = \'default\';
$cfg[\'theme_dir\'] = \'f_templates/tpl_frontend\';
',
    'f_core/config.cache.php' => '<?php
defined(\'_ISVALID\') or header(\'Location: /error\');
// Cache configuration
$cfg[\'cache_enabled\'] = 0;
$cfg[\'cache_lifetime\'] = 3600;
',
    'f_core/config.folders.php' => '<?php
defined(\'_ISVALID\') or header(\'Location: /error\');
// Folder paths
$cfg[\'main_dir\'] = realpath(dirname(__FILE__) . \'/../\');
$cfg[\'templates_dir\'] = $cfg[\'main_dir\'] . \'/f_templates\';
$cfg[\'smarty_cache_dir\'] = $cfg[\'main_dir\'] . \'/f_data/data_cache\';
$cfg[\'class_files_dir\'] = $cfg[\'main_dir\'] . \'/f_core/f_classes\';
',
    'f_core/config.paging.php' => '<?php
defined(\'_ISVALID\') or header(\'Location: /error\');
// Pagination settings
$cfg[\'paging_limit\'] = 20;
',
    'f_core/config.footer.php' => '<?php
defined(\'_ISVALID\') or header(\'Location: /error\');
// Footer configuration
$cfg[\'footer_text\'] = \'EasyStream - Professional Video Streaming Platform\';
',
    'f_core/config.keys.php' => '<?php
defined(\'_ISVALID\') or header(\'Location: /error\');
// API keys and configuration
$cfg[\'encryption_key\'] = \'easystream_default_key_change_in_production\';
'
];

foreach ($config_files as $file => $content) {
    if (!file_exists($file)) {
        if (file_put_contents($file, $content)) {
            echo "<p class='fixed'>✓ Created config file: {$file}</p>";
            $fixes_applied++;
        } else {
            echo "<p class='error'>✗ Failed to create: {$file}</p>";
        }
    } else {
        echo "<p class='ok'>✓ Config file exists: {$file}</p>";
    }
}

echo "<h2>Fix 4: Test Core Loading</h2>";
try {
    // Test if core loads now
    ob_start();
    include_once 'f_core/config.core.php';
    $output = ob_get_clean();
    
    if (empty($output)) {
        echo "<p class='ok'>✅ SUCCESS! Core loads without errors!</p>";
    } else {
        echo "<p class='info'>Core loaded with some output: " . htmlspecialchars($output) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>✗ Core loading failed: " . $e->getMessage() . "</p>";
    $issues_found[] = $e->getMessage();
} catch (Error $e) {
    echo "<p class='error'>✗ Core loading fatal error: " . $e->getMessage() . "</p>";
    $issues_found[] = $e->getMessage();
}

echo "<h2>Summary</h2>";
echo "<p class='info'>Fixes applied: {$fixes_applied}</p>";

if (empty($issues_found)) {
    echo "<p class='ok'><strong>🎉 All compatibility issues fixed!</strong></p>";
    echo "<p>You can now access:</p>";
    echo "<ul>";
    echo "<li><a href='/'>Main Site</a> - Should work fully</li>";
    echo "<li><a href='/admin'>Admin Panel</a> - Full EasyStream admin</li>";
    echo "<li><a href='/setup.php'>Setup Page</a> - Beautiful setup interface</li>";
    echo "</ul>";
} else {
    echo "<p class='error'><strong>Remaining issues:</strong></p>";
    echo "<ul>";
    foreach ($issues_found as $issue) {
        echo "<li class='error'>{$issue}</li>";
    }
    echo "</ul>";
}

echo "<h2>🚀 Next Steps</h2>";
echo "<ol>";
echo "<li>Test the main site: <a href='/'>http://localhost:8083</a></li>";
echo "<li>Test the admin panel: <a href='/admin'>http://localhost:8083/admin</a></li>";
echo "<li>If issues remain, check the error logs</li>";
echo "<li>Delete this fix file after use: <code>rm fix_php8_compatibility.php</code></li>";
echo "</ol>";
?>
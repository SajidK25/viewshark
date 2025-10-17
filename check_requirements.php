<?php
/*******************************************************************************************************************
| EasyStream Requirements Check
| Verifies all required PHP extensions and configurations are available
|*******************************************************************************************************************/

define('_ISVALID', true);

echo "<h1>EasyStream Requirements Check</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .warning{color:orange;}</style>";

$errors = [];
$warnings = [];

// Check PHP version
echo "<h2>PHP Version</h2>";
if (version_compare(PHP_VERSION, '7.4.0', '>=')) {
    echo "<p class='ok'>✓ PHP " . PHP_VERSION . " (OK)</p>";
} else {
    echo "<p class='error'>✗ PHP " . PHP_VERSION . " (Requires 7.4+)</p>";
    $errors[] = 'PHP version too old';
}

// Check required extensions
echo "<h2>Required Extensions</h2>";
$required_extensions = [
    'pdo' => 'Database connectivity',
    'pdo_mysql' => 'MySQL database support',
    'json' => 'JSON processing',
    'mbstring' => 'Multi-byte string support',
    'curl' => 'HTTP requests',
    'gd' => 'Image processing',
    'session' => 'Session management'
];

foreach ($required_extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "<p class='ok'>✓ {$ext} - {$desc}</p>";
    } else {
        echo "<p class='error'>✗ {$ext} - {$desc} (MISSING)</p>";
        $errors[] = "Missing extension: {$ext}";
    }
}

// Check optional but recommended extensions
echo "<h2>Recommended Extensions</h2>";
$recommended_extensions = [
    'redis' => 'Queue system and caching',
    'imagick' => 'Advanced image processing',
    'opcache' => 'PHP performance optimization'
];

foreach ($recommended_extensions as $ext => $desc) {
    if (extension_loaded($ext)) {
        echo "<p class='ok'>✓ {$ext} - {$desc}</p>";
    } else {
        echo "<p class='warning'>⚠ {$ext} - {$desc} (RECOMMENDED)</p>";
        $warnings[] = "Missing recommended extension: {$ext}";
    }
}

// Check directory permissions
echo "<h2>Directory Permissions</h2>";
$directories = [
    'f_data/logs' => 'Log files',
    'f_data/data_userfiles' => 'User uploaded files',
    'f_data/data_thumbs' => 'Thumbnail cache',
    'f_data/data_tmp' => 'Temporary files'
];

foreach ($directories as $dir => $desc) {
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            echo "<p class='ok'>✓ {$dir} - {$desc} (Writable)</p>";
        } else {
            echo "<p class='error'>✗ {$dir} - {$desc} (NOT WRITABLE)</p>";
            $errors[] = "Directory not writable: {$dir}";
        }
    } else {
        echo "<p class='warning'>⚠ {$dir} - {$desc} (DOES NOT EXIST)</p>";
        $warnings[] = "Directory missing: {$dir}";
    }
}

// Summary
echo "<h2>Summary</h2>";
if (empty($errors)) {
    echo "<p class='ok'><strong>✓ All critical requirements met!</strong></p>";
} else {
    echo "<p class='error'><strong>✗ Critical issues found:</strong></p>";
    echo "<ul>";
    foreach ($errors as $error) {
        echo "<li class='error'>{$error}</li>";
    }
    echo "</ul>";
}

if (!empty($warnings)) {
    echo "<p class='warning'><strong>⚠ Warnings (recommended fixes):</strong></p>";
    echo "<ul>";
    foreach ($warnings as $warning) {
        echo "<li class='warning'>{$warning}</li>";
    }
    echo "</ul>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Fix any critical errors above</li>";
echo "<li>Run: <code>docker-compose up -d</code> to start all services</li>";
echo "<li>Import database: <code>docker exec -i vs-db mysql -u easystream -p easystream < __install/easystream.sql</code></li>";
echo "<li>Run missing tables: <code>docker exec -i vs-db mysql -u easystream -p easystream < deploy/create_missing_tables.sql</code></li>";
echo "<li>Check queue worker: <code>docker logs vs-queue-worker</code></li>";
echo "</ol>";
?>
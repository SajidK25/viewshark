<?php
echo "<h1>Step-by-Step Debug</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

echo "<h2>Step 1: Basic PHP</h2>";
echo "<p class='ok'>✓ PHP is working</p>";

echo "<h2>Step 2: Define _ISVALID</h2>";
define('_ISVALID', true);
echo "<p class='ok'>✓ _ISVALID defined</p>";

echo "<h2>Step 3: Test config.define.php</h2>";
try {
    require_once 'f_core/config.define.php';
    echo "<p class='ok'>✓ config.define.php loaded</p>";
    echo "<p class='info'>REQUEST_LOG defined: " . (defined('REQUEST_LOG') ? 'YES' : 'NO') . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ config.define.php failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ config.define.php fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 4: Test config.cache.php</h2>";
try {
    require_once 'f_core/config.cache.php';
    echo "<p class='ok'>✓ config.cache.php loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ config.cache.php failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ config.cache.php fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 5: Test config.set.php</h2>";
try {
    require_once 'f_core/config.set.php';
    echo "<p class='ok'>✓ config.set.php loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ config.set.php failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ config.set.php fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 6: Test config.autoload.php</h2>";
try {
    require_once 'f_core/config.autoload.php';
    echo "<p class='ok'>✓ config.autoload.php loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ config.autoload.php failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ config.autoload.php fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 7: Test VServer class loading</h2>";
try {
    $test = new VServer();
    echo "<p class='ok'>✓ VServer class can be instantiated</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ VServer class failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ VServer class fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 8: Test VServer::var_check()</h2>";
try {
    VServer::var_check();
    echo "<p class='ok'>✓ VServer::var_check() executed successfully</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ VServer::var_check() failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ VServer::var_check() fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 9: Test other config files</h2>";
$configs = [
    'f_core/config.href.php',
    'f_core/config.folders.php', 
    'f_core/config.paging.php',
    'f_core/config.footer.php',
    'f_core/config.smarty.php',
    'f_core/config.keys.php',
    'f_core/config.logging.php'
];

foreach ($configs as $config) {
    try {
        if (file_exists($config)) {
            require_once $config;
            echo "<p class='ok'>✓ {$config} loaded</p>";
        } else {
            echo "<p class='error'>✗ {$config} not found</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>✗ {$config} failed: " . $e->getMessage() . "</p>";
        break;
    } catch (Error $e) {
        echo "<p class='error'>✗ {$config} fatal: " . $e->getMessage() . "</p>";
        break;
    }
}

echo "<h2>Step 10: Test database config</h2>";
try {
    require_once 'f_core/config.database.php';
    echo "<p class='ok'>✓ config.database.php loaded</p>";
    echo "<p class='info'>DB_HOST: " . (getenv('DB_HOST') ?: 'NOT SET') . "</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ config.database.php failed: " . $e->getMessage() . "</p>";
} catch (Error $e) {
    echo "<p class='error'>✗ config.database.php fatal: " . $e->getMessage() . "</p>";
}

echo "<h2>All Steps Completed!</h2>";
echo "<p class='ok'>If you see this message, all core files are loading properly.</p>";
?>
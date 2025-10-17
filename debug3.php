<?php
echo "<h1>Smarty Fix Test</h1>";
echo "<style>body{font-family:Arial,sans-serif;margin:20px;} .ok{color:green;} .error{color:red;} .info{color:blue;}</style>";

define('_ISVALID', true);

echo "<h2>Step 1: Load core configs</h2>";
try {
    require_once 'f_core/config.define.php';
    require_once 'f_core/config.cache.php';
    require_once 'f_core/config.set.php';
    require_once 'f_core/config.href.php';
    require_once 'f_core/config.folders.php';
    require_once 'f_core/config.paging.php';
    require_once 'f_core/config.footer.php';
    require_once 'f_core/config.autoload.php';
    echo "<p class='ok'>✓ Core configs loaded</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Core config failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 2: Test Smarty config</h2>";
try {
    require_once 'f_core/config.smarty.php';
    echo "<p class='ok'>✓ Smarty config loaded successfully!</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ Smarty config failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ Smarty config fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Step 3: Test full config.core.php</h2>";
try {
    require_once 'f_core/config.core.php';
    echo "<p class='ok'>✓ Full config.core.php loaded successfully!</p>";
} catch (Exception $e) {
    echo "<p class='error'>✗ config.core.php failed: " . $e->getMessage() . "</p>";
    exit;
} catch (Error $e) {
    echo "<p class='error'>✗ config.core.php fatal: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>Success!</h2>";
echo "<p class='ok'>All core files are now loading properly. You should be able to access setup.php and admin panel.</p>";

echo "<h2>Test Links</h2>";
echo "<p><a href='/setup.php'>setup.php</a> - Should now work</p>";
echo "<p><a href='/admin'>admin panel</a> - Should redirect to setup</p>";
echo "<p><a href='/index.php'>main site</a> - Should work</p>";
?>
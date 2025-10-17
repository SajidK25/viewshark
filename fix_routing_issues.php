<?php
// Fix common routing issues
echo "<h1>🔧 EasyStream Routing Fix</h1>";

// Fix 1: Check and fix parser.php
echo "<h2>1. Fixing parser.php</h2>";
if (file_exists('parser.php')) {
    $parser_content = file_get_contents('parser.php');
    
    // Check if the index mapping exists
    if (strpos($parser_content, '$href["index"]') === false) {
        echo "❌ Index mapping missing in parser.php<br>";
        
        // Add the index mapping
        $old_pattern = '$sections = array(
    $backend_access_url     => \'f_modules/m_backend/parser\',
    $href["error"]          => \'error\',';
        
        $new_pattern = '$sections = array(
    $backend_access_url     => \'f_modules/m_backend/parser\',
    $href["index"]          => \'index\',
    $href["error"]          => \'error\',';
        
        if (strpos($parser_content, $old_pattern) !== false) {
            $parser_content = str_replace($old_pattern, $new_pattern, $parser_content);
            file_put_contents('parser.php', $parser_content);
            echo "✅ Added index mapping to parser.php<br>";
        } else {
            echo "⚠️ Could not automatically fix parser.php - manual intervention needed<br>";
        }
    } else {
        echo "✅ Index mapping exists in parser.php<br>";
    }
    
    // Check keyCheck function
    if (strpos($parser_content, 'return null;') === false) {
        echo "❌ keyCheck function needs fixing<br>";
        
        $old_keycheck = 'function keyCheck($k, $a)
{
    foreach ($k as $v) {
        if ($v == \'@\') {
            $v = \'channel\';
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
}';
        
        $new_keycheck = 'function keyCheck($k, $a)
{
    foreach ($k as $v) {
        if ($v == \'@\') {
            $v = \'channel\';
        }
        if (in_array($v, $a)) {
            return $v;
        }
    }
    // Return empty string for root URL (home page)
    if (empty($k) || (count($k) == 1 && $k[0] === \'\')) {
        return \'\';
    }
    return null;
}';
        
        if (strpos($parser_content, $old_keycheck) !== false) {
            $parser_content = str_replace($old_keycheck, $new_keycheck, $parser_content);
            file_put_contents('parser.php', $parser_content);
            echo "✅ Fixed keyCheck function<br>";
        } else {
            echo "⚠️ Could not automatically fix keyCheck function<br>";
        }
    } else {
        echo "✅ keyCheck function looks good<br>";
    }
} else {
    echo "❌ parser.php not found<br>";
}

// Fix 2: Create environment variables file if missing
echo "<h2>2. Checking Environment Variables</h2>";
$env_vars = ['DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PASS'];
$missing_vars = [];

foreach ($env_vars as $var) {
    if (!getenv($var)) {
        $missing_vars[] = $var;
    }
}

if (!empty($missing_vars)) {
    echo "❌ Missing environment variables: " . implode(', ', $missing_vars) . "<br>";
    
    // Create a sample .env file
    $env_content = "# EasyStream Environment Variables
# Copy this to your system environment or use with docker-compose

DB_HOST=localhost
DB_NAME=easystream
DB_USER=easystream_user
DB_PASS=your_password_here

# For Docker users, these are typically set in docker-compose.yml
# For local development, set these in your system environment
";
    
    file_put_contents('.env.example', $env_content);
    echo "✅ Created .env.example file with sample configuration<br>";
    echo "⚠️ Please set your actual database credentials in your environment<br>";
} else {
    echo "✅ All environment variables are set<br>";
}

// Fix 3: Check .htaccess
echo "<h2>3. Checking .htaccess</h2>";
if (file_exists('.htaccess')) {
    $htaccess_content = file_get_contents('.htaccess');
    
    if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
        echo "✅ .htaccess exists and has RewriteEngine On<br>";
    } else {
        echo "❌ .htaccess missing RewriteEngine directive<br>";
    }
    
    if (strpos($htaccess_content, 'parser.php') !== false) {
        echo "✅ .htaccess has parser.php rewrite rule<br>";
    } else {
        echo "❌ .htaccess missing parser.php rewrite rule<br>";
    }
} else {
    echo "❌ .htaccess file not found<br>";
}

// Fix 4: Test database connection
echo "<h2>4. Testing Database Connection</h2>";
$cfg_dbhost = getenv('DB_HOST') ?: 'localhost';
$cfg_dbname = getenv('DB_NAME') ?: '';
$cfg_dbuser = getenv('DB_USER') ?: '';
$cfg_dbpass = getenv('DB_PASS') ?: '';

if ($cfg_dbhost && $cfg_dbname && $cfg_dbuser) {
    try {
        $connection = @new mysqli($cfg_dbhost, $cfg_dbuser, $cfg_dbpass, $cfg_dbname);
        if ($connection->connect_error) {
            echo "❌ Database connection failed: " . $connection->connect_error . "<br>";
            echo "💡 Try running: <code>docker-compose up -d</code> to start the database<br>";
        } else {
            echo "✅ Database connection successful<br>";
            $connection->close();
        }
    } catch (Exception $e) {
        echo "❌ Database connection error: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ Database credentials not configured<br>";
    echo "💡 Set environment variables: DB_HOST, DB_NAME, DB_USER, DB_PASS<br>";
}

echo "<h2>5. Quick Access Links</h2>";
echo "<div style='background: #f0f8ff; padding: 15px; border-left: 4px solid #007cba;'>";
echo "<p>Try these direct access links:</p>";
echo "<ul>";
echo "<li><a href='/working_index.php' target='_blank'>Working Index Page</a> - Bypasses routing</li>";
echo "<li><a href='/admin_direct.php' target='_blank'>Direct Admin Access</a> - Bypasses routing</li>";
echo "<li><a href='/test_core.php' target='_blank'>Core System Test</a> - Tests core functionality</li>";
echo "<li><a href='/full_diagnostic.php' target='_blank'>Full Diagnostic</a> - Complete system check</li>";
echo "</ul>";
echo "</div>";

echo "<h2>6. Next Steps</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
echo "<ol>";
echo "<li>If database connection fails, start Docker: <code>docker-compose up -d</code></li>";
echo "<li>If routing still fails, check Apache mod_rewrite is enabled</li>";
echo "<li>Try accessing the working_index.php page directly</li>";
echo "<li>Use admin_direct.php for admin panel access</li>";
echo "<li>Run setup.php if the system hasn't been set up yet</li>";
echo "</ol>";
echo "</div>";
?>
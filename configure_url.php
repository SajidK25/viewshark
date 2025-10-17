<?php
// URL Configuration Script for EasyStream
echo "<h1>🔧 EasyStream URL Configuration</h1>";

$config_updated = false;
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['main_url'])) {
    $new_url = trim($_POST['main_url']);
    
    // Validate URL
    if (empty($new_url)) {
        $error_message = 'URL cannot be empty';
    } elseif (!filter_var($new_url, FILTER_VALIDATE_URL)) {
        $error_message = 'Please enter a valid URL (e.g., http://localhost:8083 or https://yourdomain.com)';
    } else {
        // Update config.set.php
        if (file_exists('f_core/config.set.php')) {
            $config_content = file_get_contents('f_core/config.set.php');
            $pattern = '/\$cfg\[\'main_url\'\]\s*=\s*getenv\(\'MAIN_URL\'\)\s*\?\:\s*\'[^\']*\';/';
            $replacement = "\$cfg['main_url'] = getenv('MAIN_URL') ?: '$new_url';";
            
            if (preg_match($pattern, $config_content)) {
                $config_content = preg_replace($pattern, $replacement, $config_content);
                file_put_contents('f_core/config.set.php', $config_content);
                $config_updated = true;
            } else {
                $error_message = 'Could not find main_url configuration in config.set.php';
            }
        } else {
            $error_message = 'Configuration file f_core/config.set.php not found';
        }
        
        // Update docker-compose.yml if it exists
        if ($config_updated && file_exists('docker-compose.yml')) {
            $docker_content = file_get_contents('docker-compose.yml');
            $docker_content = preg_replace('/MAIN_URL:\s*[^\s\n]+/', "MAIN_URL: $new_url", $docker_content);
            $docker_content = preg_replace('/CRON_BASE_URL:\s*[^\s\n]+/', "CRON_BASE_URL: $new_url", $docker_content);
            file_put_contents('docker-compose.yml', $docker_content);
        }
        
        // Create .env file for easy reference
        if ($config_updated) {
            $env_content = "# EasyStream Configuration
MAIN_URL=$new_url
DB_HOST=db
DB_NAME=easystream
DB_USER=easystream
DB_PASS=easystream
REDIS_HOST=redis
REDIS_PORT=6379
";
            file_put_contents('.env.example', $env_content);
        }
    }
}

// Get current URL
$current_url = 'http://localhost:8083'; // default
if (file_exists('f_core/config.set.php')) {
    $config_content = file_get_contents('f_core/config.set.php');
    if (preg_match('/\$cfg\[\'main_url\'\]\s*=\s*getenv\(\'MAIN_URL\'\)\s*\?\:\s*\'([^\']*)\';/', $config_content, $matches)) {
        $current_url = $matches[1];
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EasyStream URL Configuration</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input[type="url"] {
            width: 100%;
            padding: 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        input[type="url"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #17a2b8;
        }
        .examples {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .examples h3 {
            margin-bottom: 15px;
            color: #333;
        }
        .examples ul {
            list-style: none;
            padding: 0;
        }
        .examples li {
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .examples li:last-child {
            border-bottom: none;
        }
        .examples code {
            background: #e9ecef;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 EasyStream URL Configuration</h1>
        
        <?php if ($config_updated): ?>
            <div class="success">
                <h3>✅ Configuration Updated Successfully!</h3>
                <p>Your EasyStream installation is now configured to use: <strong><?= htmlspecialchars($new_url) ?></strong></p>
                <p>If using Docker, restart your containers: <code>docker-compose down && docker-compose up -d</code></p>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="error">
                <h3>❌ Error</h3>
                <p><?= htmlspecialchars($error_message) ?></p>
            </div>
        <?php endif; ?>
        
        <div class="info">
            <h3>📋 Current Configuration</h3>
            <p>Current URL: <strong><?= htmlspecialchars($current_url) ?></strong></p>
            <p>This URL is used for redirects, email links, and API callbacks.</p>
        </div>
        
        <form method="POST">
            <div class="form-group">
                <label for="main_url">Main URL for EasyStream:</label>
                <input type="url" id="main_url" name="main_url" value="<?= htmlspecialchars($current_url) ?>" 
                       placeholder="http://localhost:8083" required>
            </div>
            
            <button type="submit" class="btn">Update Configuration</button>
        </form>
        
        <div class="examples">
            <h3>📝 URL Examples</h3>
            <ul>
                <li><strong>Local Development:</strong> <code>http://localhost:8083</code></li>
                <li><strong>Local Network:</strong> <code>http://192.168.1.100:8083</code></li>
                <li><strong>Custom Domain:</strong> <code>https://yourdomain.com</code></li>
                <li><strong>Subdomain:</strong> <code>https://stream.yourdomain.com</code></li>
                <li><strong>Custom Port:</strong> <code>http://yourdomain.com:8083</code></li>
            </ul>
        </div>
        
        <div class="info">
            <h3>🐳 Docker Users</h3>
            <p>After updating the URL, restart your containers:</p>
            <p><code>docker-compose down && docker-compose up -d</code></p>
            <p>The configuration will be automatically applied to all services.</p>
        </div>
        
        <div class="info">
            <h3>🔗 Quick Links</h3>
            <p>After configuration:</p>
            <ul style="list-style: none; padding: 0;">
                <li>🏠 <a href="/" target="_blank">Home Page</a></li>
                <li>🔧 <a href="/admin" target="_blank">Admin Panel</a></li>
                <li>📊 <a href="/working_index.php" target="_blank">Working Index</a></li>
                <li>🧪 <a href="/test_core.php" target="_blank">System Test</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
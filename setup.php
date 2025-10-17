<?php
/*******************************************************************************************************************
| EasyStream Setup
| Simple setup page for end users
|*******************************************************************************************************************/

define('_ISVALID', true);

// Include the core configuration to use existing database connection
if (file_exists('f_core/config.core.php')) {
    include_once 'f_core/config.core.php';
} else {
    die('EasyStream core files not found. Please ensure all files are uploaded correctly.');
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>EasyStream Setup</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container { 
            max-width: 500px; 
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            animation: slideUp 0.6s ease-out;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .logo {
            text-align: center;
            margin-bottom: 40px;
        }
        .logo h1 {
            font-size: 2.5em;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            font-weight: 700;
        }
        .logo p {
            color: #666;
            font-size: 1.1em;
            font-weight: 300;
        }
        .success, .error, .info {
            padding: 15px 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid;
            animation: fadeIn 0.5s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .success { 
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left-color: #28a745;
        }
        .error { 
            background: linear-gradient(135deg, #f8d7da, #f1b0b7);
            color: #721c24;
            border-left-color: #dc3545;
        }
        .info { 
            background: linear-gradient(135deg, #d1ecf1, #bee5eb);
            color: #0c5460;
            border-left-color: #17a2b8;
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.95em;
        }
        .form-control {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e5e9;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #fff;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }
        .btn:active {
            transform: translateY(0);
        }
        .section-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        .admin-link {
            display: inline-block;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 12px 25px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 10px 5px;
        }
        .admin-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
            color: white;
            text-decoration: none;
        }
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        .feature-item {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 15px;
            border-radius: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }
        .feature-item .icon {
            font-size: 2em;
            margin-bottom: 10px;
        }
        .feature-item h4 {
            color: #495057;
            margin-bottom: 5px;
        }
        .feature-item p {
            color: #6c757d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1>🎬 EasyStream</h1>
            <p>Professional Video Streaming Platform</p>
        </div>

<?php
$setup_complete = false;

try {
    // Check if database connection exists
    if (!isset($class_database)) {
        throw new Exception("Database connection not available. Please check your configuration.");
    }

    // Check if setup is already complete
    $stmt = $class_database->execute("SELECT cfg_value FROM db_settings WHERE cfg_name = 'setup_complete'");
    $setup_status = $stmt ? $stmt->fetchColumn() : false;
    
    if ($setup_status === '1') {
        $setup_complete = true;
        echo '<div class="success">✅ EasyStream is already set up and ready to use!</div>';
        echo '<div class="info">';
        echo '<h3 style="margin-bottom: 20px;">🚀 Your Platform is Ready</h3>';
        echo '<div class="feature-grid">';
        echo '<div class="feature-item"><div class="icon">🎥</div><h4>Video Streaming</h4><p>Upload & stream videos</p></div>';
        echo '<div class="feature-item"><div class="icon">📺</div><h4>Live Broadcasting</h4><p>RTMP streaming support</p></div>';
        echo '<div class="feature-item"><div class="icon">👥</div><h4>User Management</h4><p>Complete user system</p></div>';
        echo '<div class="feature-item"><div class="icon">⚡</div><h4>Queue System</h4><p>Background processing</p></div>';
        echo '</div>';
        echo '<div style="text-align: center; margin-top: 30px;">';
        echo '<a href="/admin" class="admin-link">🔧 Access Admin Panel</a>';
        echo '<a href="/" class="admin-link" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">🏠 View Site</a>';
        echo '</div>';
        echo '<p style="text-align: center; margin-top: 20px; color: #666;"><em>Default login: admin / admin123</em></p>';
        echo '</div>';
    }

    // Handle setup form submission
    if (!$setup_complete && $_POST['complete_setup']) {
        $admin_user = trim($_POST['admin_username']) ?: 'admin';
        $admin_pass = trim($_POST['admin_password']) ?: 'admin123';
        $admin_email = trim($_POST['admin_email']) ?: 'admin@easystream.local';
        $site_name = trim($_POST['site_name']) ?: 'EasyStream';
        $main_url = trim($_POST['main_url']) ?: 'http://localhost:8083';
        
        if (empty($admin_user) || empty($admin_pass) || empty($admin_email) || empty($main_url)) {
            throw new Exception("All fields are required.");
        }
        
        if (!filter_var($main_url, FILTER_VALIDATE_URL)) {
            throw new Exception("Please enter a valid URL for the site URL.");
        }

        // Update settings
        $settings = [
            'backend_username' => $admin_user,
            'backend_password' => $admin_pass,
            'backend_email' => $admin_email,
            'site_name' => $site_name,
            'main_url' => $main_url,
            'setup_complete' => '1'
        ];
        
        // Update config.set.php with the new URL
        if (file_exists('f_core/config.set.php')) {
            $config_content = file_get_contents('f_core/config.set.php');
            $pattern = '/\$cfg\[\'main_url\'\]\s*=\s*getenv\(\'MAIN_URL\'\)\s*\?\:\s*\'[^\']*\';/';
            $replacement = "\$cfg['main_url'] = getenv('MAIN_URL') ?: '$main_url';";
            $config_content = preg_replace($pattern, $replacement, $config_content);
            file_put_contents('f_core/config.set.php', $config_content);
        }

        foreach ($settings as $key => $value) {
            $class_database->execute("INSERT INTO db_settings (cfg_name, cfg_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE cfg_value = ?", [$key, $value, $value]);
        }

        // Auto-delete setup file for security
        $setup_file_deleted = false;
        if (file_exists(__FILE__)) {
            $setup_file_deleted = @unlink(__FILE__);
        }

        echo '<div class="success">🎉 Congratulations! EasyStream is now ready to use!</div>';
        if ($setup_file_deleted) {
            echo '<div class="success">🔒 Setup secured - installation file removed automatically.</div>';
        }
        echo '<div class="info">';
        echo '<h3 style="text-align: center; margin-bottom: 25px;">🚀 Your Streaming Platform is Live!</h3>';
        echo '<div class="feature-grid">';
        echo '<div class="feature-item"><div class="icon">🎬</div><h4>Ready to Stream</h4><p>Upload your first video</p></div>';
        echo '<div class="feature-item"><div class="icon">⚙️</div><h4>Admin Access</h4><p>Configure your platform</p></div>';
        echo '<div class="feature-item"><div class="icon">👤</div><h4>Your Account</h4><p>' . htmlspecialchars($admin_user) . '</p></div>';
        echo '<div class="feature-item"><div class="icon">🔐</div><h4>Secure Login</h4><p>Password protected</p></div>';
        echo '</div>';
        echo '<div style="text-align: center; margin: 30px 0;">';
        echo '<div class="loading"></div>';
        echo '<span style="color: #667eea; font-weight: 600;">Launching admin panel...</span>';
        echo '</div>';
        echo '</div>';
        echo '<script>setTimeout(function(){ window.location.href = "/admin"; }, 3000);</script>';
        $setup_complete = true;
    }

} catch (Exception $e) {
    echo '<div class="error">❌ Setup Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '<div class="info">';
    echo '<h3>Troubleshooting:</h3>';
    echo '<ul>';
    echo '<li>Make sure Docker services are running: <code>docker-compose up -d</code></li>';
    echo '<li>Wait 1-2 minutes for database initialization</li>';
    echo '<li>Check service status: <code>docker ps</code></li>';
    echo '</ul>';
    echo '</div>';
}

// Show setup form if not complete
if (!$setup_complete && !$_POST['complete_setup']):
?>

        <div class="info">
            <h3 style="text-align: center; margin-bottom: 15px;">🚀 Welcome to EasyStream!</h3>
            <p style="text-align: center; margin-bottom: 30px;">Let's get your professional video streaming platform ready in just a few steps.</p>
            
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="icon">🎥</div>
                    <h4>Video Streaming</h4>
                    <p>Upload & manage videos</p>
                </div>
                <div class="feature-item">
                    <div class="icon">📺</div>
                    <h4>Live Broadcasting</h4>
                    <p>RTMP streaming ready</p>
                </div>
                <div class="feature-item">
                    <div class="icon">⚡</div>
                    <h4>High Performance</h4>
                    <p>Redis & queue system</p>
                </div>
                <div class="feature-item">
                    <div class="icon">🔒</div>
                    <h4>Enterprise Security</h4>
                    <p>Advanced protection</p>
                </div>
            </div>
        </div>

        <form method="POST">
            <div class="section-title">🏢 Site Configuration</div>
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="EasyStream" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="main_url">Site URL</label>
                <input type="url" id="main_url" name="main_url" value="http://localhost:8083" class="form-control" required 
                       placeholder="http://localhost:8083">
                <small style="color: #666; font-size: 0.85em; margin-top: 5px; display: block;">
                    This is the main URL for your EasyStream installation. Use http://localhost:8083 for local development.
                </small>
            </div>

            <div class="section-title">👤 Admin Account</div>
            <div class="form-group">
                <label for="admin_username">Admin Username</label>
                <input type="text" id="admin_username" name="admin_username" value="admin" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_password">Admin Password</label>
                <input type="password" id="admin_password" name="admin_password" value="admin123" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="admin_email">Admin Email</label>
                <input type="email" id="admin_email" name="admin_email" value="admin@easystream.local" class="form-control" required>
            </div>

            <div style="margin-top: 40px;">
                <button type="submit" name="complete_setup" class="btn">
                    🚀 Launch EasyStream
                </button>
            </div>
        </form>

<?php endif; ?>

        <div style="margin-top: 40px; padding-top: 25px; border-top: 2px solid #f0f0f0; text-align: center;">
            <p style="color: #999; font-size: 0.9em; margin: 0;">
                <strong>EasyStream</strong> - Professional Video Streaming Platform<br>
                <span style="font-size: 0.8em;">Powered by Docker • Redis • MariaDB • PHP</span>
            </p>
        </div>
    </div>
</body>
</html>
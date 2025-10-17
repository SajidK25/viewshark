<?php
/*******************************************************************************************************************
| EasyStream Simple Setup
| Minimal setup without full core loading
|*******************************************************************************************************************/

// Basic setup without full EasyStream core
define('_ISVALID', true);

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
        .section-title {
            font-size: 1.3em;
            font-weight: 600;
            color: #333;
            margin: 30px 0 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
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
    // Direct database connection
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if setup is already complete
    try {
        $stmt = $pdo->prepare("SELECT cfg_value FROM db_settings WHERE cfg_name = 'setup_complete'");
        $stmt->execute();
        $setup_status = $stmt->fetchColumn();
        
        if ($setup_status === '1') {
            $setup_complete = true;
            echo '<div class="success">✅ EasyStream is already set up!</div>';
            echo '<div class="info">';
            echo '<h3 style="text-align: center; margin-bottom: 25px;">🚀 Your Platform is Ready!</h3>';
            echo '<div style="text-align: center; margin: 30px 0;">';
            echo '<a href="/admin" style="display: inline-block; background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 12px 25px; text-decoration: none; border-radius: 10px; font-weight: 600; margin: 10px 5px;">🔧 Access Admin Panel</a>';
            echo '<a href="/" style="display: inline-block; background: linear-gradient(135deg, #6f42c1, #e83e8c); color: white; padding: 12px 25px; text-decoration: none; border-radius: 10px; font-weight: 600; margin: 10px 5px;">🏠 View Site</a>';
            echo '</div>';
            echo '<p style="text-align: center; margin-top: 20px; color: #666;"><em>Default login: admin / admin123</em></p>';
            echo '</div>';
        }
    } catch (Exception $e) {
        // Tables don't exist yet, continue with setup
    }

    // Debug: Show POST data
    if (!empty($_POST)) {
        echo '<div class="info">Debug: Form submitted with data: ' . htmlspecialchars(json_encode($_POST)) . '</div>';
    }
    
    // Handle setup form submission
    if (!$setup_complete && $_POST['complete_setup']) {
        $admin_user = trim($_POST['admin_username']) ?: 'admin';
        $admin_pass = trim($_POST['admin_password']) ?: 'admin123';
        $admin_email = trim($_POST['admin_email']) ?: 'admin@easystream.local';
        $site_name = trim($_POST['site_name']) ?: 'EasyStream';
        
        if (empty($admin_user) || empty($admin_pass) || empty($admin_email)) {
            throw new Exception("All fields are required.");
        }

        // Create tables first
        $sql_file = file_get_contents('deploy/create_missing_tables.sql');
        $statements = explode(';', $sql_file);
        
        foreach ($statements as $statement) {
            $statement = trim($statement);
            if (!empty($statement)) {
                try {
                    $pdo->exec($statement);
                } catch (Exception $e) {
                    // Continue if table already exists
                }
            }
        }

        // Insert settings
        $settings = [
            'backend_username' => $admin_user,
            'backend_password' => $admin_pass,
            'backend_email' => $admin_email,
            'site_name' => $site_name,
            'setup_complete' => '1',
            'main_url' => 'http://localhost:8083'
        ];

        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("INSERT INTO db_settings (cfg_name, cfg_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE cfg_value = ?");
            $stmt->execute([$key, $value, $value]);
        }

        echo '<div class="success">🎉 Congratulations! EasyStream is now ready to use!</div>';
        echo '<div class="info">';
        echo '<h3 style="text-align: center; margin-bottom: 25px;">🚀 Your Streaming Platform is Live!</h3>';
        echo '<div style="text-align: center; margin: 30px 0;">';
        echo '<div style="display: inline-block; width: 20px; height: 20px; border: 3px solid #f3f3f3; border-top: 3px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin-right: 10px;"></div>';
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
        </div>

        <form method="POST">
            <div class="section-title">🏢 Site Configuration</div>
            <div class="form-group">
                <label for="site_name">Site Name</label>
                <input type="text" id="site_name" name="site_name" value="EasyStream" class="form-control" required>
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
                <button type="submit" name="complete_setup" value="1" class="btn" onclick="console.log('Button clicked!'); this.innerHTML='⏳ Setting up...'; this.disabled=true;">
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
    
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
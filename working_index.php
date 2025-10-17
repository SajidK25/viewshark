<?php
/*******************************************************************************************************************
| Working EasyStream Index
| A functional main page that works with the fixed core
|*******************************************************************************************************************/

define('_ISVALID', true);

try {
    // Load the core
    include_once 'f_core/config.core.php';
    
    // Get site configuration
    $site_cfg = $class_database->getConfigurations('site_name,site_description,main_url');
    $site_name = $site_cfg['site_name'] ?? 'EasyStream';
    $site_description = $site_cfg['site_description'] ?? 'Professional Video Streaming Platform';
    
    // Check if user is logged in
    $is_logged_in = VSession::isLoggedIn();
    $username = $is_logged_in ? $_SESSION['USER_NAME'] ?? 'User' : null;
    
} catch (Exception $e) {
    $error = "System Error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($site_name) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px 0;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .logo {
            font-size: 2em;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .nav {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .nav a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .nav a:hover {
            color: #667eea;
        }
        .btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 60px 20px;
            text-align: center;
        }
        .hero {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            margin-bottom: 40px;
        }
        .hero h1 {
            font-size: 3.5em;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero p {
            font-size: 1.3em;
            color: #666;
            margin-bottom: 40px;
            line-height: 1.6;
        }
        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .feature {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .feature:hover {
            transform: translateY(-5px);
        }
        .feature .icon {
            font-size: 3em;
            margin-bottom: 20px;
        }
        .feature h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #333;
        }
        .feature p {
            color: #666;
            line-height: 1.6;
        }
        .status {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-top: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            font-weight: 600;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <div class="logo">🎬 <?= htmlspecialchars($site_name) ?></div>
            <nav class="nav">
                <a href="/">Home</a>
                <a href="/videos">Videos</a>
                <a href="/channels">Channels</a>
                <?php if ($is_logged_in): ?>
                    <a href="/account">Welcome, <?= htmlspecialchars($username) ?>!</a>
                    <a href="/signout" class="btn">Logout</a>
                <?php else: ?>
                    <a href="/signin" class="btn">Sign In</a>
                    <a href="/signup" class="btn">Sign Up</a>
                <?php endif; ?>
                <a href="/admin" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997);">Admin</a>
            </nav>
        </div>
    </div>

    <div class="container">
        <?php if (isset($error)): ?>
            <div class="error">
                <h2>⚠️ System Error</h2>
                <p><?= htmlspecialchars($error) ?></p>
            </div>
        <?php else: ?>
            <div class="hero">
                <h1>Welcome to <?= htmlspecialchars($site_name) ?></h1>
                <p><?= htmlspecialchars($site_description) ?></p>
                <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap;">
                    <a href="/upload" class="btn" style="font-size: 1.1em; padding: 15px 30px;">📤 Upload Video</a>
                    <a href="/browse" class="btn" style="font-size: 1.1em; padding: 15px 30px;">🎥 Browse Videos</a>
                    <a href="/channels" class="btn" style="font-size: 1.1em; padding: 15px 30px;">📺 Channels</a>
                </div>
            </div>

            <div class="features">
                <div class="feature">
                    <div class="icon">🎥</div>
                    <h3>Video Streaming</h3>
                    <p>Upload, manage, and stream high-quality videos with advanced player features and multiple format support.</p>
                </div>
                <div class="feature">
                    <div class="icon">📺</div>
                    <h3>Live Broadcasting</h3>
                    <p>Stream live content with RTMP support, real-time chat, and professional broadcasting tools.</p>
                </div>
                <div class="feature">
                    <div class="icon">👥</div>
                    <h3>Community Features</h3>
                    <p>Build your audience with channels, subscriptions, comments, ratings, and social interactions.</p>
                </div>
                <div class="feature">
                    <div class="icon">⚡</div>
                    <h3>High Performance</h3>
                    <p>Built with Redis caching, queue processing, and optimized for scalability and speed.</p>
                </div>
                <div class="feature">
                    <div class="icon">🔒</div>
                    <h3>Enterprise Security</h3>
                    <p>Advanced security features including IP tracking, fingerprinting, and comprehensive access controls.</p>
                </div>
                <div class="feature">
                    <div class="icon">📊</div>
                    <h3>Analytics & Insights</h3>
                    <p>Detailed analytics, user tracking, and comprehensive reporting for content and audience insights.</p>
                </div>
            </div>

            <div class="status">
                <h2 class="success">🎉 System Status: Fully Operational</h2>
                <p>EasyStream is running smoothly with all core systems active.</p>
                <div style="margin-top: 20px;">
                    <a href="/admin_direct.php" class="btn">🔧 Admin Panel</a>
                    <a href="/test_core.php" class="btn" style="background: #6c757d;">🔍 System Test</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
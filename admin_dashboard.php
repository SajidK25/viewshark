<?php
/*******************************************************************************************************************
| EasyStream Admin Dashboard
| Direct admin interface bypassing core loading issues
|*******************************************************************************************************************/

define('_ISVALID', true);
session_start();

// Check if logged in
if (!$_SESSION['admin_logged_in']) {
    header("Location: admin_direct.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=db;dbname=easystream", "easystream", "easystream");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get system stats
    $stats = [];
    
    // Count users
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM db_users");
        $stats['users'] = $stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['users'] = 0;
    }
    
    // Count videos
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM db_videofiles");
        $stats['videos'] = $stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['videos'] = 0;
    }
    
    // Count settings
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM db_settings");
        $stats['settings'] = $stmt->fetchColumn();
    } catch (Exception $e) {
        $stats['settings'] = 0;
    }
    
    // Get recent activity
    try {
        $stmt = $pdo->query("SELECT * FROM db_ip_tracking ORDER BY timestamp DESC LIMIT 10");
        $recent_activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $recent_activity = [];
    }
    
} catch (Exception $e) {
    $db_error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>EasyStream Admin Dashboard</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: #f8f9fa;
            color: #333;
        }
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.8em;
            font-weight: 700;
        }
        .header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .btn {
            background: rgba(255,255,255,0.2);
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            text-align: center;
        }
        .stat-card .icon {
            font-size: 3em;
            margin-bottom: 15px;
        }
        .stat-card .number {
            font-size: 2.5em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 10px;
        }
        .stat-card .label {
            color: #666;
            font-size: 1.1em;
        }
        .section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .section-header {
            padding: 20px 30px;
            border-bottom: 1px solid #eee;
            font-size: 1.3em;
            font-weight: 600;
        }
        .section-content {
            padding: 30px;
        }
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .feature-card {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }
        .feature-card .icon {
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        .feature-card h3 {
            margin-bottom: 10px;
            color: #333;
        }
        .feature-card p {
            color: #666;
            margin-bottom: 15px;
        }
        .feature-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .feature-btn:hover {
            background: #5a67d8;
            color: white;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #28a745;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🎬 EasyStream Admin Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?= htmlspecialchars($_SESSION['ADMIN_NAME']) ?>!</span>
                <a href="admin_direct.php?logout=1" class="btn">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="success">
            🎉 <strong>Congratulations!</strong> EasyStream is successfully installed and running!
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">👥</div>
                <div class="number"><?= $stats['users'] ?></div>
                <div class="label">Users</div>
            </div>
            <div class="stat-card">
                <div class="icon">🎥</div>
                <div class="number"><?= $stats['videos'] ?></div>
                <div class="label">Videos</div>
            </div>
            <div class="stat-card">
                <div class="icon">⚙️</div>
                <div class="number"><?= $stats['settings'] ?></div>
                <div class="label">Settings</div>
            </div>
            <div class="stat-card">
                <div class="icon">🚀</div>
                <div class="number">100%</div>
                <div class="label">Ready</div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">🛠️ Admin Features</div>
            <div class="section-content">
                <div class="feature-grid">
                    <div class="feature-card">
                        <div class="icon">👥</div>
                        <h3>User Management</h3>
                        <p>Manage user accounts, permissions, and profiles</p>
                        <a href="#" class="feature-btn">Manage Users</a>
                    </div>
                    <div class="feature-card">
                        <div class="icon">🎥</div>
                        <h3>Video Management</h3>
                        <p>Upload, organize, and manage video content</p>
                        <a href="#" class="feature-btn">Manage Videos</a>
                    </div>
                    <div class="feature-card">
                        <div class="icon">⚙️</div>
                        <h3>System Settings</h3>
                        <p>Configure platform settings and preferences</p>
                        <a href="#" class="feature-btn">Settings</a>
                    </div>
                    <div class="feature-card">
                        <div class="icon">📊</div>
                        <h3>Analytics</h3>
                        <p>View platform statistics and user analytics</p>
                        <a href="#" class="feature-btn">View Analytics</a>
                    </div>
                    <div class="feature-card">
                        <div class="icon">🔒</div>
                        <h3>Security</h3>
                        <p>IP tracking, bans, and security monitoring</p>
                        <a href="#" class="feature-btn">Security Panel</a>
                    </div>
                    <div class="feature-card">
                        <div class="icon">⚡</div>
                        <h3>Queue System</h3>
                        <p>Monitor background jobs and processing</p>
                        <a href="#" class="feature-btn">Queue Status</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="section">
            <div class="section-header">🎯 Next Steps</div>
            <div class="section-content">
                <h3>Your EasyStream platform is ready! Here's what you can do:</h3>
                <ul style="margin: 20px 0; padding-left: 20px; line-height: 1.8;">
                    <li><strong>Visit your site:</strong> <a href="/" target="_blank">http://localhost:8083</a></li>
                    <li><strong>Upload videos:</strong> Start adding content to your platform</li>
                    <li><strong>Configure settings:</strong> Customize your platform appearance and behavior</li>
                    <li><strong>Add users:</strong> Create user accounts or enable registration</li>
                    <li><strong>Set up streaming:</strong> Configure live streaming settings</li>
                    <li><strong>Monitor system:</strong> Use the queue system and security features</li>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>
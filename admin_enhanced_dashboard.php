<?php
/*******************************************************************************************************************
| EasyStream Enhanced Admin Dashboard
| Modern, feature-rich admin interface with real-time monitoring
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
    
    // Get comprehensive system stats
    $stats = [];
    
    // User statistics
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total, 
                                   COUNT(CASE WHEN DATE(usr_datereg) = CURDATE() THEN 1 END) as today,
                                   COUNT(CASE WHEN usr_status = 'active' THEN 1 END) as active
                            FROM db_accountuser");
        $user_stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['users'] = $user_stats;
    } catch (Exception $e) {
        $stats['users'] = ['total' => 0, 'today' => 0, 'active' => 0];
    }
    
    // Video statistics
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total,
                                   COUNT(CASE WHEN DATE(upload_date) = CURDATE() THEN 1 END) as today,
                                   COUNT(CASE WHEN approved = '1' THEN 1 END) as approved,
                                   COUNT(CASE WHEN approved = '0' THEN 1 END) as pending,
                                   SUM(file_views) as total_views
                            FROM db_videofiles WHERE deleted = '0'");
        $video_stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['videos'] = $video_stats;
    } catch (Exception $e) {
        $stats['videos'] = ['total' => 0, 'today' => 0, 'approved' => 0, 'pending' => 0, 'total_views' => 0];
    }
    
    // Live stream statistics
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as total,
                                   COUNT(CASE WHEN live_status = 'live' THEN 1 END) as active,
                                   COUNT(CASE WHEN DATE(date_created) = CURDATE() THEN 1 END) as today
                            FROM db_livefiles WHERE deleted = '0'");
        $stream_stats = $stmt->fetch(PDO::FETCH_ASSOC);
        $stats['streams'] = $stream_stats;
    } catch (Exception $e) {
        $stats['streams'] = ['total' => 0, 'active' => 0, 'today' => 0];
    }
    
    // System health checks
    $health = [];
    
    // Database health
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = 'easystream'");
        $table_count = $stmt->fetchColumn();
        $health['database'] = ['status' => 'healthy', 'tables' => $table_count];
    } catch (Exception $e) {
        $health['database'] = ['status' => 'error', 'message' => $e->getMessage()];
    }
    
    // Storage health (basic check)
    $upload_dir = 'f_data/data_userfiles';
    if (is_dir($upload_dir) && is_writable($upload_dir)) {
        $health['storage'] = ['status' => 'healthy', 'writable' => true];
    } else {
        $health['storage'] = ['status' => 'warning', 'writable' => false];
    }
    
    // Recent activity
    try {
        $stmt = $pdo->query("SELECT ip_address, user_agent, timestamp, request_uri 
                            FROM db_ip_tracking 
                            ORDER BY timestamp DESC 
                            LIMIT 10");
        $recent_activity = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $recent_activity = [];
    }
    
    // Pending content for moderation
    try {
        $stmt = $pdo->query("SELECT file_key, file_title, usr_id, upload_date 
                            FROM db_videofiles 
                            WHERE approved = '0' AND deleted = '0' 
                            ORDER BY upload_date DESC 
                            LIMIT 5");
        $pending_content = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $pending_content = [];
    }
    
} catch (Exception $e) {
    $db_error = $e->getMessage();
}

// Handle AJAX requests
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    switch ($_GET['action']) {
        case 'stats':
            echo json_encode(['stats' => $stats, 'health' => $health]);
            break;
        case 'activity':
            echo json_encode(['activity' => $recent_activity]);
            break;
        case 'pending':
            echo json_encode(['pending' => $pending_content]);
            break;
    }
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>EasyStream Enhanced Admin Dashboard</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; 
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .header-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1 {
            font-size: 1.5em;
            font-weight: 700;
        }
        .header-actions {
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
            cursor: pointer;
        }
        .btn:hover {
            background: rgba(255,255,255,0.3);
        }
        .btn-primary {
            background: #28a745;
        }
        .btn-primary:hover {
            background: #218838;
        }
        
        /* Main Layout */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        .stat-icon {
            font-size: 2em;
            opacity: 0.8;
        }
        .stat-trend {
            font-size: 0.9em;
            padding: 4px 8px;
            border-radius: 12px;
            font-weight: 600;
        }
        .trend-up {
            background: #d4edda;
            color: #155724;
        }
        .trend-down {
            background: #f8d7da;
            color: #721c24;
        }
        .stat-number {
            font-size: 2.2em;
            font-weight: 700;
            color: #667eea;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #666;
            font-size: 1em;
            margin-bottom: 10px;
        }
        .stat-details {
            display: flex;
            gap: 15px;
            font-size: 0.9em;
            color: #888;
        }
        
        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        /* Sections */
        .section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .section-header {
            padding: 20px 25px;
            border-bottom: 1px solid #eee;
            font-size: 1.2em;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .section-content {
            padding: 25px;
        }
        
        /* Activity Feed */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .activity-item:last-child {
            border-bottom: none;
        }
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.2em;
        }
        .activity-content {
            flex: 1;
        }
        .activity-title {
            font-weight: 600;
            margin-bottom: 2px;
        }
        .activity-meta {
            font-size: 0.9em;
            color: #666;
        }
        
        /* Health Status */
        .health-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        .health-item {
            display: flex;
            align-items: center;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
        }
        .health-healthy {
            border-color: #28a745;
            background: #f8fff9;
        }
        .health-warning {
            border-color: #ffc107;
            background: #fffdf5;
        }
        .health-error {
            border-color: #dc3545;
            background: #fff5f5;
        }
        .health-icon {
            font-size: 1.5em;
            margin-right: 10px;
        }
        .health-healthy .health-icon { color: #28a745; }
        .health-warning .health-icon { color: #ffc107; }
        .health-error .health-icon { color: #dc3545; }
        
        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .action-card {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }
        .action-card:hover {
            border-color: #667eea;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-decoration: none;
            color: inherit;
        }
        .action-icon {
            font-size: 2.5em;
            margin-bottom: 10px;
            display: block;
        }
        .action-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        .action-desc {
            font-size: 0.9em;
            color: #666;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
            .header-content {
                flex-direction: column;
                gap: 10px;
            }
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>🎬 EasyStream Enhanced Dashboard</h1>
            <div class="header-actions">
                <span>Welcome, <?= htmlspecialchars($_SESSION['ADMIN_NAME']) ?>!</span>
                <button class="btn" onclick="refreshData()">🔄 Refresh</button>
                <a href="/" class="btn btn-primary" target="_blank">🌐 View Site</a>
                <a href="admin_direct.php?logout=1" class="btn">🚪 Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">👥</div>
                    <div class="stat-trend trend-up">+<?= $stats['users']['today'] ?> today</div>
                </div>
                <div class="stat-number"><?= number_format($stats['users']['total']) ?></div>
                <div class="stat-label">Total Users</div>
                <div class="stat-details">
                    <span>Active: <?= number_format($stats['users']['active']) ?></span>
                    <span>New: <?= $stats['users']['today'] ?></span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">🎥</div>
                    <div class="stat-trend trend-up">+<?= $stats['videos']['today'] ?> today</div>
                </div>
                <div class="stat-number"><?= number_format($stats['videos']['total']) ?></div>
                <div class="stat-label">Total Videos</div>
                <div class="stat-details">
                    <span>Approved: <?= number_format($stats['videos']['approved']) ?></span>
                    <span>Pending: <?= $stats['videos']['pending'] ?></span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">📺</div>
                    <div class="stat-trend trend-up">+<?= $stats['streams']['today'] ?> today</div>
                </div>
                <div class="stat-number"><?= number_format($stats['streams']['total']) ?></div>
                <div class="stat-label">Live Streams</div>
                <div class="stat-details">
                    <span>Active: <?= $stats['streams']['active'] ?></span>
                    <span>Total: <?= number_format($stats['streams']['total']) ?></span>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">👁️</div>
                    <div class="stat-trend trend-up">Growing</div>
                </div>
                <div class="stat-number"><?= number_format($stats['videos']['total_views']) ?></div>
                <div class="stat-label">Total Views</div>
                <div class="stat-details">
                    <span>Platform engagement</span>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="content-grid">
            <!-- Recent Activity -->
            <div class="section">
                <div class="section-header">
                    <span>📊 Recent Activity</span>
                    <span class="loading" id="activity-loading" style="display: none;"></span>
                </div>
                <div class="section-content" id="activity-content">
                    <?php if (empty($recent_activity)): ?>
                        <p style="text-align: center; color: #666; padding: 20px;">No recent activity</p>
                    <?php else: ?>
                        <?php foreach ($recent_activity as $activity): ?>
                            <div class="activity-item">
                                <div class="activity-icon">🌐</div>
                                <div class="activity-content">
                                    <div class="activity-title"><?= htmlspecialchars($activity['request_uri']) ?></div>
                                    <div class="activity-meta">
                                        <?= htmlspecialchars($activity['ip_address']) ?> • 
                                        <?= date('M j, Y H:i', strtotime($activity['timestamp'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System Health -->
            <div class="section">
                <div class="section-header">
                    <span>🏥 System Health</span>
                </div>
                <div class="section-content">
                    <div class="health-grid">
                        <div class="health-item health-<?= $health['database']['status'] ?>">
                            <div class="health-icon">🗄️</div>
                            <div>
                                <div style="font-weight: 600;">Database</div>
                                <div style="font-size: 0.9em; color: #666;">
                                    <?= $health['database']['status'] === 'healthy' ? $health['database']['tables'] . ' tables' : 'Error' ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="health-item health-<?= $health['storage']['status'] ?>">
                            <div class="health-icon">💾</div>
                            <div>
                                <div style="font-weight: 600;">Storage</div>
                                <div style="font-size: 0.9em; color: #666;">
                                    <?= $health['storage']['writable'] ? 'Writable' : 'Read-only' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Content -->
        <?php if (!empty($pending_content)): ?>
        <div class="section" style="margin-bottom: 30px;">
            <div class="section-header">
                <span>⏳ Pending Content Moderation</span>
                <span style="background: #ffc107; color: #856404; padding: 4px 8px; border-radius: 12px; font-size: 0.9em;">
                    <?= count($pending_content) ?> items
                </span>
            </div>
            <div class="section-content">
                <?php foreach ($pending_content as $content): ?>
                    <div class="activity-item">
                        <div class="activity-icon">🎥</div>
                        <div class="activity-content">
                            <div class="activity-title"><?= htmlspecialchars($content['file_title']) ?></div>
                            <div class="activity-meta">
                                User ID: <?= $content['usr_id'] ?> • 
                                <?= date('M j, Y H:i', strtotime($content['upload_date'])) ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <button class="btn" style="background: #28a745; font-size: 0.9em; padding: 6px 12px;">✅ Approve</button>
                            <button class="btn" style="background: #dc3545; font-size: 0.9em; padding: 6px 12px;">❌ Reject</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="section">
            <div class="section-header">
                <span>⚡ Quick Actions</span>
            </div>
            <div class="section-content">
                <div class="quick-actions">
                    <a href="f_modules/m_backend/members.php" class="action-card">
                        <div class="action-icon">👥</div>
                        <div class="action-title">User Management</div>
                        <div class="action-desc">Manage users, roles, and permissions</div>
                    </a>
                    
                    <a href="f_modules/m_backend/files.php" class="action-card">
                        <div class="action-icon">🎥</div>
                        <div class="action-title">Content Management</div>
                        <div class="action-desc">Upload, organize, and manage content</div>
                    </a>
                    
                    <a href="f_modules/m_backend/settings.php" class="action-card">
                        <div class="action-icon">⚙️</div>
                        <div class="action-title">System Settings</div>
                        <div class="action-desc">Configure platform settings</div>
                    </a>
                    
                    <a href="f_modules/m_backend/queue_management.php" class="action-card">
                        <div class="action-icon">⚡</div>
                        <div class="action-title">Queue System</div>
                        <div class="action-desc">Monitor background jobs</div>
                    </a>
                    
                    <a href="f_modules/m_backend/log_viewer.php" class="action-card">
                        <div class="action-icon">📋</div>
                        <div class="action-title">System Logs</div>
                        <div class="action-desc">View system logs and errors</div>
                    </a>
                    
                    <a href="f_modules/m_backend/ip_management.php" class="action-card">
                        <div class="action-icon">🔒</div>
                        <div class="action-title">Security Panel</div>
                        <div class="action-desc">IP tracking and security</div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-refresh functionality
        function refreshData() {
            const loading = document.getElementById('activity-loading');
            loading.style.display = 'inline-block';
            
            // Refresh stats and activity
            fetch('?action=stats')
                .then(response => response.json())
                .then(data => {
                    // Update stats (you can implement this)
                    console.log('Stats updated:', data);
                })
                .catch(error => console.error('Error:', error))
                .finally(() => {
                    loading.style.display = 'none';
                });
        }
        
        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
        
        // Add click handlers for pending content actions
        document.addEventListener('click', function(e) {
            if (e.target.textContent.includes('Approve') || e.target.textContent.includes('Reject')) {
                e.preventDefault();
                const action = e.target.textContent.includes('Approve') ? 'approve' : 'reject';
                alert(`${action} functionality would be implemented here`);
            }
        });
    </script>
</body>
</html>
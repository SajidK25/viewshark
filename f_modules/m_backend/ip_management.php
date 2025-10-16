<?php
/*******************************************************************************************************************
| IP Management Backend Interface
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once '../../f_core/config.core.php';

// Check admin access
if (!isset($_SESSION['ADMIN_NAME'])) {
    header('Location: /error');
    exit;
}

$action = VSecurity::getParam('action', 'alpha');
$ip = VSecurity::getParam('ip', 'string');

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && VSecurity::validateCSRFFromPost('ip_management')) {
    switch ($action) {
        case 'ban':
            $reason = VSecurity::postParam('reason', 'string', 'Manual ban');
            $duration = VSecurity::postParam('duration', 'int', 0);
            
            if (VIPTracker::banIP($ip, $reason, $duration, $_SESSION['ADMIN_NAME'])) {
                $success_message = "IP {$ip} has been banned successfully.";
                VIPTracker::logActivity('ip_banned', ['ip' => $ip, 'reason' => $reason]);
            } else {
                $error_message = "Failed to ban IP {$ip}.";
            }
            break;
            
        case 'unban':
            if (VIPTracker::unbanIP($ip)) {
                $success_message = "IP {$ip} has been unbanned successfully.";
                VIPTracker::logActivity('ip_unbanned', ['ip' => $ip]);
            } else {
                $error_message = "Failed to unban IP {$ip}.";
            }
            break;
    }
}

// Get IP statistics if IP is provided
$ip_stats = null;
$threat_info = null;
$ban_info = null;

if ($ip && filter_var($ip, FILTER_VALIDATE_IP)) {
    $ip_stats = VIPTracker::getIPStats($ip, 24);
    $threat_info = VIPTracker::detectThreats($ip);
    $ban_info = VIPTracker::isBanned($ip);
}

// Get recent activity
global $db;
$recent_activity = [];
try {
    $sql = "SELECT ip_address, action, COUNT(*) as count, MAX(timestamp) as last_seen 
            FROM db_ip_tracking 
            WHERE timestamp >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            GROUP BY ip_address, action 
            ORDER BY count DESC, last_seen DESC 
            LIMIT 20";
    
    $result = $db->Execute($sql);
    while ($result && !$result->EOF) {
        $recent_activity[] = [
            'ip' => $result->fields['ip_address'],
            'action' => $result->fields['action'],
            'count' => $result->fields['count'],
            'last_seen' => $result->fields['last_seen']
        ];
        $result->MoveNext();
    }
} catch (Exception $e) {
    // Handle error
}

// Get current bans
$current_bans = [];
try {
    $sql = "SELECT ban_ip, ban_reason, ban_date, ban_expires, banned_by 
            FROM db_banlist 
            WHERE ban_active = 1 
            ORDER BY ban_date DESC 
            LIMIT 50";
    
    $result = $db->Execute($sql);
    while ($result && !$result->EOF) {
        $current_bans[] = [
            'ip' => $result->fields['ban_ip'],
            'reason' => $result->fields['ban_reason'],
            'ban_date' => $result->fields['ban_date'],
            'expires' => $result->fields['ban_expires'],
            'banned_by' => $result->fields['banned_by']
        ];
        $result->MoveNext();
    }
} catch (Exception $e) {
    // Handle error
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>IP Management - EasyStream Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section { margin-bottom: 30px; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; }
        .form-group input, .form-group select, .form-group textarea { 
            width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 3px; 
        }
        .btn { padding: 10px 20px; background: #007cba; color: white; border: none; border-radius: 3px; cursor: pointer; }
        .btn:hover { background: #005a87; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; text-align: center; }
        .threat-high { color: #dc3545; font-weight: bold; }
        .threat-medium { color: #fd7e14; font-weight: bold; }
        .threat-low { color: #ffc107; }
        .threat-none { color: #28a745; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; }
        .ip-link { color: #007cba; text-decoration: none; }
        .ip-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>IP Management System</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= secure_output($success_message) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= secure_output($error_message) ?></div>
        <?php endif; ?>
        
        <!-- IP Lookup Section -->
        <div class="section">
            <h2>IP Address Lookup</h2>
            <form method="GET">
                <div class="form-group">
                    <label>IP Address:</label>
                    <input type="text" name="ip" value="<?= secure_output($ip ?? '') ?>" placeholder="Enter IP address (e.g., 192.168.1.1)">
                </div>
                <button type="submit" class="btn">Lookup IP</button>
            </form>
            
            <?php if ($ip_stats): ?>
                <h3>Statistics for <?= secure_output($ip) ?></h3>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Requests (24h)</h4>
                        <p><?= $ip_stats['total_requests'] ?></p>
                    </div>
                    <div class="stat-card">
                        <h4>Unique Actions</h4>
                        <p><?= $ip_stats['unique_actions'] ?></p>
                    </div>
                    <div class="stat-card">
                        <h4>First Seen</h4>
                        <p><?= secure_output($ip_stats['first_seen'] ?? 'Never') ?></p>
                    </div>
                    <div class="stat-card">
                        <h4>Last Seen</h4>
                        <p><?= secure_output($ip_stats['last_seen'] ?? 'Never') ?></p>
                    </div>
                </div>
                
                <?php if ($threat_info): ?>
                    <h4>Threat Assessment</h4>
                    <p>Risk Level: <span class="threat-<?= strtolower($threat_info['risk_assessment']) ?>"><?= $threat_info['risk_assessment'] ?></span></p>
                    <?php if (!empty($threat_info['threats'])): ?>
                        <ul>
                            <?php foreach ($threat_info['threats'] as $threat): ?>
                                <li><?= secure_output($threat) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($ban_info): ?>
                    <div class="alert alert-danger">
                        <strong>This IP is currently banned!</strong><br>
                        Reason: <?= secure_output($ban_info['reason']) ?><br>
                        Banned on: <?= secure_output($ban_info['ban_date']) ?><br>
                        <?php if ($ban_info['expires']): ?>
                            Expires: <?= secure_output($ban_info['expires']) ?><br>
                        <?php else: ?>
                            Permanent ban<br>
                        <?php endif; ?>
                        Banned by: <?= secure_output($ban_info['banned_by']) ?>
                    </div>
                    
                    <form method="POST" style="display: inline;">
                        <?= csrf_field('ip_management') ?>
                        <input type="hidden" name="action" value="unban">
                        <input type="hidden" name="ip" value="<?= secure_output($ip) ?>">
                        <button type="submit" class="btn btn-success">Unban IP</button>
                    </form>
                <?php else: ?>
                    <h4>Ban IP Address</h4>
                    <form method="POST">
                        <?= csrf_field('ip_management') ?>
                        <input type="hidden" name="action" value="ban">
                        <input type="hidden" name="ip" value="<?= secure_output($ip) ?>">
                        
                        <div class="form-group">
                            <label>Reason:</label>
                            <textarea name="reason" placeholder="Enter reason for ban"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Duration (hours, 0 = permanent):</label>
                            <input type="number" name="duration" value="24" min="0">
                        </div>
                        
                        <button type="submit" class="btn btn-danger">Ban IP</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Recent Activity -->
        <div class="section">
            <h2>Recent Activity (Last Hour)</h2>
            <table>
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Action</th>
                        <th>Count</th>
                        <th>Last Seen</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_activity as $activity): ?>
                        <tr>
                            <td>
                                <a href="?ip=<?= urlencode($activity['ip']) ?>" class="ip-link">
                                    <?= secure_output($activity['ip']) ?>
                                </a>
                            </td>
                            <td><?= secure_output($activity['action']) ?></td>
                            <td><?= $activity['count'] ?></td>
                            <td><?= secure_output($activity['last_seen']) ?></td>
                            <td>
                                <a href="?ip=<?= urlencode($activity['ip']) ?>" class="btn" style="padding: 5px 10px; font-size: 12px;">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Current Bans -->
        <div class="section">
            <h2>Current IP Bans</h2>
            <table>
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Reason</th>
                        <th>Banned Date</th>
                        <th>Expires</th>
                        <th>Banned By</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($current_bans as $ban): ?>
                        <tr>
                            <td>
                                <a href="?ip=<?= urlencode($ban['ip']) ?>" class="ip-link">
                                    <?= secure_output($ban['ip']) ?>
                                </a>
                            </td>
                            <td><?= secure_output($ban['reason']) ?></td>
                            <td><?= secure_output($ban['ban_date']) ?></td>
                            <td><?= $ban['expires'] ? secure_output($ban['expires']) : 'Permanent' ?></td>
                            <td><?= secure_output($ban['banned_by']) ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <?= csrf_field('ip_management') ?>
                                    <input type="hidden" name="action" value="unban">
                                    <input type="hidden" name="ip" value="<?= secure_output($ban['ip']) ?>">
                                    <button type="submit" class="btn btn-success" style="padding: 5px 10px; font-size: 12px;">Unban</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
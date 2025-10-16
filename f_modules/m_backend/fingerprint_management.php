<?php
/*******************************************************************************************************************
| Fingerprint Management Backend Interface
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once '../../f_core/config.core.php';

// Check admin access
if (!isset($_SESSION['ADMIN_NAME'])) {
    header('Location: /error');
    exit;
}

$action = VSecurity::getParam('action', 'alpha');
$fingerprint = VSecurity::getParam('fingerprint', 'string');

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && VSecurity::validateCSRFFromPost('fingerprint_management')) {
    switch ($action) {
        case 'ban':
            $reason = VSecurity::postParam('reason', 'string', 'Manual ban');
            $duration = VSecurity::postParam('duration', 'int', 0);
            
            if (VFingerprint::banFingerprint($fingerprint, $reason, $duration, $_SESSION['ADMIN_NAME'])) {
                $success_message = "Fingerprint has been banned successfully.";
                VIPTracker::logActivity('fingerprint_banned', ['fingerprint' => substr($fingerprint, 0, 16) . '...', 'reason' => $reason]);
            } else {
                $error_message = "Failed to ban fingerprint.";
            }
            break;
            
        case 'unban':
            if (VFingerprint::unbanFingerprint($fingerprint)) {
                $success_message = "Fingerprint has been unbanned successfully.";
                VIPTracker::logActivity('fingerprint_unbanned', ['fingerprint' => substr($fingerprint, 0, 16) . '...']);
            } else {
                $error_message = "Failed to unban fingerprint.";
            }
            break;
    }
}

// Get fingerprint statistics if fingerprint is provided
$fingerprint_stats = null;
$threat_info = null;
$ban_info = null;

if ($fingerprint && strlen($fingerprint) === 64) {
    $fingerprint_stats = VFingerprint::getFingerprintStats($fingerprint);
    $threat_info = VFingerprint::detectFingerprintThreats($fingerprint);
    $ban_info = VFingerprint::isBanned($fingerprint);
}

// Get recent fingerprints
global $db;
$recent_fingerprints = [];
try {
    $sql = "SELECT fingerprint_hash, first_seen, last_seen, visit_count, last_ip, last_user_id, user_agent 
            FROM db_fingerprints 
            ORDER BY last_seen DESC 
            LIMIT 20";
    
    $result = $db->Execute($sql);
    while ($result && !$result->EOF) {
        $recent_fingerprints[] = [
            'fingerprint' => $result->fields['fingerprint_hash'],
            'first_seen' => $result->fields['first_seen'],
            'last_seen' => $result->fields['last_seen'],
            'visit_count' => $result->fields['visit_count'],
            'last_ip' => $result->fields['last_ip'],
            'last_user_id' => $result->fields['last_user_id'],
            'user_agent' => $result->fields['user_agent']
        ];
        $result->MoveNext();
    }
} catch (Exception $e) {
    // Handle error
}

// Get current fingerprint bans
$current_bans = [];
try {
    $sql = "SELECT fingerprint_hash, ban_reason, ban_date, ban_expires, banned_by 
            FROM db_fingerprint_bans 
            WHERE ban_active = 1 
            ORDER BY ban_date DESC 
            LIMIT 50";
    
    $result = $db->Execute($sql);
    while ($result && !$result->EOF) {
        $current_bans[] = [
            'fingerprint' => $result->fields['fingerprint_hash'],
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

// Get suspicious fingerprints
$suspicious_fingerprints = [];
try {
    $sql = "SELECT fingerprint_hash, visit_count, last_seen, last_ip, user_agent 
            FROM db_fingerprints 
            WHERE visit_count > 100 OR 
                  (TIMESTAMPDIFF(HOUR, first_seen, last_seen) > 0 AND 
                   (visit_count / TIMESTAMPDIFF(HOUR, first_seen, last_seen)) > 50)
            ORDER BY visit_count DESC 
            LIMIT 20";
    
    $result = $db->Execute($sql);
    while ($result && !$result->EOF) {
        $fp = $result->fields['fingerprint_hash'];
        $threats = VFingerprint::detectFingerprintThreats($fp);
        
        $suspicious_fingerprints[] = [
            'fingerprint' => $fp,
            'visit_count' => $result->fields['visit_count'],
            'last_seen' => $result->fields['last_seen'],
            'last_ip' => $result->fields['last_ip'],
            'user_agent' => $result->fields['user_agent'],
            'threat_level' => $threats['risk_assessment'],
            'threats' => $threats['threats']
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
    <title>Fingerprint Management - EasyStream Admin</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
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
        .btn-small { padding: 5px 10px; font-size: 12px; }
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
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; font-size: 12px; }
        th { background: #f8f9fa; }
        .fingerprint-hash { font-family: monospace; font-size: 11px; }
        .fingerprint-link { color: #007cba; text-decoration: none; }
        .fingerprint-link:hover { text-decoration: underline; }
        .user-agent { max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .threats-list { font-size: 11px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Browser Fingerprint Management</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?= secure_output($success_message) ?></div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= secure_output($error_message) ?></div>
        <?php endif; ?>
        
        <!-- Fingerprint Lookup Section -->
        <div class="section">
            <h2>Fingerprint Lookup</h2>
            <form method="GET">
                <div class="form-group">
                    <label>Fingerprint Hash:</label>
                    <input type="text" name="fingerprint" value="<?= secure_output($fingerprint ?? '') ?>" placeholder="Enter 64-character fingerprint hash">
                </div>
                <button type="submit" class="btn">Lookup Fingerprint</button>
            </form>
            
            <?php if ($fingerprint_stats): ?>
                <h3>Statistics for Fingerprint</h3>
                <p class="fingerprint-hash"><?= secure_output($fingerprint) ?></p>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <h4>Total Visits</h4>
                        <p><?= $fingerprint_stats['visit_count'] ?></p>
                    </div>
                    <div class="stat-card">
                        <h4>First Seen</h4>
                        <p><?= secure_output($fingerprint_stats['first_seen']) ?></p>
                    </div>
                    <div class="stat-card">
                        <h4>Last Seen</h4>
                        <p><?= secure_output($fingerprint_stats['last_seen']) ?></p>
                    </div>
                    <div class="stat-card">
                        <h4>Last IP</h4>
                        <p><?= secure_output($fingerprint_stats['last_ip']) ?></p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>User Agent:</label>
                    <textarea readonly><?= secure_output($fingerprint_stats['user_agent']) ?></textarea>
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
                        <strong>This fingerprint is currently banned!</strong><br>
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
                        <?= csrf_field('fingerprint_management') ?>
                        <input type="hidden" name="action" value="unban">
                        <input type="hidden" name="fingerprint" value="<?= secure_output($fingerprint) ?>">
                        <button type="submit" class="btn btn-success">Unban Fingerprint</button>
                    </form>
                <?php else: ?>
                    <h4>Ban Fingerprint</h4>
                    <form method="POST">
                        <?= csrf_field('fingerprint_management') ?>
                        <input type="hidden" name="action" value="ban">
                        <input type="hidden" name="fingerprint" value="<?= secure_output($fingerprint) ?>">
                        
                        <div class="form-group">
                            <label>Reason:</label>
                            <textarea name="reason" placeholder="Enter reason for ban"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Duration (hours, 0 = permanent):</label>
                            <input type="number" name="duration" value="48" min="0">
                        </div>
                        
                        <button type="submit" class="btn btn-danger">Ban Fingerprint</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <!-- Suspicious Fingerprints -->
        <div class="section">
            <h2>Suspicious Fingerprints</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fingerprint</th>
                        <th>Visits</th>
                        <th>Last Seen</th>
                        <th>Last IP</th>
                        <th>Threat Level</th>
                        <th>Threats</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($suspicious_fingerprints as $fp): ?>
                        <tr>
                            <td>
                                <a href="?fingerprint=<?= urlencode($fp['fingerprint']) ?>" class="fingerprint-link">
                                    <span class="fingerprint-hash"><?= substr($fp['fingerprint'], 0, 16) ?>...</span>
                                </a>
                            </td>
                            <td><?= $fp['visit_count'] ?></td>
                            <td><?= secure_output($fp['last_seen']) ?></td>
                            <td><?= secure_output($fp['last_ip']) ?></td>
                            <td><span class="threat-<?= strtolower($fp['threat_level']) ?>"><?= $fp['threat_level'] ?></span></td>
                            <td class="threats-list">
                                <?php if (!empty($fp['threats'])): ?>
                                    <?= secure_output(implode('; ', array_slice($fp['threats'], 0, 2))) ?>
                                    <?php if (count($fp['threats']) > 2): ?>...<?php endif; ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?fingerprint=<?= urlencode($fp['fingerprint']) ?>" class="btn btn-small">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Recent Fingerprints -->
        <div class="section">
            <h2>Recent Fingerprints</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fingerprint</th>
                        <th>First Seen</th>
                        <th>Last Seen</th>
                        <th>Visits</th>
                        <th>Last IP</th>
                        <th>User ID</th>
                        <th>User Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_fingerprints as $fp): ?>
                        <tr>
                            <td>
                                <a href="?fingerprint=<?= urlencode($fp['fingerprint']) ?>" class="fingerprint-link">
                                    <span class="fingerprint-hash"><?= substr($fp['fingerprint'], 0, 16) ?>...</span>
                                </a>
                            </td>
                            <td><?= secure_output($fp['first_seen']) ?></td>
                            <td><?= secure_output($fp['last_seen']) ?></td>
                            <td><?= $fp['visit_count'] ?></td>
                            <td><?= secure_output($fp['last_ip']) ?></td>
                            <td><?= $fp['last_user_id'] ?: 'Guest' ?></td>
                            <td class="user-agent" title="<?= secure_output($fp['user_agent']) ?>">
                                <?= secure_output(substr($fp['user_agent'], 0, 50)) ?>...
                            </td>
                            <td>
                                <a href="?fingerprint=<?= urlencode($fp['fingerprint']) ?>" class="btn btn-small">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Current Fingerprint Bans -->
        <div class="section">
            <h2>Current Fingerprint Bans</h2>
            <table>
                <thead>
                    <tr>
                        <th>Fingerprint</th>
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
                                <a href="?fingerprint=<?= urlencode($ban['fingerprint']) ?>" class="fingerprint-link">
                                    <span class="fingerprint-hash"><?= substr($ban['fingerprint'], 0, 16) ?>...</span>
                                </a>
                            </td>
                            <td><?= secure_output($ban['reason']) ?></td>
                            <td><?= secure_output($ban['ban_date']) ?></td>
                            <td><?= $ban['expires'] ? secure_output($ban['expires']) : 'Permanent' ?></td>
                            <td><?= secure_output($ban['banned_by']) ?></td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <?= csrf_field('fingerprint_management') ?>
                                    <input type="hidden" name="action" value="unban">
                                    <input type="hidden" name="fingerprint" value="<?= secure_output($ban['fingerprint']) ?>">
                                    <button type="submit" class="btn btn-success btn-small">Unban</button>
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
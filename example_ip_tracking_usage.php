<?php
/*******************************************************************************************************************
| Example: IP Tracking Usage
| This file demonstrates how to use the enhanced IP tracking system
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once 'f_core/config.core.php';

// Example 1: Log user activities
VIPTracker::logActivity('page_view', [
    'page' => 'home',
    'user_type' => 'guest'
]);

VIPTracker::logActivity('video_watch', [
    'video_id' => 12345,
    'duration' => 120
]);

VIPTracker::logActivity('login_attempt', [
    'username' => 'john_doe',
    'success' => true
]);

// Example 2: Check if current user's IP is banned
$user_ip = VIPaccess::getUserIP();
$ban_info = VIPTracker::isBanned($user_ip);

if ($ban_info) {
    echo "Your IP is banned. Reason: " . $ban_info['reason'];
    exit;
}

// Example 3: Get IP statistics
$ip_stats = VIPTracker::getIPStats($user_ip, 24); // Last 24 hours
echo "Total requests in last 24h: " . $ip_stats['total_requests'];

// Example 4: Detect threats and auto-ban if necessary
$threats = VIPTracker::detectThreats($user_ip);
echo "Threat level: " . $threats['risk_assessment'];

if ($threats['threat_level'] >= 5) {
    // Auto-ban high-risk IPs
    VIPTracker::autoBan($user_ip);
    echo "IP has been automatically banned due to suspicious activity.";
}

// Example 5: Manual IP banning (admin function)
if (isset($_SESSION['ADMIN_NAME'])) {
    // Ban a specific IP
    $result = VIPTracker::banIP('192.168.1.100', 'Spam activity detected', 24, $_SESSION['ADMIN_NAME']);
    
    if ($result) {
        echo "IP banned successfully";
    }
    
    // Unban an IP
    $result = VIPTracker::unbanIP('192.168.1.100');
    
    if ($result) {
        echo "IP unbanned successfully";
    }
}

// Example 6: Integration with existing login system
function handleLogin($username, $password) {
    $user_ip = VIPaccess::getUserIP();
    
    // Check if IP is banned
    if (VIPTracker::isBanned($user_ip)) {
        VIPTracker::logActivity('login_blocked', ['username' => $username, 'reason' => 'banned_ip']);
        return false;
    }
    
    // Check rate limiting
    if (!VSecurity::checkRateLimit('login_' . $user_ip, 5, 300)) {
        VIPTracker::logActivity('login_rate_limited', ['username' => $username]);
        return false;
    }
    
    // Perform actual login validation
    $login_success = validateUserCredentials($username, $password);
    
    if ($login_success) {
        VIPTracker::logActivity('login_success', ['username' => $username]);
        return true;
    } else {
        VIPTracker::logActivity('login_failed', ['username' => $username]);
        
        // Check for brute force and auto-ban if necessary
        VIPTracker::autoBan($user_ip);
        
        return false;
    }
}

// Example 7: Monitor file uploads
function handleFileUpload($file) {
    $user_ip = VIPaccess::getUserIP();
    
    VIPTracker::logActivity('file_upload_attempt', [
        'filename' => $file['name'],
        'size' => $file['size'],
        'type' => $file['type']
    ]);
    
    // Check upload rate limiting
    if (!VSecurity::checkRateLimit('upload_' . $user_ip, 10, 3600)) {
        VIPTracker::logActivity('upload_rate_limited', ['filename' => $file['name']]);
        return false;
    }
    
    // Process upload...
    $upload_success = processFileUpload($file);
    
    if ($upload_success) {
        VIPTracker::logActivity('file_upload_success', ['filename' => $file['name']]);
    } else {
        VIPTracker::logActivity('file_upload_failed', ['filename' => $file['name']]);
    }
    
    return $upload_success;
}

// Dummy functions for example
function validateUserCredentials($username, $password) {
    // Your existing login validation logic
    return true;
}

function processFileUpload($file) {
    // Your existing file upload logic
    return true;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>IP Tracking Examples</title>
</head>
<body>
    <h1>IP Tracking System Examples</h1>
    
    <h2>Current IP Information</h2>
    <p><strong>Your IP:</strong> <?= secure_output($user_ip) ?></p>
    <p><strong>Requests (24h):</strong> <?= $ip_stats['total_requests'] ?></p>
    <p><strong>Threat Level:</strong> <span class="threat-<?= strtolower($threats['risk_assessment']) ?>"><?= $threats['risk_assessment'] ?></span></p>
    
    <?php if ($ban_info): ?>
        <div style="color: red; font-weight: bold;">
            <p>⚠️ Your IP is currently banned!</p>
            <p>Reason: <?= secure_output($ban_info['reason']) ?></p>
            <p>Banned on: <?= secure_output($ban_info['ban_date']) ?></p>
        </div>
    <?php else: ?>
        <div style="color: green;">
            <p>✅ Your IP is not banned</p>
        </div>
    <?php endif; ?>
    
    <h2>Recent Threats Detected</h2>
    <?php if (!empty($threats['threats'])): ?>
        <ul>
            <?php foreach ($threats['threats'] as $threat): ?>
                <li><?= secure_output($threat) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No threats detected for your IP.</p>
    <?php endif; ?>
    
    <style>
        .threat-high { color: #dc3545; }
        .threat-medium { color: #fd7e14; }
        .threat-low { color: #ffc107; }
        .threat-none { color: #28a745; }
    </style>
</body>
</html>
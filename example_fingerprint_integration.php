<?php
/*******************************************************************************************************************
| Example: Browser Fingerprint Integration
| This file demonstrates how to integrate fingerprinting into your existing pages
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once 'f_core/config.core.php';

// Example 1: Check fingerprint on page load
function checkFingerprintAccess() {
    // Check if we have a fingerprint in session
    if (isset($_SESSION['browser_fingerprint'])) {
        $fingerprint = $_SESSION['browser_fingerprint'];
        
        // Check if fingerprint is banned
        $ban_info = VFingerprint::isBanned($fingerprint);
        if ($ban_info) {
            // Log the banned access attempt
            VIPTracker::logActivity('banned_fingerprint_page_access', [
                'fingerprint' => substr($fingerprint, 0, 16) . '...',
                'page' => $_SERVER['REQUEST_URI'],
                'ban_reason' => $ban_info['reason']
            ]);
            
            // Redirect to error page or show ban message
            header('Location: /error');
            exit;
        }
        
        // Track page visit with fingerprint
        VFingerprint::trackFingerprint($fingerprint, [
            'page_type' => 'content_page',
            'page_url' => $_SERVER['REQUEST_URI']
        ]);
    }
}

// Example 2: Enhanced login with fingerprint checking
function handleSecureLogin($username, $password) {
    $user_ip = VIPaccess::getUserIP();
    
    // Check IP ban first
    if (VIPTracker::isBanned($user_ip)) {
        return ['success' => false, 'message' => 'Access denied - IP banned'];
    }
    
    // Check fingerprint ban if available
    if (isset($_SESSION['browser_fingerprint'])) {
        $fingerprint = $_SESSION['browser_fingerprint'];
        $ban_info = VFingerprint::isBanned($fingerprint);
        
        if ($ban_info) {
            VIPTracker::logActivity('banned_fingerprint_login_attempt', [
                'username' => $username,
                'fingerprint' => substr($fingerprint, 0, 16) . '...',
                'ban_reason' => $ban_info['reason']
            ]);
            
            return ['success' => false, 'message' => 'Access denied - Device banned'];
        }
    }
    
    // Rate limiting by IP and fingerprint
    $rate_limit_key = 'login_' . $user_ip;
    if (isset($_SESSION['browser_fingerprint'])) {
        $rate_limit_key .= '_' . substr($_SESSION['browser_fingerprint'], 0, 16);
    }
    
    if (!VSecurity::checkRateLimit($rate_limit_key, 5, 300)) {
        VIPTracker::logActivity('login_rate_limited', [
            'username' => $username,
            'fingerprint' => isset($_SESSION['browser_fingerprint']) ? substr($_SESSION['browser_fingerprint'], 0, 16) . '...' : null
        ]);
        
        return ['success' => false, 'message' => 'Too many login attempts'];
    }
    
    // Perform actual login validation
    $login_success = validateUserCredentials($username, $password);
    
    if ($login_success) {
        // Log successful login with fingerprint
        VIPTracker::logActivity('login_success', [
            'username' => $username,
            'fingerprint' => isset($_SESSION['browser_fingerprint']) ? substr($_SESSION['browser_fingerprint'], 0, 16) . '...' : null
        ]);
        
        if (isset($_SESSION['browser_fingerprint'])) {
            VFingerprint::trackFingerprint($_SESSION['browser_fingerprint'], [
                'action' => 'successful_login',
                'username' => $username
            ]);
        }
        
        return ['success' => true, 'message' => 'Login successful'];
    } else {
        // Log failed login
        VIPTracker::logActivity('login_failed', [
            'username' => $username,
            'fingerprint' => isset($_SESSION['browser_fingerprint']) ? substr($_SESSION['browser_fingerprint'], 0, 16) . '...' : null
        ]);
        
        // Check for auto-ban conditions
        if (isset($_SESSION['browser_fingerprint'])) {
            $threats = VFingerprint::detectFingerprintThreats($_SESSION['browser_fingerprint']);
            if ($threats['threat_level'] >= 4) {
                VFingerprint::autoBanFingerprint($_SESSION['browser_fingerprint']);
            }
        }
        
        return ['success' => false, 'message' => 'Invalid credentials'];
    }
}

// Example 3: File upload with fingerprint tracking
function handleSecureFileUpload($file) {
    $user_ip = VIPaccess::getUserIP();
    
    // Check bans
    if (VIPTracker::isBanned($user_ip)) {
        return ['success' => false, 'message' => 'Access denied - IP banned'];
    }
    
    if (isset($_SESSION['browser_fingerprint'])) {
        $fingerprint = $_SESSION['browser_fingerprint'];
        if (VFingerprint::isBanned($fingerprint)) {
            return ['success' => false, 'message' => 'Access denied - Device banned'];
        }
    }
    
    // Rate limiting for uploads
    $rate_limit_key = 'upload_' . $user_ip;
    if (isset($_SESSION['browser_fingerprint'])) {
        $rate_limit_key .= '_' . substr($_SESSION['browser_fingerprint'], 0, 16);
    }
    
    if (!VSecurity::checkRateLimit($rate_limit_key, 10, 3600)) {
        return ['success' => false, 'message' => 'Upload rate limit exceeded'];
    }
    
    // Validate file
    $validation = VSecurity::validateFileUpload($file, ['image/jpeg', 'image/png', 'video/mp4'], 50 * 1024 * 1024);
    
    if (!$validation['valid']) {
        // Log suspicious upload attempt
        if (isset($_SESSION['browser_fingerprint'])) {
            VFingerprint::trackFingerprint($_SESSION['browser_fingerprint'], [
                'action' => 'suspicious_upload',
                'filename' => $file['name'],
                'error' => $validation['error']
            ]);
        }
        
        return ['success' => false, 'message' => $validation['error']];
    }
    
    // Process upload
    $upload_success = processFileUpload($file);
    
    if ($upload_success) {
        // Track successful upload
        if (isset($_SESSION['browser_fingerprint'])) {
            VFingerprint::trackFingerprint($_SESSION['browser_fingerprint'], [
                'action' => 'file_upload_success',
                'filename' => $file['name'],
                'size' => $file['size']
            ]);
        }
        
        return ['success' => true, 'message' => 'File uploaded successfully'];
    } else {
        return ['success' => false, 'message' => 'Upload failed'];
    }
}

// Example 4: Comment posting with fingerprint validation
function handleCommentPost($comment_text, $video_id) {
    // Check fingerprint ban
    if (isset($_SESSION['browser_fingerprint'])) {
        $fingerprint = $_SESSION['browser_fingerprint'];
        $ban_info = VFingerprint::isBanned($fingerprint);
        
        if ($ban_info) {
            return ['success' => false, 'message' => 'Comments disabled for your device'];
        }
        
        // Rate limiting for comments
        $rate_limit_key = 'comment_' . substr($fingerprint, 0, 16);
        if (!VSecurity::checkRateLimit($rate_limit_key, 20, 3600)) {
            return ['success' => false, 'message' => 'Comment rate limit exceeded'];
        }
        
        // Track comment activity
        VFingerprint::trackFingerprint($fingerprint, [
            'action' => 'comment_post',
            'video_id' => $video_id,
            'comment_length' => strlen($comment_text)
        ]);
    }
    
    // Process comment
    $comment_success = saveComment($comment_text, $video_id);
    
    return ['success' => $comment_success, 'message' => $comment_success ? 'Comment posted' : 'Failed to post comment'];
}

// Example 5: Admin function to ban fingerprint from user activity
function adminBanUserFingerprint($user_id, $reason = 'Terms violation') {
    global $db;
    
    try {
        // Get user's recent fingerprints
        $sql = "SELECT DISTINCT fingerprint_hash 
                FROM db_fingerprints 
                WHERE last_user_id = ? 
                AND last_seen >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        
        $result = $db->Execute($sql, [$user_id]);
        $banned_count = 0;
        
        while ($result && !$result->EOF) {
            $fingerprint = $result->fields['fingerprint_hash'];
            
            if (VFingerprint::banFingerprint($fingerprint, $reason, 0, $_SESSION['ADMIN_NAME'])) {
                $banned_count++;
                
                VIPTracker::logActivity('user_fingerprint_banned', [
                    'user_id' => $user_id,
                    'fingerprint' => substr($fingerprint, 0, 16) . '...',
                    'reason' => $reason
                ]);
            }
            
            $result->MoveNext();
        }
        
        return $banned_count;
        
    } catch (Exception $e) {
        error_log("Admin fingerprint ban error: " . $e->getMessage());
        return 0;
    }
}

// Check fingerprint access on every page load
checkFingerprintAccess();

// Dummy functions for examples
function validateUserCredentials($username, $password) {
    // Your existing login validation
    return true;
}

function processFileUpload($file) {
    // Your existing file upload processing
    return true;
}

function saveComment($text, $video_id) {
    // Your existing comment saving logic
    return true;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Fingerprint Integration Example</title>
</head>
<body>
    <h1>Browser Fingerprinting Integration</h1>
    
    <div id="fingerprint-status">
        <p>Checking device fingerprint...</p>
    </div>
    
    <!-- Include the fingerprinting JavaScript -->
    <?= VFingerprint::getFingerprintingJS() ?>
    
    <script>
    // Override the sendFingerprint function to update status
    const originalSendFingerprint = sendFingerprint;
    sendFingerprint = function() {
        originalSendFingerprint();
        
        // Update status after sending fingerprint
        setTimeout(() => {
            const statusDiv = document.getElementById('fingerprint-status');
            statusDiv.innerHTML = '<p style="color: green;">✓ Device verified</p>';
        }, 1000);
    };
    </script>
    
    <h2>Integration Examples</h2>
    
    <h3>1. Login Form</h3>
    <form method="POST" action="login_handler.php">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    
    <h3>2. File Upload</h3>
    <form method="POST" action="upload_handler.php" enctype="multipart/form-data">
        <input type="file" name="upload_file" accept="image/*,video/*" required>
        <button type="submit">Upload</button>
    </form>
    
    <h3>3. Comment Form</h3>
    <form method="POST" action="comment_handler.php">
        <textarea name="comment" placeholder="Enter your comment..." required></textarea>
        <input type="hidden" name="video_id" value="123">
        <button type="submit">Post Comment</button>
    </form>
    
    <h3>Current Session Info</h3>
    <?php if (isset($_SESSION['browser_fingerprint'])): ?>
        <p><strong>Fingerprint ID:</strong> <?= substr($_SESSION['browser_fingerprint'], 0, 16) ?>...</p>
        
        <?php 
        $stats = VFingerprint::getFingerprintStats($_SESSION['browser_fingerprint']);
        if ($stats): 
        ?>
            <p><strong>Visit Count:</strong> <?= $stats['visit_count'] ?></p>
            <p><strong>First Seen:</strong> <?= $stats['first_seen'] ?></p>
            <p><strong>Last Seen:</strong> <?= $stats['last_seen'] ?></p>
        <?php endif; ?>
        
        <?php 
        $threats = VFingerprint::detectFingerprintThreats($_SESSION['browser_fingerprint']);
        ?>
        <p><strong>Threat Level:</strong> <span style="color: <?= $threats['risk_assessment'] === 'HIGH' ? 'red' : ($threats['risk_assessment'] === 'MEDIUM' ? 'orange' : 'green') ?>"><?= $threats['risk_assessment'] ?></span></p>
    <?php else: ?>
        <p><em>Fingerprint not yet captured</em></p>
    <?php endif; ?>
</body>
</html>
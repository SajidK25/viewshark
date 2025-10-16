<?php
/*******************************************************************************************************************
| Fingerprint Handler - Receives and processes browser fingerprints
|*******************************************************************************************************************/

define('_ISVALID', true);
include_once 'f_core/config.core.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

// Rate limiting for fingerprint submissions
$client_ip = VIPaccess::getUserIP();
if (!VSecurity::checkRateLimit('fingerprint_' . $client_ip, 10, 60)) {
    http_response_code(429);
    exit('Rate limit exceeded');
}

try {
    // Get JSON data
    $json_data = file_get_contents('php://input');
    $fingerprint_data = json_decode($json_data, true);
    
    if (!$fingerprint_data) {
        http_response_code(400);
        exit('Invalid JSON data');
    }
    
    // Generate fingerprint hash
    $fingerprint_hash = VFingerprint::generateFingerprint($fingerprint_data);
    
    // Check if fingerprint is banned
    $ban_info = VFingerprint::isBanned($fingerprint_hash);
    if ($ban_info) {
        // Log banned fingerprint attempt
        VIPTracker::logActivity('banned_fingerprint_access', [
            'fingerprint' => substr($fingerprint_hash, 0, 16) . '...',
            'ban_reason' => $ban_info['reason']
        ]);
        
        http_response_code(403);
        exit('Access denied');
    }
    
    // Track the fingerprint
    VFingerprint::trackFingerprint($fingerprint_hash, [
        'client_data' => $fingerprint_data,
        'page_url' => $_SERVER['HTTP_REFERER'] ?? ''
    ]);
    
    // Store fingerprint in session for future checks
    $_SESSION['browser_fingerprint'] = $fingerprint_hash;
    
    // Check for threats and auto-ban if necessary
    $threats = VFingerprint::detectFingerprintThreats($fingerprint_hash);
    if ($threats['threat_level'] >= 5) {
        VFingerprint::autoBanFingerprint($fingerprint_hash);
        
        // Log the auto-ban
        VIPTracker::logActivity('fingerprint_auto_banned', [
            'fingerprint' => substr($fingerprint_hash, 0, 16) . '...',
            'threats' => $threats['threats'],
            'threat_level' => $threats['threat_level']
        ]);
        
        http_response_code(403);
        exit('Access denied - suspicious activity detected');
    }
    
    // Return success response
    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'fingerprint_id' => substr($fingerprint_hash, 0, 16) . '...',
        'threat_level' => $threats['risk_assessment']
    ]);
    
} catch (Exception $e) {
    error_log("Fingerprint handler error: " . $e->getMessage());
    http_response_code(500);
    exit('Internal server error');
}
?>
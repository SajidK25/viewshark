<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

/**
 * Browser Fingerprinting and Banning System
 */
class VFingerprint
{
    /**
     * Generate browser fingerprint from available data
     * @param array $fingerprint_data Client-side collected data
     * @return string Unique fingerprint hash
     */
    public static function generateFingerprint($fingerprint_data = [])
    {
        // Server-side fingerprint components
        $server_components = [
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'accept_language' => $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
            'accept_encoding' => $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '',
            'accept' => $_SERVER['HTTP_ACCEPT'] ?? '',
            'connection' => $_SERVER['HTTP_CONNECTION'] ?? '',
            'upgrade_insecure_requests' => $_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS'] ?? '',
            'sec_fetch_dest' => $_SERVER['HTTP_SEC_FETCH_DEST'] ?? '',
            'sec_fetch_mode' => $_SERVER['HTTP_SEC_FETCH_MODE'] ?? '',
            'sec_fetch_site' => $_SERVER['HTTP_SEC_FETCH_SITE'] ?? '',
            'dnt' => $_SERVER['HTTP_DNT'] ?? '',
        ];
        
        // Combine server and client data
        $all_components = array_merge($server_components, $fingerprint_data);
        
        // Create stable fingerprint
        ksort($all_components);
        $fingerprint_string = json_encode($all_components);
        
        return hash('sha256', $fingerprint_string);
    }
    
    /**
     * Store fingerprint with activity tracking
     * @param string $fingerprint Fingerprint hash
     * @param array $context Additional context
     */
    public static function trackFingerprint($fingerprint, $context = [])
    {
        global $db, $class_database;
        
        $user_ip = VIPaccess::getUserIP();
        $user_id = $_SESSION['USER_ID'] ?? null;
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        // Create table if needed
        self::createFingerprintTable();
        
        try {
            // Check if fingerprint exists
            $existing = $class_database->singleFieldValue('db_fingerprints', 'id', 'fingerprint_hash', $fingerprint);
            
            if ($existing) {
                // Update existing record
                $update_data = [
                    'last_seen' => date('Y-m-d H:i:s'),
                    'visit_count' => 'visit_count + 1',
                    'last_ip' => $user_ip,
                    'last_user_id' => $user_id,
                    'context' => json_encode($context)
                ];
                
                $sql = "UPDATE `db_fingerprints` SET 
                        `last_seen` = NOW(), 
                        `visit_count` = visit_count + 1,
                        `last_ip` = ?,
                        `last_user_id` = ?,
                        `context` = ?
                        WHERE `fingerprint_hash` = ?";
                
                $db->Execute($sql, [$user_ip, $user_id, json_encode($context), $fingerprint]);
            } else {
                // Insert new fingerprint
                $insert_data = [
                    'fingerprint_hash' => $fingerprint,
                    'first_seen' => date('Y-m-d H:i:s'),
                    'last_seen' => date('Y-m-d H:i:s'),
                    'visit_count' => 1,
                    'first_ip' => $user_ip,
                    'last_ip' => $user_ip,
                    'first_user_id' => $user_id,
                    'last_user_id' => $user_id,
                    'user_agent' => $user_agent,
                    'context' => json_encode($context)
                ];
                
                $class_database->doInsert('db_fingerprints', $insert_data);
            }
            
            // Log activity
            VIPTracker::logActivity('fingerprint_tracked', [
                'fingerprint' => substr($fingerprint, 0, 16) . '...',
                'context' => $context
            ]);
            
        } catch (Exception $e) {
            error_log("Fingerprint tracking error: " . $e->getMessage());
        }
    }
    
    /**
     * Ban a browser fingerprint
     * @param string $fingerprint Fingerprint hash
     * @param string $reason Ban reason
     * @param int $duration Duration in hours (0 = permanent)
     * @param string $banned_by Admin who issued the ban
     * @return bool Success status
     */
    public static function banFingerprint($fingerprint, $reason = 'Terms violation', $duration = 0, $banned_by = null)
    {
        global $db, $class_database;
        
        $expires_at = $duration > 0 ? date('Y-m-d H:i:s', time() + ($duration * 3600)) : null;
        $banned_by = $banned_by ?? ($_SESSION['ADMIN_NAME'] ?? 'System');
        
        // Create ban table if needed
        self::createFingerprintBanTable();
        
        try {
            // Check if already banned
            $existing = $class_database->singleFieldValue('db_fingerprint_bans', 'id', 'fingerprint_hash', $fingerprint);
            
            $ban_data = [
                'fingerprint_hash' => $fingerprint,
                'ban_reason' => $reason,
                'ban_active' => 1,
                'ban_date' => date('Y-m-d H:i:s'),
                'ban_expires' => $expires_at,
                'banned_by' => $banned_by
            ];
            
            if ($existing) {
                // Update existing ban
                return $class_database->doUpdate('db_fingerprint_bans', 'fingerprint_hash', $ban_data, null);
            } else {
                // Insert new ban
                return $class_database->doInsert('db_fingerprint_bans', $ban_data);
            }
            
        } catch (Exception $e) {
            error_log("Fingerprint ban error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Unban a browser fingerprint
     * @param string $fingerprint Fingerprint hash
     * @return bool Success status
     */
    public static function unbanFingerprint($fingerprint)
    {
        global $db;
        
        try {
            $sql = "UPDATE `db_fingerprint_bans` SET 
                    `ban_active` = 0, 
                    `unban_date` = NOW() 
                    WHERE `fingerprint_hash` = ?";
            
            $result = $db->Execute($sql, [$fingerprint]);
            return $db->Affected_Rows() > 0;
            
        } catch (Exception $e) {
            error_log("Fingerprint unban error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if fingerprint is banned
     * @param string $fingerprint Fingerprint hash
     * @return array|false Ban info or false if not banned
     */
    public static function isBanned($fingerprint)
    {
        global $db;
        
        try {
            $sql = "SELECT * FROM `db_fingerprint_bans` 
                    WHERE `fingerprint_hash` = ? 
                    AND `ban_active` = 1 
                    AND (`ban_expires` IS NULL OR `ban_expires` > NOW())";
            
            $result = $db->Execute($sql, [$fingerprint]);
            
            if ($result && !$result->EOF) {
                return [
                    'banned' => true,
                    'reason' => $result->fields['ban_reason'],
                    'ban_date' => $result->fields['ban_date'],
                    'expires' => $result->fields['ban_expires'],
                    'banned_by' => $result->fields['banned_by']
                ];
            }
            
            return false;
            
        } catch (Exception $e) {
            error_log("Fingerprint ban check error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get fingerprint statistics
     * @param string $fingerprint Fingerprint hash
     * @return array Statistics
     */
    public static function getFingerprintStats($fingerprint)
    {
        global $db;
        
        try {
            $sql = "SELECT * FROM `db_fingerprints` WHERE `fingerprint_hash` = ?";
            $result = $db->Execute($sql, [$fingerprint]);
            
            if ($result && !$result->EOF) {
                return [
                    'first_seen' => $result->fields['first_seen'],
                    'last_seen' => $result->fields['last_seen'],
                    'visit_count' => (int)$result->fields['visit_count'],
                    'first_ip' => $result->fields['first_ip'],
                    'last_ip' => $result->fields['last_ip'],
                    'first_user_id' => $result->fields['first_user_id'],
                    'last_user_id' => $result->fields['last_user_id'],
                    'user_agent' => $result->fields['user_agent'],
                    'context' => json_decode($result->fields['context'], true)
                ];
            }
            
            return null;
            
        } catch (Exception $e) {
            error_log("Fingerprint stats error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Detect suspicious fingerprint patterns
     * @param string $fingerprint Fingerprint hash
     * @return array Threat assessment
     */
    public static function detectFingerprintThreats($fingerprint)
    {
        global $db;
        
        $threats = [];
        $threat_level = 0;
        
        try {
            $stats = self::getFingerprintStats($fingerprint);
            
            if (!$stats) {
                return ['threat_level' => 0, 'threats' => [], 'risk_assessment' => 'NONE'];
            }
            
            // Check visit frequency
            $first_seen = strtotime($stats['first_seen']);
            $last_seen = strtotime($stats['last_seen']);
            $time_diff = $last_seen - $first_seen;
            
            if ($time_diff > 0) {
                $visits_per_hour = ($stats['visit_count'] * 3600) / $time_diff;
                
                if ($visits_per_hour > 100) {
                    $threats[] = 'Extremely high visit frequency: ' . round($visits_per_hour, 2) . ' visits/hour';
                    $threat_level += 4;
                } elseif ($visits_per_hour > 50) {
                    $threats[] = 'High visit frequency: ' . round($visits_per_hour, 2) . ' visits/hour';
                    $threat_level += 2;
                }
            }
            
            // Check IP switching behavior
            if ($stats['first_ip'] !== $stats['last_ip']) {
                $sql = "SELECT COUNT(DISTINCT last_ip) as ip_count 
                        FROM db_fingerprints 
                        WHERE fingerprint_hash = ?";
                
                $result = $db->Execute($sql, [$fingerprint]);
                $ip_count = $result->fields['ip_count'] ?? 1;
                
                if ($ip_count > 10) {
                    $threats[] = 'Excessive IP switching: ' . $ip_count . ' different IPs';
                    $threat_level += 3;
                } elseif ($ip_count > 5) {
                    $threats[] = 'Multiple IP addresses: ' . $ip_count . ' IPs';
                    $threat_level += 1;
                }
            }
            
            // Check user account switching
            if ($stats['first_user_id'] && $stats['last_user_id'] && 
                $stats['first_user_id'] !== $stats['last_user_id']) {
                
                $sql = "SELECT COUNT(DISTINCT last_user_id) as user_count 
                        FROM db_fingerprints 
                        WHERE fingerprint_hash = ? AND last_user_id IS NOT NULL";
                
                $result = $db->Execute($sql, [$fingerprint]);
                $user_count = $result->fields['user_count'] ?? 1;
                
                if ($user_count > 5) {
                    $threats[] = 'Multiple user accounts: ' . $user_count . ' accounts';
                    $threat_level += 2;
                }
            }
            
            // Check for bot-like user agent
            $ua = strtolower($stats['user_agent']);
            $bot_indicators = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java'];
            
            foreach ($bot_indicators as $indicator) {
                if (strpos($ua, $indicator) !== false) {
                    $threats[] = 'Bot-like user agent detected';
                    $threat_level += 2;
                    break;
                }
            }
            
        } catch (Exception $e) {
            error_log("Fingerprint threat detection error: " . $e->getMessage());
        }
        
        return [
            'threat_level' => $threat_level,
            'threats' => $threats,
            'risk_assessment' => self::getRiskLevel($threat_level)
        ];
    }
    
    /**
     * Auto-ban based on fingerprint threat detection
     * @param string $fingerprint Fingerprint hash
     * @return bool True if banned
     */
    public static function autoBanFingerprint($fingerprint)
    {
        $threats = self::detectFingerprintThreats($fingerprint);
        
        if ($threats['threat_level'] >= 5) {
            $reason = 'Auto-ban: ' . implode(', ', $threats['threats']);
            return self::banFingerprint($fingerprint, $reason, 48, 'Auto-System'); // 48 hour ban
        }
        
        return false;
    }
    
    /**
     * Get JavaScript code for client-side fingerprinting
     * @return string JavaScript code
     */
    public static function getFingerprintingJS()
    {
        return '
<script>
function generateFingerprint() {
    const canvas = document.createElement("canvas");
    const ctx = canvas.getContext("2d");
    ctx.textBaseline = "top";
    ctx.font = "14px Arial";
    ctx.fillText("Browser fingerprint", 2, 2);
    
    const fingerprint = {
        screen_resolution: screen.width + "x" + screen.height,
        screen_color_depth: screen.colorDepth,
        timezone_offset: new Date().getTimezoneOffset(),
        language: navigator.language,
        languages: navigator.languages ? navigator.languages.join(",") : "",
        platform: navigator.platform,
        cookie_enabled: navigator.cookieEnabled,
        do_not_track: navigator.doNotTrack,
        canvas_fingerprint: canvas.toDataURL(),
        webgl_vendor: getWebGLVendor(),
        webgl_renderer: getWebGLRenderer(),
        plugins: getPluginsList(),
        fonts: detectFonts(),
        audio_fingerprint: getAudioFingerprint(),
        hardware_concurrency: navigator.hardwareConcurrency,
        device_memory: navigator.deviceMemory,
        connection_type: navigator.connection ? navigator.connection.effectiveType : "",
        touch_support: "ontouchstart" in window,
        local_storage: typeof(Storage) !== "undefined",
        session_storage: typeof(sessionStorage) !== "undefined",
        indexed_db: typeof(indexedDB) !== "undefined",
        webrtc_fingerprint: getWebRTCFingerprint()
    };
    
    return fingerprint;
}

function getWebGLVendor() {
    try {
        const canvas = document.createElement("canvas");
        const gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
        if (gl) {
            const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
            return debugInfo ? gl.getParameter(debugInfo.UNMASKED_VENDOR_WEBGL) : "";
        }
    } catch (e) {}
    return "";
}

function getWebGLRenderer() {
    try {
        const canvas = document.createElement("canvas");
        const gl = canvas.getContext("webgl") || canvas.getContext("experimental-webgl");
        if (gl) {
            const debugInfo = gl.getExtension("WEBGL_debug_renderer_info");
            return debugInfo ? gl.getParameter(debugInfo.UNMASKED_RENDERER_WEBGL) : "";
        }
    } catch (e) {}
    return "";
}

function getPluginsList() {
    const plugins = [];
    for (let i = 0; i < navigator.plugins.length; i++) {
        plugins.push(navigator.plugins[i].name);
    }
    return plugins.join(",");
}

function detectFonts() {
    const testFonts = ["Arial", "Helvetica", "Times", "Courier", "Verdana", "Georgia", "Palatino", "Garamond", "Bookman", "Comic Sans MS", "Trebuchet MS", "Arial Black", "Impact"];
    const detectedFonts = [];
    
    const testString = "mmmmmmmmmmlli";
    const testSize = "72px";
    const h = document.getElementsByTagName("body")[0];
    
    const baseFonts = ["monospace", "sans-serif", "serif"];
    const testDiv = document.createElement("div");
    testDiv.style.position = "absolute";
    testDiv.style.left = "-9999px";
    testDiv.style.fontSize = testSize;
    testDiv.innerHTML = testString;
    
    const defaultWidths = {};
    for (let i = 0; i < baseFonts.length; i++) {
        testDiv.style.fontFamily = baseFonts[i];
        h.appendChild(testDiv);
        defaultWidths[baseFonts[i]] = testDiv.offsetWidth;
        h.removeChild(testDiv);
    }
    
    for (let i = 0; i < testFonts.length; i++) {
        let detected = false;
        for (let j = 0; j < baseFonts.length; j++) {
            testDiv.style.fontFamily = testFonts[i] + "," + baseFonts[j];
            h.appendChild(testDiv);
            const matched = (testDiv.offsetWidth !== defaultWidths[baseFonts[j]]);
            h.removeChild(testDiv);
            detected = detected || matched;
        }
        if (detected) {
            detectedFonts.push(testFonts[i]);
        }
    }
    
    return detectedFonts.join(",");
}

function getAudioFingerprint() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const analyser = audioContext.createAnalyser();
        const gainNode = audioContext.createGain();
        
        oscillator.type = "triangle";
        oscillator.frequency.setValueAtTime(10000, audioContext.currentTime);
        
        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
        oscillator.connect(analyser);
        analyser.connect(gainNode);
        gainNode.connect(audioContext.destination);
        oscillator.start(0);
        
        const frequencyData = new Uint8Array(analyser.frequencyBinCount);
        analyser.getByteFrequencyData(frequencyData);
        
        oscillator.stop();
        audioContext.close();
        
        return Array.from(frequencyData).slice(0, 30).join(",");
    } catch (e) {
        return "";
    }
}

function getWebRTCFingerprint() {
    return new Promise((resolve) => {
        try {
            const pc = new RTCPeerConnection({iceServers: []});
            pc.createDataChannel("");
            pc.createOffer().then((offer) => {
                pc.setLocalDescription(offer);
                const lines = offer.sdp.split("\\n");
                const fingerprint = lines.find(line => line.indexOf("a=fingerprint:") === 0);
                resolve(fingerprint || "");
            }).catch(() => resolve(""));
        } catch (e) {
            resolve("");
        }
    });
}

// Send fingerprint to server
function sendFingerprint() {
    const fingerprint = generateFingerprint();
    
    getWebRTCFingerprint().then((webrtc) => {
        fingerprint.webrtc_fingerprint = webrtc;
        
        fetch("/fingerprint_handler.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify(fingerprint)
        }).catch(console.error);
    });
}

// Auto-send fingerprint when page loads
if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", sendFingerprint);
} else {
    sendFingerprint();
}
</script>';
    }
    
    /**
     * Get risk level description
     * @param int $threat_level Numeric threat level
     * @return string Risk description
     */
    private static function getRiskLevel($threat_level)
    {
        if ($threat_level >= 5) return 'HIGH';
        if ($threat_level >= 3) return 'MEDIUM';
        if ($threat_level >= 1) return 'LOW';
        return 'NONE';
    }
    
    /**
     * Create fingerprint tracking table
     */
    private static function createFingerprintTable()
    {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS `db_fingerprints` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fingerprint_hash` varchar(64) NOT NULL,
            `first_seen` datetime NOT NULL,
            `last_seen` datetime NOT NULL,
            `visit_count` int(11) NOT NULL DEFAULT 1,
            `first_ip` varchar(45) DEFAULT NULL,
            `last_ip` varchar(45) DEFAULT NULL,
            `first_user_id` int(11) DEFAULT NULL,
            `last_user_id` int(11) DEFAULT NULL,
            `user_agent` text,
            `context` text,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_fingerprint` (`fingerprint_hash`),
            KEY `idx_last_seen` (`last_seen`),
            KEY `idx_user_id` (`last_user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->Execute($sql);
        } catch (Exception $e) {
            error_log("Create fingerprint table error: " . $e->getMessage());
        }
    }
    
    /**
     * Create fingerprint ban table
     */
    private static function createFingerprintBanTable()
    {
        global $db;
        
        $sql = "CREATE TABLE IF NOT EXISTS `db_fingerprint_bans` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `fingerprint_hash` varchar(64) NOT NULL,
            `ban_reason` varchar(500) NOT NULL,
            `ban_active` tinyint(1) NOT NULL DEFAULT 1,
            `ban_date` datetime NOT NULL,
            `ban_expires` datetime DEFAULT NULL,
            `unban_date` datetime DEFAULT NULL,
            `banned_by` varchar(100) NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_fingerprint` (`fingerprint_hash`),
            KEY `idx_active_expires` (`ban_active`, `ban_expires`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        
        try {
            $db->Execute($sql);
        } catch (Exception $e) {
            error_log("Create fingerprint ban table error: " . $e->getMessage());
        }
    }
}
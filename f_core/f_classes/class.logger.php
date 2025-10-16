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
 * Enhanced Logging System for Error Tracking and Debugging
 */
class VLogger
{
    const EMERGENCY = 'emergency';
    const ALERT     = 'alert';
    const CRITICAL  = 'critical';
    const ERROR     = 'error';
    const WARNING   = 'warning';
    const NOTICE    = 'notice';
    const INFO      = 'info';
    const DEBUG     = 'debug';
    
    private static $instance = null;
    private $logPath = 'f_data/logs/';
    private $maxFileSize = 10485760; // 10MB
    private $maxFiles = 5;
    private $context = [];
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        // Ensure log directory exists
        if (!is_dir($this->logPath)) {
            mkdir($this->logPath, 0755, true);
        }
        
        // Set global context
        $this->setGlobalContext();
    }
    
    /**
     * Set global context information
     */
    private function setGlobalContext()
    {
        $this->context = [
            'timestamp' => date('Y-m-d H:i:s'),
            'request_id' => $this->generateRequestId(),
            'ip' => $this->getClientIP(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
            'request_method' => $_SERVER['REQUEST_METHOD'] ?? '',
            'user_id' => $_SESSION['USER_ID'] ?? null,
            'session_id' => session_id() ?: null,
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - ($_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true))
        ];
    }
    
    /**
     * Generate unique request ID for tracking
     */
    private function generateRequestId()
    {
        return uniqid('req_', true);
    }
    
    /**
     * Get real client IP address
     */
    private function getClientIP()
    {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
    
    /**
     * Log message with specified level
     */
    public function log($level, $message, $context = [])
    {
        $logData = array_merge($this->context, [
            'level' => $level,
            'message' => $message,
            'context' => $context,
            'backtrace' => $this->getBacktrace()
        ]);
        
        // Write to appropriate log files
        $this->writeToFile($level, $logData);
        
        // Write to database if configured
        $this->writeToDatabase($level, $logData);
        
        // Send alerts for critical errors
        if (in_array($level, [self::EMERGENCY, self::ALERT, self::CRITICAL, self::ERROR])) {
            $this->sendAlert($level, $logData);
        }
    }
    
    /**
     * Get formatted backtrace
     */
    private function getBacktrace()
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
        $formattedTrace = [];
        
        foreach ($trace as $i => $frame) {
            if ($i === 0) continue; // Skip current function
            
            $formattedTrace[] = [
                'file' => $frame['file'] ?? 'unknown',
                'line' => $frame['line'] ?? 0,
                'function' => $frame['function'] ?? 'unknown',
                'class' => $frame['class'] ?? null
            ];
        }
        
        return $formattedTrace;
    }
    
    /**
     * Write log to file
     */
    private function writeToFile($level, $logData)
    {
        $filename = $this->logPath . date('Y-m-d') . '_' . $level . '.log';
        
        // Rotate log if too large
        if (file_exists($filename) && filesize($filename) > $this->maxFileSize) {
            $this->rotateLogFile($filename);
        }
        
        $logLine = json_encode($logData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        
        file_put_contents($filename, $logLine, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Rotate log files
     */
    private function rotateLogFile($filename)
    {
        for ($i = $this->maxFiles - 1; $i > 0; $i--) {
            $oldFile = $filename . '.' . $i;
            $newFile = $filename . '.' . ($i + 1);
            
            if (file_exists($oldFile)) {
                if ($i === $this->maxFiles - 1) {
                    unlink($oldFile);
                } else {
                    rename($oldFile, $newFile);
                }
            }
        }
        
        if (file_exists($filename)) {
            rename($filename, $filename . '.1');
        }
    }
    
    /**
     * Write to database (optional)
     */
    private function writeToDatabase($level, $logData)
    {
        global $db, $cfg;
        
        // Only log to database if enabled in config
        if (!isset($cfg['database_logging']) || !$cfg['database_logging']) {
            return;
        }
        
        try {
            $sql = "INSERT INTO `db_logs` (`level`, `message`, `context`, `request_id`, `user_id`, `ip`, `created_at`) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $db->Execute($sql, [
                $level,
                $logData['message'],
                json_encode($logData['context']),
                $logData['request_id'],
                $logData['user_id'],
                $logData['ip']
            ]);
        } catch (Exception $e) {
            // Fallback to file logging if database fails
            error_log("Database logging failed: " . $e->getMessage());
        }
    }
    
    /**
     * Send alerts for critical errors
     */
    private function sendAlert($level, $logData)
    {
        global $cfg;
        
        // Only send alerts if configured
        if (!isset($cfg['error_alerts']) || !$cfg['error_alerts']) {
            return;
        }
        
        // Rate limit alerts to prevent spam
        $alertKey = 'alert_' . md5($logData['message']);
        if (!$this->checkAlertRateLimit($alertKey)) {
            return;
        }
        
        $subject = "EasyStream {$level}: " . substr($logData['message'], 0, 50);
        $body = $this->formatAlertEmail($logData);
        
        // Send email alert (implement based on your email system)
        if (isset($cfg['admin_email']) && !empty($cfg['admin_email'])) {
            $this->sendEmailAlert($cfg['admin_email'], $subject, $body);
        }
    }
    
    /**
     * Check alert rate limiting
     */
    private function checkAlertRateLimit($key, $maxAlerts = 5, $timeWindow = 3600)
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        
        $now = time();
        $alertKey = 'alert_limit_' . $key;
        
        if (!isset($_SESSION[$alertKey])) {
            $_SESSION[$alertKey] = [];
        }
        
        // Clean old alerts
        $_SESSION[$alertKey] = array_filter($_SESSION[$alertKey], function($timestamp) use ($now, $timeWindow) {
            return ($now - $timestamp) < $timeWindow;
        });
        
        // Check if limit exceeded
        if (count($_SESSION[$alertKey]) >= $maxAlerts) {
            return false;
        }
        
        // Add current alert
        $_SESSION[$alertKey][] = $now;
        
        return true;
    }
    
    /**
     * Format alert email
     */
    private function formatAlertEmail($logData)
    {
        $body = "Error Details:\n\n";
        $body .= "Level: " . strtoupper($logData['level']) . "\n";
        $body .= "Message: " . $logData['message'] . "\n";
        $body .= "Request ID: " . $logData['request_id'] . "\n";
        $body .= "Time: " . $logData['timestamp'] . "\n";
        $body .= "IP: " . $logData['ip'] . "\n";
        $body .= "User ID: " . ($logData['user_id'] ?: 'Guest') . "\n";
        $body .= "URI: " . $logData['request_uri'] . "\n";
        $body .= "Method: " . $logData['request_method'] . "\n";
        $body .= "User Agent: " . $logData['user_agent'] . "\n\n";
        
        if (!empty($logData['context'])) {
            $body .= "Context:\n" . json_encode($logData['context'], JSON_PRETTY_PRINT) . "\n\n";
        }
        
        if (!empty($logData['backtrace'])) {
            $body .= "Stack Trace:\n";
            foreach ($logData['backtrace'] as $i => $frame) {
                $body .= "#{$i} {$frame['file']}:{$frame['line']} ";
                if ($frame['class']) {
                    $body .= "{$frame['class']}::{$frame['function']}()\n";
                } else {
                    $body .= "{$frame['function']}()\n";
                }
            }
        }
        
        return $body;
    }
    
    /**
     * Send email alert
     */
    private function sendEmailAlert($to, $subject, $body)
    {
        // Use your existing email system or implement basic mail
        $headers = "From: noreply@" . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        
        mail($to, $subject, $body, $headers);
    }
    
    // Convenience methods for different log levels
    public function emergency($message, $context = []) { $this->log(self::EMERGENCY, $message, $context); }
    public function alert($message, $context = []) { $this->log(self::ALERT, $message, $context); }
    public function critical($message, $context = []) { $this->log(self::CRITICAL, $message, $context); }
    public function error($message, $context = []) { $this->log(self::ERROR, $message, $context); }
    public function warning($message, $context = []) { $this->log(self::WARNING, $message, $context); }
    public function notice($message, $context = []) { $this->log(self::NOTICE, $message, $context); }
    public function info($message, $context = []) { $this->log(self::INFO, $message, $context); }
    public function debug($message, $context = []) { $this->log(self::DEBUG, $message, $context); }
    
    /**
     * Log database errors with query information
     */
    public function logDatabaseError($error, $query = '', $params = [])
    {
        $this->error('Database Error: ' . $error, [
            'query' => $query,
            'parameters' => $params,
            'database_error' => true
        ]);
    }
    
    /**
     * Log security events
     */
    public function logSecurityEvent($event, $context = [])
    {
        $this->warning('Security Event: ' . $event, array_merge($context, [
            'security_event' => true
        ]));
    }
    
    /**
     * Log performance issues
     */
    public function logPerformanceIssue($message, $executionTime, $context = [])
    {
        $this->notice('Performance Issue: ' . $message, array_merge($context, [
            'execution_time' => $executionTime,
            'performance_issue' => true
        ]));
    }
    
    /**
     * Get recent logs for admin dashboard
     */
    public function getRecentLogs($level = null, $limit = 100)
    {
        $logs = [];
        $pattern = $this->logPath . date('Y-m-d') . '_' . ($level ?: '*') . '.log';
        
        foreach (glob($pattern) as $file) {
            $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lines = array_slice($lines, -$limit);
            
            foreach ($lines as $line) {
                $logEntry = json_decode($line, true);
                if ($logEntry) {
                    $logs[] = $logEntry;
                }
            }
        }
        
        // Sort by timestamp
        usort($logs, function($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });
        
        return array_slice($logs, 0, $limit);
    }
}
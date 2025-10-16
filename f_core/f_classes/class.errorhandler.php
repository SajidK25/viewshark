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
 * Global Error Handler for comprehensive error tracking
 */
class VErrorHandler
{
    private static $instance = null;
    private $logger;
    private $isProduction = true;
    
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct()
    {
        $this->logger = VLogger::getInstance();
        
        // Determine if we're in production mode
        global $cfg;
        $this->isProduction = !isset($cfg['debug_mode']) || !$cfg['debug_mode'];
        
        // Register error handlers
        $this->registerHandlers();
    }
    
    /**
     * Register all error handlers
     */
    private function registerHandlers()
    {
        // Set error handler for PHP errors
        set_error_handler([$this, 'handleError']);
        
        // Set exception handler for uncaught exceptions
        set_exception_handler([$this, 'handleException']);
        
        // Set shutdown handler for fatal errors
        register_shutdown_function([$this, 'handleShutdown']);
        
        // Set error reporting level
        if ($this->isProduction) {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', 0);
            ini_set('log_errors', 1);
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
        }
    }
    
    /**
     * Handle PHP errors
     */
    public function handleError($severity, $message, $file, $line, $context = [])
    {
        // Don't handle errors that are suppressed with @
        if (!(error_reporting() & $severity)) {
            return false;
        }
        
        $errorType = $this->getErrorType($severity);
        $logLevel = $this->getLogLevel($severity);
        
        $errorContext = [
            'error_type' => $errorType,
            'severity' => $severity,
            'file' => $file,
            'line' => $line,
            'context' => $context
        ];
        
        $errorMessage = "{$errorType}: {$message} in {$file} on line {$line}";
        
        $this->logger->log($logLevel, $errorMessage, $errorContext);
        
        // Don't execute PHP internal error handler
        return true;
    }
    
    /**
     * Handle uncaught exceptions
     */
    public function handleException($exception)
    {
        $errorContext = [
            'exception_class' => get_class($exception),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString(),
            'previous' => $exception->getPrevious() ? $exception->getPrevious()->getMessage() : null
        ];
        
        $errorMessage = "Uncaught Exception: " . $exception->getMessage() . 
                       " in " . $exception->getFile() . " on line " . $exception->getLine();
        
        $this->logger->critical($errorMessage, $errorContext);
        
        // Show user-friendly error page in production
        if ($this->isProduction) {
            $this->showErrorPage('An unexpected error occurred. Please try again later.');
        } else {
            $this->showDebugError($exception);
        }
    }
    
    /**
     * Handle fatal errors during shutdown
     */
    public function handleShutdown()
    {
        $error = error_get_last();
        
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $errorType = $this->getErrorType($error['type']);
            
            $errorContext = [
                'error_type' => $errorType,
                'severity' => $error['type'],
                'file' => $error['file'],
                'line' => $error['line']
            ];
            
            $errorMessage = "Fatal Error: {$error['message']} in {$error['file']} on line {$error['line']}";
            
            $this->logger->critical($errorMessage, $errorContext);
            
            // Show error page for fatal errors
            if ($this->isProduction) {
                $this->showErrorPage('A critical error occurred. Please contact support.');
            }
        }
    }
    
    /**
     * Get error type string from severity level
     */
    private function getErrorType($severity)
    {
        $errorTypes = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict Standards',
            E_RECOVERABLE_ERROR => 'Recoverable Error',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];
        
        return $errorTypes[$severity] ?? 'Unknown Error';
    }
    
    /**
     * Get log level from error severity
     */
    private function getLogLevel($severity)
    {
        switch ($severity) {
            case E_ERROR:
            case E_PARSE:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
                return VLogger::ERROR;
                
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                return VLogger::WARNING;
                
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
                return VLogger::NOTICE;
                
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                return VLogger::INFO;
                
            default:
                return VLogger::ERROR;
        }
    }
    
    /**
     * Show user-friendly error page
     */
    private function showErrorPage($message)
    {
        // Clear any output buffer
        if (ob_get_level()) {
            ob_clean();
        }
        
        http_response_code(500);
        
        // Try to use existing error template
        if (file_exists('error.php')) {
            include 'error.php';
        } else {
            // Fallback error page
            echo $this->getErrorPageHTML($message);
        }
        
        exit;
    }
    
    /**
     * Show debug error information
     */
    private function showDebugError($exception)
    {
        if (ob_get_level()) {
            ob_clean();
        }
        
        http_response_code(500);
        
        echo $this->getDebugErrorHTML($exception);
        exit;
    }
    
    /**
     * Get error page HTML
     */
    private function getErrorPageHTML($message)
    {
        return '<!DOCTYPE html>
<html>
<head>
    <title>Error - EasyStream</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f5f5f5; }
        .error-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .error-title { color: #d32f2f; font-size: 24px; margin-bottom: 20px; }
        .error-message { color: #666; line-height: 1.6; }
        .error-actions { margin-top: 30px; }
        .btn { background: #1976d2; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; }
        .btn:hover { background: #1565c0; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-title">Oops! Something went wrong</h1>
        <p class="error-message">' . htmlspecialchars($message) . '</p>
        <div class="error-actions">
            <a href="/" class="btn">Go Home</a>
            <a href="javascript:history.back()" class="btn" style="background: #666; margin-left: 10px;">Go Back</a>
        </div>
    </div>
</body>
</html>';
    }
    
    /**
     * Get debug error HTML
     */
    private function getDebugErrorHTML($exception)
    {
        $trace = $exception->getTraceAsString();
        
        return '<!DOCTYPE html>
<html>
<head>
    <title>Debug Error - EasyStream</title>
    <style>
        body { font-family: monospace; margin: 20px; background: #1e1e1e; color: #fff; }
        .error-header { background: #d32f2f; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .error-details { background: #2d2d2d; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .trace { background: #1a1a1a; padding: 15px; border-radius: 4px; overflow-x: auto; }
        pre { margin: 0; white-space: pre-wrap; }
        .file-line { color: #ffeb3b; }
        .message { color: #f44336; font-weight: bold; }
    </style>
</head>
<body>
    <div class="error-header">
        <h1>Exception: ' . htmlspecialchars(get_class($exception)) . '</h1>
    </div>
    
    <div class="error-details">
        <p><strong>Message:</strong> <span class="message">' . htmlspecialchars($exception->getMessage()) . '</span></p>
        <p><strong>File:</strong> <span class="file-line">' . htmlspecialchars($exception->getFile()) . ':' . $exception->getLine() . '</span></p>
    </div>
    
    <div class="trace">
        <h3>Stack Trace:</h3>
        <pre>' . htmlspecialchars($trace) . '</pre>
    </div>
</body>
</html>';
    }
    
    /**
     * Log custom application errors
     */
    public function logApplicationError($message, $context = [])
    {
        $this->logger->error('Application Error: ' . $message, $context);
    }
    
    /**
     * Log validation errors
     */
    public function logValidationError($field, $value, $rule, $context = [])
    {
        $message = "Validation failed for field '{$field}' with value '{$value}' against rule '{$rule}'";
        $this->logger->warning($message, array_merge($context, [
            'validation_error' => true,
            'field' => $field,
            'value' => $value,
            'rule' => $rule
        ]));
    }
    
    /**
     * Log authentication errors
     */
    public function logAuthError($message, $username = null, $context = [])
    {
        $this->logger->warning('Authentication Error: ' . $message, array_merge($context, [
            'auth_error' => true,
            'username' => $username
        ]));
    }
}
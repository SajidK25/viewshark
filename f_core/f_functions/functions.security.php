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
 * Security helper functions
 */

/**
 * Secure output escaping for templates
 * @param string $string String to escape
 * @return string Escaped string
 */
function secure_output($string)
{
    return VSecurity::escapeOutput($string);
}

/**
 * Secure JavaScript output escaping
 * @param string $string String to escape for JS
 * @return string Escaped string
 */
function secure_js($string)
{
    return VSecurity::escapeJS($string);
}

/**
 * Generate CSRF token field for forms
 * @param string $action Action name
 * @return string HTML input field
 */
function csrf_field($action = 'default')
{
    return VSecurity::getCSRFField($action);
}

/**
 * Validate CSRF token from current request
 * @param string $action Action name
 * @return bool True if valid
 */
function validate_csrf($action = 'default')
{
    return VSecurity::validateCSRFFromPost($action);
}

/**
 * Get secure parameter from GET
 * @param string $key Parameter name
 * @param string $type Parameter type
 * @param mixed $default Default value
 * @param array $options Validation options
 * @return mixed Sanitized value
 */
function get_param($key, $type = 'string', $default = null, $options = [])
{
    return VSecurity::getParam($key, $type, $default, $options);
}

/**
 * Get secure parameter from POST
 * @param string $key Parameter name
 * @param string $type Parameter type
 * @param mixed $default Default value
 * @param array $options Validation options
 * @return mixed Sanitized value
 */
function post_param($key, $type = 'string', $default = null, $options = [])
{
    return VSecurity::postParam($key, $type, $default, $options);
}

/**
 * Check rate limiting
 * @param string $key Unique identifier
 * @param int $maxAttempts Maximum attempts
 * @param int $timeWindow Time window in seconds
 * @return bool True if within limits
 */
function check_rate_limit($key, $maxAttempts = 10, $timeWindow = 300)
{
    return VSecurity::checkRateLimit($key, $maxAttempts, $timeWindow);
}

/**
 * Validate file upload securely
 * @param array $file $_FILES array element
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size
 * @return array Validation result
 */
function validate_file_upload($file, $allowedTypes = [], $maxSize = 10485760)
{
    return VSecurity::validateFileUpload($file, $allowedTypes, $maxSize);
}

/**
 * Log security events
 * @param string $event Event description
 * @param array $context Additional context
 */
function log_security_event($event, $context = [])
{
    $logger = VLogger::getInstance();
    $logger->logSecurityEvent($event, $context);
}

/**
 * Log application errors with context
 * @param string $message Error message
 * @param array $context Additional context
 */
function log_app_error($message, $context = [])
{
    $errorHandler = VErrorHandler::getInstance();
    $errorHandler->logApplicationError($message, $context);
}

/**
 * Log validation errors
 * @param string $field Field name
 * @param mixed $value Field value
 * @param string $rule Validation rule
 * @param array $context Additional context
 */
function log_validation_error($field, $value, $rule, $context = [])
{
    $errorHandler = VErrorHandler::getInstance();
    $errorHandler->logValidationError($field, $value, $rule, $context);
}

/**
 * Log authentication errors
 * @param string $message Error message
 * @param string $username Username (if available)
 * @param array $context Additional context
 */
function log_auth_error($message, $username = null, $context = [])
{
    $errorHandler = VErrorHandler::getInstance();
    $errorHandler->logAuthError($message, $username, $context);
}

/**
 * Log performance issues
 * @param string $message Performance issue description
 * @param float $executionTime Execution time in seconds
 * @param array $context Additional context
 */
function log_performance_issue($message, $executionTime, $context = [])
{
    $logger = VLogger::getInstance();
    $logger->logPerformanceIssue($message, $executionTime, $context);
}
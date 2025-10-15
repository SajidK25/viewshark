<?php
namespace Donations\Core;

abstract class Handler {
    protected $config;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->db = db();
        $this->logger = new Logger();
    }

    protected function validate($data, $rules) {
        $errors = [];
        
        foreach ($rules as $field => $rule) {
            if (!isset($data[$field]) && strpos($rule, 'required') !== false) {
                $errors[$field] = "The $field field is required.";
                continue;
            }

            if (isset($data[$field])) {
                if (strpos($rule, 'numeric') !== false && !is_numeric($data[$field])) {
                    $errors[$field] = "The $field must be a number.";
                }
                
                if (strpos($rule, 'min:') !== false) {
                    $min = substr($rule, strpos($rule, 'min:') + 4);
                    if ($data[$field] < $min) {
                        $errors[$field] = "The $field must be at least $min.";
                    }
                }
                
                if (strpos($rule, 'max:') !== false) {
                    $max = substr($rule, strpos($rule, 'max:') + 4);
                    if ($data[$field] > $max) {
                        $errors[$field] = "The $field must not be greater than $max.";
                    }
                }
                
                if (strpos($rule, 'email') !== false && !filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = "The $field must be a valid email address.";
                }
            }
        }
        
        return $errors;
    }

    protected function log($message, $level = 'info') {
        $this->logger->log($message, $level);
    }

    protected function error($message, $code = 500) {
        $this->log($message, 'error');
        throw new \Exception($message, $code);
    }

    protected function success($message, $data = null) {
        $this->log($message, 'info');
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }

    protected function formatAmount($amount) {
        return number_format($amount, 2, '.', '');
    }

    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function generateUniqueId($prefix = '') {
        return $prefix . uniqid() . bin2hex(random_bytes(8));
    }

    protected function getClientIp() {
        $ip = $_SERVER['REMOTE_ADDR'];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
    }

    protected function isAllowedIp($ip) {
        return empty($this->config['api']['allowed_ips']) || 
               in_array($ip, $this->config['api']['allowed_ips']);
    }
} 
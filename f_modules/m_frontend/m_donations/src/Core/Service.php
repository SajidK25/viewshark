<?php
namespace Donations\Core;

abstract class Service {
    protected $config;
    protected $db;
    protected $logger;

    public function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->db = db();
        $this->logger = new Logger();
    }

    protected function beginTransaction() {
        return $this->db->beginTransaction();
    }

    protected function commit() {
        return $this->db->commit();
    }

    protected function rollback() {
        return $this->db->rollback();
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
            }
        }
        
        if (!empty($errors)) {
            throw new \Exception(json_encode($errors));
        }
        
        return true;
    }

    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    protected function log($message, $level = 'info') {
        $this->logger->log($message, $level);
    }

    protected function formatAmount($amount) {
        return number_format($amount, 2, '.', '');
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

    protected function formatDate($date, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($date));
    }

    protected function truncate($text, $length = 100) {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    protected function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    protected function isActive($path) {
        return strpos($_SERVER['REQUEST_URI'], $path) !== false ? 'active' : '';
    }
} 
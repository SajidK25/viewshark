<?php
namespace Donations\Core;

class Api {
    protected $config;
    protected $db;
    protected $logger;
    protected $request;
    protected $response;

    public function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->db = db();
        $this->logger = new Logger();
        $this->request = $this->parseRequest();
        $this->response = [
            'success' => false,
            'message' => '',
            'data' => null
        ];
    }

    protected function parseRequest() {
        $request = [
            'method' => $_SERVER['REQUEST_METHOD'],
            'path' => parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
            'query' => $_GET,
            'body' => json_decode(file_get_contents('php://input'), true) ?? [],
            'headers' => getallheaders()
        ];

        return $request;
    }

    protected function validateApiKey() {
        $apiKey = $this->request['headers']['X-API-Key'] ?? null;
        
        if (!$apiKey) {
            $this->error('API key is required', 401);
            return false;
        }

        $sql = "SELECT * FROM api_keys WHERE api_key = ? AND is_active = 1";
        $key = $this->db->fetch($sql, [$apiKey]);

        if (!$key) {
            $this->error('Invalid API key', 401);
            return false;
        }

        if (!$this->checkRateLimit($key['id'])) {
            $this->error('Rate limit exceeded', 429);
            return false;
        }

        return true;
    }

    protected function checkRateLimit($apiKeyId) {
        $timeframe = $this->config['api']['rate_limit']['timeframe'];
        $maxRequests = $this->config['api']['rate_limit']['max_requests'];

        $sql = "SELECT COUNT(*) as count FROM api_rate_limits 
                WHERE api_key_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL ? SECOND)";
        
        $result = $this->db->fetch($sql, [$apiKeyId, $timeframe]);

        if ($result['count'] >= $maxRequests) {
            return false;
        }

        $sql = "INSERT INTO api_rate_limits (api_key_id) VALUES (?)";
        $this->db->execute($sql, [$apiKeyId]);

        return true;
    }

    protected function success($message, $data = null) {
        $this->response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        $this->sendResponse();
    }

    protected function error($message, $code = 400) {
        $this->response = [
            'success' => false,
            'message' => $message,
            'data' => null
        ];
        $this->sendResponse($code);
    }

    protected function sendResponse($code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($this->response);
        exit;
    }

    protected function validateInput($data, $rules) {
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
            $this->error('Validation failed', 422);
        }
        
        return true;
    }

    protected function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
} 
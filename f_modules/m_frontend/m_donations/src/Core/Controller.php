<?php
namespace Donations\Core;

class Controller {
    protected $config;
    protected $db;
    protected $logger;
    protected $view;
    protected $request;
    protected $response;

    public function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->db = db();
        $this->logger = new Logger();
        $this->view = new View();
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

    protected function validateCsrf() {
        if ($this->request['method'] === 'POST') {
            $token = $_POST['csrf_token'] ?? null;
            if (!$token || $token !== $_SESSION['csrf_token']) {
                $this->error('Invalid CSRF token', 403);
                return false;
            }
        }
        return true;
    }

    protected function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            $this->redirect('/login');
            return false;
        }
        return true;
    }

    protected function requireAdmin() {
        if (!$this->requireAuth()) {
            return false;
        }

        $sql = "SELECT is_admin FROM users WHERE id = ?";
        $user = $this->db->fetch($sql, [$_SESSION['user_id']]);

        if (!$user['is_admin']) {
            $this->error('Unauthorized access', 403);
            return false;
        }

        return true;
    }

    protected function redirect($path) {
        header('Location: ' . $this->config['base_url'] . $path);
        exit;
    }

    protected function json($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function success($message, $data = null) {
        $this->response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        $this->json($this->response);
    }

    protected function error($message, $code = 400) {
        $this->response = [
            'success' => false,
            'message' => $message,
            'data' => null
        ];
        $this->json($this->response, $code);
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

    protected function log($message, $level = 'info') {
        $this->logger->log($message, $level);
    }
} 
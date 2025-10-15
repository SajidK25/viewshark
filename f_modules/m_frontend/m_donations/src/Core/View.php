<?php
namespace Donations\Core;

class View {
    protected $config;
    protected $data = [];
    protected $layout = 'default';
    protected $view;

    public function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function setView($view) {
        $this->view = $view;
    }

    public function setData($data) {
        $this->data = array_merge($this->data, $data);
    }

    public function render() {
        if (!$this->view) {
            throw new \Exception('View not set');
        }

        $viewFile = DONATIONS_PATH . '/views/' . $this->view . '.php';
        if (!file_exists($viewFile)) {
            throw new \Exception("View file not found: {$this->view}");
        }

        $layoutFile = DONATIONS_PATH . '/views/layouts/' . $this->layout . '.php';
        if (!file_exists($layoutFile)) {
            throw new \Exception("Layout file not found: {$this->layout}");
        }

        extract($this->data);

        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        include $layoutFile;
    }

    public function partial($name, $data = []) {
        $partialFile = DONATIONS_PATH . '/views/partials/' . $name . '.php';
        if (!file_exists($partialFile)) {
            throw new \Exception("Partial file not found: {$name}");
        }

        extract(array_merge($this->data, $data));
        include $partialFile;
    }

    public function asset($path) {
        return $this->config['base_url'] . '/assets/' . $path;
    }

    public function url($path) {
        return $this->config['base_url'] . '/' . $path;
    }

    public function csrfToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function csrfField() {
        return '<input type="hidden" name="csrf_token" value="' . $this->csrfToken() . '">';
    }

    public function formatAmount($amount) {
        return number_format($amount, 2, '.', ',');
    }

    public function formatDate($date, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($date));
    }

    public function truncate($text, $length = 100) {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . '...';
    }

    public function escape($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    public function isActive($path) {
        return strpos($_SERVER['REQUEST_URI'], $path) !== false ? 'active' : '';
    }
} 
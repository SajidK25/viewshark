<?php
namespace Donations\Core;

class Session {
    private static $instance = null;
    private $config;
    private $started = false;

    private function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
        $this->start();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function start() {
        if (!$this->started) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start([
                    'cookie_httponly' => true,
                    'cookie_secure' => $this->config['session']['secure'],
                    'cookie_samesite' => 'Lax',
                    'gc_maxlifetime' => $this->config['session']['lifetime'] * 60
                ]);
            }
            $this->started = true;
        }
    }

    public function get($key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    public function set($key, $value) {
        $_SESSION[$key] = $value;
    }

    public function has($key) {
        return isset($_SESSION[$key]);
    }

    public function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public function all() {
        return $_SESSION;
    }

    public function flush() {
        $_SESSION = [];
    }

    public function regenerate($destroy = false) {
        session_regenerate_id($destroy);
    }

    public function destroy() {
        session_destroy();
        $this->started = false;
    }

    public function flash($key, $value = null) {
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
        } else {
            $value = $_SESSION['_flash'][$key] ?? null;
            unset($_SESSION['_flash'][$key]);
            return $value;
        }
    }

    public function reflash() {
        if (isset($_SESSION['_flash'])) {
            $_SESSION['_flash'] = array_merge($_SESSION['_flash'], $_SESSION['_flash']);
        }
    }

    public function keep($keys = null) {
        if ($keys === null) {
            $_SESSION['_flash'] = [];
        } else {
            foreach ((array) $keys as $key) {
                if (isset($_SESSION['_flash'][$key])) {
                    $_SESSION['_flash'][$key] = $_SESSION['_flash'][$key];
                }
            }
        }
    }

    public function token() {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }

    public function regenerateToken() {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }

    public function previousUrl() {
        return $_SESSION['_previous_url'] ?? null;
    }

    public function intended($default = null) {
        return $_SESSION['_intended_url'] ?? $default;
    }

    public function setIntendedUrl($url) {
        $_SESSION['_intended_url'] = $url;
    }

    public function pull($key, $default = null) {
        $value = $this->get($key, $default);
        $this->remove($key);
        return $value;
    }

    public function increment($key, $value = 1) {
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = 0;
        }
        $_SESSION[$key] += $value;
        return $_SESSION[$key];
    }

    public function decrement($key, $value = 1) {
        return $this->increment($key, -$value);
    }

    public function put($key, $value) {
        return $this->set($key, $value);
    }

    public function forget($key) {
        return $this->remove($key);
    }

    public function exists($key) {
        return $this->has($key);
    }

    public function missing($key) {
        return !$this->has($key);
    }

    public function save() {
        if ($this->started) {
            session_write_close();
            $this->started = false;
        }
    }

    public function getId() {
        return session_id();
    }

    public function setId($id) {
        if ($this->started) {
            session_write_close();
        }
        session_id($id);
        $this->start();
    }

    public function getName() {
        return session_name();
    }

    public function setName($name) {
        if ($this->started) {
            session_write_close();
        }
        session_name($name);
        $this->start();
    }

    public function getHandler() {
        return session_get_handler();
    }

    public function setHandler($handler) {
        if ($this->started) {
            session_write_close();
        }
        session_set_save_handler($handler);
        $this->start();
    }

    public function getCookieParams() {
        return session_get_cookie_params();
    }

    public function setCookieParams($lifetime, $path = '/', $domain = '', $secure = false, $httponly = true) {
        if ($this->started) {
            session_write_close();
        }
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
        $this->start();
    }
} 
<?php
namespace Donations\Core;

class Router {
    protected $routes = [];
    protected $config;

    public function __construct() {
        $this->config = require DONATIONS_PATH . '/config/config.php';
    }

    public function add($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function get($path, $handler) {
        $this->add('GET', $path, $handler);
    }

    public function post($path, $handler) {
        $this->add('POST', $path, $handler);
    }

    public function put($path, $handler) {
        $this->add('PUT', $path, $handler);
    }

    public function delete($path, $handler) {
        $this->add('DELETE', $path, $handler);
    }

    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = str_replace($this->config['base_url'], '', $path);

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->convertPathToRegex($route['path']);
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches); // Remove the full match
                return $this->executeHandler($route['handler'], $matches);
            }
        }

        // No route found
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found',
            'data' => null
        ]);
    }

    protected function convertPathToRegex($path) {
        return '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '([^/]+)', $path) . '$#';
    }

    protected function executeHandler($handler, $params) {
        if (is_callable($handler)) {
            return call_user_func_array($handler, $params);
        }

        if (is_string($handler)) {
            list($controller, $method) = explode('@', $handler);
            $controllerClass = "Donations\\Controllers\\{$controller}";
            $controllerInstance = new $controllerClass();
            return call_user_func_array([$controllerInstance, $method], $params);
        }

        throw new \Exception('Invalid handler');
    }

    public function group($prefix, $routes) {
        foreach ($routes as $route) {
            $this->add(
                $route['method'],
                $prefix . $route['path'],
                $route['handler']
            );
        }
    }

    public function resource($name, $controller) {
        $this->get("/{$name}", "{$controller}@index");
        $this->get("/{$name}/create", "{$controller}@create");
        $this->post("/{$name}", "{$controller}@store");
        $this->get("/{$name}/{id}", "{$controller}@show");
        $this->get("/{$name}/{id}/edit", "{$controller}@edit");
        $this->put("/{$name}/{id}", "{$controller}@update");
        $this->delete("/{$name}/{id}", "{$controller}@destroy");
    }

    public function apiResource($name, $controller) {
        $this->get("/api/{$name}", "{$controller}@index");
        $this->post("/api/{$name}", "{$controller}@store");
        $this->get("/api/{$name}/{id}", "{$controller}@show");
        $this->put("/api/{$name}/{id}", "{$controller}@update");
        $this->delete("/api/{$name}/{id}", "{$controller}@destroy");
    }
} 
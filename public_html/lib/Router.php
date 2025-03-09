<?php
declare(strict_types=1);

class Router {
    private array $routes = [];
    
    public function addRoute(string $method, string $path, string $handler): void {
        $path = rtrim($path, '/');
        $pattern = preg_replace('/{([^}]+)}/', '(?P<$1>[^/]+)', $path);
        $pattern = "#^{$pattern}/?$#";
        
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }
    
    public function get(string $path, string $handler): void {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post(string $path, string $handler): void {
        $this->addRoute('POST', $path, $handler);
    }
    
    public function put(string $path, string $handler): void {
        $this->addRoute('PUT', $path, $handler);
    }
    
    public function delete(string $path, string $handler): void {
        $this->addRoute('DELETE', $path, $handler);
    }
    
    public function dispatch(string $method, string $uri): void {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        foreach ($this->routes as $route) {
            if ($method !== $route['method']) {
                continue;
            }
            
            if (preg_match($route['pattern'], $uri, $matches)) {
                [$controller, $action] = explode('@', $route['handler']);
                $controllerClass = "App\\Controllers\\{$controller}";
                
                if (!class_exists($controllerClass)) {
                    throw new RuntimeException("Controller {$controller} not found");
                }
                
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $controller = new $controllerClass();
                
                if (!method_exists($controller, $action)) {
                    throw new RuntimeException("Action {$action} not found in {$controller}");
                }
                
                $controller->$action($params);
                return;
            }
        }
        
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
    }
} 
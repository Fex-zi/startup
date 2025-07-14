<?php

namespace Core;

class Router
{
    private $routes = [];
    private $currentRoute = null;

    public function get($uri, $action)
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post($uri, $action)
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put($uri, $action)
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function delete($uri, $action)
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    private function addRoute($method, $uri, $action)
    {
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'action' => $action
        ];
    }

    public function dispatch($requestUri, $requestMethod)
    {
        // Remove query string and clean URI
        $uri = parse_url($requestUri, PHP_URL_PATH);
        $uri = rtrim($uri, '/');
        
        // Remove base path if running in subdirectory
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }
        
        if (empty($uri)) {
            $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertToRegex($route['uri']);
                
                if (preg_match($pattern, $uri, $matches)) {
                    $this->currentRoute = $route;
                    
                    // Extract parameters from URL
                    $params = array_slice($matches, 1);
                    
                    return $this->callAction($route['action'], $params);
                }
            }
        }

        // Route not found
        $this->handleNotFound();
    }

    private function convertToRegex($uri)
    {
        // Convert {param} to regex capture groups
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $uri);
        $pattern = str_replace('/', '\/', $pattern);
        return '/^' . $pattern . '$/';
    }

    private function callAction($action, $params = [])
    {
        if (is_string($action)) {
            // Parse Controller@method format
            if (strpos($action, '@') !== false) {
                list($controller, $method) = explode('@', $action);
                
                $controllerClass = "Controllers\\{$controller}";
                
                if (class_exists($controllerClass)) {
                    $controllerInstance = new $controllerClass();
                    
                    if (method_exists($controllerInstance, $method)) {
                        return call_user_func_array([$controllerInstance, $method], $params);
                    } else {
                        throw new \Exception("Method {$method} not found in {$controllerClass}");
                    }
                } else {
                    throw new \Exception("Controller {$controllerClass} not found");
                }
            }
        } elseif (is_callable($action)) {
            return call_user_func_array($action, $params);
        }

        throw new \Exception("Invalid route action");
    }

    private function handleNotFound()
    {
        http_response_code(404);
        
        // Try to load a 404 view
        $viewPath = __DIR__ . '/../Views/errors/404.php';
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The requested page could not be found.</p>";
        }
        exit;
    }

    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }

    public function url($uri, $params = [])
    {
        $app = Application::getInstance();
        $baseUrl = $app->getConfig('app.url');
        
        // Replace parameters in URI
        foreach ($params as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }
        
        return rtrim($baseUrl, '/') . '/' . ltrim($uri, '/');
    }

    public function redirect($uri, $statusCode = 302)
    {
        $url = $this->url($uri);
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    public function back()
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        header("Location: {$referer}");
        exit;
    }
}

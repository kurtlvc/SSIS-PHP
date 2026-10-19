<?php

class Router
{
    private $routes = [];

    public function __construct()
    {
    }

    public function get($path, $handler)
    {
        $this->routes['GET'][$this->normalizePath($path)] = $handler;
        return $this;
    }

    public function post($path, $handler)
    {
        $this->routes['POST'][$this->normalizePath($path)] = $handler;
        return $this;
    }

    public function put($path, $handler)
    {
        $this->routes['PUT'][$this->normalizePath($path)] = $handler;
        return $this;
    }

    public function delete($path, $handler)
    {
        $this->routes['DELETE'][$this->normalizePath($path)] = $handler;
        return $this;
    }

    public function patch($path, $handler)
    {
        $this->routes['PATCH'][$this->normalizePath($path)] = $handler;
        return $this;
    }

    private function normalizePath($path)
    {
        return '/' . trim($path, '/');
    }

    public function dispatch($requestMethod, $requestUri)
    {
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = $this->normalizePath($path);
        $basePath = '/ssis';
        if (strpos($path, $basePath) === 0) {
            $path = substr($path, strlen($basePath));
            $path = $path === '' ? '/' : $path;
        }

        if (!isset($this->routes[$requestMethod])) {
            $this->handle404();
            return;
        }

        if (isset($this->routes[$requestMethod][$path])) {
            $this->executeRoute($this->routes[$requestMethod][$path]);
            return;
        }

        foreach ($this->routes[$requestMethod] as $routePath => $handler) {
            if (preg_match_all('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', $routePath, $matches)) {
                $pattern = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([^/]+)', $routePath);
                $pattern = '#^' . $pattern . '$#';

                if (preg_match($pattern, $path, $params)) {
                    $paramNames = $matches[0];
                    array_shift($params);
                    foreach ($paramNames as $i => $paramName) {
                        $paramKey = trim($paramName, '{}');
                        if (!isset($params[$i])) {
                            $this->handle404();
                            return;
                        }
                    }
                    $this->executeRoute($handler, $params);
                    return;
                }
            }
        }

        $this->handle404();
    }

    private function executeRoute($handler, $params = [])
    {
        if (is_string($handler) && strpos($handler, '@') !== false) {
            list($controller, $method) = explode('@', $handler);
            $controllerFile = __DIR__ . "/../Controllers/{$controller}.php";
            if (!file_exists($controllerFile)) {
                $this->handle404();
                return;
            }
            require_once $controllerFile;
            $controllerInstance = new $controller();
            if (!method_exists($controllerInstance, $method)) {
                $this->handle404();
                return;
            }
            call_user_func_array([$controllerInstance, $method], $params);
            return;
        }

        if (is_callable($handler)) {
            call_user_func($handler, $params);
            return;
        }

        $this->handle404();
    }

    private function handle404()
    {
        http_response_code(404);
        $viewFile = __DIR__ . '/../../app/Views/errors/404.php';
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
        }
    }
}

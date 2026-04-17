<?php

namespace Core\Routing;

use Core\Support\Config;
use Core\Http\Request;
use Core\Http\Response;
use Core\Support\CONSTANT;
use Exception;

class Router
{
    private string $path;
    private string $method;
    private array $parameters = [];
    private ?string $controller = null;
    private $controllerMethod = null;
    private array $middlewares = [];

    public function __construct()
    {
        $requestUri = str_replace(Config::get(CONSTANT::APP_SUBDIRECTORY), '', $_SERVER['REQUEST_URI']);
        $requestUri = str_replace('//', '/', $requestUri);
        $this->path = parse_url($requestUri, PHP_URL_PATH);
        $this->method = $_SERVER['REQUEST_METHOD'];

        // Handle method spoofing for PUT, PATCH, DELETE
        if ($this->method === 'POST' && isset($_POST['_method'])) {
            $this->method = strtoupper($_POST['_method']);
        }
    }

    /**
     * Dispatch route
     */
    public function dispatch(): void
    {
        $routes = Route::getRoutes();
        $matched = false;

        foreach ($routes as $route) {
            if ($this->matchRoute($route)) {
                $matched = true;
                $this->controller = $route['controller'];
                $this->controllerMethod = $route['controllerMethod'];
                $this->middlewares = $route['middleware'];
                break;
            }
        }

        if (!$matched) {
            abort(404);
        }

        // Execute middlewares
        $this->executeMiddlewares();

        // Execute controller
        $this->executeController();
    }

    /**
     * Match route
     */
    private function matchRoute(array $route): bool
    {
        // Check method
        if ($route['method'] !== $this->method) {
            return false;
        }

        // Check URI pattern
        return $this->matchUri($route['uri']);
    }

    /**
     * Match URI pattern
     */
    private function matchUri(string $routeUri): bool
    {
        // Normalize paths
        $routeUri = '/' . trim($routeUri, '/');
        $requestPath = '/' . trim($this->path, '/');

        if ($routeUri !== '/') {
            $routeUri = rtrim($routeUri, '/');
        }
        if ($requestPath !== '/') {
            $requestPath = rtrim($requestPath, '/');
        }

        // Split into segments
        $routeSegments = array_values(array_filter(explode('/', $routeUri)));
        $pathSegments = array_values(array_filter(explode('/', $requestPath)));

        // Check segment count
        if (count($routeSegments) !== count($pathSegments)) {
            return false;
        }

        // Match segments
        foreach ($routeSegments as $key => $segment) {
            // Check for parameter {param}
            if (preg_match('/^{(.*?)}$/', $segment, $matches)) {
                $paramName = $matches[1];
                $this->parameters[$paramName] = $pathSegments[$key];
            } else {
                // Exact match required
                if ($segment !== $pathSegments[$key]) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Execute middlewares
     */
    private function executeMiddlewares(): void
    {
        $request = new Request();

        foreach ($this->middlewares as $middlewareClass) {
            // Handle string middleware names
            if (is_string($middlewareClass)) {
                $middlewareClass = "App\\Middlewares\\{$middlewareClass}";
            }

            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware {$middlewareClass} not found");
            }

            $middleware = new $middlewareClass();

            if (!method_exists($middleware, 'handle')) {
                throw new Exception("Middleware must have a handle method");
            }

            $response = $middleware->handle($request);

            // If middleware returns false or response, stop execution
            if ($response === false || $response instanceof Response) {
                if ($response instanceof Response) {
                    $response->send();
                }
                exit();
            }
        }
    }

    /**
     * Execute controller
     */
    private function executeController(): void
    {
        if ($this->controller === null) {
            // Closure/callable
            if (is_callable($this->controllerMethod)) {
                call_user_func_array($this->controllerMethod, $this->parameters);
            }
            return;
        }

        if (!class_exists($this->controller)) {
            throw new Exception("Controller {$this->controller} not found");
        }

        $instance = new $this->controller();
        $method = $this->controllerMethod;

        if (!method_exists($instance, $method)) {
            throw new Exception("Method {$method} not found in controller {$this->controller}");
        }

        // Call controller method with parameters
        call_user_func_array([$instance, $method], $this->parameters);
    }

    /**
     * Get route URL by name
     */
    public function getRouteByName(string $name, array $data = []): string
    {
        $namedRoutes = Route::getNamedRoutes();

        if (!isset($namedRoutes[$name])) {
            return $name;
        }

        $uri = $namedRoutes[$name];

        // Replace parameters
        foreach ($data as $key => $value) {
            $uri = str_replace('{' . $key . '}', $value, $uri);
        }

        // Remove leading slash for consistency
        return ltrim($uri, '/');
    }
}

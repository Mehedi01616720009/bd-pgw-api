<?php

namespace Core\Routing;

use Exception;

class Route
{
    private static array $routes = [];
    private static ?string $prefix = null;
    private static array $middlewares = [];
    private static array $namedRoutes = [];

    /**
     * Add GET route
     */
    public static function get(string $uri, $action, string | null $name = null): void
    {
        self::addRoute('GET', $uri, $action, $name);
    }

    /**
     * Add POST route
     */
    public static function post(string $uri, $action, string | null $name = null): void
    {
        self::addRoute('POST', $uri, $action, $name);
    }

    /**
     * Add PUT route
     */
    public static function put(string $uri, $action, string | null $name = null): void
    {
        self::addRoute('PUT', $uri, $action, $name);
    }

    /**
     * Add PATCH route
     */
    public static function patch(string $uri, $action, string | null $name = null): void
    {
        self::addRoute('PATCH', $uri, $action, $name);
    }

    /**
     * Add DELETE route
     */
    public static function delete(string $uri, $action, string | null $name = null): void
    {
        self::addRoute('DELETE', $uri, $action, $name);
    }

    /**
     * Add route for any method
     */
    public static function any(string $uri, $action, string | null $name = null): void
    {
        foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method) {
            self::addRoute($method, $uri, $action, $name);
        }
    }

    /**
     * Add route for multiple methods
     */
    public static function match(array $methods, string $uri, $action, string | null $name = null): void
    {
        foreach ($methods as $method) {
            self::addRoute(strtoupper($method), $uri, $action, $name);
        }
    }

    /**
     * Group routes with prefix and middleware
     */
    public static function group(array $attributes, callable $callback): void
    {
        $previousPrefix = self::$prefix;
        $previousMiddlewares = self::$middlewares;

        // Set prefix
        if (isset($attributes['prefix'])) {
            self::$prefix = $previousPrefix . '/' . trim($attributes['prefix'], '/');
        }

        // Set middleware
        if (isset($attributes['middleware'])) {
            $middleware = is_array($attributes['middleware'])
                ? $attributes['middleware']
                : [$attributes['middleware']];
            self::$middlewares = array_merge(self::$middlewares, $middleware);
        }

        // Execute callback
        $callback();

        // Restore previous state
        self::$prefix = $previousPrefix;
        self::$middlewares = $previousMiddlewares;
    }

    /**
     * RESTful resource routes
     */
    public static function resource(string $name, string $controller): void
    {
        $name = trim($name, '/');

        self::get("/$name", [$controller, 'index'], "$name.index");
        self::get("/$name/create", [$controller, 'create'], "$name.create");
        self::post("/$name", [$controller, 'store'], "$name.store");
        self::get("/$name/{id}", [$controller, 'show'], "$name.show");
        self::get("/$name/{id}/edit", [$controller, 'edit'], "$name.edit");
        self::put("/$name/{id}", [$controller, 'update'], "$name.update");
        self::delete("/$name/{id}", [$controller, 'destroy'], "$name.destroy");
    }

    /**
     * API resource routes (without create/edit)
     */
    public static function apiResource(string $name, string $controller): void
    {
        $name = trim($name, '/');

        self::get("/$name", [$controller, 'index'], "$name.index");
        self::post("/$name", [$controller, 'store'], "$name.store");
        self::get("/$name/{id}", [$controller, 'show'], "$name.show");
        self::put("/$name/{id}", [$controller, 'update'], "$name.update");
        self::delete("/$name/{id}", [$controller, 'destroy'], "$name.destroy");
    }

    /**
     * Add middleware to route
     */
    public static function middleware($middleware): RouteRegistrar
    {
        $middlewares = is_array($middleware) ? $middleware : [$middleware];
        return new RouteRegistrar($middlewares);
    }

    /**
     * Add route
     */
    private static function addRoute(string $method, string $uri, $action, ?string $name): void
    {
        // Apply prefix
        if (self::$prefix) {
            $uri = self::$prefix . '/' . ltrim($uri, '/');
        }

        // Normalize URI
        $uri = '/' . trim($uri, '/');
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        // Parse action
        [$controller, $controllerMethod] = self::parseAction($action);

        $route = [
            'method' => $method,
            'uri' => $uri,
            'controller' => $controller,
            'controllerMethod' => $controllerMethod,
            'middleware' => self::$middlewares,
            'name' => $name,
        ];

        self::$routes[] = $route;

        // Store named route
        if ($name) {
            self::$namedRoutes[$name] = $uri;
        }
    }

    /**
     * Parse action into controller and method
     */
    private static function parseAction($action): array
    {
        if (is_array($action)) {
            return [$action[0], $action[1]];
        }

        if (is_string($action) && strpos($action, '@') !== false) {
            [$controller, $method] = explode('@', $action);
            return [$controller, $method];
        }

        if (is_callable($action)) {
            return [null, $action];
        }

        throw new Exception("Invalid route action");
    }

    /**
     * Get all routes
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Get named routes
     */
    public static function getNamedRoutes(): array
    {
        return self::$namedRoutes;
    }

    /**
     * Clear all routes
     */
    public static function clear(): void
    {
        self::$routes = [];
        self::$namedRoutes = [];
        self::$prefix = null;
        self::$middlewares = [];
    }
}

/**
 * Route Registrar for fluent middleware chaining
 */
class RouteRegistrar
{
    private array $middlewares;

    public function __construct(array $middlewares)
    {
        $this->middlewares = $middlewares;
    }

    public function group(callable $callback): void
    {
        Route::group(['middleware' => $this->middlewares], $callback);
    }
}

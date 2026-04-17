<?php

namespace Core\Http;

use Core\Security\Validator;

class Request
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $cookies;
    private string $method;
    private string $uri;
    private ?string $body;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->method = $this->server['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->server['REQUEST_URI'] ?? '/';
        $this->body = file_get_contents('php://input');
    }

    /**
     * Get request method
     */
    public function method(): string
    {
        return strtoupper($this->method);
    }

    /**
     * Get request URI
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Get request path
     */
    public function path(): string
    {
        return parse_url($this->uri, PHP_URL_PATH);
    }

    /**
     * Check if request method matches
     */
    public function isMethod(string $method): bool
    {
        return $this->method() === strtoupper($method);
    }

    /**
     * Check if request is GET
     */
    public function isGet(): bool
    {
        return $this->isMethod('GET');
    }

    /**
     * Check if request is POST
     */
    public function isPost(): bool
    {
        return $this->isMethod('POST');
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool
    {
        return isset($this->server['HTTP_X_REQUESTED_WITH'])
            && strtolower($this->server['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get all input data
     */
    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * Get specific input value
     */
    public function input(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Get only specified keys
     */
    public function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            if (isset($this->all()[$key])) {
                $data[$key] = $this->all()[$key];
            }
        }
        return $data;
    }

    /**
     * Get all except specified keys
     */
    public function except(array $keys): array
    {
        $data = $this->all();
        foreach ($keys as $key) {
            unset($data[$key]);
        }
        return $data;
    }

    /**
     * Check if input key exists
     */
    public function has(string $key): bool
    {
        return isset($this->all()[$key]);
    }

    /**
     * Check if multiple keys exist
     */
    public function hasAll(array $keys): bool
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get query parameter
     */
    public function query(string | null $key = null, $default = null)
    {
        if ($key === null) {
            return $this->get;
        }
        return $this->get[$key] ?? $default;
    }

    /**
     * Get POST data
     */
    public function post(string | null $key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    /**
     * Get uploaded file
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Check if file was uploaded
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Get all uploaded files
     */
    public function files(): array
    {
        return $this->files;
    }

    /**
     * Get cookie
     */
    public function cookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get header
     */
    public function header(string $key, $default = null)
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $default;
    }

    /**
     * Get bearer token
     */
    public function bearerToken(): ?string
    {
        $header = $this->header('Authorization');
        if ($header && preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Get client IP address
     */
    public function ip(): ?string
    {
        if (isset($this->server['HTTP_X_FORWARDED_FOR'])) {
            return $this->server['HTTP_X_FORWARDED_FOR'];
        }
        return $this->server['REMOTE_ADDR'] ?? null;
    }

    /**
     * Get user agent
     */
    public function userAgent(): ?string
    {
        return $this->server['HTTP_USER_AGENT'] ?? null;
    }

    /**
     * Get raw request body
     */
    public function body(): ?string
    {
        return $this->body;
    }

    /**
     * Get JSON body as array
     */
    public function json(): ?array
    {
        $data = json_decode($this->body, true);
        return json_last_error() === JSON_ERROR_NONE ? $data : null;
    }

    /**
     * Get server variable
     */
    public function server(string | null $key = null, $default = null)
    {
        if ($key === null) {
            return $this->server;
        }
        return $this->server[$key] ?? $default;
    }

    /**
     * Validate input data
     */
    public function validate(array $rules): array
    {
        $validator = new Validator($this->all(), $rules);

        if ($validator->fails()) {
            $_SESSION['errors'] = $validator->errors();
            $_SESSION['old'] = $this->all();
            redirect($this->server['HTTP_REFERER'] ?? '/');
        }

        return $validator->validated();
    }

    /**
     * Get old input value (for form repopulation)
     */
    public function old(string $key, $default = null)
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

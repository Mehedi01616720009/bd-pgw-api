<?php

namespace Core\Http;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private $content;

    /**
     * Set response status code
     */
    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set response header
     */
    public function setHeader(string $key, string $value): self
    {
        $this->headers[$key] = $value;
        return $this;
    }

    /**
     * Set multiple headers
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->setHeader($key, $value);
        }
        return $this;
    }

    /**
     * Set response content
     */
    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Send the response
     */
    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $key => $value) {
            header("$key: $value");
        }

        echo $this->content;
    }

    /**
     * Create JSON response
     */
    public static function json($data, int $status = 200): self
    {
        $response = new self();
        return $response
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'application/json')
            ->setContent(json_encode($data));
    }

    /**
     * Create HTML response
     */
    public static function html(string $html, int $status = 200): self
    {
        $response = new self();
        return $response
            ->setStatusCode($status)
            ->setHeader('Content-Type', 'text/html; charset=UTF-8')
            ->setContent($html);
    }

    /**
     * Create redirect response
     */
    public static function redirect(string $url, int $status = 302): self
    {
        $response = new self();
        return $response
            ->setStatusCode($status)
            ->setHeader('Location', $url)
            ->setContent('');
    }

    /**
     * Redirect back
     */
    public static function back(): self
    {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
        return self::redirect($url);
    }

    /**
     * Create download response
     */
    public static function download(string $filePath, string | null $fileName = null): self
    {
        if (!file_exists($filePath)) {
            abort(404);
        }

        $fileName = $fileName ?? basename($filePath);
        $response = new self();

        return $response
            ->setStatusCode(200)
            ->setHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Disposition' => "attachment; filename=\"$fileName\"",
                'Content-Length' => filesize($filePath)
            ])
            ->setContent(file_get_contents($filePath));
    }

    /**
     * No content response
     */
    public static function noContent(): self
    {
        $response = new self();
        return $response
            ->setStatusCode(204)
            ->setContent('');
    }

    /**
     * Created response
     */
    public static function created($data = null, string | null $location = null): self
    {
        $response = self::json($data, 201);

        if ($location) {
            $response->setHeader('Location', $location);
        }

        return $response;
    }

    /**
     * Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return self::json(['error' => $message], 401);
    }

    /**
     * Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): self
    {
        return self::json(['error' => $message], 403);
    }

    /**
     * Not found response
     */
    public static function notFound(string $message = 'Not Found'): self
    {
        return self::json(['error' => $message], 404);
    }

    /**
     * Server error response
     */
    public static function serverError(string $message = 'Internal Server Error'): self
    {
        return self::json(['error' => $message], 500);
    }

    /**
     * Set cookie
     */
    public function withCookie(
        string $name,
        string $value,
        int $expires = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = true
    ): self {
        setcookie($name, $value, time() + $expires, $path, $domain, $secure, $httponly);
        return $this;
    }

    /**
     * Flash message to session
     */
    public function with(string $key, $value): self
    {
        $_SESSION['flash'][$key] = $value;
        return $this;
    }

    /**
     * Flash success message
     */
    public function withSuccess(string $message): self
    {
        return $this->with('success', $message);
    }

    /**
     * Flash error message
     */
    public function withError(string $message): self
    {
        return $this->with('error', $message);
    }

    /**
     * Flash errors (for validation)
     */
    public function withErrors(array $errors): self
    {
        $_SESSION['errors'] = $errors;
        return $this;
    }

    /**
     * Flash input data
     */
    public function withInput(array $input): self
    {
        $_SESSION['old'] = $input;
        return $this;
    }
}

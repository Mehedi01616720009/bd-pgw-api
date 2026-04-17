<?php

use Core\Support\Config;
use Core\Http\Request;
use Core\Http\Response;
use Core\Support\CONSTANT;

/**
 * Dump and die
 */
function dd(...$vars)
{
    foreach ($vars as $var) {
        var_dump($var);
    }
    exit();
}

/**
 * Render a view
 */
function view(string $name, array $data = []): void
{
    extract($data);

    $viewPath = __DIR__ . "/../../Views/$name.php";

    if (file_exists($viewPath)) {
        require_once $viewPath;
    } else {
        echo "<h1 style='color: red;text-align: center'>View [$name] Not found</h1>";
        exit();
    }
}

/**
 * Get configuration value
 */
function config(string $key, $default = null)
{
    return Config::get($key, $default);
}

/**
 * Get environment variable
 */
function env(string $key, $default = null)
{
    return Config::env($key, $default);
}

/**
 * Generate route URL by name
 */
function route(string $routeName, array $data = []): string
{
    return config(CONSTANT::APP_URL) . (new Core\Routing\Router)->getRouteByName($routeName, $data);
}

/**
 * Redirect to route
 */
function redirect(string | null $routeName = null, array $data = []): Response
{
    if ($routeName === null) {
        $url = $_SERVER['HTTP_REFERER'] ?? '/';
    } else {
        $url = route($routeName, $data);
    }

    $response = Response::redirect($url);
    $response->send();
    exit();
}

/**
 * Redirect back
 */
function back(): Response
{
    $response = Response::back();
    $response->send();
    exit();
}

/**
 * Get public directory URL
 */
function public_dir(string $file): string
{
    if (strpos($file, '/') === 0) {
        $file = substr($file, 1);
    }

    return config(CONSTANT::APP_PUBLIC_URL) . $file;
}

/**
 * Get asset URL
 */
function asset(string $path): string
{
    return public_dir($path);
}

/**
 * public path
 */
function public_path(string $file): string
{
    if (strpos($file, '/') === 0) {
        $file = substr($file, 1);
    }

    return BASE_PATH . '/public/' . $file;
}

/**
 * Abort with HTTP status code
 */
function abort(int $code = 404, string $message = ''): void
{
    http_response_code($code);

    $errorView = __DIR__ . "/../../Views/Errors/$code.php";

    if (file_exists($errorView)) {
        view("Errors/$code", ['message' => $message]);
    } else {
        echo "<h1>Error $code</h1>";
        if ($message) {
            echo "<p>$message</p>";
        }
    }

    exit();
}

/**
 * Get current request instance
 */
function request(): Request
{
    return new Request();
}

/**
 * Create JSON response
 */
function json($data, int $status = 200): Response
{
    $response = Response::json($data, $status);
    $response->send();
    exit();
}

/**
 * Get old input value
 */
function old(string $key, $default = '')
{
    $value = $_SESSION['old'][$key] ?? $default;

    // Clear old input after retrieving
    if (isset($_SESSION['old'][$key])) {
        unset($_SESSION['old'][$key]);
    }

    return $value;
}

/**
 * Get flash message
 */
function flash(string | null $key = null)
{
    if ($key === null) {
        return $_SESSION['flash'] ?? [];
    }

    $value = $_SESSION['flash'][$key] ?? null;

    // Clear flash after retrieving
    if (isset($_SESSION['flash'][$key])) {
        unset($_SESSION['flash'][$key]);
    }

    return $value;
}

/**
 * Get validation errors
 */
function errors(string | null $key = null)
{
    if ($key === null) {
        $errors = $_SESSION['errors'] ?? [];
        unset($_SESSION['errors']);
        return $errors;
    }

    $error = $_SESSION['errors'][$key] ?? null;

    if (isset($_SESSION['errors'][$key])) {
        unset($_SESSION['errors'][$key]);
    }

    // Convert array → string
    if (is_array($error)) {
        return implode('<br>', $error);
    }

    return $error;
}

/**
 * Check if validation error exists
 */
function hasError(string $key): bool
{
    return isset($_SESSION['errors'][$key]);
}

/**
 * Escape HTML
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Get CSRF token
 */
function csrf_token(): string
{
    return \Core\Security\CSRF::getToken();
}

/**
 * Generate CSRF field
 */
function csrf_field(): string
{
    $token = csrf_token();
    return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token
 */
function csrf_verify(): bool
{
    return \Core\Security\CSRF::validateRequest();
}

/**
 * Validate request data
 */
function validate(array $rules, array $customMessages = []): array
{
    $request = request();
    $validator = new \Core\Security\Validator($request->all(), $rules, $customMessages);

    if ($validator->fails()) {
        $_SESSION['errors'] = $validator->errors();
        $_SESSION['old'] = $request->all();
        back();
    }

    return $validator->validated();
}

/**
 * Generate method field for forms
 */
function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
}

/**
 * Get session value
 */
function session(string | null $key = null, $default = null)
{
    if ($key === null) {
        return $_SESSION;
    }

    return $_SESSION[$key] ?? $default;
}

/**
 * Set session value
 */
function setSession(string $key, $value): void
{
    $_SESSION[$key] = $value;
}

/**
 * Check if user is authenticated
 */
function auth(): bool
{
    return isset($_SESSION['auth']['id']);
}

/**
 * Get authenticated user ID
 */
function user_id(): ?int
{
    return $_SESSION['auth']['id'] ?? null;
}

/**
 * Check if string starts with (PHP 8+ has this built-in)
 */
if (!function_exists('str_starts_with')) {
    function str_starts_with(string $haystack, string $needle): bool
    {
        return strpos($haystack, $needle) === 0;
    }
}

/**
 * Check if string ends with (PHP 8+ has this built-in)
 */
if (!function_exists('str_ends_with')) {
    function str_ends_with(string $haystack, string $needle): bool
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }
}

/**
 * Generate random string
 */
function str_random(int $length = 16): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Model error
 */
function modelError(string $message): object
{
    return (object) ['message' => $message];
}

/**
 * Now timestamp
 */
function now(): string
{
    return date('Y-m-d H:i:s');
}

/**
 * Today date
 */
function today(): string
{
    return date('Y-m-d');
}

/**
 * Slug generator
 */
function slugify(string $text, string $divider = '-'): string
{
    // replace non letter or digits by divider
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, $divider);

    // remove duplicate divider
    $text = preg_replace('~-+~', $divider, $text);

    // lowercase
    $text = strtolower($text);

    return $text;
}

/**
 * File upload
 */
function handleFileUpload(array $file, string $uploadDir): string | null
{
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $fileName = 'File_' . time() . '.' . $fileExtension;
    $targetPath = $uploadDir . $fileName;

    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }

    return null;
}

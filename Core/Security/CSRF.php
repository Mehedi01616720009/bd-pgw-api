<?php

namespace Core\Security;

use Core\Support\Config;
use Core\Support\CONSTANT;

class CSRF
{
    /**
     * Generate CSRF token
     */
    public static function generateToken(): string
    {
        $tokenLength = Config::get(CONSTANT::CSRF_TOKEN_LENGTH, 32);
        $token = bin2hex(random_bytes($tokenLength));

        $_SESSION['_csrf_token'] = $token;
        $_SESSION['_csrf_token_time'] = time();

        return $token;
    }

    /**
     * Get current CSRF token
     */
    public static function getToken(): string
    {
        if (!isset($_SESSION['_csrf_token'])) {
            return self::generateToken();
        }

        // Check if token expired
        $expireTime = Config::get(CONSTANT::CSRF_EXPIRE_TIME, 3600);
        $tokenTime = $_SESSION['_csrf_token_time'] ?? 0;

        if ((time() - $tokenTime) > $expireTime) {
            return self::generateToken();
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verify(string $token): bool
    {
        if (!Config::get(CONSTANT::CSRF_ENABLED, true)) {
            return true;
        }

        if (!isset($_SESSION['_csrf_token'])) {
            return false;
        }

        // Check if token expired
        $expireTime = Config::get(CONSTANT::CSRF_EXPIRE_TIME, 3600);
        $tokenTime = $_SESSION['_csrf_token_time'] ?? 0;

        if ((time() - $tokenTime) > $expireTime) {
            return false;
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Validate request CSRF token
     */
    public static function validateRequest(): bool
    {
        if (!Config::get(CONSTANT::CSRF_ENABLED, true)) {
            return true;
        }

        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if ($token === null) {
            return false;
        }

        return self::verify($token);
    }

    /**
     * Check and throw exception if invalid
     */
    public static function check(): void
    {
        if (!self::validateRequest()) {
            http_response_code(419);
            die('CSRF token mismatch. Please refresh and try again.');
        }
    }

    /**
     * Regenerate token (useful after login)
     */
    public static function regenerate(): string
    {
        unset($_SESSION['_csrf_token']);
        unset($_SESSION['_csrf_token_time']);
        return self::generateToken();
    }
}

<?php

namespace App\Core;

final class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        self::ensureSession();

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function verify(?string $submittedToken): bool
    {
        self::ensureSession();

        if (empty($_SESSION[self::SESSION_KEY]) || empty($submittedToken)) {
            return false;
        }

        return hash_equals($_SESSION[self::SESSION_KEY], $submittedToken);
    }

    private static function ensureSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}

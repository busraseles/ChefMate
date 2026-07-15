<?php

namespace App\Helpers;

final class Security
{

    public static function hardenSessionCookies(bool $isHttps): void
    {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',

            
            
            'secure'   => $isHttps,

            'httponly' => true,

            
            
            'samesite' => 'Lax',
        ]);

        

        ini_set('session.use_strict_mode', '1');
    }

    public static function sendSecurityHeaders(): void
    {

        
        header('X-Content-Type-Options: nosniff');

        
        header('X-Frame-Options: SAMEORIGIN');

        
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }

    public static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }

        if (($_SERVER['SERVER_PORT'] ?? null) === '443') {
            return true;
        }

        return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    }
}

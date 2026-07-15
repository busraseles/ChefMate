<?php

namespace App\Core;

final class Response
{
    public static function json(mixed $data, int $status = 200): never
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function ok(mixed $data = [], string $message = 'OK', int $status = 200): never
    {
        self::json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function created(mixed $data = [], string $message = 'Created'): never
    {
        self::ok($data, $message, 201);
    }

    public static function error(string $message, int $status = 400, mixed $data = []): never
    {
        self::json([
            'success' => false,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public static function unauthorized(string $message = 'Giriş yapmanız gerekiyor.'): never
    {
        self::error($message, 401);
    }

    public static function forbidden(string $message = 'Bu işlem için yetkiniz yok.'): never
    {
        self::error($message, 403);
    }

    public static function notFound(string $message = 'Kayıt bulunamadı.'): never
    {
        self::error($message, 404);
    }

    public static function serverError(string $message = 'Sunucu hatası oluştu.'): never
    {
        self::error($message, 500);
    }
}

<?php

namespace App\Helpers;

final class Url
{
    
    public static function base(): string
    {
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
        $dir = str_replace('\\', '/', dirname($scriptName));

        return rtrim($dir, '/');
    }

    public static function to(string $path): string
    {
        return self::base() . '/' . ltrim($path, '/');
    }
}

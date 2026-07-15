<?php

declare(strict_types=1);

require __DIR__ . '/../app/Core/Autoload.php';

use App\Core\Request;
use App\Core\Response;
use App\Core\Router;
use App\Helpers\Security;

$config = require __DIR__ . '/../config/config.php';
$isProduction = ($config['app']['env'] ?? 'production') === 'production';

ini_set('display_errors', $isProduction ? '0' : '1');
error_reporting(E_ALL);

set_exception_handler(static function (\Throwable $e) use ($isProduction): void {
    error_log('[ChefMate][Uncaught] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    $isApiRequest = str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/');

    if ($isApiRequest) {
        Response::serverError($isProduction ? 'Sunucu hatası oluştu.' : $e->getMessage());
    }

    http_response_code(500);

    if ($isProduction) {
        echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8">'
            . '<title>Sunucu Hatası</title></head><body style="font-family:sans-serif;padding:40px;text-align:center;">'
            . '<h1>Bir şeyler ters gitti</h1><p>Lütfen daha sonra tekrar deneyin.</p>'
            . '</body></html>';
    } else {
        echo '<!DOCTYPE html><html lang="tr"><head><meta charset="utf-8">'
            . '<title>Hata (geliştirme modu)</title></head><body style="font-family:monospace;padding:20px;">'
            . '<h1>' . htmlspecialchars($e->getMessage()) . '</h1>'
            . '<pre>' . htmlspecialchars($e->getFile() . ':' . $e->getLine() . "\n\n" . $e->getTraceAsString()) . '</pre>'
            . '</body></html>';
    }

    exit;
});

Security::hardenSessionCookies(Security::isHttps());
session_start();
Security::sendSecurityHeaders();

$router = new Router();
require __DIR__ . '/../routes/web.php'; 
require __DIR__ . '/../routes/api.php'; 

$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/';

if ($scriptDir !== '' && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir));
}

if ($path === '' || $path === false) {
    $path = '/';
}

$request = new Request();
$router->dispatch($request, $path);

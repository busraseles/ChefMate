<?php

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Url;

final class AuthMiddleware
{
    public function handle(Request $request): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['user_id'])) {

            
            $isApiRequest = str_contains($_SERVER['REQUEST_URI'] ?? '', '/api/');

            if ($isApiRequest) {
                Response::unauthorized('Bu işlem için giriş yapmalısınız.');
            }

            

            header('Location: ' . Url::to('login') . '?next=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
            exit;
        }
    }
}

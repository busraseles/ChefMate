<?php

namespace App\Core;

abstract class Controller
{

    protected function view(string $view, array $data = []): void
    {
        $viewPath = dirname(__DIR__) . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!is_file($viewPath)) {
            Response::serverError("View bulunamadı: {$view}");
        }

        extract($data, EXTR_SKIP);
        require $viewPath;
    }

    protected function json(mixed $data, int $status = 200): never
    {
        Response::json($data, $status);
    }

    protected function currentUserId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    }

    protected function currentUserRole(): string
    {
        return $_SESSION['user_role'] ?? 'user';
    }
}

<?php

namespace App\Core\Middleware;

use App\Core\Request;
use App\Core\Response;

final class RoleMiddleware
{
    public function __construct(private readonly string $requiredRole)
    {
    }

    public function handle(Request $request): void
    {
        $currentRole = $_SESSION['user_role'] ?? 'user';

        if ($currentRole !== $this->requiredRole) {
            Response::forbidden('Bu işlem için "' . $this->requiredRole . '" yetkisi gerekiyor.');
        }
    }
}

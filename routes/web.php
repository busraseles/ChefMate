<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\PageController;
use App\Controllers\RecipeController;
use App\Core\Middleware\AuthMiddleware;

$router->get('/', [PageController::class, 'home']);
$router->get('/fridge', [PageController::class, 'fridge'], [AuthMiddleware::class]);
$router->get('/about', [PageController::class, 'about']);
$router->get('/tarifler', [RecipeController::class, 'tarifler']);
$router->get('/recipe-book', [RecipeController::class, 'book'], [AuthMiddleware::class]);
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);

$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);

$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout']);

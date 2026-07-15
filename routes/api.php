<?php

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\FridgeController;
use App\Controllers\PredictionController;
use App\Controllers\RecipeController;
use App\Controllers\UserController;
use App\Core\Middleware\AuthMiddleware;

$router->post('/api/login', [AuthController::class, 'apiLogin']);
$router->post('/api/register', [AuthController::class, 'apiRegister']);

$router->get('/api/profile', [UserController::class, 'show'], [AuthMiddleware::class]);
$router->put('/api/profile', [UserController::class, 'update'], [AuthMiddleware::class]);
$router->put('/api/password', [UserController::class, 'changePassword'], [AuthMiddleware::class]);
$router->post('/api/avatar', [UserController::class, 'uploadAvatar'], [AuthMiddleware::class]);
$router->get('/api/avatar', [UserController::class, 'getAvatar'], [AuthMiddleware::class]);

$router->get('/api/notifications', [UserController::class, 'notificationsIndex'], [AuthMiddleware::class]);
$router->put('/api/notifications/read', [UserController::class, 'notificationsMarkRead'], [AuthMiddleware::class]);
$router->put('/api/notifications/read-all', [UserController::class, 'notificationsReadAll'], [AuthMiddleware::class]);
$router->delete('/api/notifications/{id}', [UserController::class, 'notificationsDestroy'], [AuthMiddleware::class]);
$router->delete('/api/notifications', [UserController::class, 'notificationsClear'], [AuthMiddleware::class]);
$router->post('/api/notifications/sync-fridge', [UserController::class, 'notificationsSyncFridge'], [AuthMiddleware::class]);

$router->get('/api/fridge', [FridgeController::class, 'index'], [AuthMiddleware::class]);
$router->post('/api/fridge', [FridgeController::class, 'store'], [AuthMiddleware::class]);
$router->put('/api/fridge/{id}', [FridgeController::class, 'update'], [AuthMiddleware::class]);
$router->delete('/api/fridge/{id}', [FridgeController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/api/shopping', [FridgeController::class, 'shoppingIndex'], [AuthMiddleware::class]);
$router->post('/api/shopping', [FridgeController::class, 'shoppingStore'], [AuthMiddleware::class]);
$router->put('/api/shopping/{id}/toggle', [FridgeController::class, 'shoppingToggle'], [AuthMiddleware::class]);
$router->delete('/api/shopping/{id}', [FridgeController::class, 'shoppingDestroy'], [AuthMiddleware::class]);

$router->get('/api/waste', [FridgeController::class, 'wasteIndex'], [AuthMiddleware::class]);
$router->post('/api/waste', [FridgeController::class, 'wasteStore'], [AuthMiddleware::class]);

$router->get('/api/recipes', [RecipeController::class, 'index'], [AuthMiddleware::class]);
$router->post('/api/recipes', [RecipeController::class, 'store'], [AuthMiddleware::class]);
$router->delete('/api/recipes/{id}', [RecipeController::class, 'destroy'], [AuthMiddleware::class]);

$router->get('/api/community-recipes', [RecipeController::class, 'community']);
$router->get('/api/recipes/proxy', [RecipeController::class, 'proxy']);
$router->get('/api/recipes/detail', [RecipeController::class, 'detail']);

$router->get('/api/recipes/ai-recommendations', [RecipeController::class, 'aiRecommendations'], [AuthMiddleware::class]);
$router->get('/api/my-likes', [RecipeController::class, 'myLikes'], [AuthMiddleware::class]);
$router->get('/api/my-comments', [RecipeController::class, 'myComments'], [AuthMiddleware::class]);
$router->put('/api/comments/{id}', [RecipeController::class, 'editComment'], [AuthMiddleware::class]);
$router->delete('/api/comments/{id}', [RecipeController::class, 'deleteComment'], [AuthMiddleware::class]);
$router->post('/api/recipes/like', [RecipeController::class, 'likePublic'], [AuthMiddleware::class]);
$router->post('/api/recipes/save', [RecipeController::class, 'savePublic'], [AuthMiddleware::class]);
$router->post('/api/recipes/comment', [RecipeController::class, 'commentPublic'], [AuthMiddleware::class]);
$router->get('/api/recipes/public', [RecipeController::class, 'getPublic'], [AuthMiddleware::class]);

$router->get('/api/user-recipes', [RecipeController::class, 'userRecipeIndex'], [AuthMiddleware::class]);
$router->post('/api/user-recipes', [RecipeController::class, 'userRecipeStore'], [AuthMiddleware::class]);
$router->delete('/api/user-recipes/{id}', [RecipeController::class, 'userRecipeDestroy'], [AuthMiddleware::class]);

$router->post('/api/predict', [PredictionController::class, 'store']);

$router->get('/api/dashboard/summary', [DashboardController::class, 'summary'], [AuthMiddleware::class]);

$router->post('/api/water', [DashboardController::class, 'addWater'], [AuthMiddleware::class]);
$router->get('/api/water/today', [DashboardController::class, 'waterToday'], [AuthMiddleware::class]);
$router->get('/api/water/weekly', [DashboardController::class, 'waterWeekly'], [AuthMiddleware::class]);

$router->get('/api/menu', [DashboardController::class, 'menuList'], [AuthMiddleware::class]);
$router->post('/api/menu', [DashboardController::class, 'menuAdd'], [AuthMiddleware::class]);
$router->delete('/api/menu/{id}', [DashboardController::class, 'menuDelete'], [AuthMiddleware::class]);
$router->get('/api/menu/today-calories', [DashboardController::class, 'menuTodayCalories'], [AuthMiddleware::class]);
$router->get('/api/menu/weekly-calories', [DashboardController::class, 'menuWeeklyCalories'], [AuthMiddleware::class]);
$router->post('/api/menu/from-recipe', [DashboardController::class, 'menuAddFromRecipe'], [AuthMiddleware::class]);

$router->post('/api/calories', [DashboardController::class, 'calorieAdd'], [AuthMiddleware::class]);
$router->get('/api/calories/today', [DashboardController::class, 'calorieToday'], [AuthMiddleware::class]);

$router->get('/api/food-calories', [DashboardController::class, 'foodCaloriesList'], [AuthMiddleware::class]);
$router->get('/api/food-calories/dropdown', [DashboardController::class, 'foodCaloriesDropdown'], [AuthMiddleware::class]);

$router->post('/api/badges/earn', [DashboardController::class, 'badgeEarn'], [AuthMiddleware::class]);
$router->get('/api/badges', [DashboardController::class, 'badgeList'], [AuthMiddleware::class]);
$router->get('/api/badges/daily-status', [DashboardController::class, 'badgeDailyStatus'], [AuthMiddleware::class]);
$router->post('/api/badges/reset-daily', [DashboardController::class, 'badgeResetDaily'], [AuthMiddleware::class]);

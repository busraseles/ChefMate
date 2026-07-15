<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\BadgeModel;
use App\Models\DashboardModel;
use App\Models\MenuModel;
use App\Models\WaterModel;
use InvalidArgumentException;

final class DashboardController extends Controller
{
    public function index(Request $request): void
    {
        $userId = $this->currentUserId();
        $data = (new DashboardModel())->getDashboardPageData($userId);

        $this->view('pages.dashboard', [
            'uid'         => $userId,
            'name'        => htmlspecialchars($_SESSION['user_name'] ?? 'Kullanıcı'),
            'waterToday'  => $data['water_today'],
            'cabinetCount' => $data['cabinet_count'],
            'recipeCount' => $data['recipe_count'],
            'badgeCount'  => $data['badge_count'],
            'shopCount'   => $data['shop_count'],
            'profileJson' => $data['profile_json'],
        ]);
    }

    public function summary(Request $request): void
    {
        $userId = $this->currentUserId();
        $data = (new DashboardModel())->getSummary($userId);

        $this->json(['success' => true, 'data' => $data], 200);
    }

    public function addWater(Request $request): void
    {
        $userId = $this->currentUserId();
        $amount = (int)$request->input('amount_ml', 0);

        if ($amount <= 0) {
            $this->json(['success' => false, 'message' => 'Geçersiz miktar.'], 400);
            return;
        }

        $total = (new WaterModel())->add($userId, $amount);
        $this->json(['success' => true, 'data' => ['today_total' => $total]], 201);
    }

    public function waterToday(Request $request): void
    {
        $userId = $this->currentUserId();
        $total = (new WaterModel())->todayTotal($userId);

        $this->json(['success' => true, 'data' => ['today_total' => $total]], 200);
    }

    public function waterWeekly(Request $request): void
    {
        $userId = $this->currentUserId();
        $data = (new WaterModel())->weekly($userId);

        $this->json(['success' => true, 'data' => $data], 200);
    }

    public function menuList(Request $request): void
    {
        $userId = $this->currentUserId();
        $date = (string)$request->query('date', date('Y-m-d'));
        $data = (new MenuModel())->listForDate($userId, $date);

        $this->json(['success' => true, 'data' => $data], 200);
    }

    public function menuAdd(Request $request): void
    {
        $userId = $this->currentUserId();
        $date = (string)$request->input('menu_date', date('Y-m-d'));
        $type = (string)$request->input('meal_type', '');
        $desc = (string)$request->input('description', '');
        $cal  = (int)$request->input('calories', 0);
        $foodId = $request->input('food_id') !== null ? (int)$request->input('food_id') : null;
        $protein = $request->input('protein_g') !== null && $request->input('protein_g') !== '' ? (float)$request->input('protein_g') : null;
        $carb    = $request->input('carb_g') !== null && $request->input('carb_g') !== '' ? (float)$request->input('carb_g') : null;
        $fat     = $request->input('fat_g') !== null && $request->input('fat_g') !== '' ? (float)$request->input('fat_g') : null;

        try {
            $newId = (new MenuModel())->add($userId, $date, $type, $desc, $cal, $foodId, $protein, $carb, $fat);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Menüye eklendi.', 'data' => ['id' => $newId]], 201);
    }

    public function menuDelete(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        (new MenuModel())->delete($id, $userId);
        $this->json(['success' => true, 'message' => 'Menü kaydı silindi.', 'data' => []], 200);
    }

    public function menuTodayCalories(Request $request): void
    {
        $userId = $this->currentUserId();
        $data = (new MenuModel())->todayCalories($userId);

        $this->json(['success' => true, 'data' => $data], 200);
    }

    public function menuWeeklyCalories(Request $request): void
    {
        $userId = $this->currentUserId();
        $data = (new MenuModel())->weeklyCalories($userId);

        $this->json(['success' => true, 'data' => $data], 200);
    }

    public function menuAddFromRecipe(Request $request): void
    {
        $userId = $this->currentUserId();
        $date = (string)$request->input('menu_date', date('Y-m-d'));
        $type = (string)$request->input('meal_type', 'öğle');
        $title = (string)$request->input('recipe_title', '');
        $calories = (int)$request->input('calories', 300);

        if (trim($title) === '') {
            $this->json(['success' => false, 'message' => 'Tarif adı boş olamaz.'], 400);
            return;
        }

        $newId = (new MenuModel())->addFromRecipe($userId, $date, $type, $title, $calories);
        $this->json(['success' => true, 'message' => 'Tarif menüye eklendi.', 'data' => ['id' => $newId]], 201);
    }

    public function calorieAdd(Request $request): void
    {
        $userId = $this->currentUserId();
        $cal = (int)$request->input('calories', 0);
        $meal = (string)$request->input('meal_type', 'atıştırmalık');
        $desc = (string)$request->input('description', 'Manuel ekleme');

        try {
            $data = (new MenuModel())->addCalorieEntry($userId, $cal, $meal, $desc);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'data' => $data], 201);
    }

    public function calorieToday(Request $request): void
    {
        $userId = $this->currentUserId();
        $total = (new MenuModel())->todayCalorieTotal($userId);

        $this->json(['success' => true, 'data' => ['today_total' => $total]], 200);
    }

    public function foodCaloriesList(Request $request): void
    {
        $this->json(['success' => true, 'data' => (new MenuModel())->foodCaloriesList()], 200);
    }

    public function foodCaloriesDropdown(Request $request): void
    {
        $this->json(['success' => true, 'data' => (new MenuModel())->foodCaloriesDropdown()], 200);
    }

    public function badgeEarn(Request $request): void
    {
        $userId = $this->currentUserId();
        $badgeKey = (string)$request->input('badge_key', '');

        if ($badgeKey === '') {
            $this->json(['success' => false, 'message' => 'badge_key gerekli'], 400);
            return;
        }

        try {
            (new BadgeModel())->earn($userId, $badgeKey);
        } catch (\Throwable) {
            $this->json(['success' => false, 'message' => 'Rozet kaydedilemedi'], 500);
            return;
        }

        $this->json(['success' => true, 'data' => ['earned' => true, 'badge_key' => $badgeKey]], 200);
    }

    public function badgeList(Request $request): void
    {
        $userId = $this->currentUserId();
        $this->json(['success' => true, 'data' => (new BadgeModel())->listForUser($userId)], 200);
    }

    public function badgeDailyStatus(Request $request): void
    {
        $userId = $this->currentUserId();
        $this->json(['success' => true, 'data' => (new BadgeModel())->dailyStatus($userId)], 200);
    }

    public function badgeResetDaily(Request $request): void
    {
        $this->json(['success' => true, 'message' => "Günlük rozetler DB'de tarih bazlı yönetiliyor.", 'data' => []], 200);
    }
}

<?php

namespace App\Models;

use App\Core\Database;
use PDO;
use PDOException;

final class DashboardModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getSummary(int $userId): array
    {
        $water = $this->db->prepare("
            SELECT COALESCE(SUM(amount_ml), 0)
            FROM water_logs
            WHERE user_id = ? AND DATE(logged_at) = CURDATE()
        ");
        $water->execute([$userId]);

        $calories = $this->db->prepare("
            SELECT COALESCE(SUM(calories), 0)
            FROM daily_menu
            WHERE user_id = ? AND menu_date = CURDATE()
        ");
        $calories->execute([$userId]);

        $fridgeCount = $this->db->prepare('SELECT COUNT(*) FROM fridge_items WHERE user_id = ?');
        $fridgeCount->execute([$userId]);

        $expiringSoon = $this->db->prepare("
            SELECT COUNT(*) FROM fridge_items
            WHERE user_id = ? AND expiry_date IS NOT NULL
              AND DATEDIFF(expiry_date, CURDATE()) BETWEEN 0 AND 3
        ");
        $expiringSoon->execute([$userId]);

        return [
            'water_ml_today'      => (int)$water->fetchColumn(),
            'calories_today'      => (int)$calories->fetchColumn(),
            'fridge_item_count'   => (int)$fridgeCount->fetchColumn(),
            'expiring_soon_count' => (int)$expiringSoon->fetchColumn(),
        ];
    }

    public function getDashboardPageData(int $userId): array
    {
        $s = $this->db->prepare("SELECT COALESCE(SUM(amount_ml),0) FROM water_logs WHERE user_id=? AND DATE(logged_at)=CURDATE()");
        $s->execute([$userId]);
        $waterToday = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT COUNT(*) FROM fridge_items WHERE user_id=?");
        $s->execute([$userId]);
        $cabinetCount = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT COUNT(*) FROM saved_recipes WHERE user_id=?");
        $s->execute([$userId]);
        $recipeCount = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT COUNT(*) FROM badges WHERE user_id=?");
        $s->execute([$userId]);
        $badgeCount = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT COUNT(*) FROM shopping_items WHERE user_id=? AND is_done=0");
        $s->execute([$userId]);
        $shopCount = (int)$s->fetchColumn();

        $s = $this->db->prepare("SELECT meta_value FROM user_meta WHERE user_id=? AND meta_key='body_profile' LIMIT 1");
        try {
            $s->execute([$userId]);
            $profileJson = $s->fetchColumn() ?: 'null';
        } catch (PDOException $e) {
            $profileJson = 'null';
        }

        return [
            'water_today'   => $waterToday,
            'cabinet_count' => $cabinetCount,
            'recipe_count'  => $recipeCount,
            'badge_count'   => $badgeCount,
            'shop_count'    => $shopCount,
            'profile_json'  => $profileJson,
        ];
    }
}

<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class MenuModel
{
    private PDO $db;
    private const ALLOWED_MEAL_TYPES = ['kahvaltı', 'öğle', 'akşam', 'atıştırmalık'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listForDate(int $userId, string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM daily_menu
            WHERE user_id = ? AND menu_date = ?
            ORDER BY meal_type
        ");
        $stmt->execute([$userId, $date]);

        return $stmt->fetchAll();
    }

    public function add(int $userId, string $date, string $mealType, string $description, int $calories, ?int $foodId, ?float $protein, ?float $carb, ?float $fat): int
    {
        if (!in_array($mealType, self::ALLOWED_MEAL_TYPES, true)) {
            throw new \InvalidArgumentException('Geçersiz öğün tipi.');
        }

        if ($foodId !== null && $foodId > 0) {
            $stmt = $this->db->prepare('SELECT calories, protein_g, carb_g, fat_g FROM food_calories WHERE id = ? LIMIT 1');
            $stmt->execute([$foodId]);
            $food = $stmt->fetch();

            if ($food) {
                if ($calories <= 0) {
                    $calories = (int)$food['calories'];
                }
                $protein = $food['protein_g'] !== null ? (float)$food['protein_g'] : null;
                $carb    = $food['carb_g']    !== null ? (float)$food['carb_g']    : null;
                $fat     = $food['fat_g']     !== null ? (float)$food['fat_g']     : null;
            }
        }

        $stmt = $this->db->prepare("
            INSERT INTO daily_menu (user_id, menu_date, meal_type, description, calories, protein_g, carb_g, fat_g)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $date, $mealType, $description, $calories > 0 ? $calories : null, $protein, $carb, $fat]);

        return (int)$this->db->lastInsertId();
    }

    public function addFromRecipe(int $userId, string $date, string $mealType, string $title, int $calories): int
    {
        if (!in_array($mealType, self::ALLOWED_MEAL_TYPES, true)) {
            $mealType = 'öğle';
        }
        if ($calories <= 0) {
            $calories = 300;
        }

        $stmt = $this->db->prepare("
            INSERT INTO daily_menu (user_id, menu_date, meal_type, description, calories)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $date, $mealType, $title, $calories]);

        return (int)$this->db->lastInsertId();
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM daily_menu WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }

    public function todayCalories(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(calories), 0) FROM daily_menu
            WHERE user_id = ? AND menu_date = CURDATE()
        ");
        $stmt->execute([$userId]);
        $total = (int)$stmt->fetchColumn();

        $stmt2 = $this->db->prepare("
            SELECT COALESCE(AVG(daily_total), 0) FROM (
                SELECT SUM(calories) AS daily_total
                FROM daily_menu
                WHERE user_id = ? AND menu_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY menu_date
            ) AS t
        ");
        $stmt2->execute([$userId]);
        $weekAvg = round((float)$stmt2->fetchColumn(), 1);

        return ['total' => $total, 'week_avg' => $weekAvg];
    }

    public function weeklyCalories(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT menu_date AS day, SUM(calories) AS total
            FROM daily_menu
            WHERE user_id = ? AND menu_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY menu_date
            ORDER BY menu_date ASC
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = date('Y-m-d', strtotime("-$i days"));
            $found = array_values(array_filter($rows, fn($r) => $r['day'] === $d));
            $data[] = [
                'day'   => $d,
                'label' => date('d/m', strtotime($d)),
                'total' => $found ? (int)$found[0]['total'] : 0,
            ];
        }

        return $data;
    }

    public function addCalorieEntry(int $userId, int $calories, string $mealType, string $description): array
    {
        if ($calories <= 0) {
            throw new \InvalidArgumentException('Geçersiz kalori.');
        }

        $date = date('Y-m-d');

        $stmt = $this->db->prepare("
            INSERT INTO daily_menu (user_id, menu_date, meal_type, description, calories)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$userId, $date, $mealType, $description, $calories]);
        $newId = (int)$this->db->lastInsertId();

        $sum = $this->db->prepare("SELECT COALESCE(SUM(calories),0) FROM daily_menu WHERE user_id=? AND menu_date=?");
        $sum->execute([$userId, $date]);

        return ['today_total' => (int)$sum->fetchColumn(), 'id' => $newId];
    }

    public function todayCalorieTotal(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(calories),0) FROM daily_menu WHERE user_id=? AND menu_date=CURDATE()");
        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();
    }

    public function foodCaloriesList(): array
    {
        try {
            return $this->db->query("
                SELECT id, name, calories, portion_size, portion_grams, protein_g, carb_g, fat_g, category
                FROM food_calories ORDER BY name ASC
            ")->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }

    public function foodCaloriesDropdown(): array
    {
        try {
            return $this->db->query("
                SELECT id, name, calories, protein_g, carb_g, fat_g, portion_size, category
                FROM food_calories ORDER BY category ASC, name ASC
            ")->fetchAll();
        } catch (\Throwable) {
            return [];
        }
    }
}

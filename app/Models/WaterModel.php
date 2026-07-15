<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class WaterModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add(int $userId, int $amountMl): int
    {
        $stmt = $this->db->prepare('INSERT INTO water_logs (user_id, amount_ml) VALUES (?, ?)');
        $stmt->execute([$userId, $amountMl]);

        return $this->todayTotal($userId);
    }

    public function todayTotal(int $userId): int
    {
        $stmt = $this->db->prepare("
            SELECT COALESCE(SUM(amount_ml), 0)
            FROM water_logs
            WHERE user_id = ? AND DATE(logged_at) = CURDATE()
        ");
        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();
    }

    public function weekly(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT DATE(logged_at) AS day, SUM(amount_ml) AS total
            FROM water_logs
            WHERE user_id = ? AND logged_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY DATE(logged_at)
            ORDER BY day ASC
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
}

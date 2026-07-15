<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class BadgeModel
{
    private PDO $db;
    private const DAILY_KEYS = ['water', 'plate', 'salt', 'rest', 'move', 'snack', 'tips', 'tasks'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function earn(int $userId, string $badgeKey): void
    {
        $stmt = $this->db->prepare("
            INSERT INTO badges (user_id, badge_key, earned_at)
            VALUES (:user_id, :badge_key, NOW())
            ON DUPLICATE KEY UPDATE earned_at = NOW()
        ");
        $stmt->execute([':user_id' => $userId, ':badge_key' => $badgeKey]);
    }

    public function earnNamed(int $userId, string $badgeKey, string $badgeName): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM badges WHERE user_id = ? AND badge_key = ? LIMIT 1');
        $stmt->execute([$userId, $badgeKey]);

        if ($stmt->fetch()) {
            return false;
        }

        $ins = $this->db->prepare('INSERT INTO badges (user_id, badge_key, badge_name) VALUES (?, ?, ?)');
        $ins->execute([$userId, $badgeKey, $badgeName]);

        return true;
    }

    public function listForUser(int $userId): array
    {
        $today = date('Y-m-d');

        $stmt = $this->db->prepare("
            SELECT badge_key, MAX(DATE(earned_at)) AS earned_date
            FROM badges WHERE user_id = ? GROUP BY badge_key
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        $result = [];
        foreach ($rows as $row) {
            $isDaily = in_array($row['badge_key'], self::DAILY_KEYS, true);
            if ($isDaily && $row['earned_date'] !== $today) {
                continue;
            }
            $result[] = [
                'badge_key'   => $row['badge_key'],
                'earned_date' => $row['earned_date'] ? date('Y-m-d', strtotime($row['earned_date'])) : null,
                'is_daily'    => $isDaily ? 1 : 0,
            ];
        }

        return $result;
    }

    public function dailyStatus(int $userId): array
    {
        $today = date('Y-m-d');
        $stmt = $this->db->prepare("
            SELECT badge_key, DATE(earned_at) AS earned_date
            FROM badges WHERE user_id = ? AND DATE(earned_at) = ?
        ");
        $stmt->execute([$userId, $today]);

        $result = [];
        foreach ($stmt->fetchAll() as $r) {
            $result[$r['badge_key']] = $r['earned_date'];
        }

        return $result;
    }
}

<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class WasteModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add(int $userId, string $itemName, ?string $amount, ?string $reason): void
    {
        if ($itemName === '') {
            throw new \InvalidArgumentException('Ürün adı boş olamaz.');
        }

        $stmt = $this->db->prepare('INSERT INTO waste_logs (user_id, item_name, amount, reason) VALUES (?, ?, ?, ?)');
        $stmt->execute([$userId, $itemName, $amount, $reason]);
    }

    public function listForUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT id, item_name, amount, reason, DATE_FORMAT(logged_at,'%d.%m.%Y %H:%i') AS logged_at
            FROM waste_logs
            WHERE user_id = ?
            ORDER BY logged_at DESC
            LIMIT 50
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }
}

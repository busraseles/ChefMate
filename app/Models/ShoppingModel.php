<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class ShoppingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listForUser(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM shopping_items
            WHERE user_id = ?
            ORDER BY is_done ASC, created_at DESC
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function add(int $userId, string $name, ?string $quantity): int
    {
        if ($name === '') {
            throw new \InvalidArgumentException('Ürün adı boş olamaz.');
        }

        $stmt = $this->db->prepare('INSERT INTO shopping_items (user_id, name, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$userId, $name, $quantity]);

        return (int)$this->db->lastInsertId();
    }

    public function toggle(int $id, int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE shopping_items SET is_done = 1 - is_done WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM shopping_items WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }
}

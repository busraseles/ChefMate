<?php

namespace App\Models;

use App\Core\Database;
use DateTime;
use PDO;
use Throwable;

final class FridgeModel
{
    private PDO $db;
    private const VALID_SHELVES = ['shelf-1', 'shelf-2', 'freezer-shelf'];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function listForUser(int $userId): array
    {
        $this->checkExpiryNotifications($userId);

        $stmt = $this->db->prepare("
            SELECT
                id, user_id, name, icon, shelf, expiry_date, calories,
                created_at, updated_at,
                DATEDIFF(expiry_date, CURDATE()) AS days_left
            FROM fridge_items
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function create(int $userId, array $input): int
    {
        $name       = trim((string)($input['name'] ?? ''));
        $icon       = trim((string)($input['icon_url'] ?? $input['icon'] ?? '')) ?: null;
        $shelf      = trim((string)($input['shelf'] ?? 'shelf-1'));
        $expiry     = trim((string)($input['expiry_date'] ?? $input['expiry'] ?? '')) ?: null;
        $productKey = trim((string)($input['product_key'] ?? '')) ?: null;
        $calories   = (int)($input['calories'] ?? 0);
        $calories   = $calories > 0 ? $calories : null;
        $category   = trim((string)($input['category'] ?? '')) ?: null;
        $emoji      = trim((string)($input['emoji'] ?? '')) ?: null;

        if ($name === '') {
            throw new \InvalidArgumentException('Ürün adı boş olamaz.');
        }

        if (!in_array($shelf, self::VALID_SHELVES, true)) {
            $shelf = 'shelf-1';
        }

        if ($expiry !== null) {
            $dt = DateTime::createFromFormat('Y-m-d', $expiry);
            if (!$dt || $dt->format('Y-m-d') !== $expiry) {
                $expiry = null;
            }
        }

        $proteinG = $carbG = $fatG = $fiberG = $portion = null;

        $product = null;
        if ($productKey) {
            $stmt = $this->db->prepare('SELECT * FROM fridge_products WHERE `key` = ? LIMIT 1');
            $stmt->execute([$productKey]);
            $product = $stmt->fetch();
        }
        if (!$product) {
            $stmt = $this->db->prepare('SELECT * FROM fridge_products WHERE name = ? LIMIT 1');
            $stmt->execute([$name]);
            $product = $stmt->fetch();
        }

        if ($product) {
            $calories   = $calories ?? ($product['calories'] > 0 ? (int)$product['calories'] : null);
            $proteinG   = $product['protein_g']  !== null ? (float)$product['protein_g']  : null;
            $carbG      = $product['carb_g']     !== null ? (float)$product['carb_g']     : null;
            $fatG       = $product['fat_g']      !== null ? (float)$product['fat_g']      : null;
            $fiberG     = $product['fiber_g']    !== null ? (float)$product['fiber_g']    : null;
            $portion    = $product['portion_size'] ?? null;
            $category   = $category ?? $product['category'];
            $emoji      = $emoji    ?? $product['emoji'];
            $productKey = $productKey ?? $product['key'];
        }

        $stmt = $this->db->prepare("
            INSERT INTO fridge_items
                (user_id, name, icon, shelf, expiry_date, calories,
                 protein_g, carb_g, fat_g, fiber_g, portion_size,
                 category, emoji, product_key)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $userId, $name, $icon, $shelf, $expiry, $calories,
            $proteinG, $carbG, $fatG, $fiberG, $portion,
            $category, $emoji, $productKey,
        ]);

        $newId = (int)$this->db->lastInsertId();

        if ($expiry !== null) {
            $this->checkExpiryNotifications($userId);
        }

        return $newId;
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM fridge_items WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
        $deleted = $stmt->rowCount() > 0;

        if ($deleted) {
            try {
                $del = $this->db->prepare("
                    DELETE FROM notifications
                    WHERE user_id = ? AND ref_table = 'fridge_items' AND ref_id = ?
                ");
                $del->execute([$userId, $id]);
            } catch (Throwable) {
                
            }
        }

        return $deleted;
    }

    public function update(int $id, int $userId, array $fields): bool
    {
        $setClauses = [];
        $params = [];

        if (array_key_exists('shelf', $fields)) {
            $shelf = trim((string)$fields['shelf']);
            if (!in_array($shelf, self::VALID_SHELVES, true)) {
                $shelf = 'shelf-1';
            }
            $setClauses[] = 'shelf = ?';
            $params[] = $shelf;
        }

        if (array_key_exists('expiry_date', $fields) || array_key_exists('expiry', $fields)) {
            $expiry = trim((string)($fields['expiry_date'] ?? $fields['expiry'] ?? '')) ?: null;
            if ($expiry !== null) {
                $dt = DateTime::createFromFormat('Y-m-d', $expiry);
                if (!$dt || $dt->format('Y-m-d') !== $expiry) {
                    $expiry = null;
                }
            }
            $setClauses[] = 'expiry_date = ?';
            $params[] = $expiry;
        }

        if (array_key_exists('calories', $fields)) {
            $cal = (int)$fields['calories'];
            $setClauses[] = 'calories = ?';
            $params[] = $cal > 0 ? $cal : null;
        }

        if (empty($setClauses)) {
            return false;
        }

        $params[] = $id;
        $params[] = $userId;

        $sql = 'UPDATE fridge_items SET ' . implode(', ', $setClauses) . ' WHERE id = ? AND user_id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        $this->checkExpiryNotifications($userId);

        return true;
    }

    

    public function syncExpiryNotifications(int $userId): void
    {
        $this->checkExpiryNotifications($userId);
    }

    private function checkExpiryNotifications(int $userId): void
    {
        $hasRefCols = false;
        try {
            $col = $this->db->query("SHOW COLUMNS FROM notifications LIKE 'ref_id'");
            $hasRefCols = (bool)$col->fetch();
        } catch (Throwable) {
            $hasRefCols = false;
        }

        $stmt = $this->db->prepare("
            SELECT id, name, expiry_date, DATEDIFF(expiry_date, CURDATE()) AS days_left
            FROM fridge_items
            WHERE user_id = ?
              AND expiry_date IS NOT NULL
              AND DATEDIFF(expiry_date, CURDATE()) BETWEEN 0 AND 3
        ");
        $stmt->execute([$userId]);
        $items = $stmt->fetchAll();

        foreach ($items as $item) {
            $days  = (int)$item['days_left'];
            $emoji = $days === 0 ? '⛔' : ($days === 1 ? '🚨' : '⚠️');
            $type  = $days === 0 ? 'danger' : ($days === 1 ? 'warning' : 'info');
            $title = "$emoji {$item['name']} — SKT Uyarısı";
            $msg   = $days === 0
                ? "{$item['name']} bugün son kullanma tarihine ulaştı!"
                : "{$item['name']} için {$days} gün kaldı.";

            if ($hasRefCols) {
                $dup = $this->db->prepare("
                    SELECT id FROM notifications
                    WHERE user_id = ? AND ref_table = 'fridge_items' AND ref_id = ? AND DATE(created_at) = CURDATE()
                    LIMIT 1
                ");
                $dup->execute([$userId, (int)$item['id']]);

                if (!$dup->fetch()) {
                    $ins = $this->db->prepare("
                        INSERT INTO notifications (user_id, type, title, message, ref_table, ref_id)
                        VALUES (?, ?, ?, ?, 'fridge_items', ?)
                    ");
                    $ins->execute([$userId, $type, $title, $msg, (int)$item['id']]);
                }
            } else {
                $dup = $this->db->prepare("
                    SELECT id FROM notifications
                    WHERE user_id = ? AND title = ? AND DATE(created_at) = CURDATE()
                    LIMIT 1
                ");
                $dup->execute([$userId, $title]);

                if (!$dup->fetch()) {
                    $ins = $this->db->prepare("
                        INSERT INTO notifications (user_id, type, title, message)
                        VALUES (?, ?, ?, ?)
                    ");
                    $ins->execute([$userId, $type, $title, $msg]);
                }
            }
        }
    }
}

<?php

namespace App\Models;

use App\Core\Database;
use App\Helpers\RecipeScraper;
use PDO;

final class UserRecipeModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function add(int $userId, string $title, string $ingredients, string $instructions, ?string $category, ?array $uploadedFile, ?string $imageUrlInput): array
    {
        if ($title === '')        throw new \InvalidArgumentException('Tarif adı boş olamaz.');
        if ($ingredients === '')  throw new \InvalidArgumentException('Malzemeler boş olamaz.');
        if ($instructions === '') throw new \InvalidArgumentException('Yapılış adımları boş olamaz.');

        $imagePath = null;

        if ($uploadedFile !== null && !empty($uploadedFile['tmp_name'])) {
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($uploadedFile['type'], $allowed, true)) {
                throw new \InvalidArgumentException('Geçersiz resim formatı.');
            }
            if ($uploadedFile['size'] > 5 * 1024 * 1024) {
                throw new \InvalidArgumentException("Resim 5 MB'dan büyük olamaz.");
            }

            $dir = dirname(__DIR__, 2) . '/uploads/user_recipes/';
            RecipeScraper::ensureDir($dir);
            $ext = pathinfo($uploadedFile['name'], PATHINFO_EXTENSION) ?: 'jpg';
            $name = 'recipe_' . $userId . '_' . uniqid() . '.' . $ext;

            if (!move_uploaded_file($uploadedFile['tmp_name'], $dir . $name)) {
                throw new \InvalidArgumentException('Resim yüklenemedi.');
            }
            $imagePath = 'uploads/user_recipes/' . $name;
        } elseif (!empty($imageUrlInput)) {
            $imagePath = trim($imageUrlInput);
        }

        $stmt = $this->db->prepare("
            INSERT INTO user_recipes
              (user_id, title, ingredients, instructions, image_path, image_url, category, source, is_public)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'user', 1)
        ");
        $stmt->execute([$userId, $title, $ingredients, $instructions, $imagePath, $imagePath, $category]);
        $newId = (int)$this->db->lastInsertId();

        $chkBadge = $this->db->prepare("SELECT id FROM badges WHERE user_id = ? AND badge_key = 'first_recipe' LIMIT 1");
        $chkBadge->execute([$userId]);
        $badgeAwarded = false;
        if (!$chkBadge->fetch()) {
            $this->db->prepare("INSERT INTO badges (user_id, badge_key, badge_name) VALUES (?, 'first_recipe', 'İlk Tarifim')")
                ->execute([$userId]);
            $badgeAwarded = true;
        }

        return ['id' => $newId, 'badge_awarded' => $badgeAwarded];
    }

    public function listForUser(int $userId, int $offset, int $limit): array
    {
        $offset = max(0, $offset);
        $limit  = min(24, max(1, $limit));

        $totalStmt = $this->db->prepare('SELECT COUNT(*) FROM user_recipes WHERE user_id = ?');
        $totalStmt->execute([$userId]);
        $total = (int)$totalStmt->fetchColumn();

        $stmt = $this->db->prepare("
            SELECT id, title, ingredients, instructions, image_path, image_url, category, created_at
            FROM user_recipes
            WHERE user_id = ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        foreach ($items as &$item) {
            $item['image'] = $item['image_url'] ?: $item['image_path'] ?: '';
        }
        unset($item);

        return ['items' => $items, 'total' => $total, 'hasMore' => ($offset + count($items)) < $total];
    }

    public function delete(int $id, int $userId): bool
    {
        $stmt = $this->db->prepare('DELETE FROM user_recipes WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);

        return $stmt->rowCount() > 0;
    }

    public function countAll(): int
    {
        try {
            return (int)$this->db->query('SELECT COUNT(*) FROM user_recipes')->fetchColumn();
        } catch (\Throwable) {
            return 0;
        }
    }

    public function browseFormat(int $offset, int $limit): array
    {
        try {
            $stmt = $this->db->prepare("
                SELECT ur.*, u.name AS user_name FROM user_recipes ur
                JOIN users u ON u.id = ur.user_id
                ORDER BY ur.created_at DESC LIMIT $limit OFFSET $offset
            ");
            $stmt->execute();
            $rows = $stmt->fetchAll();
        } catch (\Throwable) {
            return [];
        }

        return array_map(static function (array $r): array {
            return [
                'baslik'         => $r['title'],
                'resim'          => $r['image_url'] ?? '',
                'url'            => '',
                'tarih'          => date('d.m.Y', strtotime($r['created_at'])),
                'ozet'           => mb_substr(strip_tags($r['instructions'] ?? ''), 0, 130),
                'rkey'           => md5('user_recipe_' . $r['id']),
                'user_name'      => $r['user_name'],
                'ingredients'    => $r['ingredients'],
                'instructions'   => $r['instructions'],
                'is_user_recipe' => true,
                'recipe_id'      => $r['id'],
                'category'       => $r['category'] ?? '',
            ];
        }, $rows);
    }
}

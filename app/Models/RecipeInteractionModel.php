<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class RecipeInteractionModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function myLikes(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT recipe_key, recipe_title, recipe_image, recipe_url,
                   DATE_FORMAT(created_at, '%d.%m.%Y %H:%i') AS created_at
            FROM recipe_interactions
            WHERE user_id = ? AND action_type = 'like'
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function myComments(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT ri.id, ri.recipe_key, ri.recipe_title, ri.recipe_image,
                   ri.recipe_url, ri.comment_text,
                   DATE_FORMAT(ri.created_at, '%d.%m.%Y %H:%i') AS created_at,
                   u.name AS user_name
            FROM recipe_interactions ri
            JOIN users u ON u.id = ri.user_id
            WHERE ri.user_id = ? AND ri.action_type = 'comment'
            ORDER BY ri.created_at DESC
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function editComment(int $id, int $userId, string $text): void
    {
        $chk = $this->db->prepare("SELECT id FROM recipe_interactions WHERE id = ? AND user_id = ? AND action_type = 'comment'");
        $chk->execute([$id, $userId]);
        if (!$chk->fetch()) {
            throw new \InvalidArgumentException('Yorum bulunamadı veya yetkiniz yok.');
        }

        $this->db->prepare('UPDATE recipe_interactions SET comment_text = ? WHERE id = ? AND user_id = ?')
            ->execute([$text, $id, $userId]);
    }

    public function deleteComment(int $id, int $userId): void
    {
        $chk = $this->db->prepare("SELECT id FROM recipe_interactions WHERE id = ? AND user_id = ? AND action_type = 'comment'");
        $chk->execute([$id, $userId]);
        if (!$chk->fetch()) {
            throw new \InvalidArgumentException('Yorum bulunamadı veya yetkiniz yok.');
        }

        $this->db->prepare('DELETE FROM recipe_interactions WHERE id = ? AND user_id = ?')->execute([$id, $userId]);
    }

    public function toggleLike(int $userId, string $key, string $title, string $image, string $url): bool
    {
        $chk = $this->db->prepare("SELECT id FROM recipe_interactions WHERE user_id=? AND recipe_key=? AND action_type='like' LIMIT 1");
        $chk->execute([$userId, $key]);
        $existing = $chk->fetch();

        if ($existing) {
            $this->db->prepare('DELETE FROM recipe_interactions WHERE id = ?')->execute([$existing['id']]);
            return false;
        }

        $this->db->prepare("
            INSERT INTO recipe_interactions (user_id, recipe_key, recipe_title, recipe_image, recipe_url, action_type)
            VALUES (?, ?, ?, ?, ?, 'like')
        ")->execute([$userId, $key, $title, $image, $url]);

        return true;
    }

    public function toggleSave(int $userId, string $key, string $title, string $image, string $url): bool
    {
        $chk = $this->db->prepare("SELECT id FROM recipe_interactions WHERE user_id=? AND recipe_key=? AND action_type='save' LIMIT 1");
        $chk->execute([$userId, $key]);
        $existing = $chk->fetch();

        if ($existing) {
            $this->db->prepare('DELETE FROM recipe_interactions WHERE id = ?')->execute([$existing['id']]);
            $this->db->prepare('DELETE FROM saved_recipes WHERE user_id = ? AND source = ?')->execute([$userId, $url]);
            return false;
        }

        $this->db->prepare("
            INSERT INTO recipe_interactions (user_id, recipe_key, recipe_title, recipe_image, recipe_url, action_type)
            VALUES (?, ?, ?, ?, ?, 'save')
        ")->execute([$userId, $key, $title, $image, $url]);

        $chkSaved = $this->db->prepare('SELECT id FROM saved_recipes WHERE user_id=? AND source=? LIMIT 1');
        $chkSaved->execute([$userId, $url]);
        if (!$chkSaved->fetch()) {
            $this->db->prepare("INSERT INTO saved_recipes (user_id, title, ingredients, instructions, image_url, source) VALUES (?, ?, '', '', ?, ?)")
                ->execute([$userId, $title, $image, $url]);
        }

        return true;
    }

    public function addComment(int $userId, string $key, string $title, string $image, string $url, string $text): int
    {
        $this->db->prepare("
            INSERT INTO recipe_interactions (user_id, recipe_key, recipe_title, recipe_image, recipe_url, action_type, comment_text)
            VALUES (?, ?, ?, ?, ?, 'comment', ?)
        ")->execute([$userId, $key, $title, $image, $url, $text]);

        return (int)$this->db->lastInsertId();
    }

    public function getPublic(string $key, int $currentUserId): array
    {
        $cs = $this->db->prepare("
            SELECT ri.id, ri.comment_text, ri.user_id,
                   DATE_FORMAT(ri.created_at, '%d.%m.%Y %H:%i') AS created_at,
                   u.name AS user_name
            FROM recipe_interactions ri
            LEFT JOIN users u ON u.id = ri.user_id
            WHERE ri.recipe_key = ? AND ri.action_type = 'comment'
            ORDER BY ri.created_at ASC
        ");
        $cs->execute([$key]);
        $comments = $cs->fetchAll();

        foreach ($comments as &$c) {
            $c['is_mine'] = ((int)$c['user_id'] === $currentUserId);
            unset($c['user_id']);
        }
        unset($c);

        $ls = $this->db->prepare("SELECT COUNT(*) FROM recipe_interactions WHERE recipe_key=? AND action_type='like'");
        $ls->execute([$key]);

        return ['comments' => $comments, 'like_count' => (int)$ls->fetchColumn()];
    }

    public function myLikesAndSavesMap(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT recipe_key, action_type FROM recipe_interactions WHERE user_id=? AND action_type IN ('like','save')");
        $stmt->execute([$userId]);

        $likes = [];
        $saves = [];
        foreach ($stmt->fetchAll() as $r) {
            if ($r['action_type'] === 'like') $likes[$r['recipe_key']] = true;
            if ($r['action_type'] === 'save') $saves[$r['recipe_key']] = true;
        }

        return ['likes' => $likes, 'saves' => $saves];
    }
}

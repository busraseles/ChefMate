<?php

namespace App\Models;

use App\Core\Database;
use PDO;

final class UserModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, password_hash, role FROM users WHERE email = ? LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function emailExists(string $email): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);

        return (bool)$stmt->fetch();
    }

    public function create(string $name, string $email, string $passwordHash): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $email, $passwordHash]);

        return (int)$this->db->lastInsertId();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function getProfile(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, role, created_at, avatar FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function updateName(int $id, string $name): void
    {
        $stmt = $this->db->prepare('UPDATE users SET name = ? WHERE id = ?');
        $stmt->execute([$name, $id]);
    }

    public function changePassword(int $id, string $currentPassword, string $newPassword): void
    {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $hash = (string)$stmt->fetchColumn();

        if (!$hash || !password_verify($currentPassword, $hash)) {
            throw new \InvalidArgumentException('Mevcut şifre hatalı.');
        }

        $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $upd = $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?');
        $upd->execute([$newHash, $id]);
    }

    public function updateAvatar(int $id, string $avatarUrl): void
    {
        $stmt = $this->db->prepare('UPDATE users SET avatar = ? WHERE id = ?');
        $stmt->execute([$avatarUrl, $id]);
    }

    public function getAvatar(int $id): ?string
    {
        $stmt = $this->db->prepare('SELECT avatar FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();

        return $row['avatar'] ?? null;
    }

    public function listNotifications(int $userId, int $limit): array
    {
        $limit = min(50, max(1, $limit));
        $stmt = $this->db->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT $limit");
        $stmt->execute([$userId]);

        return $stmt->fetchAll();
    }

    public function unreadNotificationCount(int $userId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
        $stmt->execute([$userId]);

        return (int)$stmt->fetchColumn();
    }

    public function markNotificationRead(int $userId, ?int $id, bool $all): void
    {
        if ($all) {
            $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
            $stmt->execute([$userId]);
            return;
        }

        if ($id !== null && $id > 0) {
            $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
            $stmt->execute([$id, $userId]);
        }
    }

    public function markAllNotificationsRead(int $userId): void
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?');
        $stmt->execute([$userId]);
    }

    public function deleteNotification(int $id, int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM notifications WHERE id = ? AND user_id = ?');
        $stmt->execute([$id, $userId]);
    }

    public function clearReadNotifications(int $userId): void
    {
        $stmt = $this->db->prepare('DELETE FROM notifications WHERE user_id = ? AND is_read = 1');
        $stmt->execute([$userId]);
    }
}

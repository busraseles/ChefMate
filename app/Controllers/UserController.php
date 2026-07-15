<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\UserModel;
use InvalidArgumentException;

final class UserController extends Controller
{
    public function show(Request $request): void
    {
        $userId = $this->currentUserId();
        $profile = (new UserModel())->getProfile($userId);

        if ($profile === null) {
            $this->json(['success' => false, 'message' => 'Kullanıcı bulunamadı.'], 404);
            return;
        }

        $this->json(['success' => true, 'data' => $profile], 200);
    }

    public function update(Request $request): void
    {
        $userId = $this->currentUserId();
        $name = trim((string)$request->input('name', ''));

        if ($name === '') {
            $this->json(['success' => false, 'message' => 'Ad soyad boş olamaz.'], 400);
            return;
        }

        (new UserModel())->updateName($userId, $name);
        $_SESSION['user_name'] = $name; 

        $this->json(['success' => true, 'message' => 'Profil güncellendi.', 'data' => []], 200);
    }

    public function changePassword(Request $request): void
    {
        $userId = $this->currentUserId();
        $current = (string)$request->input('current_password', '');
        $new     = (string)$request->input('new_password', '');
        $confirm = (string)$request->input('confirm_password', '');

        if (mb_strlen($new, 'UTF-8') < 6) {
            $this->json(['success' => false, 'message' => 'Yeni şifre en az 6 karakter olmalı.'], 400);
            return;
        }

        if ($new !== $confirm) {
            $this->json(['success' => false, 'message' => 'Şifreler eşleşmiyor.'], 400);
            return;
        }

        try {
            (new UserModel())->changePassword($userId, $current, $new);
        } catch (InvalidArgumentException $e) {
            $this->json(['success' => false, 'message' => $e->getMessage()], 400);
            return;
        }

        $this->json(['success' => true, 'message' => 'Şifre güncellendi.', 'data' => []], 200);
    }

    public function uploadAvatar(Request $request): void
    {
        $userId = $this->currentUserId();
        $file = $request->file('avatar');

        if ($file === null || $file['error'] !== UPLOAD_ERR_OK) {
            $this->json(['success' => false, 'message' => 'Dosya yükleme hatası.'], 400);
            return;
        }

        if ($file['size'] > 2 * 1024 * 1024) {
            $this->json(['success' => false, 'message' => "Dosya 2MB'ı geçemez."], 400);
            return;
        }

        $mime = mime_content_type($file['tmp_name']);
        $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($mime, $allowed, true)) {
            $this->json(['success' => false, 'message' => 'Geçersiz dosya tipi.'], 400);
            return;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) ?: 'jpg';
        $uploadDir = dirname(__DIR__, 2) . '/uploads/avatars/';

        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0755, true);
        }

        $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            $this->json(['success' => false, 'message' => 'Dosya kaydedilemedi.'], 500);
            return;
        }

        $avatarUrl = 'uploads/avatars/' . $filename;
        (new UserModel())->updateAvatar($userId, $avatarUrl);
        $_SESSION['user_avatar'] = $avatarUrl;

        $this->json(['success' => true, 'message' => 'Avatar güncellendi.', 'data' => ['avatar_url' => $avatarUrl]], 200);
    }

    public function getAvatar(Request $request): void
    {
        $userId = $this->currentUserId();
        $avatarUrl = (new UserModel())->getAvatar($userId);

        $this->json(['success' => true, 'data' => ['avatar_url' => $avatarUrl]], 200);
    }

    public function notificationsIndex(Request $request): void
    {
        $userId = $this->currentUserId();
        $limit = (int)$request->query('limit', 30);

        $model = new UserModel();
        $notifs = $model->listNotifications($userId, $limit);
        $unread = $model->unreadNotificationCount($userId);

        $this->json(['success' => true, 'data' => ['notifications' => $notifs, 'unread_count' => $unread]], 200);
    }

    public function notificationsMarkRead(Request $request): void
    {
        $userId = $this->currentUserId();
        $all = (bool)$request->input('all', false);
        $id = $request->input('id') !== null ? (int)$request->input('id') : null;

        (new UserModel())->markNotificationRead($userId, $id, $all);
        $this->json(['success' => true, 'message' => 'Bildirim güncellendi.', 'data' => []], 200);
    }

    public function notificationsReadAll(Request $request): void
    {
        $userId = $this->currentUserId();
        (new UserModel())->markAllNotificationsRead($userId);

        $this->json(['success' => true, 'message' => 'Tüm bildirimler okundu.', 'data' => []], 200);
    }

    public function notificationsDestroy(Request $request): void
    {
        $userId = $this->currentUserId();
        $id = (int)$request->param('id');

        if ($id <= 0) {
            $this->json(['success' => false, 'message' => 'Geçersiz bildirim id.'], 400);
            return;
        }

        (new UserModel())->deleteNotification($id, $userId);
        $this->json(['success' => true, 'message' => 'Bildirim silindi.', 'data' => []], 200);
    }

    public function notificationsClear(Request $request): void
    {
        $userId = $this->currentUserId();
        (new UserModel())->clearReadNotifications($userId);

        $this->json(['success' => true, 'message' => 'Okunmuş bildirimler temizlendi.', 'data' => []], 200);
    }

    public function notificationsSyncFridge(Request $request): void
    {
        $userId = $this->currentUserId();
        (new \App\Models\FridgeModel())->syncExpiryNotifications($userId);
        $unread = (new UserModel())->unreadNotificationCount($userId);

        $this->json(['success' => true, 'data' => ['unread_count' => $unread]], 200);
    }
}

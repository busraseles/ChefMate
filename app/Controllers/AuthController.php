<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Request;
use App\Core\Validator;
use App\Models\UserModel;

final class AuthController extends Controller
{

    private const OLD_DASHBOARD_REDIRECT = 'dashboard';
    private const OLD_INDEX_REDIRECT     = '.';

    public function showLogin(Request $request): void
    {
        if ($this->currentUserId() !== null) {
            $this->redirect(self::OLD_DASHBOARD_REDIRECT);
        }

        $registered = (string)$request->query('registered', '');

        $this->view('auth.login', [
            'error'     => '',
            'success'   => $registered === '1' ? 'Hesabınız oluşturuldu! Şimdi giriş yapabilirsiniz.' : '',
            'oldEmail'  => '',
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function login(Request $request): void
    {
        $email    = trim((string)$request->input('email', ''));
        $password = trim((string)$request->input('password', ''));

        if (!Csrf::verify((string)$request->input('csrf_token'))) {
            $this->rerenderLogin('Oturum güvenlik doğrulaması başarısız oldu. Sayfayı yenileyip tekrar deneyin.', $email);
        }

        $missing = Validator::missing(['email' => $email, 'password' => $password], ['email', 'password']);
        if (!empty($missing)) {
            $this->rerenderLogin('E-posta ve şifre alanları boş bırakılamaz.', $email);
        }

        $user = (new UserModel())->findByEmail($email);

        

        
        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->rerenderLogin('E-posta adresi veya şifre hatalı.', $email);
        }

        session_regenerate_id(true);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        $this->redirect(self::OLD_DASHBOARD_REDIRECT);
    }

    private function rerenderLogin(string $error, string $oldEmail): never
    {
        $this->view('auth.login', [
            'error'     => $error,
            'success'   => '',
            'oldEmail'  => $oldEmail,
            'csrfToken' => Csrf::token(),
        ]);
        exit;
    }

    public function showRegister(Request $request): void
    {
        if ($this->currentUserId() !== null) {
            $this->redirect(self::OLD_DASHBOARD_REDIRECT);
        }

        $this->view('auth.register', [
            'error'     => '',
            'oldName'   => '',
            'oldEmail'  => '',
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function register(Request $request): void
    {
        $name     = trim((string)$request->input('name', ''));
        $email    = trim((string)$request->input('email', ''));
        $password = trim((string)$request->input('password', ''));
        $confirm  = trim((string)$request->input('confirm_password', ''));

        if (!Csrf::verify((string)$request->input('csrf_token'))) {
            $this->rerenderRegister('Oturum güvenlik doğrulaması başarısız oldu. Sayfayı yenileyip tekrar deneyin.', $name, $email);
        }

        $missing = Validator::missing(
            ['name' => $name, 'email' => $email, 'password' => $password, 'confirm_password' => $confirm],
            ['name', 'email', 'password', 'confirm_password']
        );
        if (!empty($missing)) {
            $this->rerenderRegister('Tüm alanları doldurunuz.', $name, $email);
        }

        if (!Validator::isEmail($email)) {
            $this->rerenderRegister('Geçerli bir e-posta adresi giriniz.', $name, $email);
        }

        if (!Validator::minLength($password, 6)) {
            $this->rerenderRegister('Şifre en az 6 karakter olmalıdır.', $name, $email);
        }

        if ($password !== $confirm) {
            $this->rerenderRegister('Şifreler eşleşmiyor.', $name, $email);
        }

        $userModel = new UserModel();

        if ($userModel->emailExists($email)) {
            $this->rerenderRegister('Bu e-posta adresi zaten kayıtlı.', $name, $email);
        }

        
        
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $userModel->create($name, $email, $hash);

        $this->redirect('login?registered=1');
    }

    private function rerenderRegister(string $error, string $oldName, string $oldEmail): never
    {
        $this->view('auth.register', [
            'error'     => $error,
            'oldName'   => $oldName,
            'oldEmail'  => $oldEmail,
            'csrfToken' => Csrf::token(),
        ]);
        exit;
    }

    public function logout(Request $request): void
    {
        session_unset();
        session_destroy();

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        $this->redirect(self::OLD_INDEX_REDIRECT);
    }

    private function redirect(string $location): never
    {
        header('Location: ' . $location);
        exit;
    }

    public function apiLogin(Request $request): void
    {
        $email    = trim((string)$request->input('email', ''));
        $password = trim((string)$request->input('password', ''));

        $missing = Validator::missing(['email' => $email, 'password' => $password], ['email', 'password']);
        if (!empty($missing)) {
            $this->json(['success' => false, 'message' => 'E-posta ve şifre alanları boş bırakılamaz.'], 400);
            return;
        }

        $user = (new UserModel())->findByEmail($email);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->json(['success' => false, 'message' => 'E-posta adresi veya şifre hatalı.'], 401);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        $this->json([
            'success' => true,
            'message' => 'Giriş başarılı.',
            'data'    => ['id' => $user['id'], 'name' => $user['name'], 'role' => $user['role']],
        ], 200);
    }

    public function apiRegister(Request $request): void
    {
        $name     = trim((string)$request->input('name', ''));
        $email    = trim((string)$request->input('email', ''));
        $password = trim((string)$request->input('password', ''));
        $confirm  = trim((string)$request->input('confirm_password', ''));

        $missing = Validator::missing(
            ['name' => $name, 'email' => $email, 'password' => $password, 'confirm_password' => $confirm],
            ['name', 'email', 'password', 'confirm_password']
        );
        if (!empty($missing)) {
            $this->json(['success' => false, 'message' => 'Tüm alanları doldurunuz.'], 400);
            return;
        }

        if (!Validator::isEmail($email)) {
            $this->json(['success' => false, 'message' => 'Geçerli bir e-posta adresi giriniz.'], 400);
            return;
        }

        if (!Validator::minLength($password, 6)) {
            $this->json(['success' => false, 'message' => 'Şifre en az 6 karakter olmalıdır.'], 400);
            return;
        }

        if ($password !== $confirm) {
            $this->json(['success' => false, 'message' => 'Şifreler eşleşmiyor.'], 400);
            return;
        }

        $userModel = new UserModel();

        if ($userModel->emailExists($email)) {
            $this->json(['success' => false, 'message' => 'Bu e-posta adresi zaten kayıtlı.'], 409);
            return;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $newId = $userModel->create($name, $email, $hash);

        $this->json([
            'success' => true,
            'message' => 'Hesap oluşturuldu.',
            'data'    => ['id' => $newId],
        ], 201);
    }
}

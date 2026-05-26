<?php

final class LoginController extends Controller
{
    public function login(array $errors = []): void
    {
        $config = require BASE_PATH . '/config/config.php';
        $appName = $config['app_name'];
        $contentView = BASE_PATH . '/app/views/auth/login.php';
        require BASE_PATH . '/app/views/auth_layout.php';
    }

    public function authenticate(): void
    {
        if (Auth::attempt($_POST['email'] ?? '', $_POST['password'] ?? '')) {
            $this->redirect('/');
        }

        $this->login(['login' => 'E-mail ou senha invalidos.']);
    }

    public function logout(): void
    {
        Auth::logout();
        $this->redirect('/login');
    }
}

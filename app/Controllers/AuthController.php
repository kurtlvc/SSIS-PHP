<?php

class AuthController
{
    private $userModel;
    private $config;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->config = App::getInstance()->getConfig();
    }

    public function login()
    {
        if (isset($_SESSION['account_id'])) {
            $this->redirectToDashboard();
        }

        $error = App::flash('error');
        App::view('auth/login', [
            'config' => $this->config,
            'error' => $error,
            'csrf_token' => App::csrf_token(),
            'academic_year' => date('Y') . '&ndash;' . date('Y', strtotime('+1 year')),
            'system_title' => 'CuyoSIS',
        ], 'none');
    }

    public function authenticate()
    {
        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/login');
            return;
        }

        if (empty($login) || empty($password)) {
            App::flash('error', 'Please enter your username and password.');
            App::redirect('/login');
            return;
        }

        $account = $this->userModel->findByLogin($login);

        if (!$account || !password_verify($password, $account['password_hash'])) {
            App::flash('error', 'Invalid username or password.');
            App::redirect('/login');
            return;
        }

        if (!$account['is_active']) {
            App::flash('error', 'Your account is deactivated. Contact the administrator.');
            App::redirect('/login');
            return;
        }

        $_SESSION['account_id'] = $account['id'];
        $_SESSION['account_role'] = $account['role'];
        $_SESSION['account_login'] = $account['login'];
        $_SESSION['owner_type'] = $account['owner_type'];
        $_SESSION['owner_id'] = $account['owner_id'];

        if ($account['owner_type'] === 'student') {
            $_SESSION['stud_id'] = $account['student_id'];
        }

        $this->redirectToDashboard();
    }

    public function logout()
    {
        session_destroy();
        App::flash('success', 'You have been logged out.');
        App::redirect('/login');
    }

    private function redirectToDashboard()
    {
        $role = $_SESSION['account_role'] ?? 'student';

        switch ($role) {
            case 'student':
                App::redirect('/student/dashboard');
                break;
            case 'faculty':
            case 'chair':
            case 'dean':
                App::redirect('/registrar/enrollments');
                break;
            case 'admin':
                App::redirect('/admin/users');
                break;
            default:
                App::redirect('/student/dashboard');
        }
    }
}

<?php

require_once __DIR__ . '/../Middleware/RateLimitMiddleware.php';

class AuthController extends BaseController
{
    private $userModel;
    protected $config;
    private $rateLimiter;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->config = App::getInstance()->getConfig();
        $this->rateLimiter = new RateLimitMiddleware();
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
            'system_title' => 'CuyoSSIS',
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

        // Rate limiting for login attempts
        $clientId = $this->rateLimiter->getClientIdentifier();
        if (!$this->rateLimiter->loginLimit($clientId)) {
            return; // Redirect already handled by middleware
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

        // Build display name: Fname M. Lname
        if ($account['owner_type'] === 'student') {
            $_SESSION['stud_id'] = $account['student_id'];
            $fname = $account['student_fname'] ?? '';
            $lname = $account['student_lname'] ?? '';
            $mname = ''; // students don't have middle name in current schema
        } else {
            $fname = $account['personnel_fname'] ?? '';
            $lname = $account['personnel_lname'] ?? '';
            // Get middle name if available
            $mname = $this->getPersonnelMiddleName($account['owner_id']);
        }

        // Format: Fname M. Lname
        $middleInitial = $mname ? strtoupper(substr(trim($mname), 0, 1)) . '. ' : '';
        $_SESSION['display_name'] = trim($fname . ' ' . $middleInitial . $lname);

        $this->redirectToDashboard();
    }

    /**
     * Get personnel middle name
     */
    private function getPersonnelMiddleName($personnelId)
    {
        $sql = "SELECT mname FROM personnel WHERE p_id = :id";
        $row = $this->db->fetchOne($sql, ['id' => $personnelId]);
        return $row ? $row['mname'] : '';
    }

    public function logout()
    {
        session_destroy();
        App::flash('success', 'You have been logged out.');
        App::redirect('/login');
    }

    public function unauthorized()
    {
        App::view('errors/unauthorized', [
            'config' => $this->config,
            'system_title' => $this->config['system_title'] ?? 'CuyoSSIS',
            'pageTitle' => 'Access Denied',
        ], 'none');
    }

    public function forgotPassword()
    {
        if (isset($_SESSION['account_id'])) {
            $this->redirectToDashboard();
            return;
        }

        App::view('auth/forgot_password', [
            'config' => $this->config,
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Forgot Password',
            'system_title' => 'CuyoSSIS',
        ], 'none');
    }

    public function sendResetLink()
    {
        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/forgot-password');
            return;
        }

        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            App::flash('error', 'Please enter a valid email address.');
            App::redirect('/forgot-password');
            return;
        }

        // Rate limiting for password reset
        $clientId = $this->rateLimiter->getClientIdentifier();
        if (!$this->rateLimiter->passwordResetLimit($clientId)) {
            return; // Redirect already handled by middleware
        }

        App::flash('success', 'If that email exists, a reset link has been sent.');
        App::redirect('/login');
    }

    public function resetPasswordForm()
    {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            App::flash('error', 'Invalid or expired reset token.');
            App::redirect('/forgot-password');
            return;
        }

        App::view('auth/reset_password', [
            'config' => $this->config,
            'token' => $token,
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Reset Password',
            'system_title' => 'CuyoSSIS',
        ], 'none');
    }

    public function resetPassword()
    {
        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/forgot-password');
            return;
        }

        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if (empty($token)) {
            App::flash('error', 'Invalid or expired reset token.');
            App::redirect('/forgot-password');
            return;
        }

        if (empty($password) || strlen($password) < 8) {
            App::flash('error', 'Password must be at least 8 characters.');
            App::redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        if ($password !== $confirm) {
            App::flash('error', 'Passwords do not match.');
            App::redirect('/reset-password?token=' . urlencode($token));
            return;
        }

        App::flash('success', 'Your password has been reset. You may now login.');
        App::redirect('/login');
    }

    private function redirectToDashboard()
    {
        $role = $_SESSION['account_role'] ?? 'student';

        switch ($role) {
            case 'student':
                App::redirect('/student/dashboard');
                break;
            case 'cashier':
                App::redirect('/cashier/dashboard');
                break;
            case 'faculty':
            case 'chair':
            case 'dean':
                App::redirect('/registrar/enrollments');
                break;
            case 'admin':
                App::redirect('/admin/dashboard');
                break;
            default:
                App::redirect('/student/dashboard');
        }
    }
}

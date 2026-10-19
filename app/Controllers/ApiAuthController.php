<?php

class ApiAuthController extends BaseController
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    private function getJsonInput()
    {
        $raw = file_get_contents('php://input');
        $data = json_decode($raw ?: '', true);
        return is_array($data) ? $data : [];
    }

    public function login()
    {
        $input = $this->getJsonInput();
        $login = trim($input['login'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($login) || empty($password)) {
            App::json(['success' => false, 'message' => 'Login and password are required.'], 400);
        }

        $account = $this->userModel->findByLogin($login);

        if (!$account || !password_verify($password, $account['password_hash'])) {
            App::json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        if (!$account['is_active']) {
            App::json(['success' => false, 'message' => 'Account is deactivated.'], 403);
        }

        $_SESSION['account_id']   = $account['id'];
        $_SESSION['account_role'] = $account['role'];
        $_SESSION['account_login'] = $account['login'];
        $_SESSION['owner_type']   = $account['owner_type'];
        $_SESSION['owner_id']     = $account['owner_id'];

        if ($account['owner_type'] === 'student') {
            $_SESSION['stud_id'] = $account['student_id'];
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['api_token'] = $token;

        App::json([
            'success'   => true,
            'message'   => 'Login successful.',
            'account'   => $this->accountPayload($account),
            'token'     => $token,
        ]);
    }

    public function forgotPassword()
    {
        $input = $this->getJsonInput();
        $email = trim($input['email'] ?? '');

        if (empty($email)) {
            App::json(['success' => false, 'message' => 'Email is required.'], 400);
        }

        App::json([
            'success'   => true,
            'message'   => 'If that email exists, a reset link has been sent.',
        ]);
    }

    public function resetPassword()
    {
        $input = $this->getJsonInput();
        $token = $input['token'] ?? '';
        $password = $input['password'] ?? '';

        if (empty($token) || empty($password)) {
            App::json(['success' => false, 'message' => 'Token and password are required.'], 400);
        }

        App::json(['success' => true, 'message' => 'Password has been reset.']);
    }

    public function logout()
    {
        if (isset($_SESSION['api_token'])) {
            unset($_SESSION['api_token']);
        }
        session_destroy();
        App::json(['success' => true, 'message' => 'Logged out.']);
    }

    public function me()
    {
        if (!$this->requireAuth()) return;

        $account = $this->userModel->findById($_SESSION['account_id']);
        if (!$account) {
            App::json(['success' => false, 'message' => 'Account not found.'], 404);
        }

        App::json([
            'success'  => true,
            'account'  => $this->accountPayload($account),
        ]);
    }

    public function changePassword()
    {
        if (!$this->requireAuth()) return;

        $input = $this->getJsonInput();
        $current = $input['current_password'] ?? '';
        $new = $input['new_password'] ?? '';

        if (empty($current) || empty($new)) {
            App::json(['success' => false, 'message' => 'Current and new password are required.'], 400);
        }

        $account = $this->userModel->findById($_SESSION['account_id']);
        if (!$account || !password_verify($current, $account['password_hash'])) {
            App::json(['success' => false, 'message' => 'Current password is incorrect.'], 401);
        }

        $this->userModel->update($_SESSION['account_id'], ['password' => $new]);
        App::json(['success' => true, 'message' => 'Password changed.']);
    }

    private function accountPayload($account)
    {
        return [
            'id'         => $account['id'],
            'login'      => $account['login'],
            'email'      => $account['email'],
            'role'       => $account['role'],
            'owner_type' => $account['owner_type'],
            'owner_id'   => $account['owner_id'],
            'is_active'  => (bool) $account['is_active'],
            'student_id' => $account['student_id'] ?? null,
        ];
    }
}

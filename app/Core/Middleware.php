<?php

class Middleware
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function auth()
    {
        if (!isset($_SESSION['account_id'])) {
            App::redirect('/login');
            return false;
        }
        return true;
    }

    public function role($roles)
    {
        if (!$this->auth()) {
            return false;
        }

        if (is_string($roles)) {
            $roles = [$roles];
        }

        if (!in_array($_SESSION['account_role'], $roles)) {
            App::redirect('/unauthorized');
            return false;
        }
        return true;
    }

    public function student()
    {
        return $this->role('student');
    }

    public function faculty()
    {
        return $this->role(['faculty', 'chair', 'dean', 'admin']);
    }

    public function registrar()
    {
        return $this->role(['chair', 'dean', 'admin']);
    }

    public function cashier()
    {
        return $this->role(['cashier', 'admin']);
    }

    public function department()
    {
        return $this->role(['chair', 'dean', 'admin']);
    }

    public function admin()
    {
        return $this->role('admin');
    }

    public function verifyOwnership($studId = null)
    {
        if (!isset($_SESSION['account_id'])) {
            return false;
        }

        $config = App::getInstance()->getConfig();

        if ($_SESSION['account_role'] === 'student') {
            $userModel = new UserModel();
            $account = $userModel->findById($_SESSION['account_id']);
            if ($account && $account['owner_type'] === 'student') {
                if ($studId !== null && $account['student_id'] != $studId) {
                    return false;
                }
            }
        }

        return true;
    }
}

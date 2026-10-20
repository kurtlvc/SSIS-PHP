<?php

class AdminController extends BaseController
{
    private $userModel;
    private $enrolmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->enrolmentModel = new EnrolmentModel();
    }

    public function users()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $accounts = $this->userModel->getAll();

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('admin/users', [
            'accounts' => $accounts,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'User Management',
            'module' => 'admin',
        ]);
    }

    public function roles()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        App::view('admin/roles', [
            'pageTitle' => 'Roles & Permissions',
            'module' => 'admin',
        ]);
    }

    public function settings()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $settings = $this->db->fetchOne("SELECT * FROM settings WHERE singleton = 1");
        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();
        $personnel = $this->userModel->getPersonnelList();

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('admin/settings', [
            'settings' => $settings,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'personnel' => $personnel,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'System Settings',
            'module' => 'admin',
        ]);
    }

    public function createUser()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/users');
            return;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $ownerType = $_POST['owner_type'] ?? '';
        $ownerId = $_POST['owner_id'] ?? '';

        $validRoles = ['student', 'faculty', 'chair', 'dean', 'admin', 'cashier'];
        $validOwnerTypes = ['personnel', 'student'];

        if (!in_array($role, $validRoles) || !in_array($ownerType, $validOwnerTypes)) {
            App::flash('error', 'Invalid role or owner type.');
            App::redirect('/admin/users');
            return;
        }

        if (empty($login) || empty($password) || empty($ownerId)) {
            App::flash('error', 'All fields are required.');
            App::redirect('/admin/users');
            return;
        }

        try {
            $this->userModel->create([
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'login' => $login,
                'password' => $password,
                'role' => $role,
            ]);
            App::flash('success', 'User account created successfully.');
        } catch (Exception $e) {
            App::flash('error', 'Failed to create user: ' . $e->getMessage());
        }

        App::redirect('/admin/users');
    }

    public function updateUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/users');
            return;
        }

        $updates = [];
        if (isset($_POST['role']) && in_array($_POST['role'], ['student', 'faculty', 'chair', 'dean', 'admin'])) {
            $updates['role'] = $_POST['role'];
        }
        if (isset($_POST['is_active'])) {
            $updates['is_active'] = $_POST['is_active'];
        }
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $updates['password'] = $_POST['password'];
        }

        if (!empty($updates)) {
            $this->userModel->update($id, $updates);
            App::flash('success', 'User updated successfully.');
        }

        App::redirect('/admin/users');
    }

    public function updateSettings()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/system-settings');
            return;
        }

        $updates = [
            'system_title' => $_POST['system_title'] ?? '',
            'sem_id' => $_POST['sem_id'] ?? 1,
            'ay_id' => $_POST['ay_id'] ?? 1,
            'upload_max_mb' => $_POST['upload_max_mb'] ?? 10,
            'rate_limit_per_minute' => $_POST['rate_limit_per_minute'] ?? 60,
            'rate_limit_login' => $_POST['rate_limit_login'] ?? 10,
            'rate_limit_strict' => $_POST['rate_limit_strict'] ?? 0,
        ];

        $this->db->update('settings', $updates, 'singleton = 1');
        App::flash('success', 'System settings updated successfully.');
        App::redirect('/admin/system-settings');
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $counts = $this->db->fetchOne("SELECT
            (SELECT COUNT(*) FROM students) AS students,
            (SELECT COUNT(*) FROM personnel WHERE is_active = 1) AS personnel,
            (SELECT COUNT(*) FROM accounts WHERE is_active = 1) AS accounts,
            (SELECT COUNT(*) FROM enrolments WHERE is_approved = 1) AS approved_enrolments,
            (SELECT COUNT(*) FROM document_requests WHERE status = 'Pending') AS pending_requests,
            (SELECT COALESCE(SUM(amount_paid),0) FROM payments WHERE DATE(payment_date) = CURDATE()) AS today_collection");

        App::view('admin/dashboard', [
            'counts' => $counts,
            'pageTitle' => 'Admin Dashboard',
            'module' => 'admin',
        ]);
    }

    public function createUserForm()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        App::view('admin/user_form', [
            'user' => null,
            'personnel' => $this->userModel->getPersonnelList(),
            'students' => $this->userModel->getStudentsList(),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Create User',
            'module' => 'admin',
        ]);
    }

    public function viewUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $user = $this->userModel->findById($id);
        if (!$user) {
            App::flash('error', 'User not found.');
            App::redirect('/admin/users');
            return;
        }

        App::view('admin/user_view', [
            'user' => $user,
            'pageTitle' => 'View User',
            'module' => 'admin',
        ]);
    }

    public function editUserForm($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $user = $this->userModel->findById($id);
        if (!$user) {
            App::flash('error', 'User not found.');
            App::redirect('/admin/users');
            return;
        }

        App::view('admin/user_form', [
            'user' => $user,
            'personnel' => $this->userModel->getPersonnelList(),
            'students' => $this->userModel->getStudentsList(),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit User',
            'module' => 'admin',
        ]);
    }

    public function toggleUserStatus($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $isActive = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 1;
        $this->db->update('accounts', ['is_active' => $isActive], 'id = :id', ['id' => $id]);
        App::flash('success', 'User status updated.');
        App::redirect('/admin/users');
    }

    public function auditLogs()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $actor = $_GET['actor_id'] ?? null;
        $params = [];
        $where = "WHERE 1=1";
        if ($actor) {
            $where .= " AND actor_id = :actor_id";
            $params['actor_id'] = $actor;
        }

        $sql = "SELECT id, actor_id, action, entity, entity_id, meta, created_at
                FROM audit_log
                {$where}
                ORDER BY id DESC LIMIT 200";

        $logs = $this->db->fetchAll($sql, $params);

        App::view('admin/audit_logs', [
            'logs' => $logs,
            'pageTitle' => 'Audit Logs',
            'module' => 'admin',
        ]);
    }
}

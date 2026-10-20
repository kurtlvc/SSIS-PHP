<?php

class ApiAdminController extends BaseController
{
    private $userModel;
    private $enrolmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel      = new UserModel();
        $this->enrolmentModel = new EnrolmentModel();
        $this->db             = Database::getInstance();
    }

    private function getJsonInput()
    {
        $data = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($data) ? $data : [];
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
            (SELECT COUNT(*) FROM document_requests WHERE status = 'Pending') AS pending_requests");

        App::json(['success' => true, 'counts' => $counts]);
    }

    public function users()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $role = $_GET['role'] ?? null;
        App::json(['success' => true, 'users' => $this->userModel->getAll($role)]);
    }

    public function viewUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $user = $this->userModel->findById($id);
        if (!$user) {
            App::json(['success' => false, 'message' => 'User not found.'], 404);
        }

        App::json(['success' => true, 'user' => $user]);
    }

    public function createUser()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $input = $this->getJsonInput();
        $validRoles = ['student', 'faculty', 'chair', 'dean', 'admin', 'cashier'];

        if (!in_array($input['role'] ?? null, $validRoles)) {
            App::json(['success' => false, 'message' => 'Invalid role.'], 400);
        }
        if (empty($input['login']) || empty($input['password']) || empty($input['owner_id'])) {
            App::json(['success' => false, 'message' => 'login, password and owner_id are required.'], 400);
        }

        try {
            $id = $this->userModel->create([
                'owner_type' => $input['owner_type'] ?? 'personnel',
                'owner_id'   => $input['owner_id'],
                'login'      => $input['login'],
                'email'      => $input['email'] ?? null,
                'password'   => $input['password'],
                'role'       => $input['role'],
            ]);
            App::json(['success' => true, 'message' => 'User created.', 'id' => $id], 201);
        } catch (Exception $e) {
            App::json(['success' => false, 'message' => 'Failed: ' . $e->getMessage()], 400);
        }
    }

    public function updateUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $input = $this->getJsonInput();
        $updates = [];
        if (isset($input['role'])) {
            $updates['role'] = $input['role'];
        }
        if (isset($input['is_active'])) {
            $updates['is_active'] = (int) $input['is_active'];
        }
        if (!empty($input['password'])) {
            $updates['password'] = $input['password'];
        }

        if (empty($updates)) {
            App::json(['success' => false, 'message' => 'Nothing to update.'], 400);
        }

        $this->userModel->update($id, $updates);
        App::json(['success' => true, 'message' => 'User updated.']);
    }

    public function toggleUserStatus($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $input      = json_decode(file_get_contents('php://input') ?: '', true) ?: [];
        $isActive   = isset($input['is_active']) ? (int) $input['is_active'] : 1;

        $this->db->update('accounts', ['is_active' => $isActive], 'id = :id', ['id' => $id]);
        App::json(['success' => true, 'message' => 'User status toggled.', 'is_active' => (bool) $isActive]);
    }

    public function settings()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $settings = $this->db->fetchOne("SELECT * FROM settings WHERE singleton = 1");
        App::json([
            'success'  => true,
            'settings' => $settings,
            'academic_years' => $this->enrolmentModel->getAllAcademicYears(),
            'semesters'      => $this->enrolmentModel->getAllSemesters(),
            'personnel'      => $this->userModel->getPersonnelList(),
        ]);
    }

    public function updateSettings()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $input = $this->getJsonInput();
        $updates = [
            'system_title'           => $input['system_title'] ?? '',
            'ay_id'                  => (int) ($input['ay_id'] ?? 1),
            'sem_id'                 => (int) ($input['sem_id'] ?? 1),
            'upload_max_mb'          => (int) ($input['upload_max_mb'] ?? 10),
            'rate_limit_per_minute'  => (int) ($input['rate_limit_per_minute'] ?? 60),
            'rate_limit_login'       => (int) ($input['rate_limit_login'] ?? 10),
            'rate_limit_strict'      => (int) ($input['rate_limit_strict'] ?? 0),
        ];

        $this->db->update('settings', $updates, 'singleton = 1');
        App::json(['success' => true, 'message' => 'Settings updated.']);
    }

    public function auditLogs()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $limit = (int) ($_GET['limit'] ?? 100);
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
                ORDER BY id DESC LIMIT :limit";

        $stmt = $this->db->getConnection()->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue(':' . $k, $v); }
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        App::json(['success' => true, 'logs' => $logs]);
    }
}

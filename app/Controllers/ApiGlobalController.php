<?php

class ApiGlobalController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->db = Database::getInstance();
    }

    public function announcements()
    {
        $onlyActive = isset($_GET['active']) ? filter_var($_GET['active'], FILTER_VALIDATE_BOOLEAN) : true;

        $where = 'WHERE is_active = 1';
        $params = [];
        if ($onlyActive === false) {
            $where = 'WHERE 1=1';
        }

        $sql = "SELECT ann_id, ann_title, ann_body, p_id, ann_status, ann_index, is_active
                FROM announcements
                {$where}
                ORDER BY ann_index ASC, ann_id DESC";

        App::json($this->db->fetchAll($sql, $params));
    }

    public function storeAnnouncement()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $input = json_decode(file_get_contents('php://input') ?: '', true) ?: $_POST;

        $data = [
            'ann_title'  => $input['ann_title'] ?? null,
            'ann_body'   => $input['ann_body'] ?? '',
            'p_id'       => $_SESSION['owner_id'] ?? $_SESSION['owner_id'] ?? 0,
            'ann_status' => (int) ($input['ann_status'] ?? 1),
            'ann_index'  => (int) ($input['ann_index'] ?? 0),
            'is_active'  => (int) ($input['is_active'] ?? 1),
        ];

        $this->db->insert('announcements', $data);
        App::json(['success' => true, 'message' => 'Announcement created.', 'ann_id' => $this->db->getConnection()->lastInsertId()]);
    }

    public function notifications()
    {
        if (!$this->requireAuth()) return;

        $sql = "SELECT id, action, entity, entity_id, meta, created_at
                FROM audit_log
                WHERE (actor_id = :actor OR :actor = 0)
                ORDER BY id DESC
                LIMIT 50";

        App::json($this->db->fetchAll($sql, ['actor' => $_SESSION['account_id'] ?? 0]));
    }

    public function markNotificationRead()
    {
        if (!$this->requireAuth()) return;

        $id = func_get_arg(0);

        $id = (int) ($id ?: (json_decode(file_get_contents('php://input') ?: '', true)['id'] ?? 0));
        if ($id <= 0) {
            App::json(['success' => false, 'message' => 'Invalid id.'], 400);
        }

        App::json(['success' => true, 'message' => 'Marked as read.', 'id' => $id]);
    }

    public function academicTerms()
    {
        $sql = "SELECT ay_id, ay_name FROM academic_years WHERE is_active = 1 ORDER BY ay_id";
        $academicYears = $this->db->fetchAll($sql);

        $sql = "SELECT sem_id, semester_name FROM semesters WHERE is_active = 1 ORDER BY sem_id";
        $semesters = $this->db->fetchAll($sql);

        $active = $this->db->fetchOne("SELECT ay_id, ay_name, sem_id, sem.semester_name
                                       FROM academic_years ay
                                       JOIN semesters sem ON sem.is_active = 1
                                       WHERE ay.is_active = 1 LIMIT 1");

        App::json([
            'active'        => $active,
            'academic_years' => $academicYears,
            'semesters'     => $semesters,
        ]);
    }
}

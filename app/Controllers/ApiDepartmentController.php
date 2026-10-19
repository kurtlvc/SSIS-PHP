<?php

class ApiDepartmentController extends BaseController
{
    private $clearanceModel;
    private $enrolmentModel;
    private $studentModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->clearanceModel   = new ClearanceModel();
        $this->enrolmentModel   = new EnrolmentModel();
        $this->studentModel     = new StudentModel();
        $this->db               = Database::getInstance();
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $counts = $this->db->fetchOne("SELECT
            (SELECT COUNT(*) FROM clearance_records WHERE status = 'Pending') AS pending,
            (SELECT COUNT(*) FROM clearance_records WHERE status = 'Cleared') AS cleared,
            (SELECT COUNT(*) FROM clearance_records WHERE status = 'On-Hold') AS on_hold");

        App::json(['success' => true, 'counts' => $counts]);
    }

    public function clearanceList()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $records = $this->clearanceModel->getClearanceQueue($_GET['sem_id'] ?? null);
        App::json([
            'success'   => true,
            'records'   => $records,
            'departments' => $this->clearanceModel->getAllDepartments(),
            'semesters' => $this->enrolmentModel->getAllSemesters(),
        ]);
    }

    public function studentClearance($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $student = $this->studentModel->findById($studId);
        $semId   = $_GET['sem_id'] ?? null;
        if (!$semId) {
            $active = $this->enrolmentModel->getActiveAcademicYearSemester();
            $semId = $active['sem_id'] ?? null;
        }

        $clearance = [];
        if ($student && $semId) {
            $clearance = $this->clearanceModel->getDepartmentsByStudent($studId, $semId);
        }

        App::json([
            'success'      => true,
            'student'      => $student,
            'clearance'    => $clearance,
            'semesters'    => $this->enrolmentModel->getAllSemesters(),
            'active_sem'   => $semId,
            'departments'  => $this->clearanceModel->getAllDepartments(),
        ]);
    }

    public function updateClearanceStatus($clearanceId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $input  = json_decode(file_get_contents('php://input') ?: '', true) ?: [];
        $status = $input['status'] ?? null;
        $valid  = ['Pending', 'Cleared', 'On-Hold'];

        if (!in_array($status, $valid)) {
            App::json(['success' => false, 'message' => 'Invalid status.'], 400);
        }

        $this->clearanceModel->updateStatus($clearanceId, $status, $_SESSION['owner_id'] ?? 0);
        App::json(['success' => true, 'message' => 'Clearance status updated.']);
    }
}

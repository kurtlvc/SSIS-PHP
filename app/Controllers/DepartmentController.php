<?php

class DepartmentController extends BaseController
{
    private $clearanceModel;
    private $enrolmentModel;

    public function __construct()
    {
        parent::__construct();
        $this->clearanceModel = new ClearanceModel();
        $this->enrolmentModel = new EnrolmentModel();
    }

    public function clearanceList()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $semId = $_GET['sem_id'] ?? null;
        $departments = $this->clearanceModel->getAllDepartments();
        $records = $this->clearanceModel->getClearanceQueue($semId);
        $semesters = $this->enrolmentModel->getAllSemesters();

        App::view('department/clearance_list', [
            'records' => $records,
            'departments' => $departments,
            'semesters' => $semesters,
            'pageTitle' => 'Clearance Queue',
            'module' => 'department',
        ]);
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $counts = $this->db->fetchOne("SELECT
            (SELECT COUNT(*) FROM clearance_records WHERE status = 'Pending') AS pending,
            (SELECT COUNT(*) FROM clearance_records WHERE status = 'Cleared') AS cleared,
            (SELECT COUNT(*) FROM clearance_records WHERE status = 'On-Hold') AS on_hold,
            (SELECT COUNT(*) FROM clearance_departments WHERE is_active = 1) AS departments");

        App::view('department/dashboard', [
            'counts' => $counts,
            'pageTitle' => 'Department Dashboard',
            'module' => 'department',
        ]);
    }

    public function studentClearance($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $studentModel = new StudentModel();
        $student = $studentModel->findById($studId);
        $semId = $_GET['sem_id'] ?? null;
        $academicTerms = $this->enrolmentModel->getActiveAcademicYearSemester();
        $semesters = $this->enrolmentModel->getAllSemesters();

        if (!$semId && $academicTerms) {
            $semId = $academicTerms['sem_id'];
        }

        $clearanceData = [];
        if ($student && $semId) {
            $clearanceData = $this->clearanceModel->getDepartmentsByStudent($studId, $semId);
        }

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('department/student_clearance', [
            'student' => $student,
            'clearanceData' => $clearanceData,
            'semesters' => $semesters,
            'activeSemester' => $semId,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Student Clearance',
            'module' => 'department',
        ]);
    }

    public function updateClearance($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/department/clearance/' . $studId);
            return;
        }

        $recordId = $_POST['record_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $validStatuses = ['Pending', 'Cleared', 'On-Hold'];

        if ($recordId && in_array($status, $validStatuses)) {
            $this->clearanceModel->updateStatus($recordId, $status, $_SESSION['owner_id'] ?? 0);
            App::flash('success', 'Clearance status updated.');
        } else {
            App::flash('error', 'Invalid clearance data.');
        }

        App::redirect('/department/clearance/' . $studId);
    }
}

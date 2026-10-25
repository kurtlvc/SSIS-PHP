<?php

class DepartmentController extends BaseController
{
    private $clearanceModel;
    private $enrolmentModel;
    private $emailService;
    private $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->clearanceModel = new ClearanceModel();
        $this->enrolmentModel = new EnrolmentModel();
        $this->emailService = new EmailService();
        $this->notificationService = new NotificationService();
    }

    public function clearanceList()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $semId = $_GET['sem_id'] ?? null;
        $deptId = $_GET['dept_id'] ?? null;
        if (!$semId) {
            $active = $this->enrolmentModel->getActiveAcademicYearSemester();
            $semId = $active['sem_id'] ?? null;
        }
        if ($semId) {
            $this->clearanceModel->ensureRecordsForTerm((int) $semId);
        }
        $departments = $this->clearanceModel->getAllDepartments();
        $records = $this->clearanceModel->getClearanceQueue($semId, $deptId);
        $semesters = $this->enrolmentModel->getAllSemesters();

        // Get department statistics
        $deptStats = $this->clearanceModel->getDepartmentStats($semId);

        App::view('department/clearance_list', [
            'records' => $records,
            'departments' => $departments,
            'semesters' => $semesters,
            'deptStats' => $deptStats,
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
            $this->clearanceModel->ensureRecordsForStudent((int) $studId, (int) $semId);
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
        $departmentId = $_POST['department_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $semId = $_POST['sem_id'] ?? null;
        $validStatuses = ['Pending', 'Cleared', 'On-Hold'];

        if (!$semId) {
            $active = $this->enrolmentModel->getActiveAcademicYearSemester();
            $semId = $active['sem_id'] ?? null;
        }

        if (in_array($status, $validStatuses) && $semId && (!$recordId || !$departmentId)) {
            $this->clearanceModel->ensureRecordsForStudent((int) $studId, (int) $semId);
            if (!$recordId && $departmentId) {
                $row = $this->db->fetchOne(
                    'SELECT clearance_id FROM clearance_records
                     WHERE stud_id = :stud_id AND sem_id = :sem_id AND department_id = :dept_id
                     ORDER BY clearance_id DESC LIMIT 1',
                    ['stud_id' => $studId, 'sem_id' => $semId, 'dept_id' => $departmentId]
                );
                $recordId = $row['clearance_id'] ?? null;
            }
        }

        if ($recordId && in_array($status, $validStatuses)) {
            $record = $this->db->fetchOne(
                "SELECT cr.*, cd.dept_name, s.emailaddress, s.fname, s.lname, s.stud_id
                 FROM clearance_records cr
                 JOIN clearance_departments cd ON cr.department_id = cd.dept_id
                 JOIN students s ON cr.stud_id = s.stud_id
                 WHERE cr.clearance_id = :id AND cr.stud_id = :stud_id",
                ['id' => $recordId, 'stud_id' => $studId]
            );

            if (!$record) {
                App::flash('error', 'Clearance record not found.');
                App::redirect('/department/clearance/' . $studId);
                return;
            }

            $this->clearanceModel->updateStatus($recordId, $status, $_SESSION['owner_id'] ?? null);
            App::flash('success', 'Clearance status updated.');

            $this->emailService->sendClearanceNotification([
                'to_email' => $record['emailaddress'],
                'to_name' => $record['fname'] . ' ' . $record['lname'],
                'student_id' => $record['stud_id'],
                'department' => $record['dept_name'],
                'status' => $status,
                'clearance_id' => $recordId,
            ]);
            $this->notificationService->clearanceNotification((int) $record['stud_id'], [
                'department' => $record['dept_name'],
                'status' => $status,
                'clearance_id' => $recordId,
            ]);
        } else {
            App::flash('error', 'Invalid clearance data.');
        }

        App::redirect('/department/clearance/' . $studId);
    }

    public function studentClearanceView()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->department()) return;

        $semId = $_GET['sem_id'] ?? null;
        $search = $_GET['search'] ?? '';
        $departments = $this->clearanceModel->getAllDepartments();
        $semesters = $this->enrolmentModel->getAllSemesters();

        // Get students with their clearance status per department
        $where = "";
        $params = [];
        if ($semId) {
            $where = "WHERE e.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }
        if ($search) {
            $where .= ($where ? " AND " : "WHERE ") . "(s.lname LIKE :search OR s.fname LIKE :search OR s.ref_no LIKE :search)";
            $params['search'] = "%$search%";
        }

        $sql = "SELECT s.stud_id, s.ref_no, s.fname, s.lname, s.mname, p.program_code,
                       GROUP_CONCAT(CONCAT(cr.dept_id, ':', cr.status) SEPARATOR '|') as clearance_data,
                       CASE 
                           WHEN COUNT(cr.record_id) = 0 THEN 'Pending'
                           WHEN SUM(cr.status = 'Cleared') = COUNT(cr.record_id) THEN 'Cleared'
                           WHEN SUM(cr.status = 'On-Hold') > 0 THEN 'On-Hold'
                           ELSE 'Pending'
                       END as overall_status
                FROM students s
                JOIN enrolments e ON s.stud_id = e.stud_id
                LEFT JOIN clearance_records cr ON cr.stud_id = s.stud_id " . ($semId ? "AND cr.sem_id = :sem_id" : "") . "
                JOIN programs p ON s.program_id = p.program_id
                $where
                GROUP BY s.stud_id
                ORDER BY s.lname, s.fname";

        $students = $this->db->fetchAll($sql, $params);

        // Parse clearance data
        foreach ($students as &$student) {
            $clearance = [];
            if (!empty($student['clearance_data'])) {
                foreach (explode('|', $student['clearance_data']) as $pair) {
                    list($deptId, $status) = explode(':', $pair);
                    $clearance[$deptId] = $status;
                }
            }
            $student['clearance'] = $clearance;
        }

        App::view('department/clearance_student_view', [
            'students' => $students,
            'departments' => $departments,
            'semesters' => $semesters,
            'activeSemester' => $semId,
            'search' => $search,
            'pageTitle' => 'Student Clearance Status',
            'module' => 'department',
        ]);
    }
}

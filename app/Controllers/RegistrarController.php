<?php

class RegistrarController extends BaseController
{
    private $enrolmentModel;
    private $userModel;
    private $documentModel;

    public function __construct()
    {
        parent::__construct();
        $this->enrolmentModel = new EnrolmentModel();
        $this->userModel = new UserModel();
        $this->documentModel = new DocumentRequestModel();
    }

    public function enrollments()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $ayId = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;
        $academicTerms = $this->enrolmentModel->getActiveAcademicYearSemester();
        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();
        $enrolments = $this->enrolmentModel->getAll($ayId, $semId);

        App::view('registrar/enrollments', [
            'enrolments' => $enrolments,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'academicTerms' => $academicTerms,
            'pageTitle' => 'Enrollment Management',
            'module' => 'registrar',
        ]);
    }

    public function studentRecord($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $studentModel = new StudentModel();
        $student = $studentModel->findById($studId);
        $enrolments = $studentModel->getEnrolments($studId);
        $grades = $studentModel->getGrades($studId);
        $gpa = $studentModel->calculateGPA($grades);

        if (!$student) {
            http_response_code(404);
            App::flash('error', 'Student not found.');
            App::redirect('/registrar/enrollments');
            return;
        }

        App::view('registrar/student_record', [
            'student' => $student,
            'enrolments' => $enrolments,
            'grades' => $grades,
            'gpa' => $gpa,
            'pageTitle' => 'Student Master Record',
            'module' => 'registrar',
        ]);
    }

    public function gradesEncode()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $ayId = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;
        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();
        $gradesToEncode = $this->enrolmentModel->getGradesToEncode($ayId, $semId);

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('registrar/grades_encode', [
            'gradesToEncode' => $gradesToEncode,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Grades Encoding',
            'module' => 'registrar',
        ]);
    }

    public function storeGrade()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/registrar/grades/encode');
            return;
        }

        $enrolmentId = $_POST['enrolment_id'] ?? null;
        $courseId = $_POST['course_id'] ?? null;
        $finalGrade = $_POST['final_grade'] ?? null;
        $remarks = $_POST['remarks'] ?? '';

        if ($enrolmentId && $courseId && $finalGrade !== null && $finalGrade !== '') {
            $this->db->insert('student_grades', [
                'enrolment_id' => $enrolmentId,
                'course_id' => $courseId,
                'p_id' => $_SESSION['owner_id'] ?? 0,
                'final_grade' => $finalGrade,
                'remarks' => $remarks,
            ]);
            App::flash('success', 'Grade encoded successfully!');
        } else {
            App::flash('error', 'All required fields must be filled.');
        }

        App::redirect('/registrar/grades/encode');
    }

    public function documentQueue()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $requests = $this->documentModel->getQueue();

        App::view('registrar/document_queue', [
            'requests' => $requests,
            'pageTitle' => 'Document Request Queue',
            'module' => 'registrar',
            'csrf_token' => App::csrf_token(),
        ]);
    }

    public function processDocument($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/registrar/documents/queue');
            return;
        }

        $status = $_POST['status'] ?? 'Processing';
        $validStatuses = ['Pending', 'Processing', 'Ready for Pickup', 'Released'];

        if (!in_array($status, $validStatuses)) {
            App::flash('error', 'Invalid status value.');
            App::redirect('/registrar/documents/queue');
            return;
        }

        $this->documentModel->updateStatus($id, $status, $_SESSION['owner_id'] ?? 0);
        App::flash('success', 'Document request status updated.');
        App::redirect('/registrar/documents/queue');
    }

    private function getJsonInput()
    {
        $data = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($data) ? $data : [];
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $counts = $this->db->fetchOne("SELECT
            (SELECT COUNT(*) FROM students) AS students,
            (SELECT COUNT(*) FROM enrolments WHERE is_approved = 1) AS approved_enrolments,
            (SELECT COUNT(*) FROM enrolments WHERE isassessed = 0) AS pending_assessment,
            (SELECT COUNT(*) FROM document_requests WHERE status IN ('Pending','Processing')) AS doc_queue");

        App::view('registrar/dashboard', [
            'counts' => $counts,
            'pageTitle' => 'Registrar Dashboard',
            'module' => 'registrar',
        ]);
    }

    public function students()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $students = $this->userModel->getStudentsList();

        App::view('registrar/students', [
            'students' => $students,
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Students',
            'module' => 'registrar',
        ]);
    }

    public function createStudentForm()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        App::view('registrar/student_form', [
            'student' => null,
            'academicYears' => $academicYears,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Add New Student',
            'module' => 'registrar',
        ]);
    }

    public function storeStudent()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/registrar/students/new');
            return;
        }

        $required = ['ref_no', 'lname', 'fname', 'curr_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/registrar/students/new');
                return;
            }
        }

        $data = [
            'ref_no'      => $_POST['ref_no'],
            'lname'       => $_POST['lname'],
            'fname'       => $_POST['fname'],
            'mname'       => $_POST['mname'] ?? null,
            'ext'         => $_POST['ext'] ?? null,
            'emailaddress' => $_POST['emailaddress'] ?? null,
            'contactno'   => $_POST['contactno'] ?? null,
            'curr_id'     => $_POST['curr_id'],
            'LRN'         => $_POST['LRN'] ?? null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('students', $data);
        App::flash('success', 'Student created successfully.');
        App::redirect('/registrar/students');
    }

    public function editStudentForm($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $studentModel = new StudentModel();
        $student = $studentModel->findById($studId);
        if (!$student) {
            App::flash('error', 'Student not found.');
            App::redirect('/registrar/students');
            return;
        }

        App::view('registrar/student_form', [
            'student' => $student,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit Student',
            'module' => 'registrar',
        ]);
    }

    public function updateStudent($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/registrar/students/' . $studId . '/edit');
            return;
        }

        $updates = [];
        foreach (['lname', 'fname', 'mname', 'ext', 'emailaddress', 'contactno', 'LRN'] as $field) {
            if (array_key_exists($field, $_POST)) {
                $updates[$field] = $_POST[$field];
            }
        }
        $updates['updated_at'] = date('Y-m-d H:i:s');

        if (!empty($updates)) {
            $this->db->update('students', $updates, 'stud_id = :id', ['id' => $studId]);
            App::flash('success', 'Student updated successfully.');
        }

        App::redirect('/registrar/students/' . $studId);
    }

    public function assessEnrollment()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $enrolmentId = $_POST['enrolment_id'] ?? null;

        if (empty($enrolmentId)) {
            App::flash('error', 'Enrolment id is required.');
            App::redirect('/registrar/enrollments');
            return;
        }

        $this->db->update('enrolments', ['isassessed' => 1, 'is_approved' => 1], 'enrolment_id = :id', ['id' => $enrolmentId]);
        App::flash('success', 'Enrolment assessed and approved.');
        App::redirect('/registrar/enrollments');
    }

    public function updateEnrollmentStatus($enrolmentId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $status = $_POST['enrol_status'] ?? null;
        $valid  = ['Pending', 'Approved', 'Rejected', 'Dropped'];

        if (!in_array($status, $valid)) {
            App::flash('error', 'Invalid enrollment status.');
            App::redirect('/registrar/enrollments');
            return;
        }

        $this->db->update('enrolments', ['enrol_status' => $status], 'enrolment_id = :id', ['id' => $enrolmentId]);
        App::flash('success', 'Enrollment status updated.');
        App::redirect('/registrar/enrollments');
    }

    public function gradeEncodeStudents($schedId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $sql = "SELECT e.enrolment_id, e.stud_id, s.ref_no, s.lname, s.fname,
                       c.code, c.description, c.units,
                       ay.ay_name, sem.semester_name, g.final_grade, g.remarks
                FROM schedules sch
                JOIN courses c ON c.course_id = sch.course_id
                JOIN enrolments e ON e.program_id = c.program_id AND e.ay_id = sch.ay_id AND e.sem_id = sch.sem_id
                JOIN students s ON s.stud_id = e.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                LEFT JOIN student_grades g ON g.enrolment_id = e.enrolment_id AND g.course_id = c.course_id
                WHERE sch.sched_id = :sched_id AND e.is_approved = 1 AND e.isassessed = 1
                ORDER BY s.lname, s.fname, c.code";

        $gradesToEncode = $this->db->fetchAll($sql, ['sched_id' => $schedId]);

        App::view('registrar/grades_encode', [
            'gradesToEncode' => $gradesToEncode,
            'academicYears'  => $this->enrolmentModel->getAllAcademicYears(),
            'semesters'      => $this->enrolmentModel->getAllSemesters(),
            'csrf_token'     => App::csrf_token(),
            'pageTitle'      => 'Grade Encoding for Schedule',
            'module'         => 'registrar',
        ]);
    }

    public function updateDocumentStatus($requestId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $status = $_POST['status'] ?? null;
        $validStatuses = ['Pending', 'Processing', 'Ready for Pickup', 'Released'];

        if (!in_array($status, $validStatuses)) {
            App::flash('error', 'Invalid status value.');
            App::redirect('/registrar/documents/queue');
            return;
        }

        $this->documentModel->updateStatus($requestId, $status, $_SESSION['owner_id'] ?? 0);
        App::flash('success', 'Document request status updated.');
        App::redirect('/registrar/documents/queue');
    }
}

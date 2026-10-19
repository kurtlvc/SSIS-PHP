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
}

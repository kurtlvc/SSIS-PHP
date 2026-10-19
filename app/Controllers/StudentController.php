<?php

class StudentController extends BaseController
{
    private $studentModel;

    public function __construct()
    {
        parent::__construct();
        $this->studentModel = new StudentModel();
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolments = $this->studentModel->getEnrolments($studId);
        $grades = $this->studentModel->getGrades($studId);

        $gpa = $this->studentModel->calculateGPA($grades);
        $recentGrades = array_slice($grades, 0, 5);

        App::view('student/dashboard', [
            'student' => $student,
            'enrolments' => $enrolments,
            'grades' => $recentGrades,
            'gpa' => $gpa,
            'pageTitle' => 'Dashboard',
            'module' => 'student',
        ]);
    }

    public function grades()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $grades = $this->studentModel->getGrades($studId);
        $gpa = $this->studentModel->calculateGPA($grades);

        App::view('student/grades', [
            'student' => $student,
            'grades' => $grades,
            'gpa' => $gpa,
            'pageTitle' => 'Grades & Academic History',
            'module' => 'student',
        ]);
    }

    public function schedule()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolments = $this->studentModel->getEnrolments($studId);
        $activeEnrolment = !empty($enrolments) ? $enrolments[0] : null;

        $schedule = [];
        if ($activeEnrolment) {
            $schedule = $this->studentModel->getSchedule($studId, $activeEnrolment['ay_id'], $activeEnrolment['sem_id']);
        }

        App::view('student/schedule', [
            'student' => $student,
            'schedule' => $schedule,
            'enrolments' => $enrolments,
            'activeEnrolment' => $activeEnrolment,
            'pageTitle' => 'My Schedule',
            'module' => 'student',
        ]);
    }

    public function documentRequest()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $requests = $this->studentModel->getDocumentRequests($studId);
        $error = App::flash('error');
        $success = App::flash('success');

        App::view('student/document_request', [
            'student' => $student,
            'requests' => $requests,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Document Request',
            'module' => 'student',
        ]);
    }

    public function storeDocumentRequest()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/student/documents/request');
            return;
        }

        $docType = $_POST['document_type'] ?? '';
        $validTypes = ['TOR', 'COR', 'Good Moral', 'Certification'];

        if (!in_array($docType, $validTypes)) {
            App::flash('error', 'Invalid document type selected.');
            App::redirect('/student/documents/request');
            return;
        }

        $this->studentModel->createDocumentRequest($this->getStudentId(), $docType);
        App::flash('success', 'Document request submitted successfully!');
        App::redirect('/student/documents/request');
    }
}

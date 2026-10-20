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

        $dateInfo = [
            'weekday' => date('l'),
            'day' => date('j'),
            'month' => date('F'),
            'year' => date('Y'),
            'time' => date('g:i A'),
        ];

        $weatherInfo = [
            'icon' => 'bi-sun',
            'temperature' => '24°',
            'condition' => 'Partly Cloudy',
            'humidity' => '65%',
            'wind' => '8 km/h',
            'forecast' => [
                ['day' => 'Mon', 'icon' => 'bi-cloud-sun', 'high' => '26°', 'low' => '18°'],
                ['day' => 'Tue', 'icon' => 'bi-cloud', 'high' => '23°', 'low' => '17°'],
                ['day' => 'Wed', 'icon' => 'bi-sun', 'high' => '25°', 'low' => '19°'],
                ['day' => 'Thu', 'icon' => 'bi-cloud-drizzle', 'high' => '22°', 'low' => '16°'],
            ],
        ];

        App::view('student/dashboard', [
            'student' => $student,
            'enrolments' => $enrolments,
            'grades' => $recentGrades,
            'gpa' => $gpa,
            'dateInfo' => $dateInfo,
            'weatherInfo' => $weatherInfo,
            'pageTitle' => 'Dashboard',
            'module' => 'student',
        ], 'cuyosis');
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

    public function profile()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $student = $this->studentModel->findById($this->getStudentId());

        App::view('student/profile', [
            'student' => $student,
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'pageTitle' => 'My Profile',
            'module' => 'student',
        ]);
    }

    public function clearance()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $clearanceModel = new ClearanceModel();
        $enrolmentModel = new EnrolmentModel();
        $active = $enrolmentModel->getActiveAcademicYearSemester();
        $semId = $active['sem_id'] ?? null;
        $clearance = $clearanceModel->getDepartmentsByStudent($studId, $semId);

        App::view('student/clearance', [
            'clearance' => $clearance,
            'semesters' => $enrolmentModel->getAllSemesters(),
            'activeSemester' => $semId,
            'pageTitle' => 'My Clearance',
            'module' => 'student',
        ]);
    }

    public function documentHistory()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $requests = $this->studentModel->getDocumentRequests($this->getStudentId());

        App::view('student/document_history', [
            'requests' => $requests,
            'pageTitle' => 'Document Request History',
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

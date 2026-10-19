<?php

class ApiStudentController extends BaseController
{
    private $studentModel;
    private $clearanceModel;

    public function __construct()
    {
        parent::__construct();
        $this->studentModel = new StudentModel();
        $this->clearanceModel = new ClearanceModel();
    }

    private function getJsonInput()
    {
        $data = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($data) ? $data : [];
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

        App::json([
            'success'    => true,
            'student'    => $student,
            'enrolments' => $enrolments,
            'grades'     => array_slice($grades, 0, 5),
            'gpa'        => $gpa,
        ]);
    }

    public function profile()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $student = $this->studentModel->findById($this->getStudentId());
        if (!$student) {
            App::json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        App::json(['success' => true, 'student' => $student]);
    }

    public function grades()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $grades = $this->studentModel->getGrades($this->getStudentId());
        $gpa = $this->studentModel->calculateGPA($grades);

        App::json(['success' => true, 'grades' => $grades, 'gpa' => $gpa]);
    }

    public function schedule()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $enrolments = $this->studentModel->getEnrolments($studId);
        $activeEnrolment = !empty($enrolments) ? $enrolments[0] : null;

        $schedule = [];
        if ($activeEnrolment) {
            $schedule = $this->studentModel->getSchedule($studId, $activeEnrolment['ay_id'], $activeEnrolment['sem_id']);
        }

        App::json(['success' => true, 'schedule' => $schedule, 'enrolments' => $enrolments]);
    }

    public function clearance()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $semId = $_GET['sem_id'] ?? null;
        $clearance = $this->clearanceModel->getStudentClearance($studId, $semId);

        $summary = [
            'cleared' => 0,
            'on_hold' => 0,
            'pending' => 0,
        ];
        foreach ($clearance as $row) {
            $status = $row['status'] ?? '';
            if ($status === 'Cleared') {
                $summary['cleared']++;
            } elseif ($status === 'On-Hold') {
                $summary['on_hold']++;
            } else {
                $summary['pending']++;
            }
        }

        App::json(['success' => true, 'clearance' => $clearance, 'summary' => $summary]);
    }

    public function storeDocumentRequest()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $input = $this->getJsonInput();
        $docType = $input['document_type'] ?? '';
        $validTypes = ['TOR', 'COR', 'Good Moral', 'Certification'];

        if (!in_array($docType, $validTypes)) {
            App::json(['success' => false, 'message' => 'Invalid document type.'], 400);
        }

        $requestId = $this->studentModel->createDocumentRequest($this->getStudentId(), $docType);
        App::json(['success' => true, 'message' => 'Document request submitted.', 'request_id' => $requestId], 201);
    }

    public function documentHistory()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $requests = $this->studentModel->getDocumentRequests($this->getStudentId());
        App::json(['success' => true, 'requests' => $requests]);
    }
}

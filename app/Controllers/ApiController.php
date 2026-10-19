<?php

class ApiController extends BaseController
{
    public function documentRequestStatus()
    {
        if (!$this->requireAuth()) return;

        $studId = $this->getStudentId();
        $studentModel = new StudentModel();
        $requests = $studentModel->getDocumentRequests($studId);

        App::json($requests);
    }

    public function studentGrades()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole('student')) return;

        $studId = $this->getStudentId();
        $studentModel = new StudentModel();
        $grades = $studentModel->getGrades($studId);
        $gpa = $studentModel->calculateGPA($grades);

        App::json([
            'grades' => $grades,
            'gpa' => $gpa,
        ]);
    }

    public function enrolmentList()
    {
        if (!$this->requireAuth()) return;

        $enrolmentModel = new EnrolmentModel();
        $ayId = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;

        if ($_SESSION['account_role'] === 'student') {
            $enrolments = $enrolmentModel->getStudentEnrolments($this->getStudentId());
        } else {
            $enrolments = $enrolmentModel->getAll($ayId, $semId);
        }

        App::json($enrolments);
    }
}

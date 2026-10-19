<?php

class ApiRegistrarController extends BaseController
{
    private $studentModel;
    private $enrolmentModel;
    private $documentModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->studentModel    = new StudentModel();
        $this->enrolmentModel  = new EnrolmentModel();
        $this->documentModel   = new DocumentRequestModel();
        $this->userModel       = new UserModel();
        $this->db              = Database::getInstance();
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

        App::json(['success' => true, 'counts' => $counts]);
    }

    public function students()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $list = $this->userModel->getStudentsList();
        App::json(['success' => true, 'students' => $list]);
    }

    public function storeStudent()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $input = $this->getJsonInput();
        $required = ['lname', 'fname', 'ref_no', 'curr_id'];
        foreach ($required as $field) {
            if (empty($input[$field])) {
                App::json(['success' => false, 'message' => "Field {$field} is required."], 400);
            }
        }

        $data = [
            'ref_no'           => $input['ref_no'],
            'lname'            => $input['lname'],
            'fname'            => $input['fname'] ?? null,
            'mname'            => $input['mname'] ?? null,
            'ext'              => $input['ext'] ?? null,
            'emailaddress'     => $input['emailaddress'] ?? null,
            'curr_id'          => $input['curr_id'],
            'created_at'       => date('Y-m-d H:i:s'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ];
        $studId = $this->db->insert('students', $data);

        App::json(['success' => true, 'message' => 'Student created.', 'stud_id' => $studId], 201);
    }

    public function studentRecord($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $student    = $this->studentModel->findById($studId);
        $enrolments = $this->studentModel->getEnrolments($studId);
        $grades     = $this->studentModel->getGrades($studId);
        $gpa        = $this->studentModel->calculateGPA($grades);

        if (!$student) {
            App::json(['success' => false, 'message' => 'Student not found.'], 404);
        }

        App::json([
            'success'    => true,
            'student'    => $student,
            'enrolments' => $enrolments,
            'grades'     => $grades,
            'gpa'        => $gpa,
        ]);
    }

    public function updateStudent($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $input = $this->getJsonInput();
        $updates = [];
        foreach (['lname', 'fname', 'mname', 'ext', 'emailaddress', 'contactno', 'LRN'] as $field) {
            if (array_key_exists($field, $input)) {
                $updates[$field] = $input[$field];
            }
        }
        $updates['updated_at'] = date('Y-m-d H:i:s');

        if (empty($updates)) {
            App::json(['success' => false, 'message' => 'No fields to update.'], 400);
        }

        $this->db->update('students', $updates, 'stud_id = :id', ['id' => $studId]);
        App::json(['success' => true, 'message' => 'Student updated.']);
    }

    public function enrollments()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $ayId  = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;
        $list  = $this->enrolmentModel->getAll($ayId, $semId);

        App::json([
            'success'    => true,
            'enrolments' => $list,
            'academic_years' => $this->enrolmentModel->getAllAcademicYears(),
            'semesters' => $this->enrolmentModel->getAllSemesters(),
            'active_term' => $this->enrolmentModel->getActiveAcademicYearSemester(),
        ]);
    }

    public function assessEnrollment()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $input = $this->getJsonInput();
        $enrolmentId = (int) ($input['enrolment_id'] ?? 0);

        if ($enrolmentId <= 0) {
            App::json(['success' => false, 'message' => 'Enrolment id required.'], 400);
        }

        $this->db->update('enrolments', ['isassessed' => 1, 'is_approved' => 1], 'enrolment_id = :id', ['id' => $enrolmentId]);
        App::json(['success' => true, 'message' => 'Enrolment assessed.']);
    }

    public function updateEnrollmentStatus($enrolmentId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $input = $this->getJsonInput();
        $status = $input['enrol_status'] ?? null;

        if (empty($status)) {
            App::json(['success' => false, 'message' => 'Status required.'], 400);
        }

        $this->db->update('enrolments', ['enrol_status' => $status], 'enrolment_id = :id', ['id' => $enrolmentId]);
        App::json(['success' => true, 'message' => 'Enrollment status updated.']);
    }

    public function assignedCourses()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $pId = $_SESSION['owner_id'] ?? 0;
        $ayId  = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;

        $params = ['p_id' => $pId];
        $where = "WHERE sch.p_id = :p_id";
        if ($ayId) { $where .= " AND sch.ay_id = :ay_id"; $params['ay_id'] = $ayId; }
        if ($semId) { $where .= " AND sch.sem_id = :sem_id"; $params['sem_id'] = $semId; }

        $sql = "SELECT sch.sched_id, sch.class_no, c.code, c.description, c.units,
                       ay.ay_name, sem.semester_name
                FROM schedules sch
                JOIN courses c ON c.course_id = sch.course_id
                JOIN academic_years ay ON sch.ay_id = ay.ay_id
                JOIN semesters sem ON sch.sem_id = sem.sem_id
                {$where}
                ORDER BY ay.ay_name DESC, c.code";

        App::json(['success' => true, 'courses' => $this->db->fetchAll($sql, $params)]);
    }

    public function gradeRoster($schedId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $sql = "SELECT e.enrolment_id, e.stud_id, s.ref_no, s.lname, s.fname,
                       c.code, c.units, g.final_grade, g.remarks
                FROM schedules sch
                JOIN courses c ON c.course_id = sch.course_id
                JOIN enrolments e ON e.program_id = c.program_id AND e.ay_id = sch.ay_id AND e.sem_id = sch.sem_id
                JOIN students s ON s.stud_id = e.stud_id
                LEFT JOIN student_grades g ON g.enrolment_id = e.enrolment_id AND g.course_id = c.course_id
                WHERE sch.sched_id = :sched_id AND e.is_approved = 1 AND e.isassessed = 1
                ORDER BY s.lname, s.fname";

        App::json(['success' => true, 'roster' => $this->db->fetchAll($sql, ['sched_id' => $schedId])]);
    }

    public function storeBatchGrades()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $input = $this->getJsonInput();
        $schedId    = (int) ($input['sched_id'] ?? 0);
        $grades     = $input['grades'] ?? [];
        $pId        = $_SESSION['owner_id'] ?? 0;

        if ($schedId <= 0 || empty($grades)) {
            App::json(['success' => false, 'message' => 'sched_id and grades are required.'], 400);
        }

        $this->db->beginTransaction();
        try {
            foreach ($grades as $grade) {
                $exists = $this->db->fetchOne(
                    "SELECT 1 FROM student_grades WHERE enrolment_id = :e AND course_id = :c",
                    ['e' => $grade['enrolment_id'], 'c' => $grade['course_id']]
                );
                if (!$exists) {
                    $this->db->insert('student_grades', [
                        'enrolment_id'  => $grade['enrolment_id'],
                        'course_id'     => $grade['course_id'],
                        'p_id'          => $pId,
                        'final_grade'   => $grade['final_grade'],
                        'remarks'       => $grade['remarks'] ?? '',
                    ]);
                }
            }
            $this->db->commit();
            App::json(['success' => true, 'message' => 'Batch grades saved.']);
        } catch (Exception $e) {
            $this->db->rollback();
            App::json(['success' => false, 'message' => 'Failed to save grades.'], 500);
        }
    }

    public function documentQueue()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $requests = $this->documentModel->getQueue($_GET['sem_id'] ?? null);
        App::json(['success' => true, 'requests' => $requests]);
    }

    public function updateDocumentStatus($requestId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->registrar()) return;

        $input = $this->getJsonInput();
        $status    = $input['status'] ?? 'Processing';
        $valid     = ['Pending', 'Processing', 'Ready for Pickup', 'Released'];
        if (!in_array($status, $valid)) {
            App::json(['success' => false, 'message' => 'Invalid status.'], 400);
        }
        $this->documentModel->updateStatus($requestId, $status, $_SESSION['owner_id'] ?? 0);
        App::json(['success' => true, 'message' => 'Document status updated.']);
    }
}

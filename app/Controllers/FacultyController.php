<?php

class FacultyController extends BaseController
{
    private $enrolmentModel;
    private $programModel;

    public function __construct()
    {
        parent::__construct();
        $this->enrolmentModel = new EnrolmentModel();
        $this->programModel = new ProgramModel();
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $facultyId = $_SESSION['owner_id'] ?? 0;

        // Get active semester
        $academicTerms = $this->enrolmentModel->getActiveAcademicYearSemester();
        $semId = $academicTerms['sem_id'] ?? null;
        $ayId = $academicTerms['ay_id'] ?? null;

        // Get faculty's schedules for current term
        $schedules = [];
        $stats = ['total_classes' => 0, 'total_students' => 0, 'pending_grades' => 0, 'submitted_grades' => 0];

        if ($semId && $ayId) {
            $schedules = $this->programModel->getFacultySchedules($facultyId, $semId, $ayId);
            
            // Get grade submission stats for this faculty
            $stats = $this->getFacultyGradeStats($facultyId, $semId, $ayId);
        }

        // Get announcements for faculty
        $announcements = $this->getFacultyAnnouncements();

        App::view('faculty/dashboard', [
            'schedules' => $schedules,
            'stats' => $stats,
            'currentSemester' => $semId ? $this->programModel->getSemesterName($semId) : 'No active term',
            'announcements' => $announcements,
            'pageTitle' => 'Faculty Dashboard',
            'module' => 'faculty',
        ]);
    }

    public function gradeSubmissions()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $facultyId = $_SESSION['owner_id'] ?? 0;
        // Use existing $this->enrolmentModel

        $ayId = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;
        $statusFilter = $_GET['status_filter'] ?? null;

        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();

        $where = "WHERE 1=1";
        $params = [];

        if ($ayId) {
            $where .= " AND s.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where .= " AND s.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }
        if ($statusFilter) {
            $where .= " AND gs.status = :status";
            $params['status'] = $statusFilter;
        }

        // Faculty only sees their own submissions
        $where .= " AND gs.faculty_id = :faculty_id";
        $params['faculty_id'] = $_SESSION['owner_id'] ?? 0;

        $sql = "SELECT gs.*, sc.class_no, c.code, c.description, c.units, ay.ay_name, sem.semester_name,
                       COUNT(gsi.item_id) as student_count,
                       r.fname as reviewed_by_fname, r.lname as reviewed_by_lname
                FROM grade_submissions gs
                JOIN schedules sc ON gs.sched_id = sc.sched_id
                JOIN courses c ON sc.course_id = c.course_id
                JOIN academic_years ay ON sc.ay_id = ay.ay_id
                JOIN semesters sem ON sc.sem_id = sem.sem_id
                LEFT JOIN personnel r ON gs.reviewed_by = r.p_id
                LEFT JOIN grade_submission_items gsi ON gs.submission_id = gsi.submission_id
                $where
                GROUP BY gs.submission_id
                ORDER BY gs.created_at DESC";

        $submissions = $this->db->fetchAll($sql, $params);

        // Get stats
        $statsSql = "SELECT status, COUNT(*) as count FROM grade_submissions WHERE 1=1";
        if ($where !== "WHERE 1=1") {
            $statsSql .= " AND " . substr($where, 7);
        }
        $statsSql .= " GROUP BY status";
        $statsRows = $this->db->fetchAll($statsSql, $params);
        $stats = [];
        foreach ($statsRows as $row) {
            $stats[$row['status']] = $row['count'];
        }

        App::view('faculty/grade_submissions', [
            'submissions' => $submissions,
            'stats' => $stats,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'pageTitle' => 'My Grade Submissions',
            'module' => 'faculty',
        ]);
    }

    public function gradeSubmissionCreate()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $ayId = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;

        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();

        $schedules = [];
        $existingSubmissions = [];

        if ($ayId && $semId) {
            $facultyId = $_SESSION['owner_id'] ?? 0;
            $sql = "SELECT s.*, c.code, c.description, c.units,
                           d.day_name, ts.time_start, te.time_end, r.room_name,
                           (SELECT COUNT(*) FROM enrolment_courses ec WHERE ec.sched_id = s.sched_id) as enrolled_count
                    FROM schedules s
                    JOIN courses c ON s.course_id = c.course_id
                    LEFT JOIN schedule_daytimes sd ON s.sched_id = sd.sched_id
                    LEFT JOIN days d ON sd.day_id = d.day_id
                    LEFT JOIN times ts ON sd.time_start_id = ts.time_id
                    LEFT JOIN times te ON sd.time_end_id = te.time_id
                    LEFT JOIN rooms r ON sd.room_id = r.room_id
                    WHERE s.p_id = :faculty_id AND s.ay_id = :ay_id AND s.sem_id = :sem_id
                    ORDER BY c.code, s.class_no";

            $schedules = $this->db->fetchAll($sql, [
                'faculty_id' => $_SESSION['owner_id'] ?? 0,
                'ay_id' => $ayId,
                'sem_id' => $semId
            ]);

            $existingSql = "SELECT sched_id FROM grade_submissions WHERE faculty_id = :fid AND ay_id = :ay AND sem_id = :sem";
            $existing = $this->db->fetchAll($existingSql, [
                'fid' => $_SESSION['owner_id'] ?? 0,
                'ay' => $ayId,
                'sem' => $semId
            ]);
            $existingSubmissions = array_column($existing, 'sched_id');
        }

        App::view('faculty/grade_submission_create', [
            'schedules' => $schedules,
            'existingSubmissions' => $existingSubmissions,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'pageTitle' => 'Create Grade Submission',
            'module' => 'faculty',
        ]);
    }

    public function gradeSubmissionCreateConfirm($schedId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $existing = $this->db->fetchOne("SELECT * FROM grade_submissions WHERE sched_id = :id AND faculty_id = :fid", [
            'id' => $schedId,
            'fid' => $_SESSION['owner_id'] ?? 0
        ]);

        if ($existing) {
            App::flash('error', 'A submission already exists for this schedule.');
            App::redirect('/faculty/grade-submissions/create');
            return;
        }

        $submissionId = $this->db->insert('grade_submissions', [
            'sched_id' => $schedId,
            'faculty_id' => $_SESSION['owner_id'] ?? 0,
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Pre-populate with enrolled students
        $enrolments = $this->db->fetchAll("
            SELECT e.enrolment_id, s.stud_id
            FROM enrolments e
            JOIN enrolment_courses ec ON e.enrolment_id = ec.enrolment_id
            JOIN schedules s ON ec.sched_id = s.sched_id
            JOIN students s ON e.stud_id = s.stud_id
            WHERE ec.sched_id = :sched_id AND e.is_approved = 1 AND e.isassessed = 1
        ", ['sched_id' => $schedId]);

        foreach ($enrolments as $enrol) {
            $this->db->insert('grade_submission_items', [
                'submission_id' => $submissionId,
                'enrolment_id' => $enrol['enrolment_id'],
                'grade_value' => null,
                'remarks' => null,
                'is_final' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        App::flash('success', 'Grade submission draft created. You can now enter grades.');
        App::redirect('/faculty/grade-submissions/' . $submissionId);
    }

    public function gradeSubmissionDetail($submissionId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $submission = $this->db->fetchOne("
            SELECT gs.*, sc.class_no, c.code, c.description, c.units, ay.ay_name, sem.semester_name,
                   p.fname as faculty_fname, p.lname as faculty_lname,
                   r.fname as reviewed_by_fname, r.lname as reviewed_by_lname
            FROM grade_submissions gs
            JOIN schedules sc ON gs.sched_id = sc.sched_id
            JOIN courses c ON sc.course_id = c.course_id
            JOIN academic_years ay ON gs.ay_id = ay.ay_id
            JOIN semesters sem ON gs.sem_id = sem.sem_id
            LEFT JOIN personnel p ON gs.faculty_id = p.p_id
            LEFT JOIN personnel r ON gs.reviewed_by = r.p_id
            WHERE gs.submission_id = :id", ['id' => $submissionId]);

        if (!$submission) {
            App::flash('error', 'Submission not found.');
            App::redirect('/faculty/grade-submissions');
            return;
        }

        // Check ownership
        if ($submission['faculty_id'] != ($_SESSION['owner_id'] ?? 0)) {
            App::flash('error', 'Access denied.');
            App::redirect('/faculty/grade-submissions');
            return;
        }

        $items = $this->db->fetchAll("
            SELECT gsi.*, s.fname, s.lname, s.ref_no
            FROM grade_submission_items gsi
            JOIN enrolments e ON gsi.enrolment_id = e.enrolment_id
            JOIN students s ON e.stud_id = s.stud_id
            WHERE gsi.submission_id = :id
            ORDER BY s.lname, s.fname", ['id' => $submissionId]);

        $reviewHistory = $this->db->fetchAll("
            SELECT gr.*, p.fname as reviewer_fname, p.lname as reviewer_lname
            FROM grade_reviews gr
            LEFT JOIN personnel p ON gr.reviewer_id = p.p_id
            WHERE gr.submission_id = :id
            ORDER BY gr.created_at DESC", ['id' => $submissionId]);

        App::view('faculty/grade_submission_detail', [
            'submission' => $submission,
            'items' => $items,
            'reviewHistory' => $reviewHistory,
            'pageTitle' => 'Submission Details',
            'module' => 'faculty',
        ]);
    }

    public function gradeSubmissionSubmit($submissionId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/faculty/grade-submissions/' . $submissionId);
            return;
        }

        $submission = $this->db->fetchOne("SELECT * FROM grade_submissions WHERE submission_id = :id", ['id' => $submissionId]);
        if (!$submission || $submission['status'] !== 'draft') {
            App::flash('error', 'Submission not available for submission.');
            App::redirect('/faculty/grade-submissions/' . $submissionId);
            return;
        }

        // Check if all grades are entered
        $items = $this->db->fetchAll("SELECT * FROM grade_submission_items WHERE submission_id = :id AND grade_value IS NULL", ['id' => $submissionId]);
        if (!empty($items)) {
            App::flash('error', 'All grades must be entered before submitting.');
            App::redirect('/faculty/grade-submissions/' . $submissionId);
            return;
        }

        $this->db->update('grade_submissions', [
            'status' => 'submitted',
            'submitted_at' => date('Y-m-d H:i:s')
        ], 'submission_id = :id', ['id' => $submissionId]);

        App::flash('success', 'Submission sent for review.');
        App::redirect('/faculty/grade-submissions/' . $submissionId);
    }

    public function gradeSubmissionEdit($submissionId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        $submission = $this->db->fetchOne("SELECT * FROM grade_submissions WHERE submission_id = :id", ['id' => $submissionId]);
        if (!$submission || $submission['status'] !== 'draft') {
            App::flash('error', 'Only draft submissions can be edited.');
            App::redirect('/faculty/grade-submissions/' . $submissionId);
            return;
        }

        // Check ownership
        if ($submission['faculty_id'] != ($_SESSION['owner_id'] ?? 0)) {
            App::flash('error', 'Access denied.');
            App::redirect('/faculty/grade-submissions');
            return;
        }

        $items = $this->db->fetchAll("
            SELECT gsi.*, s.fname, s.lname, s.ref_no
            FROM grade_submission_items gsi
            JOIN enrolments e ON gsi.enrolment_id = e.enrolment_id
            JOIN students s ON e.stud_id = s.stud_id
            WHERE gsi.submission_id = :id
            ORDER BY s.lname, s.fname", ['id' => $submissionId]);

        App::view('faculty/grade_submission_edit', [
            'submission' => $submission,
            'items' => $items,
            'pageTitle' => 'Edit Grades',
            'module' => 'faculty',
        ]);
    }

    public function gradeSubmissionUpdateGrades($submissionId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->faculty()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/faculty/grade-submissions/' . $submissionId . '/edit');
            return;
        }

        $submission = $this->db->fetchOne("SELECT * FROM grade_submissions WHERE submission_id = :id", ['id' => $submissionId]);
        if (!$submission || $submission['status'] !== 'draft') {
            App::flash('error', 'Only draft submissions can be edited.');
            App::redirect('/faculty/grade-submissions/' . $submissionId);
            return;
        }

        // Check ownership
        if ($submission['faculty_id'] != ($_SESSION['owner_id'] ?? 0)) {
            App::flash('error', 'Access denied.');
            App::redirect('/faculty/grade-submissions');
            return;
        }

        $grades = $_POST['grades'] ?? [];
        $remarks = $_POST['remarks'] ?? [];
        $isFinal = $_POST['is_final'] ?? [];

        if (empty($grades)) {
            App::flash('error', 'No grades submitted.');
            App::redirect('/faculty/grade-submissions/' . $submissionId . '/edit');
            return;
        }

        $this->db->beginTransaction();
        try {
            foreach ($grades as $itemId => $gradeValue) {
                $remark = $remarks[$itemId] ?? '';
                $final = isset($isFinal[$itemId]) ? 1 : 0;

                // Validate grade
                if ($gradeValue !== '' && ($gradeValue < 0 || $gradeValue > 100)) {
                    throw new Exception('Invalid grade value for item ' . $itemId);
                }

                $this->db->update('grade_submission_items', [
                    'grade_value' => $gradeValue !== '' ? $gradeValue : null,
                    'remarks' => $remark,
                    'is_final' => $final,
                    'updated_at' => date('Y-m-d H:i:s')
                ], 'item_id = :id', ['id' => $itemId]);
            }

            $this->db->commit();
            App::flash('success', 'Grades saved successfully.');
        } catch (Exception $e) {
            $this->db->rollback();
            App::flash('error', 'Failed to save grades: ' . $e->getMessage());
        }

        App::redirect('/faculty/grade-submissions/' . $submissionId . '/edit');
    }

    /**
     * Get faculty grade statistics
     */
    private function getFacultyGradeStats($facultyId, $semId, $ayId)
    {
        $sql = "SELECT 
                    COUNT(DISTINCT gs.submission_id) as total_submissions,
                    COUNT(DISTINCT CASE WHEN gs.status = 'draft' THEN gs.submission_id END) as draft_count,
                    COUNT(DISTINCT CASE WHEN gs.status = 'submitted' THEN gs.submission_id END) as submitted_count,
                    COUNT(DISTINCT CASE WHEN gs.status IN ('approved', 'posted') THEN gs.submission_id END) as approved_count,
                    COUNT(DISTINCT gsi.item_id) as total_grade_items,
                    COUNT(DISTINCT CASE WHEN gsi.grade_value IS NOT NULL AND gsi.is_final = 1 THEN gsi.item_id END) as finalized_grades
                FROM grade_submissions gs
                JOIN schedules s ON gs.sched_id = s.sched_id
                LEFT JOIN grade_submission_items gsi ON gs.submission_id = gsi.submission_id
                WHERE gs.faculty_id = :fid AND s.sem_id = :sem_id AND s.ay_id = :ay_id";

        $stats = $this->db->fetchOne($sql, [
            'fid' => $facultyId,
            'sem_id' => $semId,
            'ay_id' => $ayId
        ]);

        return $stats ?: [
            'total_submissions' => 0,
            'draft_count' => 0,
            'submitted_count' => 0,
            'approved_count' => 0,
            'total_grade_items' => 0,
            'finalized_grades' => 0
        ];
    }

    /**
     * Get announcements for faculty
     */
    private function getFacultyAnnouncements()
    {
        $sql = "SELECT a.*, p.fname as author_fname, p.lname as author_lname
                FROM announcements a
                LEFT JOIN personnel p ON a.p_id = p.p_id
                WHERE a.is_active = 1 AND a.ann_status = 1
                ORDER BY a.ann_index ASC, a.ann_id DESC
                LIMIT 5";
        return $this->db->fetchAll($sql);
    }
}
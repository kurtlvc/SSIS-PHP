<?php

require_once __DIR__ . '/../Services/AuditService.php';
require_once __DIR__ . '/../Models/ProgramModel.php';

class AdminController extends BaseController
{
    private $userModel;
    private $enrolmentModel;
    private $programModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
        $this->enrolmentModel = new EnrolmentModel();
        $this->programModel = new ProgramModel();
    }

    public function users()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $accounts = $this->userModel->getAll();

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('admin/users', [
            'accounts' => $accounts,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'User Management',
            'module' => 'admin',
        ]);
    }

    public function roles()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        App::view('admin/roles', [
            'pageTitle' => 'Roles & Permissions',
            'module' => 'admin',
        ]);
    }

    public function settings()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $settings = $this->db->fetchOne("SELECT * FROM settings WHERE singleton = 1");
        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();
        $personnel = $this->userModel->getPersonnelList();

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('admin/settings', [
            'settings' => $settings,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'personnel' => $personnel,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'System Settings',
            'module' => 'admin',
        ]);
    }

    public function createUser()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/users');
            return;
        }

        $login = $_POST['login'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? '';
        $ownerType = $_POST['owner_type'] ?? '';
        $ownerId = $_POST['owner_id'] ?? '';

        $validRoles = ['student', 'faculty', 'chair', 'dean', 'admin', 'cashier'];
        $validOwnerTypes = ['personnel', 'student'];

        if (!in_array($role, $validRoles) || !in_array($ownerType, $validOwnerTypes)) {
            App::flash('error', 'Invalid role or owner type.');
            App::redirect('/admin/users');
            return;
        }

        if (empty($login) || empty($password) || empty($ownerId)) {
            App::flash('error', 'All fields are required.');
            App::redirect('/admin/users');
            return;
        }

        try {
            $newValues = [
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'login' => $login,
                'role' => $role,
            ];
            $userId = $this->userModel->create($newValues);
            $this->auditCreate('accounts', $userId, $newValues);
            App::flash('success', 'User account created successfully.');
        } catch (Exception $e) {
            App::flash('error', 'Failed to create user: ' . $e->getMessage());
        }

        App::redirect('/admin/users');
    }

    public function updateUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/users');
            return;
        }

        $updates = [];
        if (isset($_POST['role']) && in_array($_POST['role'], ['student', 'faculty', 'chair', 'dean', 'admin'])) {
            $updates['role'] = $_POST['role'];
        }
        if (isset($_POST['is_active'])) {
            $updates['is_active'] = $_POST['is_active'];
        }
        if (isset($_POST['password']) && !empty($_POST['password'])) {
            $updates['password'] = $_POST['password'];
        }

        if (!empty($updates)) {
            // Get old values for audit
            $oldValues = $this->userModel->findById($id);
            $this->userModel->update($id, $updates);
            $this->auditUpdate('accounts', $id, $oldValues, $updates);
            App::flash('success', 'User updated successfully.');
        }

        App::redirect('/admin/users');
    }

    public function updateSettings()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/system-settings');
            return;
        }

        $updates = [
            'system_title' => $_POST['system_title'] ?? '',
            'sem_id' => $_POST['sem_id'] ?? 1,
            'ay_id' => $_POST['ay_id'] ?? 1,
            'upload_max_mb' => $_POST['upload_max_mb'] ?? 10,
            'rate_limit_per_minute' => $_POST['rate_limit_per_minute'] ?? 60,
            'rate_limit_login' => $_POST['rate_limit_login'] ?? 10,
            'rate_limit_strict' => $_POST['rate_limit_strict'] ?? 0,
        ];

        $this->db->update('settings', $updates, 'singleton = 1');
        App::flash('success', 'System settings updated successfully.');
        App::redirect('/admin/system-settings');
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $counts = $this->db->fetchOne("SELECT
            (SELECT COUNT(*) FROM students) AS students,
            (SELECT COUNT(*) FROM personnel WHERE is_active = 1) AS personnel,
            (SELECT COUNT(*) FROM accounts WHERE is_active = 1) AS accounts,
            (SELECT COUNT(*) FROM enrolments WHERE is_approved = 1) AS approved_enrolments,
            (SELECT COUNT(*) FROM document_requests WHERE status = 'Pending') AS pending_requests,
            (SELECT COALESCE(SUM(amount_paid),0) FROM payments WHERE DATE(payment_date) = CURDATE()) AS today_collection");

        App::view('admin/dashboard', [
            'counts' => $counts,
            'pageTitle' => 'Admin Dashboard',
            'module' => 'admin',
        ]);
    }

    public function createUserForm()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        App::view('admin/user_form', [
            'user' => null,
            'personnel' => $this->userModel->getPersonnelList(),
            'students' => $this->userModel->getStudentsList(),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Create User',
            'module' => 'admin',
        ]);
    }

    public function viewUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $user = $this->userModel->findById($id);
        if (!$user) {
            App::flash('error', 'User not found.');
            App::redirect('/admin/users');
            return;
        }

        App::view('admin/user_view', [
            'user' => $user,
            'pageTitle' => 'View User',
            'module' => 'admin',
        ]);
    }

    public function editUserForm($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $user = $this->userModel->findById($id);
        if (!$user) {
            App::flash('error', 'User not found.');
            App::redirect('/admin/users');
            return;
        }

        App::view('admin/user_form', [
            'user' => $user,
            'personnel' => $this->userModel->getPersonnelList(),
            'students' => $this->userModel->getStudentsList(),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit User',
            'module' => 'admin',
        ]);
    }

    public function toggleUserStatus($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/users');
            return;
        }

        $isActive   = isset($_POST['is_active']) ? (int) $_POST['is_active'] : 1;
        // Get old values for audit
        $oldValues = $this->userModel->findById($id);
        $this->db->update('accounts', ['is_active' => $isActive], 'id = :id', ['id' => $id]);
        $this->auditUpdate('accounts', $id, $oldValues, ['is_active' => $isActive]);
        App::flash('success', 'User status updated.');
        App::redirect('/admin/users');
    }

    public function deleteUser($id)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/users');
            return;
        }

        // Prevent self-deletion
        if ($_SESSION['account_id'] == $id) {
            App::flash('error', 'You cannot delete your own account.');
            App::redirect('/admin/users');
            return;
        }

        $oldValues = $this->userModel->findById($id);
        $this->db->delete('accounts', 'id = :id', ['id' => $id]);
        $this->auditDelete('accounts', $id, $oldValues);
        App::flash('success', 'User deleted successfully.');
        App::redirect('/admin/users');
    }

    public function auditLogs()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $actor = $_GET['actor_id'] ?? null;
        $params = [];
        $where = "WHERE 1=1";
        if ($actor) {
            $where .= " AND actor_id = :actor_id";
            $params['actor_id'] = $actor;
        }

        $sql = "SELECT id, actor_id, action, entity, entity_id, meta, created_at
                FROM audit_log
                {$where}
                ORDER BY id DESC LIMIT 200";

        $logs = $this->db->fetchAll($sql, $params);

        App::view('admin/audit_logs', [
            'logs' => $logs,
            'pageTitle' => 'Audit Logs',
            'module' => 'admin',
        ]);
    }

    // ============================================================
    // Program Management
    // ============================================================

    public function programs()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $programs = $this->programModel->getAll();

        App::view('admin/programs', [
            'programs' => $programs,
            'pageTitle' => 'Program Management',
            'module' => 'admin',
        ]);
    }

    public function programCreate()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $campuses = $this->programModel->getAllCampuses();

        App::view('admin/program_form', [
            'program' => null,
            'campuses' => $campuses,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Create Program',
            'module' => 'admin',
        ]);
    }

    public function storeProgram()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/programs/create');
            return;
        }

        $required = ['program_code', 'description', 'campus_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/programs/create');
                return;
            }
        }

        $data = [
            'program_code' => $_POST['program_code'],
            'description' => $_POST['description'],
            'campus_id' => $_POST['campus_id'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $programId = $this->programModel->create($data);
        $this->auditCreate('programs', $programId, $data);
        App::flash('success', 'Program created successfully.');
        App::redirect('/admin/programs');
    }

    public function programEdit($programId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $program = $this->programModel->findById($programId);
        if (!$program) {
            App::flash('error', 'Program not found.');
            App::redirect('/admin/programs');
            return;
        }

        $campuses = $this->programModel->getAllCampuses();

        App::view('admin/program_form', [
            'program' => $program,
            'campuses' => $this->programModel->getAllCampuses(),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit Program',
            'module' => 'admin',
        ]);
    }

    public function updateProgram($programId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/programs/' . $programId . '/edit');
            return;
        }

        $required = ['program_code', 'description', 'campus_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/programs/' . $programId . '/edit');
                return;
            }
        }

        $oldValues = $this->programModel->findById($programId);
        $updates = [
            'program_code' => $_POST['program_code'],
            'description' => $_POST['description'],
            'campus_id' => $_POST['campus_id'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->programModel->update($programId, $updates);
        $this->auditUpdate('programs', $programId, $oldValues, $updates);
        App::flash('success', 'Program updated successfully.');
        App::redirect('/admin/programs');
    }

    public function deleteProgram($programId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/programs');
            return;
        }

        // Prevent deletion if program has curriculums
        $curriculums = $this->programModel->getAllCurriculums($programId);
        if (!empty($curriculums)) {
            App::flash('error', 'Cannot delete program with existing curriculums.');
            App::redirect('/admin/programs');
            return;
        }

        $oldValues = $this->programModel->findById($programId);
        $this->programModel->delete($programId);
        $this->auditDelete('programs', $programId, $oldValues);
        App::flash('success', 'Program deleted successfully.');
        App::redirect('/admin/programs');
    }

    // ============================================================
    // Curriculum Management
    // ============================================================

    public function curriculums()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $curriculums = $this->programModel->getAllCurriculums();
        $programs = $this->programModel->getActive();

        App::view('admin/curriculums', [
            'curriculums' => $curriculums,
            'programs' => $programs,
            'pageTitle' => 'Curriculum Management',
            'module' => 'admin',
        ]);
    }

    public function curriculumCreate()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $programs = $this->programModel->getActive();

        App::view('admin/curriculum_form', [
            'curriculum' => null,
            'programs' => $programs,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Create Curriculum',
            'module' => 'admin',
        ]);
    }

    public function storeCurriculum()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/curriculums/create');
            return;
        }

        $required = ['program_id', 'effective_year', 'description'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/curriculums/create');
                return;
            }
        }

        $data = [
            'program_id' => $_POST['program_id'],
            'effective_year' => $_POST['effective_year'],
            'description' => $_POST['description'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $currId = $this->programModel->createCurriculum($data);
        $this->auditCreate('curriculums', $currId, $data);
        App::flash('success', 'Curriculum created successfully.');
        App::redirect('/admin/curriculums');
    }

    public function curriculumEdit($currId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $curriculum = $this->programModel->findCurriculumById($currId);
        if (!$curriculum) {
            App::flash('error', 'Curriculum not found.');
            App::redirect('/admin/curriculums');
            return;
        }

        $programs = $this->programModel->getActive();

        App::view('admin/curriculum_form', [
            'curriculum' => $curriculum,
            'programs' => $this->programModel->getActive(),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit Curriculum',
            'module' => 'admin',
        ]);
    }

    public function updateCurriculum($currId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/curriculums/' . $currId . '/edit');
            return;
        }

        $required = ['program_id', 'effective_year', 'description'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/curriculums/' . $currId . '/edit');
                return;
            }
        }

        $oldValues = $this->programModel->findCurriculumById($currId);
        $updates = [
            'program_id' => $_POST['program_id'],
            'effective_year' => $_POST['effective_year'],
            'description' => $_POST['description'],
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->programModel->updateCurriculum($currId, $updates);
        $this->auditUpdate('curriculums', $currId, $oldValues, $updates);
        App::flash('success', 'Curriculum updated successfully.');
        App::redirect('/admin/curriculums');
    }

    public function deleteCurriculum($currId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/curriculums');
            return;
        }

        // Check if curriculum has courses
        $courses = $this->programModel->getCoursesByCurriculum($currId);
        if (!empty($courses)) {
            App::flash('error', 'Cannot delete curriculum with existing courses.');
            App::redirect('/admin/curriculums');
            return;
        }

        $oldValues = $this->programModel->findCurriculumById($currId);
        // Need to add deleteCurriculum method to ProgramModel
        // $this->programModel->deleteCurriculum($currId);
        // For now just soft delete
        $this->db->update('curriculums', ['is_active' => 0], 'curr_id = :id', ['id' => $currId]);
        $this->auditDelete('curriculums', $currId, $oldValues);
        App::flash('success', 'Curriculum deactivated successfully.');
        App::redirect('/admin/curriculums');
    }

    // ============================================================
    // Course Management
    // ============================================================

    public function courses()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $currId = $_GET['curr_id'] ?? null;
        $courses = $this->programModel->getAllCourses($currId);
        $curriculums = $this->programModel->getAllCurriculums();

        App::view('admin/courses', [
            'courses' => $courses,
            'curriculums' => $curriculums,
            'activeCurriculum' => $currId,
            'pageTitle' => 'Course Management',
            'module' => 'admin',
        ]);
    }

    public function courseCreate()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $curriculums = $this->programModel->getAllCurriculums();

        App::view('admin/course_form', [
            'course' => null,
            'curriculums' => $curriculums,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Create Course',
            'module' => 'admin',
        ]);
    }

    public function storeCourse()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/courses/create');
            return;
        }

        $required = ['curr_id', 'code', 'description', 'units', 'lec_units', 'lab_units'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/courses/create');
                return;
            }
        }

        $data = [
            'curr_id' => $_POST['curr_id'],
            'code' => $_POST['code'],
            'description' => $_POST['description'],
            'units' => $_POST['units'],
            'lec_units' => $_POST['lec_units'],
            'lab_units' => $_POST['lab_units'],
            'prerequisite' => $_POST['prerequisite'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $courseId = $this->programModel->createCourse($data);
        $this->auditCreate('courses', $courseId, $data);
        App::flash('success', 'Course created successfully.');
        App::redirect('/admin/courses');
    }

    public function courseEdit($courseId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $course = $this->programModel->findCourseById($courseId);
        if (!$course) {
            App::flash('error', 'Course not found.');
            App::redirect('/admin/courses');
            return;
        }

        $curriculums = $this->programModel->getAllCurriculums();

        App::view('admin/course_form', [
            'course' => $course,
            'curriculums' => $curriculums,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit Course',
            'module' => 'admin',
        ]);
    }

    public function updateCourse($courseId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/courses/' . $courseId . '/edit');
            return;
        }

        $required = ['curr_id', 'code', 'description', 'units', 'lec_units', 'lab_units'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/courses/' . $courseId . '/edit');
                return;
            }
        }

        $oldValues = $this->programModel->findCourseById($courseId);
        $updates = [
            'curr_id' => $_POST['curr_id'],
            'code' => $_POST['code'],
            'description' => $_POST['description'],
            'units' => $_POST['units'],
            'lec_units' => $_POST['lec_units'],
            'lab_units' => $_POST['lab_units'],
            'prerequisite' => $_POST['prerequisite'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->programModel->updateCourse($courseId, $updates);
        $this->auditUpdate('courses', $courseId, $oldValues, $updates);
        App::flash('success', 'Course updated successfully.');
        App::redirect('/admin/courses');
    }

    public function deleteCourse($courseId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/courses');
            return;
        }

        // Check if course has schedules
        $schedules = $this->programModel->getSchedule($courseId);
        if (!empty($schedules)) {
            App::flash('error', 'Cannot delete course with existing schedules.');
            App::redirect('/admin/courses');
            return;
        }

        $oldValues = $this->programModel->findCourseById($courseId);
        $this->programModel->deleteCourse($courseId);
        $this->auditDelete('courses', $courseId, $oldValues);
        App::flash('success', 'Course deleted successfully.');
        App::redirect('/admin/courses');
    }

    // ============================================================
    // Schedule Management
    // ============================================================

    public function schedules()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $courseId = $_GET['course_id'] ?? null;
        $schedules = $courseId ? $this->programModel->getSchedule($courseId) : [];
        $courses = $this->programModel->getAllCourses();
        $days = $this->programModel->getAllDays();
        $times = $this->programModel->getAllTimes();
        $rooms = $this->programModel->getAllRooms();

        App::view('admin/schedules', [
            'schedules' => $schedules,
            'courses' => $courses,
            'days' => $days,
            'times' => $times,
            'rooms' => $rooms,
            'activeCourse' => $courseId,
            'pageTitle' => 'Schedule Management',
            'module' => 'admin',
        ]);
    }

    public function storeSchedule()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/schedules');
            return;
        }

        $required = ['course_id', 'day_id', 'time_start_id', 'time_end_id', 'room_id'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/schedules');
                return;
            }
        }

        // Check for time conflicts
        $conflict = $this->db->fetchOne("
            SELECT 1 FROM schedule_daytimes sd
            JOIN schedule_daytimes sd2 ON sd.day_id = sd2.day_id
            WHERE sd.course_id = :course_id
            AND sd.time_start_id = :time_start_id
            AND sd.time_end_id = :time_end_id
            AND sd.day_id = :day_id
            AND sd.room_id = :room_id
        ", [
            'course_id' => $_POST['course_id'],
            'day_id' => $_POST['day_id'],
            'time_start_id' => $_POST['time_start_id'],
            'time_end_id' => $_POST['time_end_id'],
            'room_id' => $_POST['room_id'],
        ]);

        if ($conflict) {
            App::flash('error', 'Schedule conflict detected for this room and time.');
            App::redirect('/admin/schedules');
            return;
        }

        $data = [
            'course_id' => $_POST['course_id'],
            'day_id' => $_POST['day_id'],
            'time_start_id' => $_POST['time_start_id'],
            'time_end_id' => $_POST['time_end_id'],
            'room_id' => $_POST['room_id'],
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $scheduleId = $this->programModel->createSchedule($data);
        $this->auditCreate('schedule_daytimes', $scheduleId, $data);
        App::flash('success', 'Schedule created successfully.');
        App::redirect('/admin/schedules');
    }

    public function deleteSchedule($scheduleId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/schedules');
            return;
        }

        $this->programModel->deleteSchedule($scheduleId);
        $this->auditDelete('schedule_daytimes', $scheduleId, ['schedule_id' => $scheduleId]);
        App::flash('success', 'Schedule deleted successfully.');
        App::redirect('/admin/schedules');
    }

    // ============================================================
    // Announcement Management
    // ============================================================

    public function announcements()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $announcements = $this->db->fetchAll("SELECT * FROM announcements ORDER BY ann_index ASC, ann_id DESC");

        App::view('admin/announcements', [
            'announcements' => $announcements,
            'pageTitle' => 'Announcements',
            'module' => 'admin',
        ]);
    }

    public function announcementCreate()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        App::view('admin/announcement_form', [
            'announcement' => null,
            'targetRoles' => [],
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Create Announcement',
            'module' => 'admin',
        ]);
    }

    public function storeAnnouncement()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/announcements/create');
            return;
        }

        $required = ['ann_title', 'ann_body', 'ann_status'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/announcements/create');
                return;
            }
        }

        $targetRoles = $_POST['target_roles'] ?? [];
        $data = [
            'ann_title' => $_POST['ann_title'],
            'ann_body' => $_POST['ann_body'],
            'ann_status' => (int)($_POST['ann_status'] ?? 1),
            'ann_index' => (int)($_POST['ann_index'] ?? 0),
            'priority' => $_POST['priority'] ?? 'normal',
            'target_roles' => json_encode($targetRoles),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'p_id' => $_SESSION['owner_id'] ?? 0,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $annId = $this->db->insert('announcements', $data);
        $this->auditCreate('announcements', $annId, $data);
        App::flash('success', 'Announcement created successfully.');
        App::redirect('/admin/announcements');
    }

    public function announcementEdit($annId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        $announcement = $this->db->fetchOne('SELECT * FROM announcements WHERE ann_id = :id', ['id' => $annId]);
        if (!$announcement) {
            App::flash('error', 'Announcement not found.');
            App::redirect('/admin/announcements');
            return;
        }

        $targetRoles = json_decode($announcement['target_roles'] ?? '[]', true) ?: [];

        App::view('admin/announcement_form', [
            'announcement' => $announcement,
            'targetRoles' => $targetRoles,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Edit Announcement',
            'module' => 'admin',
        ]);
    }

    public function updateAnnouncement($annId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/announcements/' . $annId . '/edit');
            return;
        }

        $required = ['ann_title', 'ann_body', 'ann_status'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                App::flash('error', 'All required fields must be filled.');
                App::redirect('/admin/announcements/' . $annId . '/edit');
                return;
            }
        }

        $targetRoles = $_POST['target_roles'] ?? [];
        $oldValues = $this->db->fetchOne('SELECT * FROM announcements WHERE ann_id = :id', ['id' => $annId]);
        $updates = [
            'ann_title' => $_POST['ann_title'],
            'ann_body' => $_POST['ann_body'],
            'ann_status' => (int)($_POST['ann_status'] ?? 1),
            'ann_index' => (int)($_POST['ann_index'] ?? 0),
            'priority' => $_POST['priority'] ?? 'normal',
            'target_roles' => json_encode($_POST['target_roles'] ?? []),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->update('announcements', $updates, 'ann_id = :id', ['id' => $annId]);
        $this->auditUpdate('announcements', $annId, $oldValues, $updates);
        App::flash('success', 'Announcement updated successfully.');
        App::redirect('/admin/announcements');
    }

    public function deleteAnnouncement($annId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->admin()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/admin/announcements');
            return;
        }

        $oldValues = $this->db->fetchOne('SELECT * FROM announcements WHERE ann_id = :id', ['id' => $annId]);
        $this->db->delete('announcements', 'ann_id = :id', ['id' => $annId]);
        $this->auditDelete('announcements', $annId, $oldValues);
        App::flash('success', 'Announcement deleted successfully.');
        App::redirect('/admin/announcements');
    }
}

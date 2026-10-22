<?php

class ProgramModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all programs
     */
    public function getAll(): array
    {
        $sql = "SELECT p.*, c.campus_name 
                FROM programs p
                LEFT JOIN campuses c ON p.campus_id = c.campus_id
                ORDER BY p.program_code";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get active programs
     */
    public function getActive(): array
    {
        $sql = "SELECT p.*, c.campus_name 
                FROM programs p
                LEFT JOIN campuses c ON p.campus_id = c.campus_id
                WHERE p.is_active = 1
                ORDER BY p.program_code";
        return $this->db->fetchAll($sql);
    }

    /**
     * Find program by ID
     */
    public function findById(int $programId): ?array
    {
        $sql = "SELECT p.*, c.campus_name 
                FROM programs p
                LEFT JOIN campuses c ON p.campus_id = c.campus_id
                WHERE p.program_id = :id";
        return $this->db->fetchOne($sql, ['id' => $programId]);
    }

    /**
     * Create new program
     */
    public function create(array $data): int
    {
        return $this->db->insert('programs', $data);
    }

    /**
     * Update program
     */
    public function update(int $programId, array $data): bool
    {
        return $this->db->update('programs', $data, 'program_id = :id', ['id' => $programId]);
    }

    /**
     * Delete program
     */
    public function delete(int $programId): bool
    {
        return $this->db->delete('programs', 'program_id = :id', ['id' => $programId]);
    }

    /**
     * Get all curriculums
     */
    public function getAllCurriculums(?int $programId = null): array
    {
        $sql = "SELECT c.*, p.program_code, p.description as program_desc
                FROM curriculums c
                JOIN programs p ON c.program_id = p.program_id";
        $params = [];

        if ($programId) {
            $sql .= " WHERE c.program_id = :program_id";
            $params['program_id'] = $programId;
        }

        $sql .= " ORDER BY p.program_code, c.effective_year DESC";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Find curriculum by ID
     */
    public function findCurriculumById(int $currId): ?array
    {
        $sql = "SELECT c.*, p.program_code, p.description as program_desc
                FROM curriculums c
                JOIN programs p ON c.program_id = p.program_id
                WHERE c.curr_id = :id";
        return $this->db->fetchOne($sql, ['id' => $currId]);
    }

    /**
     * Create curriculum
     */
    public function createCurriculum(array $data): int
    {
        return $this->db->insert('curriculums', $data);
    }

    /**
     * Update curriculum
     */
    public function updateCurriculum(int $currId, array $data): bool
    {
        return $this->db->update('curriculums', $data, 'curr_id = :id', ['id' => $currId]);
    }

    /**
     * Get courses by curriculum
     */
    public function getCoursesByCurriculum(int $currId): array
    {
        $sql = "SELECT c.*, 
                       d.day_name, d.day_code,
                       ts.time_start, te.time_end,
                       r.room_name
                FROM courses c
                LEFT JOIN schedule_daytimes sd ON c.course_id = sd.course_id
                LEFT JOIN days d ON sd.day_id = d.day_id
                LEFT JOIN times ts ON sd.time_start_id = ts.time_id
                LEFT JOIN times te ON sd.time_end_id = te.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                WHERE c.curr_id = :curr_id
                ORDER BY c.code";
        return $this->db->fetchAll($sql, ['curr_id' => $currId]);
    }

    /**
     * Get all courses
     */
    public function getAllCourses(?int $currId = null): array
    {
        $sql = "SELECT c.*, curr.effective_year, p.program_code
                FROM courses c
                JOIN curriculums curr ON c.curr_id = curr.curr_id
                JOIN programs p ON curr.program_id = p.program_id";
        $params = [];

        if ($currId) {
            $sql .= " WHERE c.curr_id = :curr_id";
            $params['curr_id'] = $currId;
        }

        $sql .= " ORDER BY p.program_code, c.code";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Find course by ID
     */
    public function findCourseById(int $courseId): ?array
    {
        $sql = "SELECT c.*, curr.effective_year, p.program_code
                FROM courses c
                JOIN curriculums curr ON c.curr_id = curr.curr_id
                JOIN programs p ON curr.program_id = p.program_id
                WHERE c.course_id = :id";
        return $this->db->fetchOne($sql, ['id' => $courseId]);
    }

    /**
     * Create course
     */
    public function createCourse(array $data): int
    {
        return $this->db->insert('courses', $data);
    }

    /**
     * Update course
     */
    public function updateCourse(int $courseId, array $data): bool
    {
        return $this->db->update('courses', $data, 'course_id = :id', ['id' => $courseId]);
    }

    /**
     * Delete course
     */
    public function deleteCourse(int $courseId): bool
    {
        return $this->db->delete('courses', 'course_id = :id', ['id' => $courseId]);
    }

    /**
     * Get all campuses
     */
    public function getAllCampuses(): array
    {
        $sql = "SELECT * FROM campuses WHERE is_active = 1 ORDER BY campus_name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all departments
     */
    public function getAllDepartments(): array
    {
        $sql = "SELECT * FROM departments WHERE is_active = 1 ORDER BY dept_name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all rooms
     */
    public function getAllRooms(): array
    {
        $sql = "SELECT * FROM rooms WHERE is_active = 1 ORDER BY room_name";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all days
     */
    public function getAllDays(): array
    {
        $sql = "SELECT * FROM days WHERE is_active = 1 ORDER BY day_order";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all times
     */
    public function getAllTimes(): array
    {
        $sql = "SELECT * FROM times WHERE is_active = 1 ORDER BY time_start";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get schedule for a course
     */
    public function getSchedule(int $courseId): array
    {
        $sql = "SELECT s.*, d.day_name, ts.time_start, te.time_end, r.room_name
                FROM schedule_daytimes sd
                JOIN days d ON sd.day_id = d.day_id
                JOIN times ts ON sd.time_start_id = ts.time_id
                JOIN times te ON sd.time_end_id = te.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                WHERE sd.course_id = :course_id";
        return $this->db->fetchAll($sql, ['course_id' => $courseId]);
    }

    /**
     * Create schedule
     */
    public function createSchedule(array $data): int
    {
        return $this->db->insert('schedule_daytimes', $data);
    }

    /**
     * Update schedule
     */
    public function updateSchedule(int $scheduleId, array $data): bool
    {
        return $this->db->update('schedule_daytimes', $data, 'sd_id = :id', ['id' => $scheduleId]);
    }

    /**
     * Get faculty schedules for a term
     */
    public function getFacultySchedules(int $facultyId, int $semId, int $ayId): array
    {
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

        return $this->db->fetchAll($sql, [
            'faculty_id' => $facultyId,
            'ay_id' => $ayId,
            'sem_id' => $semId
        ]);
    }

    /**
     * Delete schedule
     */
    public function deleteSchedule(int $scheduleId): bool
    {
        return $this->db->delete('schedule_daytimes', 'sd_id = :id', ['id' => $scheduleId]);
    }

    /**
     * Get semester name by ID
     */
    public function getSemesterName(int $semId): ?string
    {
        $row = $this->db->fetchOne('SELECT semester_name FROM semesters WHERE sem_id = :id', ['id' => $semId]);
        return $row ? $row['semester_name'] : null;
    }
}
<?php

class StudentModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById($studId)
    {
        $sql = "SELECT s.*, c.year, p.program_code, p.description as program_desc
                FROM students s
                LEFT JOIN curriculums c ON s.curr_id = c.curr_id
                LEFT JOIN programs p ON c.program_id = p.program_id
                WHERE s.stud_id = :stud_id";
        return $this->db->fetchOne($sql, ['stud_id' => $studId]);
    }

    public function getEnrolments($studId)
    {
        $sql = "SELECT e.*, ay.ay_name, sem.semester_name, p.program_code
                FROM enrolments e
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE e.stud_id = :stud_id
                ORDER BY e.ay_id DESC, e.sem_id DESC";
        return $this->db->fetchAll($sql, ['stud_id' => $studId]);
    }

    public function getGrades($studId)
    {
        $sql = "SELECT g.*, c.code, c.description, sem.semester_name, ay.ay_name
                FROM student_grades g
                JOIN enrolments e ON g.enrolment_id = e.enrolment_id
                JOIN courses c ON g.course_id = c.course_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                WHERE e.stud_id = :stud_id
                ORDER BY e.ay_id DESC, e.sem_id DESC, c.code";
        return $this->db->fetchAll($sql, ['stud_id' => $studId]);
    }

    public function getSchedule($studId, $ayId = null, $semId = null)
    {
        $params = ['stud_id' => $studId];
        $where = '';

        $sql = "SELECT s.class_no, s.sched_id, c.code, c.description, c.units,
                       p.lname as prof_lname, p.fname as prof_fname,
                       d.day_name, t.time_start, t2.time_end, r.room_name,
                       ay.ay_name, sem.semester_name
                FROM enrolments e
                JOIN schedules sch ON sch.course_id IN (
                    SELECT DISTINCT c.course_id
                    FROM courses c
                    JOIN enrolments en ON en.program_id = c.program_id AND en.stud_id = :stud_id
                )
                JOIN courses c ON sch.course_id = c.course_id
                LEFT JOIN schedule_daytimes sd ON sch.sched_id = sd.sched_id
                LEFT JOIN days d ON sd.day_id = d.day_id
                LEFT JOIN times t ON sd.time_start_id = t.time_id
                LEFT JOIN times t2 ON sd.time_end_id = t.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                LEFT JOIN personnel p ON sch.p_id = p.p_id
                JOIN academic_years ay ON sch.ay_id = ay.ay_id
                JOIN semesters sem ON sch.sem_id = sem.sem_id
                WHERE e.stud_id = :stud_id AND sch.ay_id = e.ay_id AND sch.sem_id = e.sem_id";

        if ($ayId) {
            $where .= " AND sch.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where .= " AND sch.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql .= $where . " ORDER BY d.day_id, t.time_start";
        return $this->db->fetchAll($sql, $params);
    }

    public function getDocumentRequests($studId)
    {
        $sql = "SELECT dr.*, cd.dept_name
                FROM document_requests dr
                LEFT JOIN clearance_departments cd ON dr.processed_by = cd.dept_id
                WHERE dr.stud_id = :stud_id
                ORDER BY dr.request_date DESC";
        return $this->db->fetchAll($sql, ['stud_id' => $studId]);
    }

    public function createDocumentRequest($studId, $type)
    {
        $data = [
            'stud_id' => $studId,
            'document_type' => $type,
            'status' => 'Pending',
        ];
        return $this->db->insert('document_requests', $data);
    }

    public function calculateGPA($grades)
    {
        if (empty($grades)) return 0;
        $totalPoints = 0;
        $totalUnits = 0;
        foreach ($grades as $grade) {
            if ($grade['final_grade'] === null) continue;
            $units = $grade['units'] ?? 0;
            $totalPoints += $grade['final_grade'] * $units;
            $totalUnits += $units;
        }
        return $totalUnits > 0 ? round($totalPoints / $totalUnits, 2) : 0;
    }
}

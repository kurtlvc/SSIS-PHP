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
        $sql = "SELECT g.*, c.code, c.description, c.units, sem.semester_name, ay.ay_name
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

        $sql = "SELECT sch.class_no, sch.sched_id, c.code, c.description, c.units,
                       p.lname as prof_lname, p.fname as prof_fname,
                       ay.ay_name, sem.semester_name,
                       GROUP_CONCAT(DISTINCT CONCAT(d.day_code, ' ',
                           TIME_FORMAT(ts.time_start, '%H:%i'), '-',
                           TIME_FORMAT(te.time_end, '%H:%i'), ' ', r.room_name)
                           ORDER BY d.day_id SEPARATOR ' / ') AS schedule_info
                FROM enrolments e
                JOIN enrolment_courses ec ON e.enrolment_id = ec.enrolment_id
                JOIN schedules sch ON ec.sched_id = sch.sched_id
                JOIN courses c ON sch.course_id = c.course_id
                LEFT JOIN schedule_daytimes sd ON sch.sched_id = sd.sched_id
                LEFT JOIN days d ON sd.day_id = d.day_id
                LEFT JOIN times ts ON sd.time_start_id = ts.time_id
                LEFT JOIN times te ON sd.time_end_id = te.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                LEFT JOIN personnel p ON sch.p_id = p.p_id
                JOIN academic_years ay ON sch.ay_id = ay.ay_id
                JOIN semesters sem ON sch.sem_id = sem.sem_id
                WHERE e.stud_id = :stud_id";

        if ($ayId) {
            $where .= " AND sch.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where .= " AND sch.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql .= $where . "
                GROUP BY sch.sched_id, sch.class_no, c.code, c.description, c.units,
                         p.lname, p.fname, ay.ay_name, sem.semester_name
                ORDER BY MIN(d.day_id), MIN(ts.time_start), c.code";
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

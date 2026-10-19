<?php

class EnrolmentModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAll($ayId = null, $semId = null)
    {
        $params = [];
        $where = 'WHERE 1=1';

        if ($ayId) {
            $where .= " AND e.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where .= " AND e.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql = "SELECT e.*, s.ref_no, s.lname, s.fname,
                       ay.ay_name, sem.semester_name, p.program_code
                FROM enrolments e
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                {$where}
                ORDER BY e.ay_id DESC, e.sem_id DESC, s.lname, s.fname";
        return $this->db->fetchAll($sql, $params);
    }

    public function findById($enrolmentId)
    {
        $sql = "SELECT e.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       ay.ay_name, sem.semester_name, p.program_code, p.description as program_desc
                FROM enrolments e
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE e.enrolment_id = :enrolment_id";
        return $this->db->fetchOne($sql, ['enrolment_id' => $enrolmentId]);
    }

    public function getStudentEnrolments($studId)
    {
        $sql = "SELECT e.*, ay.ay_name, sem.semester_name
                FROM enrolments e
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                WHERE e.stud_id = :stud_id
                ORDER BY e.ay_id DESC, e.sem_id DESC";
        return $this->db->fetchAll($sql, ['stud_id' => $studId]);
    }

    public function getGradesToEncode($ayId = null, $semId = null, $courseId = null)
    {
        $params = [];
        $where = [];

        if ($ayId) {
            $where[] = "e.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where[] = "e.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql = "SELECT e.enrolment_id, e.stud_id, s.ref_no, s.lname, s.fname,
                       c.course_id, c.code, c.description, c.units,
                       ay.ay_name, sem.semester_name
                FROM enrolments e
                JOIN students s ON e.stud_id = s.stud_id
                JOIN courses c ON c.course_id IN (
                    SELECT DISTINCT c2.course_id FROM courses c2
                    JOIN programs p ON c2.program_id = p.program_id
                    WHERE p.program_id = e.program_id
                )
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                WHERE e.is_approved = 1 AND e.isassessed = 1";

        if (!empty($where)) {
            $sql .= " AND " . implode(" AND ", $where);
        }

        $sql .= " AND NOT EXISTS (
            SELECT 1 FROM student_grades g WHERE g.enrolment_id = e.enrolment_id AND g.course_id = c.course_id
        )
        ORDER BY e.ay_id DESC, e.sem_id DESC, s.lname, c.code";

        return $this->db->fetchAll($sql, $params);
    }

    public function getActiveAcademicYearSemester()
    {
        $sql = "SELECT ay.ay_id, ay.ay_name, sem.sem_id, sem.semester_name
                FROM academic_years ay
                CROSS JOIN semesters sem
                WHERE ay.is_active = 1 AND sem.is_active = 1
                LIMIT 1";
        return $this->db->fetchOne($sql);
    }

    public function getAllAcademicYears()
    {
        $sql = "SELECT * FROM academic_years ORDER BY ay_name DESC";
        return $this->db->fetchAll($sql);
    }

    public function getAllSemesters()
    {
        $sql = "SELECT * FROM semesters ORDER BY sem_id";
        return $this->db->fetchAll($sql);
    }
}

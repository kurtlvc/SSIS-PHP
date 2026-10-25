<?php

class ClearanceModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getAllDepartments()
    {
        $sql = "SELECT * FROM clearance_departments WHERE is_active = 1 ORDER BY dept_name";
        return $this->db->fetchAll($sql);
    }

    public function getClearanceQueue($semId = null, $deptId = null)
    {
        $params = [];
        $where = "WHERE 1=1";

        if ($semId) {
            $where .= " AND c.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }
        if ($deptId) {
            $where .= " AND c.department_id = :dept_id";
            $params['dept_id'] = $deptId;
        }

        $sql = "SELECT c.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       sem.semester_name, d.dept_name
                FROM clearance_records c
                JOIN students s ON c.stud_id = s.stud_id
                JOIN semesters sem ON c.sem_id = sem.sem_id
                JOIN clearance_departments d ON c.department_id = d.dept_id
                {$where}
                ORDER BY c.status, s.lname, s.fname";
        return $this->db->fetchAll($sql, $params);
    }

    public function getStudentClearance($studId, $semId = null)
    {
        $params = ['stud_id' => $studId];
        $where = "WHERE c.stud_id = :stud_id";

        if ($semId) {
            $where .= " AND c.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql = "SELECT c.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       sem.semester_name, d.dept_name,
                       per.lname as cleared_lname, per.fname as cleared_fname
                FROM clearance_records c
                JOIN students s ON c.stud_id = s.stud_id
                JOIN semesters sem ON c.sem_id = sem.sem_id
                JOIN clearance_departments d ON c.department_id = d.dept_id
                LEFT JOIN personnel per ON c.cleared_by = per.p_id
                {$where}
                ORDER BY c.status, d.dept_name";
        return $this->db->fetchAll($sql, $params);
    }

    public function createRecord($studId, $semId, $departmentId)
    {
        $data = [
            'stud_id' => $studId,
            'sem_id' => $semId,
            'department_id' => $departmentId,
            'status' => 'Pending',
        ];
        return $this->db->insert('clearance_records', $data);
    }

    public function updateStatus($recordId, $status, $personnelId)
    {
        $data = [
            'status' => $status,
            'cleared_by' => $personnelId ?: null,
            'cleared_at' => $status === 'Cleared' ? date('Y-m-d H:i:s') : null,
        ];
        return $this->db->update('clearance_records', $data, 'clearance_id = :id', ['id' => $recordId]);
    }

    /**
     * Ensure every active department has a clearance row for a student/term.
     */
    public function ensureRecordsForStudent(int $studId, int $semId): void
    {
        $sql = "INSERT INTO clearance_records (stud_id, sem_id, department_id, status)
                SELECT :stud_id, :sem_id, d.dept_id, 'Pending'
                FROM clearance_departments d
                WHERE d.is_active = 1
                  AND NOT EXISTS (
                      SELECT 1 FROM clearance_records cr
                      WHERE cr.stud_id = :stud_id_check
                        AND cr.sem_id = :sem_id_check
                        AND cr.department_id = d.dept_id
                  )";
        $this->db->query($sql, [
            'stud_id' => $studId,
            'sem_id' => $semId,
            'stud_id_check' => $studId,
            'sem_id_check' => $semId,
        ]);
    }

    /**
     * Ensure approved students in a term all have clearance rows.
     */
    public function ensureRecordsForTerm(int $semId): void
    {
        $sql = "INSERT INTO clearance_records (stud_id, sem_id, department_id, status)
                SELECT e.stud_id, e.sem_id, d.dept_id, 'Pending'
                FROM enrolments e
                CROSS JOIN clearance_departments d
                WHERE e.sem_id = :sem_id
                  AND e.is_approved = 1
                  AND d.is_active = 1
                  AND COALESCE(e.enrol_status, '') NOT IN ('Dropped', 'Rejected')
                  AND NOT EXISTS (
                      SELECT 1 FROM clearance_records cr
                      WHERE cr.stud_id = e.stud_id
                        AND cr.sem_id = e.sem_id
                        AND cr.department_id = d.dept_id
                  )";
        $this->db->query($sql, ['sem_id' => $semId]);
    }

    public function getDepartmentsByStudent($studId, $semId)
    {
        $sql = "SELECT d.*, c.status, c.clearance_id, c.cleared_at
                FROM clearance_departments d
                LEFT JOIN clearance_records c ON c.stud_id = :stud_id AND c.sem_id = :sem_id AND c.department_id = d.dept_id
                WHERE d.is_active = 1
                ORDER BY d.dept_name";
        return $this->db->fetchAll($sql, ['stud_id' => $studId, 'sem_id' => $semId]);
    }

    /**
     * Get department statistics for a semester
     */
    public function getDepartmentStats($semId = null)
    {
        $params = [];

        $sql = "SELECT d.dept_id, d.dept_name, d.dept_code,
                       COUNT(cr.clearance_id) as total,
                       SUM(CASE WHEN cr.status = 'Cleared' THEN 1 ELSE 0 END) as cleared,
                       SUM(CASE WHEN cr.status = 'Pending' THEN 1 ELSE 0 END) as pending,
                       SUM(CASE WHEN cr.status = 'On-Hold' THEN 1 ELSE 0 END) as on_hold
                FROM clearance_departments d
                LEFT JOIN clearance_records cr ON cr.department_id = d.dept_id";

        if ($semId) {
            $sql .= " AND cr.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql .= " WHERE d.is_active = 1
                GROUP BY d.dept_id, d.dept_name, d.dept_code
                ORDER BY d.dept_name";

        return $this->db->fetchAll($sql, $params);
    }
}

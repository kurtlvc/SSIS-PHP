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

    public function getClearanceQueue($semId = null)
    {
        $params = [];
        $where = "WHERE 1=1";

        if ($semId) {
            $where .= " AND c.sem_id = :sem_id";
            $params['sem_id'] = $semId;
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
            'cleared_by' => $personnelId,
            'cleared_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update('clearance_records', $data, 'clearance_id = :id', ['id' => $recordId]);
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
}

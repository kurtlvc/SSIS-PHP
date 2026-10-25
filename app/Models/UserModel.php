<?php

class UserModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findByLogin($login)
    {
        $sql = "SELECT a.*, 
                       s.stud_id as student_id,
                       s.lname as student_lname,
                       s.fname as student_fname,
                       p.p_id as personnel_id,
                       p.lname as personnel_lname,
                       p.fname as personnel_fname
                FROM accounts a
                LEFT JOIN students s ON a.owner_type = 'student' AND a.owner_id = s.stud_id
                LEFT JOIN personnel p ON a.owner_type = 'personnel' AND a.owner_id = p.p_id
                WHERE a.login = :login AND a.is_active = 1";
        return $this->db->fetchOne($sql, ['login' => $login]);
    }

    public function findById($id)
    {
        $sql = "SELECT a.*, 
                       s.stud_id as student_id,
                       s.lname as student_lname,
                       s.fname as student_fname,
                       s.emailaddress as student_email,
                       p.lname as personnel_lname,
                       p.fname as personnel_fname
                FROM accounts a
                LEFT JOIN students s ON a.owner_type = 'student' AND a.owner_id = s.stud_id
                LEFT JOIN personnel p ON a.owner_type = 'personnel' AND a.owner_id = p.p_id
                WHERE a.id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }

    public function create($data)
    {
        $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        unset($data['password']);
        return $this->db->insert('accounts', $data);
    }

    public function getAll($role = null)
    {
        $sql = "SELECT a.*, 
                       s.lname as student_lname, s.fname as student_fname,
                       p.lname as personnel_lname, p.fname as personnel_fname
                FROM accounts a
                LEFT JOIN students s ON a.owner_type = 'student' AND a.owner_id = s.stud_id
                LEFT JOIN personnel p ON a.owner_type = 'personnel' AND a.owner_id = p.p_id
                WHERE 1=1";
        $params = [];
        if ($role) {
            $sql .= " AND a.role = :role";
            $params['role'] = $role;
        }
        $sql .= " ORDER BY a.created_at DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function update($id, $data)
    {
        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
            unset($data['password']);
        }
        return $this->db->update('accounts', $data, 'id = :id', ['id' => $id]);
    }

    public function deactivate($id)
    {
        return $this->db->update('accounts', ['is_active' => 0], 'id = :id', ['id' => $id]);
    }

    public function getPersonnelList()
    {
        $sql = "SELECT p_id, lname, fname, title FROM personnel WHERE is_active = 1 ORDER BY lname";
        return $this->db->fetchAll($sql);
    }

    public function getStudentsList()
    {
        $sql = "SELECT s.stud_id, s.ref_no, s.lname, s.fname, s.mname, s.emailaddress, p.program_code, p.description, c.year
                FROM students s
                LEFT JOIN curriculums c ON s.curr_id = c.curr_id
                LEFT JOIN programs p ON c.program_id = p.program_id
                ORDER BY s.lname, s.fname";
        return $this->db->fetchAll($sql);
    }
}

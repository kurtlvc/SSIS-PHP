<?php

class DocumentRequestModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getQueue($semId = null)
    {
        $params = [];
        $where = "WHERE 1=1";

        if ($semId) {
            $where .= " AND dr.request_date >= (SELECT ay_id FROM academic_years WHERE is_active = 1 LIMIT 1)";
        }

        $sql = "SELECT dr.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       sem.semester_name, p.program_code,
                       pr.lname as processed_lname, pr.fname as processed_fname
                FROM document_requests dr
                JOIN students s ON dr.stud_id = s.stud_id
                LEFT JOIN enrolments e ON e.stud_id = s.stud_id
                LEFT JOIN semesters sem ON e.sem_id = sem.sem_id
                LEFT JOIN programs p ON e.program_id = p.program_id
                LEFT JOIN personnel pr ON dr.processed_by = pr.p_id
                {$where}
                ORDER BY dr.request_date DESC";
        return $this->db->fetchAll($sql, $params);
    }

    public function updateStatus($requestId, $status, $personnelId = null)
    {
        $data = [
            'status' => $status,
            'processed_by' => $personnelId,
            'processed_at' => date('Y-m-d H:i:s'),
        ];
        return $this->db->update('document_requests', $data, 'request_id = :id', ['id' => $requestId]);
    }

    public function findById($requestId)
    {
        $sql = "SELECT dr.*, s.ref_no, s.lname, s.fname, s.emailaddress
                FROM document_requests dr
                JOIN students s ON dr.stud_id = s.stud_id
                WHERE dr.request_id = :id";
        return $this->db->fetchOne($sql, ['id' => $requestId]);
    }
}

<?php

class PaymentModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function getDashboardSummary($ayId = null, $semId = null)
    {
        $where = "WHERE 1=1";
        $params = [];

        if ($ayId) {
            $where .= " AND p.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where .= " AND p.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql = "SELECT 
                       COUNT(p.payment_id) as total_transactions,
                       SUM(p.amount_paid) as total_amount,
                       p.payment_date
                FROM payments p
                JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                {$where}
                GROUP BY DATE(p.payment_date)
                ORDER BY p.payment_date DESC
                LIMIT 30";
        return $this->db->fetchAll($sql, $params);
    }

    public function getPendingPayments()
    {
        $sql = "SELECT e.enrolment_id, s.ref_no, s.lname, s.fname,
                       e.total_units, ay.ay_name, sem.semester_name,
                       p.program_code
                FROM enrolments e
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE e.isassessed = 1 AND e.is_approved = 1 AND e.is_cleared = 0
                ORDER BY s.lname, s.fname";
        return $this->db->fetchAll($sql);
    }

    public function getPaymentsByEnrolment($enrolmentId)
    {
        $sql = "SELECT p.*, per.lname as cashier_lname, per.fname as cashier_fname
                FROM payments p
                JOIN personnel per ON p.cashier_id = per.p_id
                WHERE p.enrolment_id = :enrolment_id
                ORDER BY p.payment_date DESC";
        return $this->db->fetchAll($sql, ['enrolment_id' => $enrolmentId]);
    }

    public function findReceipt($receiptNo)
    {
        $sql = "SELECT p.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       e.total_units, ay.ay_name, sem.semester_name,
                       pr.lname as cashier_lname, pr.fname as cashier_fname,
                       pgm.program_code, pgm.description as program_desc
                FROM payments p
                JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs pgm ON e.program_id = pgm.program_id
                JOIN personnel pr ON p.cashier_id = pr.p_id
                WHERE p.receipt_no = :receipt_no";
        return $this->db->fetchOne($sql, ['receipt_no' => $receiptNo]);
    }

    public function generateReceiptNo($ayId)
    {
        $ayCode = "AY" . $ayId;
        $sql = "SELECT COUNT(*) + 1 as next_num FROM payments 
                WHERE MONTH(payment_date) = MONTH(NOW()) AND YEAR(payment_date) = YEAR(NOW())";
        $row = $this->db->fetchOne($sql);
        $nextNum = $row ? $row['next_num'] : 1;
        $year = date('Y');
        return sprintf("RCPT-%s-%s-%05d", $year, $ayCode, $nextNum);
    }

    public function create($data)
    {
        return $this->db->insert('payments', $data);
    }
}

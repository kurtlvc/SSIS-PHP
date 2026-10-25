<?php

class PaymentModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Completed collections grouped by payment date.
     */
    public function getDashboardSummary($ayId = null, $semId = null)
    {
        $where = "WHERE p.status = 'completed'";
        $params = [];

        if ($ayId) {
            $where .= " AND e.ay_id = :ay_id";
            $params['ay_id'] = $ayId;
        }
        if ($semId) {
            $where .= " AND e.sem_id = :sem_id";
            $params['sem_id'] = $semId;
        }

        $sql = "SELECT COUNT(p.payment_id) AS total_transactions,
                       COALESCE(SUM(p.amount_paid), 0) AS total_amount,
                       DATE(p.payment_date) AS payment_date
                FROM payments p
                JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                {$where}
                GROUP BY DATE(p.payment_date)
                ORDER BY payment_date DESC
                LIMIT 30";
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Approved assessments that still have a balance.
     */
    public function getPendingPayments()
    {
        $sql = "SELECT e.enrolment_id, s.ref_no, s.lname, s.fname,
                       e.total_units, ay.ay_name, sem.semester_name,
                       p.program_code, ea.assessment_id, ea.net_amount, ea.status AS assessment_status,
                       COALESCE((SELECT SUM(pp.amount_paid) FROM payments pp
                                 WHERE pp.enrolment_id = e.enrolment_id AND pp.status = 'completed'), 0) AS amount_paid
                FROM enrolments e
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                LEFT JOIN enrolment_assessments ea ON ea.assessment_id = (
                    SELECT MAX(ea2.assessment_id)
                    FROM enrolment_assessments ea2
                    WHERE ea2.enrolment_id = e.enrolment_id
                )
                WHERE e.isassessed = 1
                  AND e.is_approved = 1
                  AND e.is_cleared = 0
                  AND COALESCE(e.enrol_status, '') NOT IN ('Dropped', 'Rejected')
                  AND (ea.status IS NULL OR ea.status IN ('pending', 'partial'))
                ORDER BY s.lname, s.fname";
        return $this->db->fetchAll($sql);
    }

    public function getPaymentsByEnrolment($enrolmentId)
    {
        $sql = "SELECT p.*, per.lname AS cashier_lname, per.fname AS cashier_fname,
                       pt.gateway_reference, pt.gateway_name, pt.status AS transaction_status
                FROM payments p
                LEFT JOIN personnel per ON p.cashier_id = per.p_id
                LEFT JOIN payment_transactions pt ON pt.transaction_id = (
                    SELECT MAX(pt2.transaction_id) FROM payment_transactions pt2
                    WHERE pt2.payment_id = p.payment_id
                )
                WHERE p.enrolment_id = :enrolment_id
                ORDER BY p.payment_date DESC, p.payment_id DESC";
        return $this->db->fetchAll($sql, ['enrolment_id' => $enrolmentId]);
    }

    public function findReceipt($receiptNo)
    {
        $sql = "SELECT p.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       e.total_units, e.ay_id, e.sem_id, ay.ay_name, sem.semester_name,
                       pr.lname AS cashier_lname, pr.fname AS cashier_fname,
                       pgm.program_code, pgm.description AS program_desc,
                       pt.gateway_reference, pt.gateway_name
                FROM payments p
                JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs pgm ON e.program_id = pgm.program_id
                LEFT JOIN personnel pr ON p.cashier_id = pr.p_id
                LEFT JOIN payment_transactions pt ON pt.transaction_id = (
                    SELECT MAX(pt2.transaction_id) FROM payment_transactions pt2
                    WHERE pt2.payment_id = p.payment_id
                )
                WHERE p.receipt_no = :receipt_no";
        return $this->db->fetchOne($sql, ['receipt_no' => $receiptNo]);
    }

    public function findById($paymentId)
    {
        return $this->db->fetchOne('SELECT * FROM payments WHERE payment_id = :id', ['id' => $paymentId]);
    }

    public function generateReceiptNo($ayId)
    {
        $row = $this->db->fetchOne('SELECT COALESCE(MAX(payment_id), 0) + 1 AS next_num FROM payments');
        $nextNum = $row ? (int) $row['next_num'] : 1;
        return sprintf('RCPT-%s-AY%s-%05d', date('Y'), $ayId, $nextNum);
    }

    public function create($data)
    {
        return $this->db->insert('payments', $data);
    }

    public function getAssessmentByEnrolment(int $enrolmentId): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM enrolment_assessments WHERE enrolment_id = :id ORDER BY assessment_id DESC LIMIT 1',
            ['id' => $enrolmentId]
        );
    }

    public function getTotalCompleted(int $enrolmentId): float
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount_paid), 0) AS total
             FROM payments WHERE enrolment_id = :id AND status = 'completed'",
            ['id' => $enrolmentId]
        );
        return $row ? (float) $row['total'] : 0.0;
    }
}

<?php

class ApiCashierController extends BaseController
{
    private $paymentModel;
    private $enrolmentModel;
    private $userModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel    = new PaymentModel();
        $this->enrolmentModel  = new EnrolmentModel();
        $this->userModel       = new UserModel();
        $this->db              = Database::getInstance();
    }

    private function getJsonInput()
    {
        $data = json_decode(file_get_contents('php://input') ?: '', true);
        return is_array($data) ? $data : [];
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $ayId  = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;

        App::json([
            'success'            => true,
            'summary'            => $this->paymentModel->getDashboardSummary($ayId, $semId),
            'pending_payments'   => $this->paymentModel->getPendingPayments(),
            'academic_years'     => $this->enrolmentModel->getAllAcademicYears(),
            'semesters'          => $this->enrolmentModel->getAllSemesters(),
        ]);
    }

    public function assessments($studId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $sql = "SELECT e.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       p.program_code, ay.ay_name, sem.semester_name
                FROM enrolments e
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE e.stud_id = :stud_id AND e.is_approved = 1
                ORDER BY e.ay_id DESC, e.sem_id DESC LIMIT 1";

        $enrolment = $this->db->fetchOne($sql, ['stud_id' => $studId]);
        if (!$enrolment) {
            App::json(['success' => false, 'message' => 'Active enrolment not found.'], 404);
        }

        $payments = $this->paymentModel->getPaymentsByEnrolment($enrolment['enrolment_id']);
        $paidTotal = 0;
        foreach ($payments as $p) {
            $paidTotal += (float) ($p['amount_paid'] ?? 0);
        }
        $balance = (float) ($enrolment['total_units'] ?? 0) * 500 - $paidTotal;

        App::json([
            'success'     => true,
            'enrolment'   => $enrolment,
            'payments'    => $payments,
            'paid_total'  => $paidTotal,
            'balance'     => max(0, $balance),
        ]);
    }

    public function storePayment()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $input = $this->getJsonInput();
        $enrolmentId = (int) ($input['enrolment_id'] ?? 0);
        $amount       = (float) ($input['amount_paid'] ?? 0);
        $ayId         = (int) ($input['ay_id'] ?? 1);

        if ($enrolmentId <= 0 || $amount <= 0) {
            App::json(['success' => false, 'message' => 'Invalid payment data.'], 400);
        }

        $receiptNo = $this->paymentModel->generateReceiptNo($ayId);

        $this->db->beginTransaction();
        try {
            $this->paymentModel->create([
                'enrolment_id' => $enrolmentId,
                'receipt_no'   => $receiptNo,
                'amount_paid'  => $amount,
                'cashier_id'   => $_SESSION['owner_id'] ?? 0,
            ]);
            $this->db->update('enrolments', ['isassessed' => 1], 'enrolment_id = :id', ['id' => $enrolmentId]);
            $this->db->commit();
            App::json(['success' => true, 'message' => 'Payment processed.', 'receipt_no' => $receiptNo], 201);
        } catch (Exception $e) {
            $this->db->rollback();
            App::json(['success' => false, 'message' => 'Payment failed.'], 500);
        }
    }

    public function receipt($receiptNo)
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $receipt = $this->paymentModel->findReceipt($receiptNo);
        if (!$receipt) {
            App::json(['success' => false, 'message' => 'Receipt not found.'], 404);
        }

        App::json(['success' => true, 'receipt' => $receipt]);
    }

    public function dailyReport()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $day = $_GET['date'] ?? date('Y-m-d');
        $sql = "SELECT p.payment_id, p.receipt_no, p.amount_paid, p.payment_date,
                       s.ref_no, s.lname, s.fname, pr.lname as cashier_lname, pr.fname as cashier_fname
                FROM payments p
                JOIN enrolments e ON e.enrolment_id = p.enrolment_id
                JOIN students s ON s.stud_id = e.stud_id
                LEFT JOIN personnel pr ON pr.p_id = p.cashier_id
                WHERE DATE(p.payment_date) = :day
                ORDER BY p.payment_date DESC";

        $rows = $this->db->fetchAll($sql, ['day' => $day]);
        $total = 0;
        foreach ($rows as $r) {
            $total += (float) ($r['amount_paid'] ?? 0);
        }

        App::json([
            'success'      => true,
            'date'         => $day,
            'transactions' => $rows,
            'count'        => count($rows),
            'total_amount' => $total,
        ]);
    }
}

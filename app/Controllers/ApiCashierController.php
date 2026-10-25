<?php

class ApiCashierController extends BaseController
{
    private $paymentModel;
    private $enrolmentModel;
    private $userModel;
    private $paymentGateway;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel    = new PaymentModel();
        $this->enrolmentModel  = new EnrolmentModel();
        $this->userModel       = new UserModel();
        $this->paymentGateway  = new PaymentGatewayService();
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

        $assessment = $this->paymentModel->getAssessmentByEnrolment((int) $enrolment['enrolment_id']);
        $payments = $this->paymentModel->getPaymentsByEnrolment((int) $enrolment['enrolment_id']);
        $paidTotal = $this->paymentModel->getTotalCompleted((int) $enrolment['enrolment_id']);
        $balance = $assessment ? max(0, (float) $assessment['net_amount'] - $paidTotal) : 0;

        App::json([
            'success'     => true,
            'enrolment'   => $enrolment,
            'assessment'  => $assessment,
            'payments'    => $payments,
            'paid_total'  => $paidTotal,
            'balance'     => $balance,
        ]);
    }

    public function storePayment()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $input = $this->getJsonInput();
        $enrolmentId = (int) ($input['enrolment_id'] ?? 0);
        $amount       = (float) ($input['amount_paid'] ?? 0);
        $gateway      = $input['gateway'] ?? 'simulated';

        if ($enrolmentId <= 0 || $amount <= 0) {
            App::json(['success' => false, 'message' => 'Invalid payment data.'], 400);
        }

        $enrolment = $this->enrolmentModel->findById($enrolmentId);
        if (!$enrolment) {
            App::json(['success' => false, 'message' => 'Enrolment not found.'], 404);
        }

        $assessment = $this->paymentModel->getAssessmentByEnrolment((int) $enrolmentId);
        if (!$assessment) {
            App::json(['success' => false, 'message' => 'No assessment exists for this enrolment.'], 400);
        }

        $balance = (float) $assessment['net_amount'] - $this->paymentModel->getTotalCompleted((int) $enrolmentId);
        if ($amount > $balance + 0.01) {
            App::json(['success' => false, 'message' => 'Amount exceeds the remaining balance.'], 400);
        }

        $receiptNo = $this->paymentModel->generateReceiptNo((int) $enrolment['ay_id']);
        $paymentId = (int) $this->paymentModel->create([
            'enrolment_id' => $enrolmentId,
            'receipt_no'   => $receiptNo,
            'amount_paid'  => $amount,
            'payment_date' => date('Y-m-d H:i:s'),
            'cashier_id'   => $_SESSION['owner_id'] ?? null,
            'status'       => 'pending',
        ]);

        $gatewayResult = $this->paymentGateway->processPayment($paymentId, $gateway);

        if ($gatewayResult['success']) {
            App::json([
                'success' => true,
                'message' => 'Payment processed successfully.',
                'receipt_no' => $gatewayResult['receipt_no'],
                'gateway_reference' => $gatewayResult['gateway_reference'],
                'auth_code' => $gatewayResult['auth_code'],
            ], 201);
        } else {
            $this->db->update('payments', ['status' => 'failed'], 'payment_id = :id', ['id' => $paymentId]);
            App::json(['success' => false, 'message' => 'Payment failed: ' . $gatewayResult['error']], 500);
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
                  AND p.status = 'completed'
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

<?php

require_once __DIR__ . '/../Services/PaymentGatewayService.php';
require_once __DIR__ . '/../Services/EmailService.php';

class CashierController extends BaseController
{
    private $paymentModel;
    private $enrolmentModel;
    private $userModel;
    private $paymentGateway;
    private $emailService;

    public function __construct()
    {
        parent::__construct();
        $this->paymentModel = new PaymentModel();
        $this->enrolmentModel = new EnrolmentModel();
        $this->userModel = new UserModel();
        $this->paymentGateway = new PaymentGatewayService();
        $this->emailService = new EmailService();
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $ayId = $_GET['ay_id'] ?? null;
        $semId = $_GET['sem_id'] ?? null;
        $academicYears = $this->enrolmentModel->getAllAcademicYears();
        $semesters = $this->enrolmentModel->getAllSemesters();
        $summary = $this->paymentModel->getDashboardSummary($ayId, $semId);
        $pendingPayments = $this->paymentModel->getPendingPayments();

        App::view('cashier/dashboard', [
            'summary' => $summary,
            'pendingPayments' => $pendingPayments,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'pageTitle' => 'Cashier Dashboard',
            'module' => 'cashier',
        ]);
    }

    public function paymentProcess()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $enrolmentId = $_GET['enrolment_id'] ?? null;
        $enrolment = null;

        if ($enrolmentId) {
            $enrolment = $this->enrolmentModel->findById($enrolmentId);
        }

        $assessment = $enrolment
            ? $this->paymentModel->getAssessmentByEnrolment((int) $enrolment['enrolment_id'])
            : null;
        $balance = $assessment
            ? max(0, (float) $assessment['net_amount'] - $this->paymentModel->getTotalCompleted((int) $enrolment['enrolment_id']))
            : null;

        $cashiers = $this->userModel->getPersonnelList();

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('cashier/payment_process', [
            'enrolment' => $enrolment,
            'assessment' => $assessment,
            'balance' => $balance,
            'cashiers' => $cashiers,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Process Payment',
            'module' => 'cashier',
        ]);
    }

    public function storePayment()
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/cashier/payments/process');
            return;
        }

        $enrolmentId = $_POST['enrolment_id'] ?? null;
        $amount = (float) ($_POST['amount_paid'] ?? 0);
        $gateway = $_POST['gateway'] ?? 'simulated';

        if (!$enrolmentId || $amount <= 0) {
            App::flash('error', 'Invalid payment data.');
            App::redirect('/cashier/payments/process');
            return;
        }

        $enrolment = $this->enrolmentModel->findById($enrolmentId);
        if (!$enrolment) {
            App::flash('error', 'Enrolment not found.');
            App::redirect('/cashier/payments/process');
            return;
        }

        $assessment = $this->paymentModel->getAssessmentByEnrolment((int) $enrolmentId);
        if (!$assessment) {
            App::flash('error', 'No assessment exists for this enrolment.');
            App::redirect('/cashier/payments/process?enrolment_id=' . $enrolmentId);
            return;
        }

        $balance = (float) $assessment['net_amount'] - $this->paymentModel->getTotalCompleted((int) $enrolmentId);
        if ($amount > $balance + 0.01) {
            App::flash('error', 'Amount exceeds the remaining balance of ₱' . number_format(max(0, $balance), 2));
            App::redirect('/cashier/payments/process?enrolment_id=' . $enrolmentId);
            return;
        }

        $receiptNo = $this->paymentModel->generateReceiptNo((int) $enrolment['ay_id']);
        $paymentId = (int) $this->paymentModel->create([
            'enrolment_id' => $enrolmentId,
            'receipt_no' => $receiptNo,
            'amount_paid' => $amount,
            'payment_date' => date('Y-m-d H:i:s'),
            'cashier_id' => $_SESSION['owner_id'] ?? null,
            'status' => 'pending',
        ]);

        $gatewayResult = $this->paymentGateway->processPayment($paymentId, $gateway);

        if ($gatewayResult['success']) {
            App::flash('success', 'Payment processed successfully. Receipt: ' . $gatewayResult['receipt_no']);
            App::redirect('/cashier/receipts/' . urlencode($gatewayResult['receipt_no']));
        } else {
            $this->db->update('payments', ['status' => 'failed'], 'payment_id = :id', ['id' => $paymentId]);
            App::flash('error', 'Payment failed: ' . $gatewayResult['error']);
            App::redirect('/cashier/payments/process?enrolment_id=' . $enrolmentId);
        }
    }

    public function receipt($receiptNo)
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin'])) return;

        $receipt = $this->paymentModel->findReceipt($receiptNo);

        if (!$receipt) {
            http_response_code(404);
            App::view('errors/404', ['pageTitle' => 'Receipt Not Found', 'module' => 'cashier']);
            return;
        }

        App::view('cashier/receipt', [
            'receipt' => $receipt,
            'pageTitle' => 'Official Receipt',
            'module' => 'cashier',
        ]);
    }

    public function receiptPdf($receiptNo)
    {
        if (!$this->requireAuth()) return;
        if (!$this->requireRole(['cashier', 'admin', 'student'])) return;

        $receipt = $this->paymentModel->findReceipt($receiptNo);

        if (!$receipt) {
            http_response_code(404);
            App::view('errors/404', ['pageTitle' => 'Receipt Not Found', 'module' => 'cashier']);
            return;
        }

        // For student access, verify ownership
        if ($this->middleware->student()) {
            $studId = $this->getStudentId();
            $ownsReceipt = $this->db->fetchOne(
                'SELECT 1 FROM payments p JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                 WHERE p.receipt_no = :receipt AND e.stud_id = :stud_id',
                ['receipt' => $receiptNo, 'stud_id' => $studId]
            );
            if (!$ownsReceipt) {
                http_response_code(403);
                App::view('errors/unauthorized', ['pageTitle' => 'Unauthorized', 'module' => 'cashier']);
                return;
            }
        }

        // Render print-optimized version (browser print-to-PDF)
        App::view('cashier/receipt', [
            'receipt' => $receipt,
            'pageTitle' => 'Official Receipt',
            'module' => 'cashier',
            'pdf_mode' => true,
        ]);
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

        App::view('cashier/daily_report', [
            'date' => $day,
            'transactions' => $rows,
            'count' => count($rows),
            'total' => $total,
            'pageTitle' => 'Daily Report',
            'module' => 'cashier',
        ]);
    }

    /**
     * Public receipt verification (no auth required)
     * Used by QR code on receipts
     */
    public function verifyReceipt($receiptNo)
    {
        $receipt = $this->paymentModel->findReceipt($receiptNo);

        if (!$receipt) {
            http_response_code(404);
            App::view('cashier/verify_receipt', [
                'receipt' => null,
                'pageTitle' => 'Receipt Verification',
                'module' => 'cashier',
                'verified' => false,
            ]);
            return;
        }

        // Only show completed payments
        $isVerified = $receipt['status'] === 'completed';

        App::view('cashier/verify_receipt', [
            'receipt' => $receipt,
            'pageTitle' => 'Receipt Verification',
            'module' => 'cashier',
            'verified' => $isVerified,
        ]);
    }
}

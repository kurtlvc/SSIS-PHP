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

        $cashiers = $this->userModel->getPersonnelList();

        $error = App::flash('error');
        $success = App::flash('success');

        App::view('cashier/payment_process', [
            'enrolment' => $enrolment,
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
        $amount = $_POST['amount_paid'] ?? 0;
        $ayId = $_POST['ay_id'] ?? 1;
        $gateway = $_POST['gateway'] ?? 'simulated'; // simulated, gcash, paymaya, bank

        if ($enrolmentId && $amount > 0) {
            // Get enrolment details for payment gateway
            $enrolment = $this->enrolmentModel->findById($enrolmentId);
            if (!$enrolment) {
                App::flash('error', 'Enrolment not found.');
                App::redirect('/cashier/payments/process');
                return;
            }

            // Process through simulated gateway
            $gatewayResult = $this->paymentGateway->processPayment($enrolmentId, $gateway);

            if ($gatewayResult['success']) {
                // Gateway success - payment already recorded by gateway service
                // The gateway service already created payment record and updated enrolment
                App::flash('success', "Payment processed successfully via {$gateway}. Reference: {$gatewayResult['gateway_reference']}");
                App::redirect('/cashier/receipts/' . $gatewayResult['gateway_reference']);
            } else {
                App::flash('error', 'Payment failed: ' . $gatewayResult['error']);
                App::redirect('/cashier/payments/process?enrolment_id=' . $enrolmentId);
            }
        } else {
            App::flash('error', 'Invalid payment data.');
            App::redirect('/cashier/payments/process');
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

        App::view('cashier/daily_report', [
            'date' => $day,
            'transactions' => $rows,
            'count' => count($rows),
            'total' => $total,
            'pageTitle' => 'Daily Report',
            'module' => 'cashier',
        ]);
    }
}

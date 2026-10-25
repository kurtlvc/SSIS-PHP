<?php

require_once __DIR__ . '/EmailService.php';
require_once __DIR__ . '/NotificationService.php';

class PaymentGatewayService
{
    private $db;
    private $config;
    private $emailService;
    private $notificationService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
        $this->emailService = new EmailService();
        $this->notificationService = new NotificationService();
    }

    /**
     * Process an existing pending payment.
     *
     * The demo gateway is deterministic: a valid pending payment succeeds.
     * Real provider integration can replace the response block below.
     */
    public function processPayment(int $paymentId, string $gateway = 'simulated'): array
    {
        $payment = $this->getPaymentDetails($paymentId);
        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }
        if ($payment['status'] === 'completed') {
            return ['success' => false, 'error' => 'Payment was already completed'];
        }
        if ($payment['status'] === 'refunded') {
            return ['success' => false, 'error' => 'Payment was refunded'];
        }

        $reference = $this->generateGatewayReference($gateway);
        $transactionId = $this->createTransaction($paymentId, $gateway, $reference, (float) $payment['amount_paid']);

        $gatewayResponse = [
            'gateway' => $gateway,
            'reference' => $reference,
            'amount' => (float) $payment['amount_paid'],
            'currency' => 'PHP',
            'timestamp' => date('c'),
            'status' => 'success',
            'simulated' => true,
            'auth_code' => 'AUTH' . strtoupper(substr(md5($reference), 0, 8)),
        ];

        $this->updateTransaction($transactionId, 'success', $gatewayResponse);
        $this->completePayment($paymentId, $gatewayResponse['auth_code'], $reference);

        return [
            'success' => true,
            'payment_id' => $paymentId,
            'transaction_id' => $transactionId,
            'gateway_reference' => $reference,
            'auth_code' => $gatewayResponse['auth_code'],
            'receipt_no' => $payment['receipt_no'],
            'message' => 'Payment processed successfully',
        ];
    }

    private function getPaymentDetails(int $paymentId): ?array
    {
        $sql = "SELECT p.*, s.ref_no, s.lname, s.fname, s.emailaddress, s.stud_id,
                       e.total_units, e.is_approved, ay.ay_name, sem.semester_name,
                       pgm.program_code, pgm.description AS program_desc
                FROM payments p
                JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs pgm ON e.program_id = pgm.program_id
                WHERE p.payment_id = :payment_id";
        return $this->db->fetchOne($sql, ['payment_id' => $paymentId]);
    }

    private function createTransaction(int $paymentId, string $gateway, string $reference, float $amount): int
    {
        return (int) $this->db->insert('payment_transactions', [
            'payment_id' => $paymentId,
            'gateway_reference' => $reference,
            'gateway_name' => $gateway,
            'amount' => $amount,
            'currency' => 'PHP',
            'status' => 'pending',
            'gateway_response' => json_encode(['status' => 'pending', 'simulated' => true]),
        ]);
    }

    private function updateTransaction(int $transactionId, string $status, array $response): void
    {
        $this->db->update('payment_transactions', [
            'status' => $status,
            'gateway_response' => json_encode($response),
            'processed_at' => date('Y-m-d H:i:s'),
        ], 'transaction_id = :id', ['id' => $transactionId]);
    }

    /**
     * Mark the payment complete and reconcile assessment/enrollment.
     */
    private function completePayment(int $paymentId, string $authCode, string $gatewayReference): void
    {
        $now = date('Y-m-d H:i:s');
        $payment = $this->db->fetchOne('SELECT * FROM payments WHERE payment_id = :id', ['id' => $paymentId]);
        if (!$payment) {
            return;
        }

        $this->db->update('payments', [
            'auth_code' => $authCode,
            'status' => 'completed',
            'completed_at' => $now,
        ], 'payment_id = :id', ['id' => $paymentId]);

        $enrolmentId = (int) $payment['enrolment_id'];
        $assessment = $this->db->fetchOne(
            'SELECT * FROM enrolment_assessments WHERE enrolment_id = :id ORDER BY assessment_id DESC LIMIT 1',
            ['id' => $enrolmentId]
        );
        $totalPaid = $this->db->fetchOne(
            "SELECT COALESCE(SUM(amount_paid), 0) AS total
             FROM payments WHERE enrolment_id = :id AND status = 'completed'",
            ['id' => $enrolmentId]
        );
        $paid = $totalPaid ? (float) $totalPaid['total'] : 0.0;
        $fullyPaid = !$assessment || $paid >= (float) $assessment['net_amount'];

        if ($assessment) {
            $this->db->update('enrolment_assessments', [
                'status' => $fullyPaid ? 'paid' : 'partial',
                'paid_at' => $fullyPaid ? $now : null,
            ], 'assessment_id = :id', ['id' => $assessment['assessment_id']]);
        }

        if ($fullyPaid) {
            $this->db->update('enrolments', [
                'is_cleared' => 1,
                'college_cleared' => 1,
                'cleared_at' => $now,
                'enrol_status' => 'Enrolled',
            ], 'enrolment_id = :id', ['id' => $enrolmentId]);

            $this->db->query(
                "UPDATE enrolment_carts SET status = 'paid' WHERE enrolment_id = :id",
                ['id' => $enrolmentId]
            );
        }

        $paymentData = $this->db->fetchOne(
            'SELECT p.*, s.ref_no, s.lname, s.fname, s.emailaddress, s.stud_id
             FROM payments p
             JOIN enrolments e ON p.enrolment_id = e.enrolment_id
             JOIN students s ON e.stud_id = s.stud_id
             WHERE p.payment_id = :id',
            ['id' => $paymentId]
        );

        if ($paymentData) {
            $this->notificationService->paymentNotification((int) $paymentData['stud_id'], [
                'amount' => $paymentData['amount_paid'],
                'receipt_no' => $paymentData['receipt_no'],
                'payment_id' => $paymentId,
            ]);

            $this->emailService->sendPaymentReceipt([
                'to_email' => $paymentData['emailaddress'],
                'to_name' => $paymentData['fname'] . ' ' . $paymentData['lname'],
                'student_ref' => $paymentData['ref_no'],
                'receipt_no' => $paymentData['receipt_no'],
                'amount' => $paymentData['amount_paid'],
                'payment_date' => $paymentData['payment_date'],
                'transaction_ref' => $gatewayReference,
            ]);
        }
    }

    private function generateGatewayReference(string $gateway): string
    {
        $prefix = match ($gateway) {
            'gcash' => 'GCASH',
            'paymaya' => 'PYMYA',
            'bank' => 'BANK',
            default => 'SIM',
        };
        return $prefix . '-' . date('YmdHis') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function getTransactionHistory(int $paymentId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM payment_transactions WHERE payment_id = :payment_id ORDER BY created_at DESC',
            ['payment_id' => $paymentId]
        );
    }

    /**
     * Refund a completed payment (demo behaviour).
     */
    public function refundPayment(int $paymentId, float $amount, string $reason): array
    {
        $payment = $this->getPaymentDetails($paymentId);
        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }
        if ($payment['status'] !== 'completed') {
            return ['success' => false, 'error' => 'Payment not completed'];
        }
        if ($amount <= 0 || $amount > (float) $payment['amount_paid']) {
            return ['success' => false, 'error' => 'Invalid refund amount'];
        }

        $reference = $this->generateGatewayReference('simulated');
        $transactionId = (int) $this->db->insert('payment_transactions', [
            'payment_id' => $paymentId,
            'gateway_reference' => $reference,
            'gateway_name' => 'simulated',
            'amount' => -$amount,
            'currency' => 'PHP',
            'status' => 'success',
            'gateway_response' => json_encode([
                'status' => 'success',
                'type' => 'refund',
                'reason' => $reason,
            ]),
            'processed_at' => date('Y-m-d H:i:s'),
        ]);

        $this->db->update('payments', [
            'status' => $amount >= (float) $payment['amount_paid'] ? 'refunded' : 'partial_refund',
            'refunded_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => date('Y-m-d H:i:s'),
        ], 'payment_id = :id', ['id' => $paymentId]);

        $assessment = $this->db->fetchOne(
            'SELECT * FROM enrolment_assessments WHERE enrolment_id = :id ORDER BY assessment_id DESC LIMIT 1',
            ['id' => $payment['enrolment_id']]
        );
        if ($assessment) {
            $this->db->update('enrolment_assessments', [
                'status' => 'partial',
                'paid_at' => null,
            ], 'assessment_id = :id', ['id' => $assessment['assessment_id']]);
        }
        $this->db->update('enrolments', [
            'is_cleared' => 0,
            'college_cleared' => 0,
            'cleared_at' => null,
        ], 'enrolment_id = :id', ['id' => $payment['enrolment_id']]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'refund_amount' => $amount,
            'message' => 'Refund processed successfully',
        ];
    }
}

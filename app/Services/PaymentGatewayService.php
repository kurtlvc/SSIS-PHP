<?php

class PaymentGatewayService
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
    }

    /**
     * Process a payment through simulated gateway
     */
    public function processPayment(int $paymentId, string $gateway = 'simulated'): array
    {
        $payment = $this->getPaymentDetails($paymentId);
        if (!$payment) {
            return ['success' => false, 'error' => 'Payment not found'];
        }

        // Create transaction record
        $transactionId = $this->createTransaction($paymentId, $gateway, $payment['amount_paid']);

        // Simulate gateway processing delay
        usleep(500000); // 0.5 second

        // Simulate gateway response (95% success rate)
        $success = (mt_rand(1, 100) <= 95);

        $gatewayResponse = [
            'gateway' => $gateway,
            'reference' => $this->generateGatewayReference($gateway),
            'amount' => $payment['amount_paid'],
            'currency' => 'PHP',
            'timestamp' => date('c'),
            'simulated' => true
        ];

        if ($success) {
            $gatewayResponse['status'] = 'success';
            $gatewayResponse['auth_code'] = 'AUTH' . strtoupper(substr(md5(uniqid()), 0, 8));
            
            // Update payment and transaction
            $this->updateTransaction($transactionId, 'success', $gatewayResponse);
            $this->completePayment($paymentId, $gatewayResponse['auth_code']);
            
            // Send receipt notification (simulated email)
            $this->sendPaymentReceipt($payment);
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'gateway_reference' => $gatewayResponse['reference'],
                'auth_code' => $gatewayResponse['auth_code'],
                'message' => 'Payment processed successfully'
            ];
        } else {
            $gatewayResponse['status'] = 'failed';
            $gatewayResponse['error_code'] = 'INSUFFICIENT_FUNDS';
            $gatewayResponse['error_message'] = 'Simulated payment failure - insufficient funds';
            
            $this->updateTransaction($transactionId, 'failed', $gatewayResponse);
            
            return [
                'success' => false,
                'transaction_id' => $transactionId,
                'gateway_reference' => $gatewayResponse['reference'],
                'error' => $gatewayResponse['error_message'],
                'error_code' => $gatewayResponse['error_code']
            ];
        }
    }

    /**
     * Get payment details with student info
     */
    private function getPaymentDetails(int $paymentId): ?array
    {
        $sql = "SELECT p.*, s.ref_no, s.lname, s.fname, s.emailaddress,
                       e.total_units, ay.ay_name, sem.semester_name,
                       pgm.program_code, pgm.description as program_desc
                FROM payments p
                JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                JOIN students s ON e.stud_id = s.stud_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs pgm ON e.program_id = pgm.program_id
                WHERE p.payment_id = :payment_id";
        return $this->db->fetchOne($sql, ['payment_id' => $paymentId]);
    }

    /**
     * Create transaction record
     */
    private function createTransaction(int $paymentId, string $gateway, float $amount): int
    {
        $reference = $this->generateGatewayReference($gateway);
        $data = [
            'payment_id' => $paymentId,
            'gateway_reference' => $reference,
            'gateway_name' => $gateway,
            'amount' => $amount,
            'currency' => 'PHP',
            'status' => 'pending',
            'gateway_response' => json_encode(['status' => 'pending', 'simulated' => true]),
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->db->insert('payment_transactions', $data);
    }

    /**
     * Update transaction with gateway response
     */
    private function updateTransaction(int $transactionId, string $status, array $response): void
    {
        $this->db->update('payment_transactions', [
            'status' => $status,
            'gateway_response' => json_encode($response),
            'processed_at' => date('Y-m-d H:i:s')
        ], 'transaction_id = :id', ['id' => $transactionId]);
    }

    /**
     * Complete payment in payments table
     */
    private function completePayment(int $paymentId, string $authCode): void
    {
        $this->db->update('payments', [
            'auth_code' => $authCode,
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s')
        ], 'payment_id = :id', ['id' => $paymentId]);

        // Update enrolment status
        $payment = $this->db->fetchOne('SELECT enrolment_id FROM payments WHERE payment_id = :id', ['id' => $paymentId]);
        if ($payment) {
            $this->db->update('enrolments', [
                'is_cleared' => 1,
                'college_cleared' => 1,
                'cleared_at' => date('Y-m-d H:i:s')
            ], 'enrolment_id = :id', ['id' => $payment['enrolment_id']]);
        }
    }

    /**
     * Generate gateway reference number
     */
    private function generateGatewayReference(string $gateway): string
    {
        $prefix = match($gateway) {
            'gcash' => 'GCASH',
            'paymaya' => 'PYMYA',
            'bank' => 'BANK',
            default => 'SIM'
        };
        return $prefix . '-' . date('YmdHis') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    }

    /**
     * Simulate sending payment receipt via email
     */
    private function sendPaymentReceipt(array $payment): void
    {
        $emailService = new EmailService();
        $emailService->sendPaymentReceipt([
            'to_email' => $payment['emailaddress'],
            'to_name' => $payment['fname'] . ' ' . $payment['lname'],
            'student_ref' => $payment['ref_no'],
            'receipt_no' => $payment['receipt_no'],
            'amount' => $payment['amount_paid'],
            'program' => $payment['program_desc'],
            'ay' => $payment['ay_name'],
            'semester' => $payment['semester_name'],
            'payment_date' => $payment['payment_date'],
            'transaction_ref' => $payment['auth_code'] ?? 'N/A'
        ]);
    }

    /**
     * Get payment transaction history
     */
    public function getTransactionHistory(int $paymentId): array
    {
        $sql = "SELECT * FROM payment_transactions WHERE payment_id = :payment_id ORDER BY created_at DESC";
        return $this->db->fetchAll($sql, ['payment_id' => $paymentId]);
    }

    /**
     * Refund a payment (simulate)
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

        if ($amount > $payment['amount_paid']) {
            return ['success' => false, 'error' => 'Refund amount exceeds payment'];
        }

        // Create refund transaction
        $transactionId = $this->createTransaction($paymentId, 'simulated', -$amount);
        
        $gatewayResponse = [
            'gateway' => 'simulated',
            'reference' => $this->generateGatewayReference('simulated'),
            'amount' => -$amount,
            'currency' => 'PHP',
            'timestamp' => date('c'),
            'status' => 'success',
            'simulated' => true,
            'type' => 'refund',
            'reason' => $reason
        ];

        $this->updateTransaction($transactionId, 'success', $gatewayResponse);

        // Update payment status
        $this->db->update('payments', [
            'status' => $amount >= $payment['amount_paid'] ? 'refunded' : 'partial_refund',
            'refunded_amount' => $amount,
            'refund_reason' => $reason,
            'refunded_at' => date('Y-m-d H:i:s')
        ], 'payment_id = :id', ['id' => $paymentId]);

        return [
            'success' => true,
            'transaction_id' => $transactionId,
            'refund_amount' => $amount,
            'message' => 'Refund processed successfully'
        ];
    }
}
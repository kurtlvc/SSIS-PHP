<?php

class EmailService
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
    }

    /**
     * Send email (simulated - logs to communication_log)
     */
    public function send(array $params): array
    {
        $required = ['to_email', 'template_key', 'subject', 'message'];
        foreach ($required as $field) {
            if (empty($params[$field])) {
                return ['success' => false, 'error' => "Missing required field: $field"];
            }
        }

        // Log the email (simulated)
        $logId = $this->logCommunication([
            'recipient_type' => $params['recipient_type'] ?? 'student',
            'recipient_id' => $params['recipient_id'] ?? 0,
            'recipient_email' => $params['to_email'],
            'channel' => 'email',
            'template_key' => $params['template_key'],
            'subject' => $params['subject'],
            'message' => $params['message'],
            'status' => 'simulated',
            'reference_type' => $params['reference_type'] ?? null,
            'reference_id' => $params['reference_id'] ?? null,
            'sent_at' => date('Y-m-d H:i:s')
        ]);

        // In production, this would call actual SMTP/mail service
        // For simulation, we just log it
        error_log("[SIMULATED EMAIL] To: {$params['to_email']} | Subject: {$params['subject']} | Template: {$params['template_key']}");

        return [
            'success' => true,
            'log_id' => $logId,
            'message' => 'Email queued (simulated)'
        ];
    }

    /**
     * Send payment receipt email
     */
    public function sendPaymentReceipt(array $data): array
    {
        $subject = "Payment Receipt - {$data['receipt_no']}";
        $message = $this->renderPaymentReceiptTemplate($data);

        return $this->send([
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? '',
            'recipient_type' => 'student',
            'recipient_id' => $data['student_id'] ?? 0,
            'template_key' => 'payment_receipt',
            'subject' => $subject,
            'message' => $message,
            'reference_type' => 'payment',
            'reference_id' => $data['payment_id'] ?? null
        ]);
    }

    /**
     * Send grade notification email
     */
    public function sendGradeNotification(array $data): array
    {
        $subject = "Grade Posted - {$data['course_code']}";
        $message = $this->renderGradeNotificationTemplate($data);

        return $this->send([
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? '',
            'recipient_type' => 'student',
            'recipient_id' => $data['student_id'] ?? 0,
            'template_key' => 'grade_posted',
            'subject' => $subject,
            'message' => $message,
            'reference_type' => 'grade',
            'reference_id' => $data['grade_id'] ?? null
        ]);
    }

    /**
     * Send clearance notification email
     */
    public function sendClearanceNotification(array $data): array
    {
        $subject = "Clearance Status Update - {$data['department']}";
        $message = $this->renderClearanceTemplate($data);

        return $this->send([
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? '',
            'recipient_type' => 'student',
            'recipient_id' => $data['student_id'] ?? 0,
            'template_key' => 'clearance_update',
            'subject' => $subject,
            'message' => $message,
            'reference_type' => 'clearance',
            'reference_id' => $data['clearance_id'] ?? null
        ]);
    }

    /**
     * Send document request notification
     */
    public function sendDocumentNotification(array $data): array
    {
        $subject = "Document Request Update - {$data['document_type']}";
        $message = $this->renderDocumentTemplate($data);

        return $this->send([
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? '',
            'recipient_type' => 'student',
            'recipient_id' => $data['student_id'] ?? 0,
            'template_key' => 'document_update',
            'subject' => $subject,
            'message' => $message,
            'reference_type' => 'document',
            'reference_id' => $data['request_id'] ?? null
        ]);
    }

    /**
     * Send enrollment confirmation
     */
    public function sendEnrollmentConfirmation(array $data): array
    {
        $subject = "Enrollment Confirmed - {$data['ay_name']} {$data['semester_name']}";
        $message = $this->renderEnrollmentTemplate($data);

        return $this->send([
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? '',
            'recipient_type' => 'student',
            'recipient_id' => $data['student_id'] ?? 0,
            'template_key' => 'enrollment_confirmed',
            'subject' => $subject,
            'message' => $message,
            'reference_type' => 'enrollment',
            'reference_id' => $data['enrolment_id'] ?? null
        ]);
    }

    /**
     * Send announcement notification
     */
    public function sendAnnouncementNotification(array $data): array
    {
        $subject = "Announcement: {$data['title']}";
        $message = $this->renderAnnouncementTemplate($data);

        return $this->send([
            'to_email' => $data['to_email'],
            'to_name' => $data['to_name'] ?? '',
            'recipient_type' => $data['recipient_type'] ?? 'student',
            'recipient_id' => $data['recipient_id'] ?? 0,
            'template_key' => 'announcement',
            'subject' => $subject,
            'message' => $message,
            'reference_type' => 'announcement',
            'reference_id' => $data['announcement_id'] ?? null
        ]);
    }

    /**
     * Log communication to database
     */
    private function logCommunication(array $data): int
    {
        return $this->db->insert('communication_log', $data);
    }

    /**
     * Render payment receipt HTML template
     */
    private function renderPaymentReceiptTemplate(array $data): string
    {
        $systemTitle = $this->config['system_title'] ?? 'CuyoSSIS';
        $receiptNo = $data['receipt_no'] ?? 'N/A';
        $amount = number_format($data['amount'] ?? 0, 2);
        $date = $data['payment_date'] ?? date('F j, Y');
        $studentName = $data['to_name'] ?? 'Student';
        $studentRef = $data['student_ref'] ?? 'N/A';
        $program = $data['program'] ?? 'N/A';
        $ay = $data['ay'] ?? 'N/A';
        $semester = $data['semester'] ?? 'N/A';
        $transactionRef = $data['transaction_ref'] ?? 'N/A';

        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='utf-8'>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #0d9488, #0f766e); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; }
        .receipt-box { background: white; border: 2px solid #0d9488; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f3f4f6; }
        .row:last-child { border-bottom: none; }
        .label { color: #6b7280; font-weight: 500; }
        .value { color: #111827; font-weight: 600; }
        .amount { color: #059669; font-size: 1.25rem; font-weight: 700; }
        .footer { background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem; border-radius: 0 0 8px 8px; border: 1px solid #e5e7eb; border-top: none; }
        .logo { font-size: 1.5rem; font-weight: 700; color: #0d9488; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <div class='logo'>{$systemTitle}</div>
            <p style='margin: 10px 0 0; opacity: 0.9;'>CuyoTech University</p>
        </div>
        <div class='content'>
            <h2 style='margin-top: 0; color: #111827;'>Payment Receipt</h2>
            <p>Dear <strong>{$studentName}</strong>,</p>
            <p>Thank you for your payment. This receipt confirms your transaction has been processed successfully.</p>
            
            <div class='receipt-box'>
                <div class='row'>
                    <span class='label'>Receipt Number:</span>
                    <span class='value'>{$receiptNo}</span>
                </div>
                <div class='row'>
                    <span class='label'>Transaction Reference:</span>
                    <span class='value'>{$transactionRef}</span>
                </div>
                <div class='row'>
                    <span class='label'>Date:</span>
                    <span class='value'>{$date}</span>
                </div>
                <div class='row'>
                    <span class='label'>Student:</span>
                    <span class='value'>{$studentName} ({$studentRef})</span>
                </div>
                <div class='row'>
                    <span class='label'>Program:</span>
                    <span class='value'>{$program}</span>
                </div>
                <div class='row'>
                    <span class='label'>Academic Year:</span>
                    <span class='value'>{$ay}</span>
                </div>
                <div class='row'>
                    <span class='label'>Semester:</span>
                    <span class='value'>{$semester}</span>
                </div>
                <div class='row' style='border-bottom: 2px solid #0d9488;'>
                    <span class='label'>Amount Paid:</span>
                    <span class='value amount'>₱ {$amount}</span>
                </div>
            </div>
            
            <p style='color: #6b7280; font-size: 0.875rem;'>This is a system-generated receipt. No signature required.</p>
            <p style='color: #6b7280; font-size: 0.875rem;'>For questions, contact the Cashier's Office.</p>
        </div>
        <div class='footer'>
            <p>{$systemTitle} &middot; CuyoTech University</p>
            <p>This is a simulated email for development purposes.</p>
        </div>
    </div>
</body>
</html>
        ";
    }

    private function renderGradeNotificationTemplate(array $data): string
    {
        $systemTitle = $this->config['system_title'] ?? 'CuyoSSIS';
        return "
<!DOCTYPE html>
<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: #0d9488; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
        <h1>{$systemTitle}</h1>
        <p>CuyoTech University</p>
    </div>
    <div style='background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb;'>
        <h2>Grade Posted</h2>
        <p>Dear <strong>{$data['to_name']}</strong>,</p>
        <p>A new grade has been posted for your course:</p>
        <table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>
            <tr style='background: #0d9488; color: white;'><th style='padding: 10px;'>Course</th><th style='padding: 10px;'>Grade</th></tr>
            <tr style='border-bottom: 1px solid #e5e7eb;'><td style='padding: 10px;'>{$data['course_code']} - {$data['course_name']}</td><td style='padding: 10px; text-align: center; font-weight: bold; color: #059669;'>{$data['grade']}</td></tr>
        </table>
        <p>Log in to the portal to view your complete grade report.</p>
    </div>
    <div style='background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem; border-radius: 0 0 8px 8px;'>
        {$systemTitle} &middot; CuyoTech University
    </div>
</body></html>
        ";
    }

    private function renderClearanceTemplate(array $data): string
    {
        $systemTitle = $this->config['system_title'] ?? 'CuyoSSIS';
        $statusColor = match($data['status']) {
            'Cleared' => '#059669',
            'On-Hold' => '#dc2626',
            'Pending' => '#d97706',
            default => '#374155'
        };
        return "
<!DOCTYPE html>
<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: #0d9488; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
        <h1>{$systemTitle}</h1>
    </div>
    <div style='background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb;'>
        <h2>Clearance Status Update</h2>
        <p>Dear <strong>{$data['to_name']}</strong>,</p>
        <p>Your clearance status for <strong>{$data['department']}</strong> has been updated:</p>
        <div style='background: white; border: 2px solid {$statusColor}; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;'>
            <span style='font-size: 1.5rem; font-weight: bold; color: {$statusColor};'>{$data['status']}</span>
        </div>
        <p>Log in to the portal to view your complete clearance status.</p>
    </div>
    <div style='background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem;'>
        {$systemTitle} &middot; CuyoTech University
    </div>
</body></html>
        ";
    }

    private function renderDocumentTemplate(array $data): string
    {
        $systemTitle = $this->config['system_title'] ?? 'CuyoSSIS';
        $statusColor = match($data['status']) {
            'Approved' => '#059669',
            'Ready for Pickup' => '#0d9488',
            'Processing' => '#d97706',
            'Rejected' => '#dc2626',
            default => '#374155'
        };
        return "
<!DOCTYPE html>
<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: #0d9488; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
        <h1>{$systemTitle}</h1>
    </div>
    <div style='background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb;'>
        <h2>Document Request Update</h2>
        <p>Dear <strong>{$data['to_name']}</strong>,</p>
        <p>Your document request for <strong>{$data['document_type']}</strong> has been updated:</p>
        <div style='background: white; border: 2px solid {$statusColor}; border-radius: 8px; padding: 20px; margin: 20px 0; text-align: center;'>
            <span style='font-size: 1.5rem; font-weight: bold; color: {$statusColor};'>{$data['status']}</span>
        </div>
        <p>Log in to the portal to view your request history.</p>
    </div>
    <div style='background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem;'>
        {$systemTitle} &middot; CuyoTech University
    </div>
</body></html>
        ";
    }

    private function renderEnrollmentTemplate(array $data): string
    {
        $systemTitle = $this->config['system_title'] ?? 'CuyoSSIS';
        return "
<!DOCTYPE html>
<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: #0d9488; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
        <h1>{$systemTitle}</h1>
    </div>
    <div style='background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb;'>
        <h2>Enrollment Confirmed</h2>
        <p>Dear <strong>{$data['to_name']}</strong>,</p>
        <p>Your enrollment for <strong>{$data['ay_name']} {$data['semester_name']}</strong> has been confirmed.</p>
        <p>Total Units: <strong>{$data['total_units']}</strong></p>
        <p>Please proceed to payment to complete your enrollment.</p>
    </div>
    <div style='background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem;'>
        {$systemTitle} &middot; CuyoTech University
    </div>
</body></html>
        ";
    }

    private function renderAnnouncementTemplate(array $data): string
    {
        $systemTitle = $this->config['system_title'] ?? 'CuyoSSIS';
        return "
<!DOCTYPE html>
<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: #0d9488; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
        <h1>{$systemTitle}</h1>
    </div>
    <div style='background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb;'>
        <h2>{$data['title']}</h2>
        <p>Dear <strong>{$data['to_name']}</strong>,</p>
        <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid #0d9488;'>
            {$data['content']}
        </div>
    </div>
    <div style='background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem;'>
        {$systemTitle} &middot; CuyoTech University
    </div>
</body></html>
        ";
    }

    /**
     * Get communication log for a recipient
     */
    public function getCommunicationLog(string $recipientType, int $recipientId, int $limit = 50): array
    {
        $sql = "SELECT * FROM communication_log 
                WHERE recipient_type = :type AND recipient_id = :id 
                ORDER BY created_at DESC LIMIT :limit";
        return $this->db->fetchAll($sql, [
            'type' => $recipientType,
            'id' => $recipientId,
            'limit' => $limit
        ]);
    }
}
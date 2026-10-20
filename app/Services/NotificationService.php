<?php

class NotificationService
{
    private $db;
    private $emailService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->emailService = new EmailService();
    }

    /**
     * Create a notification for a student (uses existing notifications table)
     */
    public function createForStudent(int $studId, string $title, string $message, string $type = 'info', ?string $referenceType = null, ?int $referenceId = null): int
    {
        $data = [
            'stud_id' => $studId,
            'account_id' => $studId, // Assuming student account_id matches stud_id
            'url' => $referenceType && $referenceId ? "/student/{$referenceType}/{$referenceId}" : null,
            'message' => $title . ': ' . $message,
            'datetime' => date('Y-m-d H:i:s'),
            'timestamp' => time(),
            'seen' => 0
        ];

        return $this->db->insert('notifications', $data);
    }

    /**
     * Create notification for personnel/admin (uses account_id)
     */
    public function createForAccount(int $accountId, string $title, string $message, string $type = 'info', ?string $referenceType = null, ?int $referenceId = null): int
    {
        $data = [
            'stud_id' => 0,
            'account_id' => $accountId,
            'url' => $referenceType && $referenceId ? "/admin/{$referenceType}/{$referenceId}" : null,
            'message' => $title . ': ' . $message,
            'datetime' => date('Y-m-d H:i:s'),
            'timestamp' => time(),
            'seen' => 0
        ];

        return $this->db->insert('notifications', $data);
    }

    /**
     * Create notification and send email
     */
    public function notify(int $recipientId, string $recipientType, string $title, string $message, string $notificationType = 'info', ?string $referenceType = null, ?int $referenceId = null, ?string $email = null): int
    {
        if ($recipientType === 'student') {
            $notificationId = $this->createForStudent($recipientId, $title, $message, $notificationType, $referenceType, $referenceId);
        } else {
            $notificationId = $this->createForAccount($recipientId, $title, $message, $notificationType, $referenceType, $referenceId);
        }

        // Send email if provided
        if ($email) {
            $this->emailService->send([
                'to_email' => $email,
                'to_name' => '',
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'template_key' => 'notification_' . $notificationType,
                'subject' => $title,
                'message' => $this->renderNotificationEmail($title, $message, $notificationType),
                'reference_type' => $referenceType,
                'reference_id' => $referenceId
            ]);
        }

        return $notificationId;
    }

    /**
     * Get notifications for a student
     */
    public function getForStudent(int $studId, array $options = []): array
    {
        $limit = $options['limit'] ?? 20;
        $offset = $options['offset'] ?? 0;
        $unreadOnly = $options['unread_only'] ?? false;

        $where = "WHERE stud_id = :stud_id";
        $params = ['stud_id' => $studId];

        if ($unreadOnly) {
            $where .= " AND seen = 0";
        }

        $sql = "SELECT id, message, datetime, timestamp, seen, url 
                FROM notifications $where 
                ORDER BY datetime DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get notifications for an account
     */
    public function getForAccount(int $accountId, array $options = []): array
    {
        $limit = $options['limit'] ?? 20;
        $offset = $options['offset'] ?? 0;
        $unreadOnly = $options['unread_only'] ?? false;

        $where = "WHERE account_id = :account_id AND stud_id = 0";
        $params = ['account_id' => $accountId];

        if ($unreadOnly) {
            $where .= " AND seen = 0";
        }

        $sql = "SELECT id, message, datetime, timestamp, seen, url 
                FROM notifications $where 
                ORDER BY datetime DESC LIMIT :limit OFFSET :offset";
        $params['limit'] = $limit;
        $params['offset'] = $offset;

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get unread count for student
     */
    public function getUnreadCountForStudent(int $studId): int
    {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE stud_id = :stud_id AND seen = 0";
        $row = $this->db->fetchOne($sql, ['stud_id' => $studId]);
        return $row ? (int)$row['count'] : 0;
    }

    /**
     * Get unread count for account
     */
    public function getUnreadCountForAccount(int $accountId): int
    {
        $sql = "SELECT COUNT(*) as count FROM notifications WHERE account_id = :account_id AND stud_id = 0 AND seen = 0";
        $row = $this->db->fetchOne($sql, ['account_id' => $accountId]);
        return $row ? (int)$row['count'] : 0;
    }

    /**
     * Mark as read
     */
    public function markAsRead(int $notificationId, int $recipientId): bool
    {
        // Try stud_id first
        $sql = "UPDATE notifications SET seen = 1 WHERE id = :id AND stud_id = :recipient_id";
        $result = $this->db->query($sql, ['id' => $notificationId, 'recipient_id' => $recipientId])->rowCount();
        
        if ($result === 0) {
            // Try account_id
            $sql = "UPDATE notifications SET seen = 1 WHERE id = :id AND account_id = :recipient_id AND stud_id = 0";
            $result = $this->db->query($sql, ['id' => $notificationId, 'recipient_id' => $recipientId])->rowCount();
        }
        
        return $result > 0;
    }

    /**
     * Mark all as read for student
     */
    public function markAllAsReadForStudent(int $studId): int
    {
        $sql = "UPDATE notifications SET seen = 1 WHERE stud_id = :stud_id AND seen = 0";
        return $this->db->query($sql, ['stud_id' => $studId])->rowCount();
    }

    /**
     * Mark all as read for account
     */
    public function markAllAsReadForAccount(int $accountId): int
    {
        $sql = "UPDATE notifications SET seen = 1 WHERE account_id = :account_id AND stud_id = 0 AND seen = 0";
        return $this->db->query($sql, ['account_id' => $accountId])->rowCount();
    }

    /**
     * Broadcast to multiple students
     */
    public function broadcastToStudents(array $studIds, string $title, string $message, string $type = 'info'): int
    {
        $count = 0;
        foreach ($studIds as $id) {
            $this->createForStudent($id, $title, $message, $type);
            $count++;
        }
        return $count;
    }

    /**
     * Broadcast to multiple accounts
     */
    public function broadcastToAccounts(array $accountIds, string $title, string $message, string $type = 'info'): int
    {
        $count = 0;
        foreach ($accountIds as $id) {
            $this->createForAccount($id, $title, $message, $type);
            $count++;
        }
        return $count;
    }

    /**
     * System-wide broadcast
     */
    public function systemBroadcast(string $title, string $message, string $type = 'system'): int
    {
        // Get all active students
        $students = $this->db->fetchAll("SELECT stud_id FROM students");
        $studentIds = array_column($students, 'stud_id');
        $this->broadcastToStudents($studentIds, $title, $message, $type);

        // Get all active personnel accounts
        $personnel = $this->db->fetchAll("SELECT a.owner_id as id FROM accounts a WHERE a.owner_type = 'personnel' AND a.is_active = 1");
        $accountIds = array_column($personnel, 'id');
        $this->broadcastToAccounts($accountIds, $title, $message, $type);

        return count($studentIds) + count($accountIds);
    }

    /**
     * Create payment notification
     */
    public function paymentNotification(int $studentId, array $paymentData): int
    {
        return $this->createForStudent($studentId, 
            'Payment Confirmed', 
            "Your payment of ₱" . number_format($paymentData['amount'], 2) . " has been confirmed. Receipt: {$paymentData['receipt_no']}", 
            'payment', 
            'payment', 
            $paymentData['payment_id'] ?? null
        );
    }

    /**
     * Create grade notification
     */
    public function gradeNotification(int $studentId, array $gradeData): int
    {
        return $this->createForStudent($studentId, 
            'Grade Posted', 
            "Grade for {$gradeData['course_code']} has been posted: {$gradeData['grade']}", 
            'grade', 
            'grade', 
            $gradeData['grade_id'] ?? null
        );
    }

    /**
     * Create clearance notification
     */
    public function clearanceNotification(int $studentId, array $clearanceData): int
    {
        return $this->createForStudent($studentId, 
            'Clearance Status Update', 
            "Your clearance for {$clearanceData['department']} is now {$clearanceData['status']}", 
            'clearance', 
            'clearance', 
            $clearanceData['clearance_id'] ?? null
        );
    }

    /**
     * Create document request notification
     */
    public function documentNotification(int $studentId, array $docData): int
    {
        return $this->createForStudent($studentId, 
            'Document Request Update', 
            "Your {$docData['document_type']} request is now {$docData['status']}", 
            'document', 
            'document', 
            $docData['request_id'] ?? null
        );
    }

    /**
     * Create enrollment notification
     */
    public function enrollmentNotification(int $studentId, array $enrollmentData): int
    {
        return $this->createForStudent($studentId, 
            'Enrollment Status', 
            "Your enrollment for {$enrollmentData['ay_name']} {$enrollmentData['semester_name']} is {$enrollmentData['status']}", 
            'enrollment', 
            'enrollment', 
            $enrollmentData['enrolment_id'] ?? null
        );
    }

    /**
     * Delete old read notifications
     */
    public function cleanup(int $daysOld = 90): int
    {
        $sql = "DELETE FROM notifications 
                WHERE seen = 1 
                AND datetime < DATE_SUB(NOW(), INTERVAL :days DAY)";
        return $this->db->query($sql, ['days' => $daysOld])->rowCount();
    }

    /**
     * Render notification email template
     */
    private function renderNotificationEmail(string $title, string $message, string $type): string
    {
        $systemTitle = App::getInstance()->getConfig()['system_title'] ?? 'CuyoSSIS';
        $typeColors = [
            'info' => '#3b82f6',
            'success' => '#10b981',
            'warning' => '#f59e0b',
            'danger' => '#ef4444',
            'payment' => '#0d9488',
            'grade' => '#8b5cf6',
            'clearance' => '#ec4899',
            'document' => '#06b6d4',
            'enrollment' => '#14b8a6',
            'system' => '#6366f1'
        ];
        $color = $typeColors[$type] ?? '#3b82f6';

        return "
<!DOCTYPE html>
<html><body style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
    <div style='background: {$color}; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0;'>
        <h1>{$systemTitle}</h1>
        <p>CuyoTech University</p>
    </div>
    <div style='background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb;'>
        <h2 style='color: {$color}; margin-top: 0;'>{$title}</h2>
        <p>Dear User,</p>
        <div style='background: white; padding: 20px; border-radius: 8px; border-left: 4px solid {$color};'>
            {$message}
        </div>
        <p style='margin-top: 20px; color: #6b7280; font-size: 0.875rem;'>
            Log in to the portal to view details.
        </p>
    </div>
    <div style='background: #f3f4f6; padding: 20px; text-align: center; color: #6b7280; font-size: 0.875rem; border-radius: 0 0 8px 8px;'>
        {$systemTitle} &middot; CuyoTech University
    </div>
</body></html>
        ";
    }
}
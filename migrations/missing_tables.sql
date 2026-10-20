-- ============================================================================
-- CuyoSSIS - Missing Database Tables (Migration)
-- Run after seed.sql
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ============================================================
-- 1. NOTIFICATIONS SYSTEM
-- ============================================================
CREATE TABLE IF NOT EXISTS notifications (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_type ENUM('student', 'personnel', 'admin', 'all') NOT NULL,
    recipient_id INT NULL, -- NULL for broadcast to type
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'danger', 'payment', 'grade', 'clearance', 'document', 'enrollment', 'system') DEFAULT 'info',
    reference_type VARCHAR(50) NULL, -- 'payment', 'grade', 'clearance', etc.
    reference_id INT NULL,
    is_read TINYINT(1) DEFAULT 0,
    read_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NULL,
    INDEX idx_recipient (recipient_type, recipient_id),
    INDEX idx_unread (is_read),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 2. ANNOUNCEMENTS
-- ============================================================
CREATE TABLE IF NOT EXISTS announcements (
    announcement_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    target_roles JSON NULL, -- ['student', 'faculty', 'admin'] or null for all
    target_departments JSON NULL, -- department IDs or null for all
    priority ENUM('low', 'normal', 'high', 'urgent') DEFAULT 'normal',
    is_published TINYINT(1) DEFAULT 0,
    published_at DATETIME NULL,
    expires_at DATETIME NULL,
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_published (is_published, published_at),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. AUDIT LOG (if not exists)
-- ============================================================
CREATE TABLE IF NOT EXISTS audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NULL,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NULL,
    entity_id INT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_account (account_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. API TOKENS (JWT refresh tokens)
-- ============================================================
CREATE TABLE IF NOT EXISTS api_tokens (
    token_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    token_type ENUM('access', 'refresh') DEFAULT 'refresh',
    expires_at DATETIME NOT NULL,
    revoked_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_account (account_id),
    INDEX idx_hash (token_hash),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. PASSWORD RESETS
-- ============================================================
CREATE TABLE IF NOT EXISTS password_resets (
    reset_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    token_hash VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_token (token_hash),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. RATE LIMITS
-- ============================================================
CREATE TABLE IF NOT EXISTS rate_limits (
    rate_limit_id INT AUTO_INCREMENT PRIMARY KEY,
    identifier VARCHAR(255) NOT NULL, -- IP or account_id
    action VARCHAR(100) NOT NULL, -- 'login', 'api', 'password_reset'
    count INT DEFAULT 1,
    window_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    UNIQUE KEY uk_identifier_action (identifier, action),
    INDEX idx_expires (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. ENROLLMENT ASSESSMENTS (assessment before payment)
-- ============================================================
CREATE TABLE IF NOT EXISTS enrolment_assessments (
    assessment_id INT AUTO_INCREMENT PRIMARY KEY,
    enrolment_id INT NOT NULL,
    tuition_fee DECIMAL(10,2) DEFAULT 0,
    misc_fee DECIMAL(10,2) DEFAULT 0,
    lab_fee DECIMAL(10,2) DEFAULT 0,
    other_fees DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) GENERATED ALWAYS AS (tuition_fee + misc_fee + lab_fee + other_fees) STORED,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    net_amount DECIMAL(10,2) GENERATED ALWAYS AS (total_amount - discount_amount) STORED,
    status ENUM('pending', 'paid', 'partial', 'cancelled', 'refunded') DEFAULT 'pending',
    assessed_by INT NULL,
    assessed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    paid_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE CASCADE,
    INDEX idx_enrolment (enrolment_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. PAYMENT TRANSACTIONS (gateway simulation)
-- ============================================================
CREATE TABLE IF NOT EXISTS payment_transactions (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_id INT NOT NULL,
    gateway_reference VARCHAR(100) NOT NULL, -- simulated gateway ref
    gateway_name VARCHAR(50) DEFAULT 'simulated', -- 'simulated', 'gcash', 'paymaya', 'bank'
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'PHP',
    status ENUM('pending', 'success', 'failed', 'cancelled', 'refunded') DEFAULT 'pending',
    gateway_response JSON NULL, -- simulated response
    processed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE CASCADE,
    UNIQUE KEY uk_gateway_ref (gateway_reference),
    INDEX idx_payment (payment_id),
    INDEX idx_status (status),
    INDEX idx_gateway (gateway_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. GRADE SUBMISSION WORKFLOW
-- ============================================================
CREATE TABLE IF NOT EXISTS grade_submissions (
    submission_id INT AUTO_INCREMENT PRIMARY KEY,
    sched_id INT NOT NULL,
    faculty_id INT NOT NULL,
    status ENUM('draft', 'submitted', 'reviewed', 'approved', 'rejected', 'posted') DEFAULT 'draft',
    submitted_at DATETIME NULL,
    reviewed_by INT NULL,
    reviewed_at DATETIME NULL,
    review_notes TEXT NULL,
    approved_by INT NULL,
    approved_at DATETIME NULL,
    posted_by INT NULL,
    posted_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sched (sched_id),
    INDEX idx_faculty (faculty_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Grade submission items (individual student grades)
CREATE TABLE IF NOT EXISTS grade_submission_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    submission_id INT NOT NULL,
    enrolment_id INT NOT NULL,
    grade_value DECIMAL(5,2) NULL,
    remarks VARCHAR(50) NULL,
    is_final TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (submission_id) REFERENCES grade_submissions(submission_id) ON DELETE CASCADE,
    FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE CASCADE,
    UNIQUE KEY uk_submission_enrolment (submission_id, enrolment_id),
    INDEX idx_enrolment (enrolment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. ENROLLMENT CART (student subject selection)
-- ============================================================
CREATE TABLE IF NOT EXISTS enrolment_carts (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    stud_id INT NOT NULL,
    sem_id INT NOT NULL,
    ay_id INT NOT NULL,
    status ENUM('draft', 'submitted', 'assessed', 'paid', 'cancelled') DEFAULT 'draft',
    total_units INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (stud_id) REFERENCES students(stud_id) ON DELETE CASCADE,
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id),
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
    UNIQUE KEY uk_student_sem_ay (stud_id, sem_id, ay_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS enrolment_cart_items (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    cart_id INT NOT NULL,
    sched_id INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cart_id) REFERENCES enrolment_carts(cart_id) ON DELETE CASCADE,
    FOREIGN KEY (sched_id) REFERENCES schedules(sched_id) ON DELETE CASCADE,
    UNIQUE KEY uk_cart_sched (cart_id, sched_id),
    INDEX idx_sched (sched_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. EMAIL/SMS LOG (simulation tracking)
-- ============================================================
CREATE TABLE IF NOT EXISTS communication_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    recipient_type ENUM('student', 'personnel', 'admin') NOT NULL,
    recipient_id INT NOT NULL,
    recipient_email VARCHAR(255) NULL,
    recipient_phone VARCHAR(20) NULL,
    channel ENUM('email', 'sms', 'push') NOT NULL,
    template_key VARCHAR(100) NOT NULL, -- 'payment_receipt', 'grade_posted', etc.
    subject VARCHAR(255) NULL,
    message TEXT NOT NULL,
    status ENUM('queued', 'sent', 'failed', 'simulated') DEFAULT 'queued',
    reference_type VARCHAR(50) NULL,
    reference_id INT NULL,
    sent_at DATETIME NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (recipient_type, recipient_id),
    INDEX idx_status (status),
    INDEX idx_template (template_key),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. SETTINGS EXTENSIONS (for new features)
-- ============================================================================
-- These will be added to the existing settings table via UPDATE/INSERT

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- SAMPLE DATA FOR NEW TABLES
-- ============================================================

-- Clearance Departments (if not already seeded)
INSERT IGNORE INTO clearance_departments (dept_id, dept_name, dept_code, is_active) VALUES
(1, 'Library', 'LIB', 1),
(2, 'Laboratory', 'LAB', 1),
(3, 'Finance/Cashier', 'FIN', 1),
(4, 'Student Affairs', 'SA', 1),
(5, 'Academic Affairs', 'AA', 1),
(6, 'Information Technology', 'IT', 1),
(7, 'Registrar', 'REG', 1),
(8, 'Guidance Office', 'GO', 1);

-- Sample Announcements
INSERT IGNORE INTO announcements (announcement_id, title, content, target_roles, priority, is_published, published_at, created_by, created_at) VALUES
(1, 'Welcome to AY 2026-2027', 'The new academic year has officially started. Please check your enrollment status and schedules.', '["student", "faculty", "admin"]', 'high', 1, NOW(), 1, NOW()),
(2, 'Enrollment Period Extended', 'The enrollment period for 1st Semester AY 2026-2027 has been extended until September 15, 2026.', '["student"]', 'normal', 1, NOW(), 1, NOW()),
(3, 'System Maintenance Notice', 'SSIS will undergo scheduled maintenance on October 25, 2026 from 2:00 AM to 4:00 AM. Some services may be temporarily unavailable.', '["student", "faculty", "admin"]', 'normal', 1, NOW(), 1, NOW()),
(4, 'Grade Submission Deadline', 'All faculty members are reminded to submit grades by October 30, 2026. Late submissions will require Dean approval.', '["faculty"]', 'high', 1, NOW(), 1, NOW()),
(5, 'New Payment Options Available', 'Students can now pay tuition fees via GCash and PayMaya through the online portal.', '["student"]', 'normal', 1, NOW(), 1, NOW());

-- Notification Templates (stored as reference)
-- These are used by the NotificationService

SET FOREIGN_KEY_CHECKS = 1;
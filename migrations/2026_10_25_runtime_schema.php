<?php
/**
 * Idempotent runtime-schema migration.
 *
 * Usage: php migrations/2026_10_25_runtime_schema.php
 *
 * Adds the enrollment, assessment, grade-workflow, payment-transaction and
 * communication tables/columns required by the application. Safe to run more
 * than once.
 */

$config = require __DIR__ . '/../config/config.php';
$db = $config['db'];
$pdo = new PDO(
    "mysql:host={$db['host']};port={$db['port']};dbname={$db['database']};charset={$db['charset']}",
    $db['username'],
    $db['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

function tableExists(PDO $pdo, string $table): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?");
    $stmt->execute([$table]);
    return (int) $stmt->fetchColumn() > 0;
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ?");
    $stmt->execute([$table, $column]);
    return (int) $stmt->fetchColumn() > 0;
}

function indexExists(PDO $pdo, string $table, string $index): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ?");
    $stmt->execute([$table, $index]);
    return (int) $stmt->fetchColumn() > 0;
}

function constraintExists(PDO $pdo, string $table, string $constraint): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.table_constraints WHERE constraint_schema = DATABASE() AND table_name = ? AND constraint_name = ?");
    $stmt->execute([$table, $constraint]);
    return (int) $stmt->fetchColumn() > 0;
}

function addColumn(PDO $pdo, string $table, string $column, string $definition): void
{
    if (!columnExists($pdo, $table, $column)) {
        $pdo->exec("ALTER TABLE `{$table}` ADD COLUMN `{$column}` {$definition}");
        echo "Added {$table}.{$column}\n";
    }
}

function addIndex(PDO $pdo, string $table, string $index, string $definition): void
{
    if (!indexExists($pdo, $table, $index)) {
        $pdo->exec("ALTER TABLE `{$table}` ADD INDEX `{$index}` {$definition}");
        echo "Added index {$table}.{$index}\n";
    }
}

function addConstraint(PDO $pdo, string $table, string $constraint, string $definition): void
{
    if (!constraintExists($pdo, $table, $constraint)) {
        $pdo->exec("ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` {$definition}");
        echo "Added constraint {$table}.{$constraint}\n";
    }
}

$tables = [
    'enrolment_carts' => "CREATE TABLE IF NOT EXISTS enrolment_carts (
        cart_id INT AUTO_INCREMENT PRIMARY KEY,
        stud_id INT NOT NULL,
        sem_id INT NOT NULL,
        ay_id INT NOT NULL,
        enrolment_id INT NULL,
        status ENUM('draft','submitted','assessed','paid','cancelled') DEFAULT 'draft',
        total_units INT DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_student_sem_ay (stud_id, sem_id, ay_id),
        KEY idx_enrolment_carts_enrolment (enrolment_id),
        KEY idx_enrolment_carts_status (status),
        FOREIGN KEY (stud_id) REFERENCES students(stud_id) ON DELETE CASCADE,
        FOREIGN KEY (sem_id) REFERENCES semesters(sem_id),
        FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
        FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'enrolment_cart_items' => "CREATE TABLE IF NOT EXISTS enrolment_cart_items (
        item_id INT AUTO_INCREMENT PRIMARY KEY,
        cart_id INT NOT NULL,
        sched_id INT NOT NULL,
        added_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_cart_sched (cart_id, sched_id),
        KEY idx_enrolment_cart_items_sched (sched_id),
        FOREIGN KEY (cart_id) REFERENCES enrolment_carts(cart_id) ON DELETE CASCADE,
        FOREIGN KEY (sched_id) REFERENCES schedules(sched_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'enrolment_courses' => "CREATE TABLE IF NOT EXISTS enrolment_courses (
        enrolment_course_id INT AUTO_INCREMENT PRIMARY KEY,
        enrolment_id INT NOT NULL,
        sched_id INT NOT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_enrolment_sched (enrolment_id, sched_id),
        KEY idx_enrolment_courses_sched (sched_id),
        FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE CASCADE,
        FOREIGN KEY (sched_id) REFERENCES schedules(sched_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'enrolment_assessments' => "CREATE TABLE IF NOT EXISTS enrolment_assessments (
        assessment_id INT AUTO_INCREMENT PRIMARY KEY,
        enrolment_id INT NOT NULL,
        tuition_fee DECIMAL(10,2) DEFAULT 0.00,
        misc_fee DECIMAL(10,2) DEFAULT 0.00,
        lab_fee DECIMAL(10,2) DEFAULT 0.00,
        other_fees DECIMAL(10,2) DEFAULT 0.00,
        total_amount DECIMAL(12,2) GENERATED ALWAYS AS (tuition_fee + misc_fee + lab_fee + other_fees) STORED,
        discount_amount DECIMAL(10,2) DEFAULT 0.00,
        net_amount DECIMAL(12,2) GENERATED ALWAYS AS (tuition_fee + misc_fee + lab_fee + other_fees - discount_amount) STORED,
        status ENUM('pending','paid','partial','cancelled','refunded') DEFAULT 'pending',
        assessed_by INT NULL,
        assessed_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        paid_at DATETIME NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_enrolment_assessments_enrolment (enrolment_id),
        KEY idx_enrolment_assessments_status (status),
        FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'grade_submissions' => "CREATE TABLE IF NOT EXISTS grade_submissions (
        submission_id INT AUTO_INCREMENT PRIMARY KEY,
        sched_id INT NOT NULL,
        faculty_id INT NOT NULL,
        status ENUM('draft','submitted','reviewed','approved','rejected','posted') DEFAULT 'draft',
        submitted_at DATETIME NULL,
        reviewed_by INT NULL,
        reviewed_at DATETIME NULL,
        review_notes TEXT NULL,
        approved_by INT NULL,
        approved_at DATETIME NULL,
        posted_by INT NULL,
        posted_at DATETIME NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        KEY idx_grade_submissions_sched (sched_id),
        KEY idx_grade_submissions_faculty (faculty_id),
        KEY idx_grade_submissions_status (status),
        FOREIGN KEY (sched_id) REFERENCES schedules(sched_id),
        FOREIGN KEY (faculty_id) REFERENCES personnel(p_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'grade_submission_items' => "CREATE TABLE IF NOT EXISTS grade_submission_items (
        item_id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        enrolment_id INT NOT NULL,
        course_id INT NULL,
        grade_value DECIMAL(5,2) NULL,
        remarks VARCHAR(50) NULL,
        is_final TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        UNIQUE KEY uk_submission_enrolment (submission_id, enrolment_id),
        KEY idx_grade_submission_items_enrolment (enrolment_id),
        KEY idx_grade_submission_items_course (course_id),
        FOREIGN KEY (submission_id) REFERENCES grade_submissions(submission_id) ON DELETE CASCADE,
        FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE CASCADE,
        FOREIGN KEY (course_id) REFERENCES courses(course_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'grade_reviews' => "CREATE TABLE IF NOT EXISTS grade_reviews (
        review_id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        reviewer_id INT NOT NULL,
        action VARCHAR(50) NOT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_grade_reviews_submission (submission_id),
        KEY idx_grade_reviews_reviewer (reviewer_id),
        FOREIGN KEY (submission_id) REFERENCES grade_submissions(submission_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'payment_transactions' => "CREATE TABLE IF NOT EXISTS payment_transactions (
        transaction_id INT AUTO_INCREMENT PRIMARY KEY,
        payment_id INT NOT NULL,
        gateway_reference VARCHAR(100) NOT NULL,
        gateway_name VARCHAR(50) DEFAULT 'simulated',
        amount DECIMAL(10,2) NOT NULL,
        currency VARCHAR(3) DEFAULT 'PHP',
        status ENUM('pending','success','failed','cancelled','refunded') DEFAULT 'pending',
        gateway_response JSON NULL,
        processed_at DATETIME NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uk_gateway_ref (gateway_reference),
        KEY idx_payment_transactions_payment (payment_id),
        KEY idx_payment_transactions_status (status),
        KEY idx_payment_transactions_gateway (gateway_name),
        FOREIGN KEY (payment_id) REFERENCES payments(payment_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
    'communication_log' => "CREATE TABLE IF NOT EXISTS communication_log (
        log_id INT AUTO_INCREMENT PRIMARY KEY,
        recipient_type ENUM('student','personnel','admin') NOT NULL,
        recipient_id INT NOT NULL,
        recipient_email VARCHAR(255) NULL,
        recipient_phone VARCHAR(20) NULL,
        channel ENUM('email','sms','push') NOT NULL,
        template_key VARCHAR(100) NOT NULL,
        subject VARCHAR(255) NULL,
        message TEXT NOT NULL,
        status ENUM('queued','sent','failed','simulated') DEFAULT 'queued',
        reference_type VARCHAR(50) NULL,
        reference_id INT NULL,
        sent_at DATETIME NULL,
        error_message TEXT NULL,
        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
        KEY idx_communication_recipient (recipient_type, recipient_id),
        KEY idx_communication_status (status),
        KEY idx_communication_template (template_key),
        KEY idx_communication_created (created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
];

foreach ($tables as $table => $sql) {
    $existed = tableExists($pdo, $table);
    $pdo->exec($sql);
    echo ($existed ? "Verified" : "Created") . " table {$table}\n";
}

addColumn($pdo, 'enrolments', 'cleared_at', 'DATETIME NULL');
addColumn($pdo, 'enrolment_carts', 'enrolment_id', 'INT NULL');
addIndex($pdo, 'enrolment_carts', 'idx_enrolment_carts_enrolment', '(enrolment_id)');
addConstraint($pdo, 'enrolment_carts', 'fk_enrolment_carts_enrolment', 'FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id) ON DELETE SET NULL');

addColumn($pdo, 'payments', 'status', "ENUM('pending','completed','failed','refunded','partial_refund') NOT NULL DEFAULT 'completed'");
addColumn($pdo, 'payments', 'auth_code', 'VARCHAR(100) NULL');
addColumn($pdo, 'payments', 'completed_at', 'DATETIME NULL');
addColumn($pdo, 'payments', 'refunded_amount', 'DECIMAL(10,2) NOT NULL DEFAULT 0.00');
addColumn($pdo, 'payments', 'refund_reason', 'TEXT NULL');
addColumn($pdo, 'payments', 'refunded_at', 'DATETIME NULL');
$pdo->exec('ALTER TABLE payments MODIFY cashier_id INT NULL');
echo "Verified payments.cashier_id is nullable\n";

addColumn($pdo, 'grade_submission_items', 'course_id', 'INT NULL');
addIndex($pdo, 'grade_submission_items', 'idx_grade_submission_items_course', '(course_id)');
addConstraint($pdo, 'grade_submission_items', 'fk_grade_submission_items_course', 'FOREIGN KEY (course_id) REFERENCES courses(course_id)');

addColumn($pdo, 'rate_limits', 'expires_at', 'DATETIME NULL');

echo "Migration complete.\n";

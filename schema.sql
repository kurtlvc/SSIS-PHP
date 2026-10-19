-- ============================================================================
-- CuyoSIS - CuyoTech University Student Information System
-- Complete Database Schema
-- Integrates core academic tables, authentication, and new modules
-- (Cashier, Department, Registrar, Document Requests)
-- ============================================================================

-- 1. REFERENCE & LOOKUP TABLES
-- ---------------------------------------------------------
CREATE TABLE academic_years (
    ay_id INT AUTO_INCREMENT PRIMARY KEY,
    ay_name VARCHAR(50) NOT NULL,
    is_active TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE semesters (
    sem_id INT AUTO_INCREMENT PRIMARY KEY,
    semester_name VARCHAR(50) NOT NULL,
    is_active TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE days (
    day_id INT AUTO_INCREMENT PRIMARY KEY,
    day_name VARCHAR(20) NOT NULL,
    day_code VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE times (
    time_id INT AUTO_INCREMENT PRIMARY KEY,
    time_start TIME NOT NULL,
    time_end TIME NOT NULL,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rooms (
    room_id INT AUTO_INCREMENT PRIMARY KEY,
    room_name VARCHAR(100) NOT NULL,
    room_type_id INT,
    building_id INT,
    status TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. CORE SYSTEM & ENTITY TABLES
-- ---------------------------------------------------------
CREATE TABLE settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    system_title VARCHAR(255) NOT NULL,
    sem_id INT NOT NULL,
    ay_id INT NOT NULL,
    id_prefix VARCHAR(10),
    rle INT,
    upload_max_mb INT UNSIGNED NOT NULL DEFAULT 10,
    rate_limit_per_minute INT UNSIGNED NOT NULL DEFAULT 60,
    rate_limit_login     INT UNSIGNED NOT NULL DEFAULT 10,
    rate_limit_strict     TINYINT(1) NOT NULL DEFAULT 0,
    singleton TINYINT NOT NULL DEFAULT 1,
    UNIQUE KEY uq_settings_singleton (singleton),
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE personnel (
    p_id INT AUTO_INCREMENT PRIMARY KEY,
    lname VARCHAR(100) NOT NULL,
    fname VARCHAR(100) NOT NULL,
    mname VARCHAR(100),
    ext VARCHAR(10),
    title VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE programs (
    program_id INT AUTO_INCREMENT PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    program_code VARCHAR(50) NOT NULL,
    section_code VARCHAR(50),
    college_id INT,
    p_id INT,
    type INT,
    is_active TINYINT(1) DEFAULT 1,
    is_board TINYINT(1) DEFAULT 0,
    FOREIGN KEY (p_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE curriculums (
    curr_id INT AUTO_INCREMENT PRIMARY KEY,
    ay_id INT NOT NULL,
    program_id INT NOT NULL,
    major_id INT,
    year VARCHAR(10),
    is_active TINYINT(1) DEFAULT 1,
    program_default TINYINT(1) DEFAULT 0,
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
    FOREIGN KEY (program_id) REFERENCES programs(program_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE students (
    stud_id INT AUTO_INCREMENT PRIMARY KEY,
    ref_no VARCHAR(50) UNIQUE,
    lname VARCHAR(100) NOT NULL,
    fname VARCHAR(100) NOT NULL,
    mname VARCHAR(100),
    ext VARCHAR(10),
    gender INT,
    birthdate DATE,
    birthplace VARCHAR(255),
    nationality VARCHAR(50),
    civilstatus INT,
    religion VARCHAR(100),
    emailaddress VARCHAR(255),
    contactno VARCHAR(20),
    address_house VARCHAR(255),
    address_brgy_id INT,
    address_citytown_id INT,
    address_province_id INT,
    address_zipcode INT,
    LRN VARCHAR(50),
    curr_id INT,
    student_type INT,
    is_regular TINYINT(1),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (curr_id) REFERENCES curriculums(curr_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE courses (
    course_id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    major_id INT,
    curr_id INT NOT NULL,
    ay_id INT NOT NULL,
    sem_id INT NOT NULL,
    year_id INT,
    code VARCHAR(50) NOT NULL,
    description VARCHAR(255) NOT NULL,
    units DECIMAL(5,2) NOT NULL,
    lec_units DECIMAL(5,2),
    lab_units DECIMAL(5,2),
    has_lab_room TINYINT(1) DEFAULT 0,
    class_type INT,
    lab_type INT,
    type INT,
    is_review_course TINYINT(1) DEFAULT 0,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (curr_id) REFERENCES curriculums(curr_id),
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. ACADEMIC SCHEDULES & ENROLLMENT
-- ---------------------------------------------------------
CREATE TABLE blocks (
    block_id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    major_id INT,
    year_id INT,
    sequence_no INT,
    block_name VARCHAR(100) NOT NULL,
    is_regular TINYINT(1) DEFAULT 1,
    curr_id INT NOT NULL,
    max INT,
    sem_id INT NOT NULL,
    ay_id INT NOT NULL,
    is_open TINYINT(1) DEFAULT 1,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (curr_id) REFERENCES curriculums(curr_id),
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id),
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE schedules (
    sched_id INT AUTO_INCREMENT PRIMARY KEY,
    class_no INT UNIQUE NOT NULL,
    course_id INT NOT NULL,
    sem_id INT NOT NULL,
    ay_id INT NOT NULL,
    p_id INT NULL,
    class_size INT,
    remarks TEXT,
    nstp_type INT,
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id),
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
    FOREIGN KEY (p_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE blocking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    block_id INT NOT NULL,
    sched_id INT NOT NULL,
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (block_id) REFERENCES blocks(block_id),
    FOREIGN KEY (sched_id) REFERENCES schedules(sched_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE schedule_daytimes (
    daytime_id INT AUTO_INCREMENT PRIMARY KEY,
    sched_id INT NOT NULL,
    day_id INT NOT NULL,
    time_start_id INT NOT NULL,
    time_end_id INT NOT NULL,
    room_id INT NOT NULL,
    type INT,
    status INT,
    dt_type VARCHAR(50),
    teacher_id INT NULL,
    FOREIGN KEY (sched_id) REFERENCES schedules(sched_id),
    FOREIGN KEY (day_id) REFERENCES days(day_id),
    FOREIGN KEY (time_start_id) REFERENCES times(time_id),
    FOREIGN KEY (time_end_id) REFERENCES times(time_id),
    FOREIGN KEY (room_id) REFERENCES rooms(room_id),
    FOREIGN KEY (teacher_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE enrolments (
    enrolment_id INT AUTO_INCREMENT PRIMARY KEY,
    stud_id INT NOT NULL,
    program_id INT NOT NULL,
    major_id INT,
    year_id INT,
    section_id INT,
    sem_id INT NOT NULL,
    ay_id INT NOT NULL,
    curr_id INT NOT NULL,
    total_course INT,
    total_units DECIMAL(5,2),
    isassessed TINYINT(1) DEFAULT 0,
    enrol_status VARCHAR(50),
    is_approved TINYINT(1) DEFAULT 0,
    enrolment_type INT,
    has_regform TINYINT(1) DEFAULT 0,
    is_cleared TINYINT(1) DEFAULT 0,
    college_cleared TINYINT(1) DEFAULT 0,
    registration_date DATETIME,
    FOREIGN KEY (stud_id) REFERENCES students(stud_id),
    FOREIGN KEY (program_id) REFERENCES programs(program_id),
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id),
    FOREIGN KEY (ay_id) REFERENCES academic_years(ay_id),
    FOREIGN KEY (curr_id) REFERENCES curriculums(curr_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. CLASSROOM (POSTS, FILES, SUBMISSIONS)
-- ---------------------------------------------------------
CREATE TABLE question_topics (
    topic_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL UNIQUE,
    is_active TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE question_cases (
    case_id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    body TEXT NULL,
    p_id INT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (p_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE item_sets (
    set_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    p_id INT NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (p_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    post_id INT AUTO_INCREMENT PRIMARY KEY,
    sched_id INT NOT NULL,
    p_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    details TEXT,
    type_id INT NULL,
    term_id INT,
    hps INT,
    start DATETIME,
    deadline DATETIME,
    duration_minutes INT NULL,
    allow_late TINYINT(1) DEFAULT 0,
    set_id INT,
    comp_id INT,
    topic_id INT NULL,
    case_id INT NULL,
    url_key VARCHAR(255) UNIQUE,
    is_visible TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    is_bank TINYINT(1) NOT NULL DEFAULT 0,
    bank_kind VARCHAR(10) NULL,
    posted_at DATETIME,
    INDEX idx_posts_bank (is_bank, p_id),
    FOREIGN KEY (sched_id) REFERENCES schedules(sched_id),
    FOREIGN KEY (p_id) REFERENCES personnel(p_id),
    FOREIGN KEY (topic_id) REFERENCES question_topics(topic_id),
    FOREIGN KEY (case_id) REFERENCES question_cases(case_id),
    FOREIGN KEY (set_id) REFERENCES item_sets(set_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_files (
    file_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    stud_id INT,
    orig_filename VARCHAR(255) NOT NULL,
    filename VARCHAR(255) NOT NULL,
    extension VARCHAR(50),
    size INT,
    drive_key VARCHAR(255),
    uc_link VARCHAR(255),
    view_link VARCHAR(255),
    preview_link VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (stud_id) REFERENCES students(stud_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_links (
    link_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    stud_id INT,
    title VARCHAR(255) NOT NULL,
    url VARCHAR(255) NOT NULL,
    is_embed TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (stud_id) REFERENCES students(stud_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE submissions (
    submit_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    stud_id INT NOT NULL,
    score DECIMAL(5,2),
    hps INT,
    comment TEXT,
    answers JSON NULL,
    status INT,
    submitted_at DATETIME,
    started_at DATETIME NULL,
    FOREIGN KEY (post_id) REFERENCES posts(post_id),
    FOREIGN KEY (stud_id) REFERENCES students(stud_id),
    UNIQUE KEY uq_submission_post_student (post_id, stud_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE item_set_posts (
    set_id INT NOT NULL,
    post_id INT NOT NULL,
    sort INT NOT NULL DEFAULT 0,
    PRIMARY KEY (set_id, post_id),
    FOREIGN KEY (set_id) REFERENCES item_sets(set_id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE post_options (
    option_id INT AUTO_INCREMENT PRIMARY KEY,
    post_id INT NOT NULL,
    label VARCHAR(10) NULL,
    body TEXT NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    sort INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    FOREIGN KEY (post_id) REFERENCES posts(post_id) ON DELETE CASCADE,
    INDEX idx_post_options_post (post_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. COMMUNICATIONS & NOTIFICATIONS
-- ---------------------------------------------------------
CREATE TABLE announcements (
    ann_id INT AUTO_INCREMENT PRIMARY KEY,
    ann_title VARCHAR(255) NULL,
    ann_body TEXT NOT NULL,
    p_id INT NOT NULL,
    ann_status INT,
    ann_index INT,
    is_active TINYINT(1) DEFAULT 1,
    FOREIGN KEY (p_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stud_id INT NULL,
    account_id BIGINT UNSIGNED NULL,
    url VARCHAR(255),
    message TEXT NOT NULL,
    datetime DATETIME,
    timestamp VARCHAR(50),
    seen TINYINT(1) DEFAULT 0,
    FOREIGN KEY (stud_id) REFERENCES students(stud_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. AUTH & ACCOUNTS MODULE (Admin)
-- ---------------------------------------------------------
CREATE TABLE accounts (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    owner_type    ENUM('personnel','student') NOT NULL,
    owner_id      INT NOT NULL,
    login         VARCHAR(190) NOT NULL UNIQUE,
    email         VARCHAR(190) NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          ENUM('student','faculty','chair','dean','admin') NOT NULL,
    is_active     TINYINT(1) NOT NULL DEFAULT 1,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_account_owner (owner_type, owner_id),
    INDEX idx_accounts_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE notifications
  ADD INDEX idx_notifications_account (account_id),
  ADD CONSTRAINT fk_notifications_account
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE;

CREATE TABLE api_tokens (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    INDEX idx_tokens_expiry (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE audit_log (
    id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_id   BIGINT UNSIGNED NULL,
    action     VARCHAR(60) NOT NULL,
    entity     VARCHAR(60) NOT NULL,
    entity_id  VARCHAR(60) NOT NULL,
    meta       JSON NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_audit_actor (actor_id),
    INDEX idx_audit_entity (entity, entity_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE rate_limits (
    id            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key`         VARCHAR(191) NOT NULL,
    route         VARCHAR(128) NOT NULL,
    attempts      SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    window_start  INT UNSIGNED NOT NULL,
    UNIQUE KEY uq_rl_window (`key`, route, window_start),
    INDEX idx_rl_window (window_start)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    account_id BIGINT UNSIGNED NOT NULL,
    token_hash CHAR(64) NOT NULL UNIQUE,
    expires_at DATETIME NOT NULL,
    used_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE,
    INDEX idx_password_resets_expiry (expires_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. REGISTRAR GRADES
-- ---------------------------------------------------------
CREATE TABLE student_grades (
    grade_id INT AUTO_INCREMENT PRIMARY KEY,
    enrolment_id INT NOT NULL,
    course_id INT NOT NULL,
    p_id INT NOT NULL,
    final_grade DECIMAL(3,2),
    remarks VARCHAR(50),
    encoded_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id),
    FOREIGN KEY (course_id) REFERENCES courses(course_id),
    FOREIGN KEY (p_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. CASHIER MODULE (PAYMENTS)
-- ---------------------------------------------------------
CREATE TABLE payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    enrolment_id INT NOT NULL,
    receipt_no VARCHAR(50) UNIQUE NOT NULL,
    amount_paid DECIMAL(10,2) NOT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    cashier_id INT NOT NULL,
    FOREIGN KEY (enrolment_id) REFERENCES enrolments(enrolment_id),
    FOREIGN KEY (cashier_id) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. DEPARTMENT MODULE (CLEARANCES)
-- ---------------------------------------------------------
CREATE TABLE clearance_records (
    clearance_id INT AUTO_INCREMENT PRIMARY KEY,
    stud_id INT NOT NULL,
    sem_id INT NOT NULL,
    department_id INT NOT NULL,
    status ENUM('Pending', 'Cleared', 'On-Hold') DEFAULT 'Pending',
    cleared_by INT,
    cleared_at DATETIME NULL,
    FOREIGN KEY (stud_id) REFERENCES students(stud_id),
    FOREIGN KEY (sem_id) REFERENCES semesters(sem_id),
    FOREIGN KEY (cleared_by) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. DOCUMENT REQUEST SYSTEM
-- ---------------------------------------------------------
CREATE TABLE document_requests (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    stud_id INT NOT NULL,
    document_type ENUM('TOR', 'COR', 'Good Moral', 'Certification') NOT NULL,
    request_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Processing', 'Ready for Pickup', 'Released') DEFAULT 'Pending',
    processed_by INT,
    processed_at DATETIME NULL,
    FOREIGN KEY (stud_id) REFERENCES students(stud_id),
    FOREIGN KEY (processed_by) REFERENCES personnel(p_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. CLEARANCE DEPARTMENTS REFERENCE TABLE
-- ---------------------------------------------------------
CREATE TABLE clearance_departments (
    dept_id INT AUTO_INCREMENT PRIMARY KEY,
    dept_name VARCHAR(100) NOT NULL,
    dept_code VARCHAR(50) NOT NULL UNIQUE,
    is_active TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

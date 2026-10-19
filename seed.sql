-- ============================================================================
-- CuyoSSIS - CuyoTech University Student Services Information System
-- Seed Data for Development & Testing
-- ============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data
DELETE FROM audit_log;
DELETE FROM api_tokens;
DELETE FROM password_resets;
DELETE FROM rate_limits;
DELETE FROM notifications;
DELETE FROM accounts;
DELETE FROM document_requests;
DELETE FROM clearance_records;
DELETE FROM clearance_departments;
DELETE FROM payments;
DELETE FROM student_grades;
DELETE FROM submissions;
DELETE FROM post_options;
DELETE FROM item_set_posts;
DELETE FROM posts;
DELETE FROM item_sets;
DELETE FROM schedule_daytimes;
DELETE FROM blocking;
DELETE FROM schedules;
DELETE FROM blocks;
DELETE FROM enrolments;
DELETE FROM courses;
DELETE FROM students;
DELETE FROM curriculums;
DELETE FROM programs;
DELETE FROM personnel;
DELETE FROM settings;
DELETE FROM semesters;
DELETE FROM academic_years;
DELETE FROM times;
DELETE FROM days;
DELETE FROM rooms;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. REFERENCE DATA
-- ---------------------------------------------------------
INSERT INTO academic_years (ay_id, ay_name, is_active) VALUES
(1, '2026-2027', 1),
(2, '2025-2026', 0),
(3, '2024-2025', 0);

INSERT INTO semesters (sem_id, semester_name, is_active) VALUES
(1, '1st Semester', 1),
(2, '2nd Semester', 0),
(3, 'Summer', 0);

INSERT INTO days (day_id, day_name, day_code) VALUES
(1, 'Monday', 'M'),
(2, 'Tuesday', 'T'),
(3, 'Wednesday', 'W'),
(4, 'Thursday', 'Th'),
(5, 'Friday', 'F'),
(6, 'Saturday', 'S');

INSERT INTO times (time_id, time_start, time_end, is_active) VALUES
(1, '08:00:00', '09:00:00', 1),
(2, '09:00:00', '10:00:00', 1),
(3, '10:00:00', '11:00:00', 1),
(4, '11:00:00', '12:00:00', 1),
(5, '13:00:00', '14:00:00', 1),
(6, '14:00:00', '15:00:00', 1),
(7, '15:00:00', '16:00:00', 1);

INSERT INTO rooms (room_id, room_name, room_type_id, building_id, status) VALUES
(1, 'Room 101', 1, 1, 1),
(2, 'Room 102', 1, 1, 1),
(3, 'Room 201', 1, 1, 1),
(4, 'Lab 1', 2, 1, 1),
(5, 'Lab 2', 2, 1, 1),
(6, 'Room 202', 1, 1, 1);

-- 2. PERSONNEL
-- ---------------------------------------------------------
INSERT INTO personnel (p_id, lname, fname, mname, ext, title, is_active) VALUES
(1, 'Admin', 'System', NULL, NULL, 'Dr.', 1),
(2, 'Smith', 'John', 'M', NULL, 'Prof.', 1),
(3, 'Johnson', 'Maria', 'S', NULL, 'Prof.', 1),
(4, 'Williams', 'David', 'R', NULL, 'Mr.', 1),
(5, 'Brown', 'Sarah', 'K', NULL, 'Ms.', 1),
(6, 'Davis', 'Robert', 'L', NULL, 'Prof.', 1),
(7, 'Garcia', 'Elena', 'P', NULL, 'Dr.', 1),
(8, 'Miller', 'James', 'T', NULL, 'Mr.', 1),
(9, 'Wilson', 'Lisa', 'A', NULL, 'Prof.', 1),
(10, 'Taylor', 'Michael', 'B', NULL, 'Mr.', 1);

-- 3. PROGRAMS
-- ---------------------------------------------------------
INSERT INTO programs (program_id, description, program_code, section_code, college_id, p_id, type, is_active, is_board) VALUES
(1, 'Bachelor of Science in Computer Science', 'BSCS', 'CS', 1, 7, 1, 1, 0),
(2, 'Bachelor of Science in Information Technology', 'BSIT', 'IT', 1, 7, 1, 1, 0),
(3, 'Bachelor of Science in Business Administration', 'BSBA', 'BA', 2, 8, 1, 1, 0),
(4, 'Bachelor of Arts in Psychology', 'ABPsyc', 'Psy', 2, 9, 1, 1, 0);

-- 4. CURRICULA
-- ---------------------------------------------------------
INSERT INTO curriculums (curr_id, ay_id, program_id, major_id, year, is_active, program_default) VALUES
(1, 1, 1, NULL, '2021', 1, 1),
(2, 1, 2, NULL, '2021', 1, 1),
(3, 1, 3, NULL, '2020', 1, 1),
(4, 1, 4, NULL, '2020', 1, 1);

-- 5. STUDENTS
-- ---------------------------------------------------------
INSERT INTO students (stud_id, ref_no, lname, fname, mname, ext, gender, birthdate, birthplace, nationality, civilstatus, religion, emailaddress, contactno, address_house, address_brgy_id, address_citytown_id, address_province_id, address_zipcode, LRN, curr_id, student_type, is_regular, created_at, updated_at) VALUES
(1, '2026-0001', 'Aguilar', 'Juan', 'P', NULL, 1, '2003-05-15', 'Manila', 'Filipino', 1, 'Roman Catholic', 'juan.aguilar@student.cuytech.edu', '09171234567', '123 Rizal St', 1, 1, 1, 1000, '123456789012', 1, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(2, '2026-0002', 'Santos', 'Maria', 'C', NULL, 0, '2003-08-22', 'Quezon City', 'Filipino', 1, 'Roman Catholic', 'maria.santos@student.cuytech.edu', '09182345678', '456 Mabini St', 1, 1, 1, 1000, '123456789013', 1, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(3, '2026-0003', 'Reyes', 'Carlos', 'M', 'Jr.', 1, '2002-11-30', 'Cebu City', 'Filipino', 2, 'Roman Catholic', 'carlos.reyes@student.cuytech.edu', '09193456789', '789 Bonifacio St', 1, 1, 1, 1000, '123456789014', 2, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(4, '2026-0004', 'Cruz', 'Ana', 'L', NULL, 0, '2003-02-14', 'Davao City', 'Filipino', 1, 'Born Again', 'ana.cruz@student.cuytech.edu', '09204567890', '321 EDSA', 1, 1, 1, 1000, '123456789015', 3, 1, 0, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(5, '2026-0005', 'Torres', 'Miguel', 'A', NULL, 1, '2002-07-25', 'Iloilo City', 'Filipino', 1, 'Roman Catholic', 'miguel.torres@student.cuytech.edu', '09215678901', '654 Quezon Ave', 1, 1, 1, 1000, '123456789016', 4, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(6, '2026-0006', 'Lee', 'Sophia', 'W', NULL, 0, '2003-12-03', 'Zamboanga City', 'Filipino', 1, 'Roman Catholic', 'sophia.lee@student.cuytech.edu', '09226789012', '987 Commonwealth Ave', 1, 1, 1, 1000, '123456789017', 1, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(7, '2026-0007', 'Castro', 'David', 'R', NULL, 1, '2003-04-18', 'Bacolod City', 'Filipino', 1, 'Roman Catholic', 'david.castro@student.cuytech.edu', '09237890123', '111 Taft Ave', 1, 1, 1, 1000, '123456789018', 2, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(8, '2026-0008', 'Diaz', 'Isabella', 'M', NULL, 0, '2002-09-10', 'Baguio City', 'Filipino', 3, 'Agnostic', 'isabella.diaz@student.cuytech.edu', '09248901234', '222 Roxas Blvd', 1, 1, 1, 1000, '123456789019', 3, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(9, '2026-0009', 'Martinez', 'Ethan', 'J', NULL, 1, '2003-03-21', 'Cagayan de Oro', 'Filipino', 1, 'Roman Catholic', 'ethan.martinez@student.cuytech.edu', '09259012345', '333 Del Pilar St', 1, 1, 1, 1000, '123456789020', 4, 1, 0, '2026-10-01 08:00:00', '2026-10-01 08:00:00'),
(10, '2026-0010', 'Robinson', 'Olivia', 'K', NULL, 0, '2002-06-12', 'General Santos', 'Filipino', 1, 'Christian', 'olivia.robinson@student.cuytech.edu', '09260123456', '444 Session Rd', 1, 1, 1, 1000, '123456789021', 1, 1, 1, '2026-10-01 08:00:00', '2026-10-01 08:00:00');

-- 6. COURSES
-- ---------------------------------------------------------
INSERT INTO courses (course_id, program_id, major_id, curr_id, ay_id, sem_id, year_id, code, description, units, lec_units, lab_units, has_lab_room, class_type, lab_type, type, is_review_course) VALUES
(1, 1, NULL, 1, 1, 1, 1, 'CS101', 'Introduction to Programming', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(2, 1, NULL, 1, 1, 1, 1, 'CS102', 'Computer Programming 1', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(3, 1, NULL, 1, 1, 1, 1, 'CS103', 'Discrete Structures', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(4, 1, NULL, 1, 1, 1, 1, 'CS104', 'Database Systems', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(5, 1, NULL, 1, 1, 1, 1, 'CS105', 'Networking Fundamentals', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(6, 2, NULL, 2, 1, 1, 1, 'IT101', 'Introduction to IT', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(7, 2, NULL, 2, 1, 1, 1, 'IT102', 'System Administration', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(8, 2, NULL, 2, 1, 1, 1, 'IT103', 'Web Development', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(9, 2, NULL, 2, 1, 1, 1, 'IT104', 'Mobile Applications', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(10, 3, NULL, 3, 1, 1, 1, 'BA101', 'Business Mathematics', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(11, 3, NULL, 3, 1, 1, 1, 'BA102', 'Financial Accounting', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(12, 4, NULL, 4, 1, 1, 1, 'Psy101', 'Introduction to Psychology', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0),
(13, 4, NULL, 4, 1, 1, 1, 'Psy102', 'Social Psychology', 3.0, 3.0, 0.0, 0, 1, NULL, 1, 0);

-- 7. SCHEDULES
-- ---------------------------------------------------------
INSERT INTO schedules (sched_id, class_no, course_id, sem_id, ay_id, p_id, class_size, remarks) VALUES
(1, 1001, 1, 1, 1, 2, 40, ''),
(2, 1002, 2, 1, 1, 3, 40, ''),
(3, 1003, 3, 1, 1, 6, 40, ''),
(4, 1004, 4, 1, 1, 4, 40, ''),
(5, 1005, 5, 1, 1, 5, 40, ''),
(6, 2001, 6, 1, 1, 2, 40, ''),
(7, 2002, 7, 1, 1, 3, 40, ''),
(8, 2003, 8, 1, 1, 4, 40, ''),
(9, 2004, 9, 1, 1, 5, 40, ''),
(10, 3001, 10, 1, 1, 6, 50, ''),
(11, 3002, 11, 1, 1, 4, 50, ''),
(12, 4001, 12, 1, 1, 7, 45, ''),
(13, 4002, 13, 1, 1, 9, 45, '');

INSERT INTO schedule_daytimes (daytime_id, sched_id, day_id, time_start_id, time_end_id, room_id, type, status, dt_type, teacher_id) VALUES
(1, 1, 1, 1, 3, 1, 1, 1, 'regular', 2),
(2, 1, 3, 1, 3, 2, 1, 1, 'regular', 2),
(3, 2, 2, 4, 6, 1, 1, 1, 'regular', 3),
(4, 2, 4, 4, 6, 3, 1, 1, 'regular', 3),
(5, 3, 1, 1, 3, 4, 1, 1, 'regular', 6),
(6, 4, 5, 5, 7, 5, 1, 1, 'regular', 4),
(7, 5, 2, 5, 7, 1, 1, 1, 'regular', 5),
(8, 6, 3, 4, 6, 2, 1, 1, 'regular', 2),
(9, 7, 5, 4, 6, 4, 1, 1, 'regular', 3),
(10, 8, 1, 5, 7, 3, 1, 1, 'regular', 4),
(11, 9, 4, 5, 7, 5, 1, 1, 'regular', 5),
(12, 10, 2, 1, 3, 6, 1, 1, 'regular', 6),
(13, 11, 5, 4, 6, 1, 1, 1, 'regular', 4),
(14, 12, 3, 5, 7, 2, 1, 1, 'regular', 7),
(15, 13, 4, 4, 6, 4, 1, 1, 'regular', 9);

-- 8. ENROLMENTS
-- ---------------------------------------------------------
INSERT INTO enrolments (enrolment_id, stud_id, program_id, major_id, year_id, section_id, sem_id, ay_id, curr_id, total_course, total_units, isassessed, enrol_status, is_approved, enrolment_type, has_regform, is_cleared, college_cleared, registration_date) VALUES
(1, 1, 1, NULL, 1, 1, 1, 1, 1, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 09:00:00'),
(2, 2, 1, NULL, 1, 1, 1, 1, 1, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 09:30:00'),
(3, 3, 2, NULL, 1, 2, 1, 1, 2, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 10:00:00'),
(4, 4, 3, NULL, 1, 1, 1, 1, 3, 5, 15.0, 0, 'Pending Assessment', 0, 1, 0, 0, 0, '2026-10-05 10:30:00'),
(5, 5, 4, NULL, 1, 1, 1, 1, 4, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 1, 0, '2026-10-05 11:00:00'),
(6, 6, 1, NULL, 1, 1, 1, 1, 1, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 11:30:00'),
(7, 7, 2, NULL, 1, 3, 1, 1, 2, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 12:00:00'),
(8, 8, 3, NULL, 1, 1, 1, 1, 3, 5, 15.0, 0, 'Pending Assessment', 0, 1, 0, 0, 0, '2026-10-05 12:30:00'),
(9, 9, 4, NULL, 1, 1, 1, 1, 4, 4, 12.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 13:00:00'),
(10, 10, 1, NULL, 1, 1, 1, 1, 1, 5, 15.0, 1, 'Enrolled', 1, 1, 1, 0, 0, '2026-10-05 13:30:00');

-- 9. STUDENT_GRADES
-- ---------------------------------------------------------
INSERT INTO student_grades (grade_id, enrolment_id, course_id, p_id, final_grade, remarks, encoded_at) VALUES
(1, 1, 1, 2, 3.0, 'Passed', '2026-09-20 10:00:00'),
(2, 1, 2, 3, 2.75, 'Passed', '2026-09-20 10:00:00'),
(3, 1, 3, 6, 3.0, 'Passed', '2026-09-20 10:00:00'),
(4, 5, 12, 7, 3.0, 'Passed', '2026-09-20 10:00:00'),
(5, 5, 13, 9, 2.5, 'Passed', '2026-09-20 10:00:00'),
(6, 9, 12, 7, 3.0, 'Passed', '2026-09-20 10:00:00');

-- 10. PAYMENTS
-- ---------------------------------------------------------
INSERT INTO payments (payment_id, enrolment_id, receipt_no, amount_paid, payment_date, cashier_id) VALUES
(1, 1, 'RCPT-2026-AY1-00001', 25000.00, '2026-10-06 09:00:00', 8),
(2, 2, 'RCPT-2026-AY1-00002', 25000.00, '2026-10-06 09:30:00', 8),
(3, 3, 'RCPT-2026-AY1-00003', 15000.00, '2026-10-06 10:00:00', 10),
(4, 5, 'RCPT-2026-AY1-00004', 30000.00, '2026-10-06 11:00:00', 8),
(5, 6, 'RCPT-2026-AY1-00005', 25000.00, '2026-10-06 11:30:00', 10);

-- 11. CLEARANCE DEPARTMENTS
-- ---------------------------------------------------------
INSERT INTO clearance_departments (dept_id, dept_name, dept_code, is_active) VALUES
(1, 'Library', 'LIB', 1),
(2, 'Laboratory', 'LAB', 1),
(3, 'Finance/Cashier', 'FIN', 1),
(4, 'Academic Affairs', 'AAF', 1),
(5, 'Student Affairs', 'STD', 1),
(6, 'Information Technology', 'ITD', 1);

-- 12. CLEARANCE RECORDS
-- ---------------------------------------------------------
INSERT INTO clearance_records (clearance_id, stud_id, sem_id, department_id, status, cleared_by, cleared_at) VALUES
(1, 1, 1, 1, 'Cleared', 10, '2026-10-07 10:00:00'),
(2, 1, 1, 2, 'Cleared', 10, '2026-10-07 10:05:00'),
(3, 1, 1, 3, 'Pending', NULL, NULL),
(4, 1, 1, 4, 'On-Hold', 10, NULL),
(5, 2, 1, 1, 'Cleared', 8, '2026-10-07 11:00:00'),
(6, 2, 1, 2, 'Pending', NULL, NULL),
(7, 2, 1, 3, 'Cleared', 8, '2026-10-07 11:05:00'),
(8, 3, 1, 1, 'On-Hold', 10, NULL),
(9, 5, 1, 1, 'Cleared', 8, '2026-10-07 12:00:00'),
(10, 5, 1, 2, 'Cleared', 8, '2026-10-07 12:05:00');

-- 13. DOCUMENT REQUESTS
-- ---------------------------------------------------------
INSERT INTO document_requests (request_id, stud_id, document_type, request_date, status, processed_by, processed_at) VALUES
(1, 1, 'TOR', '2026-10-08 09:00:00', 'Ready for Pickup', 6, '2026-10-08 10:30:00'),
(2, 5, 'COR', '2026-10-08 10:00:00', 'Pending', NULL, NULL),
(3, 9, 'Good Moral', '2026-10-08 11:00:00', 'Processing', 6, '2026-10-08 11:30:00'),
(4, 1, 'Certification', '2026-10-08 12:00:00', 'Released', 6, '2026-10-08 14:00:00'),
(5, 2, 'TOR', '2026-10-09 09:00:00', 'Pending', NULL, NULL);

-- 14. ACCOUNTS
-- ---------------------------------------------------------
INSERT INTO accounts (id, owner_type, owner_id, login, email, password_hash, role, is_active, created_at) VALUES
(1, 'personnel', 1, 'admin', 'admin@cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'admin', 1, '2026-10-01 00:00:00'),
(2, 'personnel', 2, 'prof.smith', 'smith@cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'faculty', 1, '2026-10-01 00:00:00'),
(3, 'personnel', 7, 'dean.garcia', 'garcia@cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'dean', 1, '2026-10-01 00:00:00'),
(4, 'personnel', 8, 'cashier.miller', 'miller@cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'faculty', 1, '2026-10-01 00:00:00'),
(5, 'student', 1, 'student001', 'juan.aguilar@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(6, 'student', 2, 'student002', 'maria.santos@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(7, 'student', 3, 'student003', 'carlos.reyes@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(8, 'student', 4, 'student004', 'ana.cruz@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(9, 'student', 5, 'student005', 'miguel.torres@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(10, 'student', 6, 'student006', 'sophia.lee@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(11, 'student', 7, 'student007', 'david.castro@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(12, 'student', 8, 'student008', 'isabella.diaz@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(13, 'student', 9, 'student009', 'ethan.martinez@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00'),
(14, 'student', 10, 'student010', 'olivia.robinson@student.cuytech.edu', '$2y$12$Iex93s7RLrav9U9lsRyFheHZNOUjok0C60rveEapRh4k2PhpE7VYC', 'student', 1, '2026-10-01 00:00:00');

-- 15. SETTINGS
-- ---------------------------------------------------------
INSERT INTO settings (system_title, sem_id, ay_id, id_prefix, rle, upload_max_mb, rate_limit_per_minute, rate_limit_login, rate_limit_strict, singleton) VALUES
('CuyoSSIS - CuyoTech University', 1, 1, 'CUY', 4, 10, 60, 10, 0, 1);

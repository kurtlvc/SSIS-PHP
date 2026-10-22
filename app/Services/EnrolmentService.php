<?php

class EnrolmentService
{
    private $db;
    private $config;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->config = App::getInstance()->getConfig();
    }

    /**
     * Get or create student's enrollment cart
     */
    public function getOrCreateCart(int $studId, int $semId, int $ayId): array
    {
        $cart = $this->db->fetchOne(
            "SELECT * FROM enrolment_carts WHERE stud_id = :stud_id AND sem_id = :sem_id AND ay_id = :ay_id",
            ['stud_id' => $studId, 'sem_id' => $semId, 'ay_id' => $ayId]
        );

        if (!$cart) {
            $cartId = $this->db->insert('enrolment_carts', [
                'stud_id' => $studId,
                'sem_id' => $semId,
                'ay_id' => $ayId,
                'status' => 'draft',
                'total_units' => 0
            ]);
            $cart = ['cart_id' => $cartId, 'status' => 'draft', 'total_units' => 0];
        }

        return $cart;
    }

    /**
     * Add schedule to cart
     */
    public function addToCart(int $cartId, int $schedId): array
    {
        $existing = $this->db->fetchOne(
            "SELECT * FROM enrolment_cart_items WHERE cart_id = :cart_id AND sched_id = :sched_id",
            ['cart_id' => $cartId, 'sched_id' => $schedId]
        );

        if ($existing) {
            return ['success' => false, 'error' => 'Schedule already in cart'];
        }

        $schedule = $this->db->fetchOne(
            "SELECT s.*, c.code, c.description, c.units 
             FROM schedules s 
             JOIN courses c ON s.course_id = c.course_id 
             WHERE s.sched_id = :sched_id",
            ['sched_id' => $schedId]
        );

        if (!$schedule) {
            return ['success' => false, 'error' => 'Schedule not found'];
        }

        if ($schedule['class_size'] <= 0) {
            return ['success' => false, 'error' => 'Class is full'];
        }

        $this->db->insert('enrolment_cart_items', [
            'cart_id' => $cartId,
            'sched_id' => $schedId
        ]);

        $this->updateCartTotals($cartId);

        return [
            'success' => true,
            'schedule' => $schedule
        ];
    }

    /**
     * Remove from cart
     */
    public function removeFromCart(int $cartId, int $schedId): array
    {
        $this->db->delete('enrolment_cart_items', 'cart_id = :cart_id AND sched_id = :sched_id', [
            'cart_id' => $cartId,
            'sched_id' => $schedId
        ]);

        $this->updateCartTotals($cartId);

        return ['success' => true];
    }

    /**
     * Get cart items with schedule details
     */
    public function getCartItems(int $cartId): array
    {
        $sql = "SELECT ci.*, s.class_no, c.code, c.description, c.units, c.lec_units, c.lab_units,
                       p.lname as prof_lname, p.fname as prof_fname,
                       d.day_name, t.time_start, t.time_end, r.room_name
                FROM enrolment_cart_items ci
                JOIN schedules s ON ci.sched_id = s.sched_id
                JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN personnel p ON s.p_id = p.p_id
                LEFT JOIN schedule_daytimes sd ON s.sched_id = sd.sched_id
                LEFT JOIN days d ON sd.day_id = d.day_id
                LEFT JOIN times t ON sd.time_start_id = t.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                WHERE ci.cart_id = :cart_id
                ORDER BY ci.added_at";
        return $this->db->fetchAll($sql, ['cart_id' => $cartId]);
    }

    /**
     * Update cart total units
     */
    private function updateCartTotals(int $cartId): void
    {
        $sql = "SELECT SUM(c.units) as total_units 
                FROM enrolment_cart_items ci
                JOIN schedules s ON ci.sched_id = s.sched_id
                JOIN courses c ON s.course_id = c.course_id
                WHERE ci.cart_id = :cart_id";
        $row = $this->db->fetchOne($sql, ['cart_id' => $cartId]);
        $totalUnits = $row ? (int)$row['total_units'] : 0;

        $this->db->update('enrolment_carts', ['total_units' => $totalUnits], 'cart_id = :id', ['id' => $cartId]);
    }

    /**
     * Submit cart for assessment
     */
    public function submitForAssessment(int $cartId): array
    {
        $cart = $this->db->fetchOne('SELECT * FROM enrolment_carts WHERE cart_id = :id', ['id' => $cartId]);
        if (!$cart) {
            return ['success' => false, 'error' => 'Cart not found'];
        }

        if ($cart['status'] !== 'draft') {
            return ['success' => false, 'error' => 'Cart already submitted'];
        }

        $items = $this->getCartItems($cartId);
        if (empty($items)) {
            return ['success' => false, 'error' => 'Cart is empty'];
        }

        $enrolmentId = $this->db->insert('enrolments', [
            'stud_id' => $cart['stud_id'],
            'sem_id' => $cart['sem_id'],
            'ay_id' => $cart['ay_id'],
            'total_course' => count($items),
            'total_units' => $cart['total_units'],
            'isassessed' => 1,
            'enrol_status' => 'pending_assessment',
            'is_approved' => 0,
            'enrolment_type' => 'regular',
            'registration_date' => date('Y-m-d')
        ]);

        foreach ($items as $item) {
            $this->db->insert('enrolment_courses', [
                'enrolment_id' => $enrolmentId,
                'sched_id' => $item['sched_id']
            ]);
        }

        $this->db->update('enrolment_carts', ['status' => 'assessed'], 'cart_id = :id', ['id' => $cartId]);

        $this->createAssessment($enrolmentId, $items);

        return [
            'success' => true,
            'enrolment_id' => $enrolmentId,
            'message' => 'Enrollment submitted for assessment'
        ];
    }

    /**
     * Create assessment based on cart items
     */
    private function createAssessment(int $enrolmentId, array $items): int
    {
        $totalUnits = 0;
        foreach ($items as $item) {
            $totalUnits += $item['units'];
        }

        $tuitionPerUnit = $this->config['fees']['tuition_per_unit'] ?? 1500;
        $miscFee = $this->config['fees']['misc_fee'] ?? 2000;
        $labFeePerUnit = $this->config['fees']['lab_fee_per_unit'] ?? 500;

        $tuitionFee = $totalUnits * $tuitionPerUnit;
        $labFee = 0;
        foreach ($items as $item) {
            if ($item['lab_units'] > 0) {
                $labFee += $item['lab_units'] * $labFeePerUnit;
            }
        }

        $assessmentId = $this->db->insert('enrolment_assessments', [
            'enrolment_id' => $enrolmentId,
            'tuition_fee' => $tuitionFee,
            'misc_fee' => $miscFee,
            'lab_fee' => $labFee,
            'other_fees' => 0,
            'status' => 'pending',
            'assessed_at' => date('Y-m-d H:i:s')
        ]);

        return $assessmentId;
    }

    /**
     * Get available schedules for enrollment
     */
    public function getAvailableSchedules(int $studId, int $semId, int $ayId): array
    {
        $student = $this->db->fetchOne('SELECT curr_id, program_id FROM students WHERE stud_id = :id', ['id' => $studId]);
        if (!$student) return [];

        $currId = $student['curr_id'];

        $sql = "SELECT s.sched_id, s.class_no, c.code, c.description, c.units, c.lec_units, c.lab_units,
                       p.lname as prof_lname, p.fname as prof_fname, p.p_id as prof_id,
                       s.class_size, s.remarks,
                       d.day_name, d.day_code,
                       ts.time_start, te.time_end,
                       r.room_name
                FROM schedules s
                JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN personnel p ON s.p_id = p.p_id
                LEFT JOIN schedule_daytimes sd ON s.sched_id = sd.sched_id
                LEFT JOIN days d ON sd.day_id = d.day_id
                LEFT JOIN times ts ON sd.time_start_id = ts.time_id
                LEFT JOIN times te ON sd.time_end_id = te.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                WHERE c.curr_id = :curr_id 
                AND s.sem_id = :sem_id 
                AND s.ay_id = :ay_id
                AND s.class_size > 0
                ORDER BY c.code, s.class_no";

        return $this->db->fetchAll($sql, [
            'curr_id' => $currId,
            'sem_id' => $semId,
            'ay_id' => $ayId
        ]);
    }

    /**
     * Get student's current enrollments
     */
    public function getStudentEnrolments(int $studId): array
    {
        $sql = "SELECT e.*, ay.ay_name, sem.semester_name, p.program_code, p.description as program_desc
                FROM enrolments e
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE e.stud_id = :stud_id
                ORDER BY e.registration_date DESC";
        return $this->db->fetchAll($sql, ['stud_id' => $studId]);
    }

    /**
     * Get assessment for enrolment
     */
    public function getAssessment(int $enrolmentId): ?array
    {
        return $this->db->fetchOne('SELECT * FROM enrolment_assessments WHERE enrolment_id = :id', ['id' => $enrolmentId]);
    }

    /**
     * Get enrolment courses
     */
    public function getEnrolmentCourses(int $enrolmentId): array
    {
        $sql = "SELECT ec.*, c.code, c.description, c.units, c.lec_units, c.lab_units,
                       s.class_no, p.lname as prof_lname, p.fname as prof_fname
                FROM enrolment_courses ec
                JOIN schedules s ON ec.sched_id = s.sched_id
                JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN personnel p ON s.p_id = p.p_id
                WHERE ec.enrolment_id = :enrolment_id";
        return $this->db->fetchAll($sql, ['enrolment_id' => $enrolmentId]);
    }

    /**
     * Get semester name by ID
     */
    public function getSemesterName(int $semId): ?string
    {
        $row = $this->db->fetchOne('SELECT semester_name FROM semesters WHERE sem_id = :id', ['id' => $semId]);
        return $row ? $row['semester_name'] : null;
    }
}

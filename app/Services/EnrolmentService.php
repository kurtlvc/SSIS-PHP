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
     * Get or create a student's enrollment cart for a term.
     */
    public function getOrCreateCart(int $studId, int $semId, int $ayId): array
    {
        $cart = $this->db->fetchOne(
            "SELECT * FROM enrolment_carts
             WHERE stud_id = :stud_id AND sem_id = :sem_id AND ay_id = :ay_id",
            ['stud_id' => $studId, 'sem_id' => $semId, 'ay_id' => $ayId]
        );

        if (!$cart) {
            $cartId = $this->db->insert('enrolment_carts', [
                'stud_id' => $studId,
                'sem_id' => $semId,
                'ay_id' => $ayId,
                'status' => 'draft',
                'total_units' => 0,
            ]);
            $cart = [
                'cart_id' => (int) $cartId,
                'stud_id' => $studId,
                'sem_id' => $semId,
                'ay_id' => $ayId,
                'enrolment_id' => null,
                'status' => 'draft',
                'total_units' => 0,
            ];
        }

        return $cart;
    }

    /**
     * Add a schedule to the cart.
     */
    public function addToCart(int $cartId, int $schedId): array
    {
        $cart = $this->db->fetchOne('SELECT * FROM enrolment_carts WHERE cart_id = :id', ['id' => $cartId]);
        if (!$cart) {
            return ['success' => false, 'error' => 'Cart not found'];
        }
        if ($cart['status'] !== 'draft') {
            return ['success' => false, 'error' => 'This cart can no longer be modified'];
        }

        $existing = $this->db->fetchOne(
            "SELECT 1 FROM enrolment_cart_items WHERE cart_id = :cart_id AND sched_id = :sched_id",
            ['cart_id' => $cartId, 'sched_id' => $schedId]
        );
        if ($existing) {
            return ['success' => false, 'error' => 'Schedule already in cart'];
        }

        $schedule = $this->db->fetchOne(
            "SELECT s.*, c.code, c.description, c.units, c.curr_id
             FROM schedules s
             JOIN courses c ON s.course_id = c.course_id
             WHERE s.sched_id = :sched_id",
            ['sched_id' => $schedId]
        );

        if (!$schedule) {
            return ['success' => false, 'error' => 'Schedule not found'];
        }

        $student = $this->db->fetchOne(
            "SELECT s.stud_id, s.curr_id FROM students s WHERE s.stud_id = :id",
            ['id' => $cart['stud_id']]
        );
        if (!$student || (int) $student['curr_id'] !== (int) $schedule['curr_id']) {
            return ['success' => false, 'error' => 'This subject is not part of your curriculum'];
        }
        if ((int) $schedule['sem_id'] !== (int) $cart['sem_id'] || (int) $schedule['ay_id'] !== (int) $cart['ay_id']) {
            return ['success' => false, 'error' => 'This schedule is not offered in the selected term'];
        }
        if ((int) $schedule['class_size'] <= 0) {
            return ['success' => false, 'error' => 'Class is full'];
        }

        $conflict = $this->db->fetchOne(
            "SELECT 1
             FROM enrolment_cart_items ci
             JOIN schedule_daytimes current_dt ON current_dt.sched_id = ci.sched_id
             JOIN schedule_daytimes new_dt ON new_dt.sched_id = :sched_id
             WHERE ci.cart_id = :cart_id
               AND current_dt.day_id = new_dt.day_id
               AND NOT (current_dt.time_end_id <= new_dt.time_start_id OR current_dt.time_start_id >= new_dt.time_end_id)
             LIMIT 1",
            ['cart_id' => $cartId, 'sched_id' => $schedId]
        );
        if ($conflict) {
            return ['success' => false, 'error' => 'This schedule conflicts with another subject in your cart'];
        }

        $this->db->insert('enrolment_cart_items', [
            'cart_id' => $cartId,
            'sched_id' => $schedId,
        ]);

        $this->updateCartTotals($cartId);

        return ['success' => true, 'schedule' => $schedule];
    }

    /**
     * Remove a schedule from the cart.
     */
    public function removeFromCart(int $cartId, int $schedId): array
    {
        $cart = $this->db->fetchOne('SELECT status FROM enrolment_carts WHERE cart_id = :id', ['id' => $cartId]);
        if (!$cart || $cart['status'] !== 'draft') {
            return ['success' => false, 'error' => 'This cart can no longer be modified'];
        }

        $this->db->delete(
            'enrolment_cart_items',
            'cart_id = :cart_id AND sched_id = :sched_id',
            ['cart_id' => $cartId, 'sched_id' => $schedId]
        );

        $this->updateCartTotals($cartId);

        return ['success' => true];
    }

    /**
     * Get cart items with one row per schedule and a combined schedule label.
     */
    public function getCartItems(int $cartId): array
    {
        $sql = "SELECT ci.item_id, ci.cart_id, ci.sched_id, ci.added_at,
                       s.class_no, s.class_size, c.code, c.description, c.units,
                       c.lec_units, c.lab_units,
                       p.lname AS prof_lname, p.fname AS prof_fname,
                       GROUP_CONCAT(DISTINCT CONCAT(d.day_code, ' ',
                           TIME_FORMAT(ts.time_start, '%H:%i'), '-',
                           TIME_FORMAT(te.time_end, '%H:%i'), ' ', r.room_name)
                           ORDER BY d.day_id SEPARATOR ' / ') AS schedule_info
                FROM enrolment_cart_items ci
                JOIN schedules s ON ci.sched_id = s.sched_id
                JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN personnel p ON s.p_id = p.p_id
                LEFT JOIN schedule_daytimes sd ON s.sched_id = sd.sched_id
                LEFT JOIN days d ON sd.day_id = d.day_id
                LEFT JOIN times ts ON sd.time_start_id = ts.time_id
                LEFT JOIN times te ON sd.time_end_id = te.time_id
                LEFT JOIN rooms r ON sd.room_id = r.room_id
                WHERE ci.cart_id = :cart_id
                GROUP BY ci.item_id, ci.cart_id, ci.sched_id, ci.added_at,
                         s.class_no, s.class_size, c.code, c.description, c.units,
                         c.lec_units, c.lab_units, p.lname, p.fname
                ORDER BY c.code";
        return $this->db->fetchAll($sql, ['cart_id' => $cartId]);
    }

    /**
     * Recalculate cart units from distinct schedules.
     */
    private function updateCartTotals(int $cartId): void
    {
        $row = $this->db->fetchOne(
            "SELECT COALESCE(SUM(c.units), 0) AS total_units
             FROM enrolment_cart_items ci
             JOIN schedules s ON ci.sched_id = s.sched_id
             JOIN courses c ON s.course_id = c.course_id
             WHERE ci.cart_id = :cart_id",
            ['cart_id' => $cartId]
        );
        $totalUnits = $row ? (float) $row['total_units'] : 0;

        $this->db->update('enrolment_carts', ['total_units' => $totalUnits], 'cart_id = :id', ['id' => $cartId]);
    }

    /**
     * Submit a cart for registrar assessment.
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

        $student = $this->db->fetchOne(
            "SELECT s.stud_id, s.is_regular, s.curr_id, c.program_id
             FROM students s
             JOIN curriculums c ON s.curr_id = c.curr_id
             WHERE s.stud_id = :id",
            ['id' => $cart['stud_id']]
        );
        if (!$student) {
            return ['success' => false, 'error' => 'Student curriculum record not found'];
        }

        $existing = $this->db->fetchOne(
            "SELECT enrolment_id FROM enrolments
             WHERE stud_id = :stud_id AND sem_id = :sem_id AND ay_id = :ay_id
               AND COALESCE(enrol_status, '') NOT IN ('Dropped', 'Rejected')
             LIMIT 1",
            [
                'stud_id' => $cart['stud_id'],
                'sem_id' => $cart['sem_id'],
                'ay_id' => $cart['ay_id'],
            ]
        );
        if ($existing) {
            return ['success' => false, 'error' => 'You already have an enrollment for this term'];
        }

        $term = $this->db->fetchOne(
            "SELECT ay.ay_name, sem.semester_name
             FROM academic_years ay CROSS JOIN semesters sem
             WHERE ay.ay_id = :ay_id AND sem.sem_id = :sem_id",
            ['ay_id' => $cart['ay_id'], 'sem_id' => $cart['sem_id']]
        );

        $totalUnits = 0.0;
        foreach ($items as $item) {
            $totalUnits += (float) $item['units'];
        }

        $this->db->beginTransaction();
        try {
            $enrolmentId = $this->db->insert('enrolments', [
                'stud_id' => $cart['stud_id'],
                'program_id' => $student['program_id'],
                'sem_id' => $cart['sem_id'],
                'ay_id' => $cart['ay_id'],
                'curr_id' => $student['curr_id'],
                'total_course' => count($items),
                'total_units' => $totalUnits,
                'isassessed' => 0,
                'enrol_status' => 'Pending Assessment',
                'is_approved' => 0,
                'enrolment_type' => $student['is_regular'] ? 1 : 2,
                'has_regform' => 0,
                'is_cleared' => 0,
                'college_cleared' => 0,
                'registration_date' => date('Y-m-d H:i:s'),
            ]);

            foreach ($items as $item) {
                $this->db->insert('enrolment_courses', [
                    'enrolment_id' => $enrolmentId,
                    'sched_id' => $item['sched_id'],
                ]);
            }

            $this->createAssessment((int) $enrolmentId, $items);

            $this->db->update(
                'enrolment_carts',
                ['status' => 'submitted', 'enrolment_id' => $enrolmentId],
                'cart_id = :id',
                ['id' => $cartId]
            );

            $this->db->commit();

            return [
                'success' => true,
                'enrolment_id' => (int) $enrolmentId,
                'ay_name' => $term['ay_name'] ?? '',
                'semester_name' => $term['semester_name'] ?? '',
                'message' => 'Enrollment submitted for assessment',
            ];
        } catch (Throwable $e) {
            $this->db->rollback();
            return ['success' => false, 'error' => 'Unable to submit enrollment: ' . $e->getMessage()];
        }
    }

    /**
     * Create an assessment for a submitted enrollment.
     */
    private function createAssessment(int $enrolmentId, array $items): int
    {
        $totalUnits = 0.0;
        $labUnits = 0.0;
        foreach ($items as $item) {
            $totalUnits += (float) $item['units'];
            $labUnits += (float) ($item['lab_units'] ?? 0);
        }

        $tuitionPerUnit = (float) ($this->config['fees']['tuition_per_unit'] ?? 1500);
        $miscFee = (float) ($this->config['fees']['misc_fee'] ?? 2000);
        $labFeePerUnit = (float) ($this->config['fees']['lab_fee_per_unit'] ?? 500);

        return (int) $this->db->insert('enrolment_assessments', [
            'enrolment_id' => $enrolmentId,
            'tuition_fee' => $totalUnits * $tuitionPerUnit,
            'misc_fee' => $miscFee,
            'lab_fee' => $labUnits * $labFeePerUnit,
            'other_fees' => 0,
            'discount_amount' => 0,
            'status' => 'pending',
        ]);
    }

    /**
     * Get available schedules for a student's curriculum and term.
     */
    public function getAvailableSchedules(int $studId, int $semId, int $ayId): array
    {
        $student = $this->db->fetchOne('SELECT curr_id FROM students WHERE stud_id = :id', ['id' => $studId]);
        if (!$student) {
            return [];
        }

        $sql = "SELECT s.sched_id, s.class_no, s.class_size, s.remarks,
                       c.code, c.description, c.units, c.lec_units, c.lab_units,
                       p.lname AS prof_lname, p.fname AS prof_fname, p.p_id AS prof_id,
                       GROUP_CONCAT(DISTINCT CONCAT(d.day_code, ' ',
                           TIME_FORMAT(ts.time_start, '%H:%i'), '-',
                           TIME_FORMAT(te.time_end, '%H:%i'), ' ', r.room_name)
                           ORDER BY d.day_id SEPARATOR ' / ') AS schedule_info
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
                GROUP BY s.sched_id, s.class_no, s.class_size, s.remarks,
                         c.code, c.description, c.units, c.lec_units, c.lab_units,
                         p.lname, p.fname, p.p_id
                ORDER BY c.code, s.class_no";

        return $this->db->fetchAll($sql, [
            'curr_id' => $student['curr_id'],
            'sem_id' => $semId,
            'ay_id' => $ayId,
        ]);
    }

    /**
     * Get a student's enrollment history.
     */
    public function getStudentEnrolments(int $studId): array
    {
        $sql = "SELECT e.*, ay.ay_name, sem.semester_name, p.program_code, p.description AS program_desc
                FROM enrolments e
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE e.stud_id = :stud_id
                ORDER BY e.ay_id DESC, e.sem_id DESC";
        return $this->db->fetchAll($sql, ['stud_id' => $studId]);
    }

    /**
     * Get the assessment for an enrollment.
     */
    public function getAssessment(int $enrolmentId): ?array
    {
        return $this->db->fetchOne(
            'SELECT * FROM enrolment_assessments WHERE enrolment_id = :id ORDER BY assessment_id DESC LIMIT 1',
            ['id' => $enrolmentId]
        );
    }

    /**
     * Get the assessment linked to a cart.
     */
    public function getAssessmentByCart(int $cartId): ?array
    {
        $sql = "SELECT ea.*, e.enrolment_id, e.stud_id, e.is_approved,
                       ay.ay_name, sem.semester_name, p.program_code
                FROM enrolment_assessments ea
                JOIN enrolments e ON ea.enrolment_id = e.enrolment_id
                JOIN enrolment_carts ec ON ec.enrolment_id = e.enrolment_id
                JOIN academic_years ay ON e.ay_id = ay.ay_id
                JOIN semesters sem ON e.sem_id = sem.sem_id
                JOIN programs p ON e.program_id = p.program_id
                WHERE ec.cart_id = :cart_id
                ORDER BY ea.assessment_id DESC
                LIMIT 1";
        return $this->db->fetchOne($sql, ['cart_id' => $cartId]);
    }

    /**
     * Get the courses belonging to an enrollment.
     */
    public function getEnrolmentCourses(int $enrolmentId): array
    {
        $sql = "SELECT ec.*, c.code, c.description, c.units, c.lec_units, c.lab_units,
                       s.class_no, p.lname AS prof_lname, p.fname AS prof_fname
                FROM enrolment_courses ec
                JOIN schedules s ON ec.sched_id = s.sched_id
                JOIN courses c ON s.course_id = c.course_id
                LEFT JOIN personnel p ON s.p_id = p.p_id
                WHERE ec.enrolment_id = :enrolment_id
                ORDER BY c.code";
        return $this->db->fetchAll($sql, ['enrolment_id' => $enrolmentId]);
    }

    /**
     * Active academic year + semester.
     */
    public function getActiveAcademicYearSemester(): ?array
    {
        return $this->db->fetchOne(
            "SELECT ay.ay_id, ay.ay_name, sem.sem_id, sem.semester_name
             FROM academic_years ay
             CROSS JOIN semesters sem
             WHERE ay.is_active = 1 AND sem.is_active = 1
             LIMIT 1"
        );
    }

    /**
     * Get semester name by ID.
     */
    public function getSemesterName(int $semId): ?string
    {
        $row = $this->db->fetchOne('SELECT semester_name FROM semesters WHERE sem_id = :id', ['id' => $semId]);
        return $row ? $row['semester_name'] : null;
    }
}

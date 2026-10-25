<?php

require_once __DIR__ . '/../Services/EnrolmentService.php';
require_once __DIR__ . '/../Services/PaymentGatewayService.php';
require_once __DIR__ . '/../Services/NotificationService.php';

class StudentController extends BaseController
{
    private $studentModel;
    private $notificationService;

    public function __construct()
    {
        parent::__construct();
        $this->studentModel = new StudentModel();
        $this->notificationService = new NotificationService();
    }

    public function dashboard()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolments = $this->studentModel->getEnrolments($studId);
        $grades = $this->studentModel->getGrades($studId);

        $gpa = $this->studentModel->calculateGPA($grades);
        $recentGrades = array_slice($grades, 0, 5);
        $requests = $this->studentModel->getDocumentRequests($studId);

        $dateInfo = [
            'weekday' => date('l'),
            'day' => date('j'),
            'month' => date('F'),
            'year' => date('Y'),
            'time' => date('g:i A'),
        ];

        $weatherInfo = [
            'icon' => 'bi-sun',
            'temperature' => '24°',
            'condition' => 'Partly Cloudy',
            'humidity' => '65%',
            'wind' => '8 km/h',
            'forecast' => [
                ['day' => 'Mon', 'icon' => 'bi-cloud-sun', 'high' => '26°', 'low' => '18°'],
                ['day' => 'Tue', 'icon' => 'bi-cloud', 'high' => '23°', 'low' => '17°'],
                ['day' => 'Wed', 'icon' => 'bi-sun', 'high' => '25°', 'low' => '19°'],
                ['day' => 'Thu', 'icon' => 'bi-cloud-drizzle', 'high' => '22°', 'low' => '16°'],
            ],
        ];

        App::view('student/dashboard', [
            'student' => $student,
            'enrolments' => $enrolments,
            'grades' => $recentGrades,
            'recentGrades' => $recentGrades,
            'requests' => $requests,
            'gpa' => $gpa,
            'dateInfo' => $dateInfo,
            'weatherInfo' => $weatherInfo,
            'pageTitle' => 'Dashboard',
            'module' => 'student',
        ]);
    }

    public function grades()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $grades = $this->studentModel->getGrades($studId);
        $gpa = $this->studentModel->calculateGPA($grades);

        // Calculate passed/failed counts
        $passedCount = 0;
        $failedCount = 0;
        if (!empty($grades)) {
            foreach ($grades as $g) {
                if ($g['remarks'] === 'Passed' || ($g['final_grade'] !== null && $g['final_grade'] >= 3.0)) {
                    $passedCount++;
                } else {
                    $failedCount++;
                }
            }
        }

        App::view('student/grades', [
            'student' => $student,
            'grades' => $grades,
            'gpa' => $gpa,
            'passedCount' => $passedCount,
            'failedCount' => $failedCount,
            'pageTitle' => 'Grades & Academic History',
            'module' => 'student',
        ]);
    }

    public function schedule()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolments = $this->studentModel->getEnrolments($studId);
        $activeEnrolment = !empty($enrolments) ? $enrolments[0] : null;

        $schedule = [];
        if ($activeEnrolment) {
            $schedule = $this->studentModel->getSchedule($studId, $activeEnrolment['ay_id'], $activeEnrolment['sem_id']);
        }

        App::view('student/schedule', [
            'student' => $student,
            'schedule' => $schedule,
            'enrolments' => $enrolments,
            'activeEnrolment' => $activeEnrolment,
            'pageTitle' => 'My Schedule',
            'module' => 'student',
        ]);
    }

    public function profile()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $student = $this->studentModel->findById($this->getStudentId());

        App::view('student/profile', [
            'student' => $student,
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'pageTitle' => 'My Profile',
            'module' => 'student',
        ]);
    }

    public function clearance()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $clearanceModel = new ClearanceModel();
        $enrolmentModel = new EnrolmentModel();
        $active = $enrolmentModel->getActiveAcademicYearSemester();
        $semId = $active['sem_id'] ?? null;
        if ($semId && $studId) {
            $clearanceModel->ensureRecordsForStudent((int) $studId, (int) $semId);
        }
        $clearance = $clearanceModel->getDepartmentsByStudent($studId, $semId);

        App::view('student/clearance', [
            'clearance' => $clearance,
            'semesters' => $enrolmentModel->getAllSemesters(),
            'activeSemester' => $semId,
            'pageTitle' => 'My Clearance',
            'module' => 'student',
        ]);
    }

    public function documentHistory()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $requests = $this->studentModel->getDocumentRequests($this->getStudentId());

        App::view('student/document_history', [
            'requests' => $requests,
            'pageTitle' => 'Document Request History',
            'module' => 'student',
        ]);
    }

    public function documentRequest()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $requests = $this->studentModel->getDocumentRequests($studId);
        $error = App::flash('error');
        $success = App::flash('success');

        App::view('student/document_request', [
            'student' => $student,
            'requests' => $requests,
            'error' => $error,
            'success' => $success,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Document Request',
            'module' => 'student',
        ]);
    }

    public function storeDocumentRequest()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/student/documents/request');
            return;
        }

        $docType = $_POST['document_type'] ?? '';
        $validTypes = ['TOR', 'COR', 'Good Moral', 'Certification'];

        if (!in_array($docType, $validTypes)) {
            App::flash('error', 'Invalid document type selected.');
            App::redirect('/student/documents/request');
            return;
        }

        $requestId = $this->studentModel->createDocumentRequest($this->getStudentId(), $docType);
        App::flash('success', 'Document request submitted successfully!');

        // Send document notification
        $student = $this->studentModel->findById($this->getStudentId());
        if ($student) {
            $this->notificationService->documentNotification($student['stud_id'], [
                'document_type' => $docType,
                'status' => 'Pending',
                'request_id' => $requestId,
            ]);
        }

        App::redirect('/student/documents/request');
    }

    public function enrollmentCart()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolmentModel = new EnrolmentModel();

        // Get active semester
        $academicTerms = $enrolmentModel->getActiveAcademicYearSemester();
        $semId = $academicTerms['sem_id'] ?? null;
        $ayId = $academicTerms['ay_id'] ?? null;

        if (!$semId || !$ayId) {
            App::flash('error', 'No active academic term found.');
            App::redirect('/student/dashboard');
            return;
        }

        // Get or create cart
        $enrolmentService = new EnrolmentService();
        $cart = $enrolmentService->getOrCreateCart($studId, $semId, $ayId);
        $cartItems = $enrolmentService->getCartItems($cart['cart_id']);

        $assessment = null;
        if (in_array($cart['status'], ['assessed', 'paid'])) {
            $assessment = $enrolmentService->getAssessmentByCart((int) $cart['cart_id']);
        }

        App::view('student/enrollment_cart', [
            'student' => $student,
            'cart' => $cart,
            'cartItems' => $cartItems,
            'currentSemester' => $enrolmentModel->getSemesterName($semId),
            'assessment' => $assessment,
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Enrollment Cart',
            'module' => 'student',
        ]);
    }

    public function enrollmentAvailable()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolmentService = new EnrolmentService();

        $academicTerms = $enrolmentService->getActiveAcademicYearSemester();
        $semId = $academicTerms['sem_id'] ?? null;
        $ayId = $academicTerms['ay_id'] ?? null;

        if (!$semId || !$ayId) {
            App::flash('error', 'No active academic term found.');
            App::redirect('/student/dashboard');
            return;
        }

        // Get cart to check what's already in it
        $cart = $enrolmentService->getOrCreateCart($studId, $semId, $ayId);
        $cartItems = $enrolmentService->getCartItems($cart['cart_id']);
        $cartSchedIds = array_column($cartItems, 'sched_id');

        // Get already enrolled schedules
        $enrolments = $this->studentModel->getEnrolments($studId);
        $enrolledSchedIds = [];
        foreach ($enrolments as $enrolment) {
            $courses = $enrolmentService->getEnrolmentCourses($enrolment['enrolment_id']);
            foreach ($courses as $course) {
                $enrolledSchedIds[] = $course['sched_id'];
            }
        }

        // Get available schedules
        $availableSchedules = $enrolmentService->getAvailableSchedules($studId, $semId, $ayId);

        App::view('student/enrollment_available', [
            'student' => $student,
            'availableSchedules' => $availableSchedules,
            'cartItems' => $cartItems,
            'cartSchedIds' => $cartSchedIds,
            'enrolledSchedIds' => $enrolledSchedIds,
            'currentSemester' => $enrolmentService->getSemesterName($semId),
            'error' => App::flash('error'),
            'success' => App::flash('success'),
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Available Subjects',
            'module' => 'student',
        ]);
    }

    public function addToCart()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/student/enrollment/available');
            return;
        }

        $schedId = $_POST['sched_id'] ?? null;
        $studId = $this->getStudentId();
        $enrolmentService = new EnrolmentService();

        $academicTerms = $enrolmentService->getActiveAcademicYearSemester();
        $semId = $academicTerms['sem_id'] ?? null;
        $ayId = $academicTerms['ay_id'] ?? null;

        if (!$semId || !$ayId) {
            App::flash('error', 'No active academic term found.');
            App::redirect('/student/enrollment/available');
            return;
        }

        $cart = $enrolmentService->getOrCreateCart($studId, $semId, $ayId);
        $result = $enrolmentService->addToCart($cart['cart_id'], $schedId);

        if ($result['success']) {
            App::flash('success', 'Subject added to cart!');
        } else {
            App::flash('error', $result['error']);
        }

        App::redirect('/student/enrollment/available');
    }

    public function removeFromCart()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/student/enrollment/cart');
            return;
        }

        $cartId = $_POST['cart_id'] ?? null;
        $schedId = $_POST['sched_id'] ?? null;
        $enrolmentService = new EnrolmentService();

        if ($cartId && $schedId) {
            $result = $enrolmentService->removeFromCart($cartId, $schedId);
            if ($result['success']) {
                App::flash('success', 'Subject removed from cart.');
            }
        }

        App::redirect('/student/enrollment/cart');
    }

    public function submitCart()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/student/enrollment/cart');
            return;
        }

        $cartId = $_POST['cart_id'] ?? null;
        $enrolmentService = new EnrolmentService();

        if ($cartId) {
            $result = $enrolmentService->submitForAssessment($cartId);
            if ($result['success']) {
                App::flash('success', $result['message']);

                // Send enrollment notification
                $this->notificationService->enrollmentNotification($this->getStudentId(), [
                    'ay_name' => $result['ay_name'] ?? 'Current Term',
                    'semester_name' => $result['semester_name'] ?? 'Current Semester',
                    'status' => 'Submitted for Assessment',
                    'enrolment_id' => $result['enrolment_id'] ?? null,
                ]);
            } else {
                App::flash('error', $result['error']);
            }
        }

        App::redirect('/student/enrollment/cart');
    }

    public function paymentPay()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $assessmentId = $_GET['assessment_id'] ?? null;

        if (!$assessmentId) {
            App::flash('error', 'Invalid assessment.');
            App::redirect('/student/payment/assessments');
            return;
        }

        $enrolmentModel = new EnrolmentModel();
        $assessment = $enrolmentModel->getAssessmentById($assessmentId);

        if (!$assessment || $assessment['stud_id'] != $this->getStudentId()) {
            App::flash('error', 'Assessment not found.');
            App::redirect('/student/payment/assessments');
            return;
        }

        $paymentModel = new PaymentModel();
        $totalPaid = $paymentModel->getTotalCompleted((int) $assessment['enrolment_id']);
        $balance = max(0, (float) $assessment['net_amount'] - $totalPaid);
        $assessment['balance'] = $balance;

        App::view('student/payment_pay', [
            'assessment' => $assessment,
            'csrf_token' => App::csrf_token(),
            'pageTitle' => 'Pay Assessment',
            'module' => 'student',
        ]);
    }

    public function paymentProcess()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        if (!App::verify_csrf($_POST['csrf_token'] ?? '')) {
            App::flash('error', 'Invalid CSRF token.');
            App::redirect('/student/payment/assessments');
            return;
        }

        $assessmentId = $_POST['assessment_id'] ?? null;
        $gateway = $_POST['gateway'] ?? 'simulated';

        if (!$assessmentId) {
            App::flash('error', 'Invalid assessment.');
            App::redirect('/student/payment/assessments');
            return;
        }

        $enrolmentModel = new EnrolmentModel();
        $assessment = $enrolmentModel->getAssessmentById($assessmentId);

        if (!$assessment || $assessment['stud_id'] != $this->getStudentId()) {
            App::flash('error', 'Assessment not found.');
            App::redirect('/student/payment/assessments');
            return;
        }
        if (!$assessment['is_approved']) {
            App::flash('error', 'Your enrollment has not been approved by the registrar yet.');
            App::redirect('/student/payment/assessments');
            return;
        }
        if ($assessment['status'] === 'paid') {
            App::flash('error', 'This assessment is already paid.');
            App::redirect('/student/payment/assessments');
            return;
        }

        $paymentModel = new PaymentModel();
        $receiptNo = $paymentModel->generateReceiptNo((int) $assessment['ay_id']);
        $amount = (float) ($_POST['amount_paid'] ?? $assessment['net_amount']);
        $paymentId = (int) $paymentModel->create([
            'enrolment_id' => $assessment['enrolment_id'],
            'receipt_no' => $receiptNo,
            'amount_paid' => $amount,
            'payment_date' => date('Y-m-d H:i:s'),
            'cashier_id' => null,
            'status' => 'pending',
        ]);

        $paymentGateway = new PaymentGatewayService();
        $result = $paymentGateway->processPayment($paymentId, $gateway);

        if ($result['success']) {
            App::flash('success', 'Payment processed successfully. Receipt: ' . $result['receipt_no']);
            App::redirect('/student/payment/receipt/' . urlencode($result['receipt_no']));
        } else {
            $this->db->update('payments', ['status' => 'failed'], 'payment_id = :id', ['id' => $paymentId]);
            App::flash('error', 'Payment failed: ' . $result['error']);
            App::redirect('/student/payment/pay?assessment_id=' . $assessmentId);
        }
    }

    public function paymentAssessments()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolmentModel = new EnrolmentModel();

        $assessments = $enrolmentModel->getStudentAssessments($studId);

        App::view('student/payment_assessments', [
            'student' => $student,
            'assessments' => $assessments,
            'pageTitle' => 'Payment Assessments',
            'module' => 'student',
        ]);
    }

    public function paymentHistory($assessmentId)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolmentModel = new EnrolmentModel();

        $assessment = $enrolmentModel->getAssessmentById($assessmentId);

        if (!$assessment || $assessment['stud_id'] != $studId) {
            App::flash('error', 'Assessment not found.');
            App::redirect('/student/payment/assessments');
            return;
        }

        $payments = (new PaymentModel())->getPaymentsByEnrolment((int) $assessment['enrolment_id']);

        App::view('student/payment_history', [
            'student' => $this->studentModel->findById($studId),
            'assessment' => $assessment,
            'payments' => $payments,
            'pageTitle' => 'Payment History',
            'module' => 'student',
        ]);
    }

    public function paymentReceipt($receiptNo)
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $receipt = (new PaymentModel())->findReceipt($receiptNo);
        $ownsReceipt = false;
        if ($receipt) {
            $row = $this->db->fetchOne(
                'SELECT 1 FROM payments p JOIN enrolments e ON p.enrolment_id = e.enrolment_id
                 WHERE p.receipt_no = :receipt AND e.stud_id = :stud_id',
                ['receipt' => $receiptNo, 'stud_id' => $this->getStudentId()]
            );
            $ownsReceipt = (bool) $row;
        }

        if (!$ownsReceipt) {
            App::flash('error', 'Receipt not found.');
            App::redirect('/student/payment/assessments');
            return;
        }

        App::view('cashier/receipt', [
            'receipt' => $receipt,
            'pageTitle' => 'Official Receipt',
            'module' => 'student',
        ]);
    }

    public function enrollmentHistory()
    {
        if (!$this->requireAuth()) return;
        if (!$this->middleware->student()) return;

        $studId = $this->getStudentId();
        $student = $this->studentModel->findById($studId);
        $enrolments = $this->studentModel->getEnrolments($studId);

        App::view('student/enrollment_history', [
            'student' => $student,
            'enrolments' => $enrolments,
            'pageTitle' => 'Enrollment History',
            'module' => 'student',
        ]);
    }
}

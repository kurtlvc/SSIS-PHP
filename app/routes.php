<?php

$router = App::getInstance()->getRouter();

// ============================================================
// Authentication & Account Management (Frontend)
// ============================================================
$router->get('/', 'AuthController@login');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/forgot-password', 'AuthController@forgotPassword');
$router->post('/forgot-password', 'AuthController@sendResetLink');
$router->get('/reset-password', 'AuthController@resetPasswordForm');
$router->post('/reset-password', 'AuthController@resetPassword');
$router->get('/logout', 'AuthController@logout');

// ============================================================
// Authentication & Account Management (API v1)
// Middleware: guest for unauthenticated; auth:jwt for profile
// ============================================================
$router->post('/api/v1/auth/login', 'ApiAuthController@login');
$router->post('/api/v1/auth/forgot-password', 'ApiAuthController@forgotPassword');
$router->post('/api/v1/auth/reset-password', 'ApiAuthController@resetPassword');
$router->post('/api/v1/auth/logout', 'ApiAuthController@logout');
$router->get('/api/v1/auth/me', 'ApiAuthController@me');
$router->put('/api/v1/auth/change-password', 'ApiAuthController@changePassword');

// ============================================================
// Student Portal (Frontend)
// Middleware: auth, role:student
// ============================================================
$router->get('/student/dashboard', 'StudentController@dashboard');
$router->get('/student/profile', 'StudentController@profile');
$router->get('/student/grades', 'StudentController@grades');
$router->get('/student/schedule', 'StudentController@schedule');
$router->get('/student/clearance', 'StudentController@clearance');
$router->get('/student/documents/request', 'StudentController@documentRequest');
$router->post('/student/documents/request', 'StudentController@storeDocumentRequest');
$router->get('/student/documents/history', 'StudentController@documentHistory');

// ============================================================
// Student Portal (API v1)
// Middleware: auth:jwt, role:student
// ============================================================
$router->get('/api/v1/student/dashboard', 'ApiStudentController@dashboard');
$router->get('/api/v1/student/profile', 'ApiStudentController@profile');
$router->get('/api/v1/student/grades', 'ApiStudentController@grades');
$router->get('/api/v1/student/schedule', 'ApiStudentController@schedule');
$router->get('/api/v1/student/clearance', 'ApiStudentController@clearance');
$router->post('/api/v1/student/documents', 'ApiStudentController@storeDocumentRequest');
$router->get('/api/v1/student/documents', 'ApiStudentController@documentHistory');

// ============================================================
// Registrar Module (Frontend)
// Middleware: auth, role:admin,dean,chair (faculty for grade encoding)
// ============================================================
$router->get('/registrar/dashboard', 'RegistrarController@dashboard');
$router->get('/registrar/students', 'RegistrarController@students');
$router->get('/registrar/students/new', 'RegistrarController@createStudentForm');
$router->post('/registrar/students/new', 'RegistrarController@storeStudent');
$router->get('/registrar/students/{stud_id}', 'RegistrarController@studentRecord');
$router->get('/registrar/students/{stud_id}/edit', 'RegistrarController@editStudentForm');
$router->post('/registrar/students/{stud_id}/edit', 'RegistrarController@updateStudent');
$router->get('/registrar/enrollments', 'RegistrarController@enrollments');
$router->post('/registrar/enrollments/assess', 'RegistrarController@assessEnrollment');
$router->patch('/registrar/enrollments/{enrolment_id}/status', 'RegistrarController@updateEnrollmentStatus');
$router->get('/registrar/grades/encode', 'RegistrarController@gradesEncode');
$router->get('/registrar/grades/encode/{sched_id}', 'RegistrarController@gradeEncodeStudents');
$router->post('/api/v1/registrar/grades/batch', 'ApiRegistrarController@storeBatchGrades');
$router->get('/registrar/documents/queue', 'RegistrarController@documentQueue');
$router->patch('/registrar/documents/{request_id}/status', 'RegistrarController@updateDocumentStatus');

// ============================================================
// Registrar Module (API v1)
// Middleware: auth:jwt, role:admin,dean,chair (faculty for grade encoding)
// ============================================================
$router->get('/api/v1/registrar/dashboard', 'ApiRegistrarController@dashboard');
$router->get('/api/v1/registrar/students', 'ApiRegistrarController@students');
$router->post('/api/v1/registrar/students', 'ApiRegistrarController@storeStudent');
$router->get('/api/v1/registrar/students/{stud_id}', 'ApiRegistrarController@studentRecord');
$router->put('/api/v1/registrar/students/{stud_id}', 'ApiRegistrarController@updateStudent');
$router->get('/api/v1/registrar/enrollments', 'ApiRegistrarController@enrollments');
$router->post('/api/v1/registrar/enrollments', 'ApiRegistrarController@assessEnrollment');
$router->patch('/api/v1/registrar/enrollments/{enrolment_id}/status', 'ApiRegistrarController@updateEnrollmentStatus');
$router->get('/api/v1/registrar/grades/courses', 'ApiRegistrarController@assignedCourses');
$router->get('/api/v1/registrar/grades/students/{sched_id}', 'ApiRegistrarController@gradeRoster');
$router->post('/api/v1/registrar/grades/batch', 'ApiRegistrarController@storeBatchGrades');
$router->get('/api/v1/registrar/documents', 'ApiRegistrarController@documentQueue');
$router->patch('/api/v1/registrar/documents/{request_id}/status', 'ApiRegistrarController@updateDocumentStatus');

// ============================================================
// Cashier Module (Frontend)
// Middleware: auth, role:admin,cashier
// ============================================================
$router->get('/cashier/dashboard', 'CashierController@dashboard');
$router->get('/cashier/payments/process', 'CashierController@paymentProcess');
$router->post('/cashier/payments/process', 'CashierController@storePayment');
$router->get('/cashier/receipts/{receipt_no}', 'CashierController@receipt');
$router->get('/cashier/reports/daily', 'CashierController@dailyReport');

// ============================================================
// Cashier Module (API v1)
// Middleware: auth:jwt, role:admin,cashier
// ============================================================
$router->get('/api/v1/cashier/dashboard', 'ApiCashierController@dashboard');
$router->get('/api/v1/cashier/assessments/{stud_id}', 'ApiCashierController@assessments');
$router->post('/api/v1/cashier/payments', 'ApiCashierController@storePayment');
$router->get('/api/v1/cashier/receipts/{receipt_no}', 'ApiCashierController@receipt');
$router->get('/api/v1/cashier/reports/daily', 'ApiCashierController@dailyReport');

// ============================================================
// Department Module (Frontend)
// Middleware: auth, role:admin,chair,dean
// ============================================================
$router->get('/department/dashboard', 'DepartmentController@dashboard');
$router->get('/department/clearance', 'DepartmentController@clearanceList');
$router->get('/department/clearance/{stud_id}', 'DepartmentController@studentClearance');
$router->post('/department/clearance/{stud_id}', 'DepartmentController@updateClearance');

// ============================================================
// Department Module (API v1)
// Middleware: auth:jwt, role:admin,chair,dean
// ============================================================
$router->get('/api/v1/department/dashboard', 'ApiDepartmentController@dashboard');
$router->get('/api/v1/department/clearance', 'ApiDepartmentController@clearanceList');
$router->get('/api/v1/department/clearance/students/{stud_id}', 'ApiDepartmentController@studentClearance');
$router->patch('/api/v1/department/clearance/{clearance_id}', 'ApiDepartmentController@updateClearanceStatus');

// ============================================================
// Admin Module (Frontend)
// Middleware: auth, role:admin
// ============================================================
$router->get('/admin/dashboard', 'AdminController@dashboard');
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/users/create', 'AdminController@createUserForm');
$router->post('/admin/users/create', 'AdminController@createUser');
$router->get('/admin/users/{id}', 'AdminController@viewUser');
$router->get('/admin/users/{id}/edit', 'AdminController@editUserForm');
$router->post('/admin/users/{id}/edit', 'AdminController@updateUser');
$router->patch('/admin/users/{id}/toggle-status', 'AdminController@toggleUserStatus');
$router->get('/admin/system-settings', 'AdminController@settings');
$router->post('/admin/settings/update', 'AdminController@updateSettings');
$router->get('/admin/audit-logs', 'AdminController@auditLogs');

// ============================================================
// Admin Module (API v1)
// Middleware: auth:jwt, role:admin
// ============================================================
$router->get('/api/v1/admin/dashboard', 'ApiAdminController@dashboard');
$router->get('/api/v1/admin/users', 'ApiAdminController@users');
$router->post('/api/v1/admin/users', 'ApiAdminController@createUser');
$router->get('/api/v1/admin/users/{id}', 'ApiAdminController@viewUser');
$router->put('/api/v1/admin/users/{id}', 'ApiAdminController@updateUser');
$router->patch('/api/v1/admin/users/{id}/toggle-status', 'ApiAdminController@toggleUserStatus');
$router->get('/api/v1/admin/settings', 'ApiAdminController@settings');
$router->put('/api/v1/admin/settings', 'ApiAdminController@updateSettings');
$router->get('/api/v1/admin/audit-logs', 'ApiAdminController@auditLogs');

// ============================================================
// Global & Shared Utilities (API v1)
// Middleware: auth:jwt (all authenticated roles)
// ============================================================
$router->get('/api/v1/announcements', 'ApiGlobalController@announcements');
$router->post('/api/v1/announcements', 'ApiGlobalController@storeAnnouncement');
$router->get('/api/v1/notifications', 'ApiGlobalController@notifications');
$router->patch('/api/v1/notifications/{id}/read', 'ApiGlobalController@markNotificationRead');
$router->get('/api/v1/lookups/academic-terms', 'ApiGlobalController@academicTerms');

// ============================================================
// Legacy API (keep for backward compatibility)
// ============================================================
$router->get('/api/document/request', 'ApiController@documentRequestStatus');
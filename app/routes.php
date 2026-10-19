<?php

$router = App::getInstance()->getRouter();

// Authentication
$router->get('/', 'AuthController@login');
$router->get('/login', 'AuthController@login');
$router->post('/login', 'AuthController@authenticate');
$router->get('/logout', 'AuthController@logout');

// Student Portal
$router->get('/student/dashboard', 'StudentController@dashboard');
$router->get('/student/grades', 'StudentController@grades');
$router->get('/student/schedule', 'StudentController@schedule');
$router->get('/student/documents/request', 'StudentController@documentRequest');
$router->post('/student/documents/request', 'StudentController@storeDocumentRequest');

// Registrar Module
$router->get('/registrar/enrollments', 'RegistrarController@enrollments');
$router->get('/registrar/students/{stud_id}', 'RegistrarController@studentRecord');
$router->get('/registrar/grades/encode', 'RegistrarController@gradesEncode');
$router->post('/registrar/grades/encode', 'RegistrarController@storeGrade');
$router->get('/registrar/documents/queue', 'RegistrarController@documentQueue');

// Cashier Module
$router->get('/cashier/dashboard', 'CashierController@dashboard');
$router->get('/cashier/payments/process', 'CashierController@paymentProcess');
$router->post('/cashier/payments/process', 'CashierController@storePayment');
$router->get('/cashier/receipts/{receipt_no}', 'CashierController@receipt');

// Department Module
$router->get('/department/clearance', 'DepartmentController@clearanceList');
$router->get('/department/clearance/{stud_id}', 'DepartmentController@studentClearance');
$router->post('/department/clearance/{stud_id}', 'DepartmentController@updateClearance');

// Admin Module
$router->get('/admin/users', 'AdminController@users');
$router->get('/admin/roles', 'AdminController@roles');
$router->get('/admin/system-settings', 'AdminController@settings');
$router->post('/admin/users/create', 'AdminController@createUser');
$router->post('/admin/users/update/{id}', 'AdminController@updateUser');
$router->post('/admin/settings/update', 'AdminController@updateSettings');

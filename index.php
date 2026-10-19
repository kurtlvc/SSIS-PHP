<?php

require_once __DIR__ . '/app/Core/App.php';
require_once __DIR__ . '/app/Core/Router.php';
require_once __DIR__ . '/app/Core/Middleware.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/Models/UserModel.php';
require_once __DIR__ . '/app/Models/StudentModel.php';
require_once __DIR__ . '/app/Models/EnrolmentModel.php';
require_once __DIR__ . '/app/Models/PaymentModel.php';
require_once __DIR__ . '/app/Models/ClearanceModel.php';
require_once __DIR__ . '/app/Models/DocumentRequestModel.php';
require_once __DIR__ . '/app/Controllers/BaseController.php';
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/StudentController.php';
require_once __DIR__ . '/app/Controllers/RegistrarController.php';
require_once __DIR__ . '/app/Controllers/CashierController.php';
require_once __DIR__ . '/app/Controllers/DepartmentController.php';
require_once __DIR__ . '/app/Controllers/AdminController.php';
require_once __DIR__ . '/app/Controllers/ApiController.php';
require_once __DIR__ . '/app/Controllers/ApiAuthController.php';
require_once __DIR__ . '/app/Controllers/ApiStudentController.php';
require_once __DIR__ . '/app/Controllers/ApiRegistrarController.php';
require_once __DIR__ . '/app/Controllers/ApiCashierController.php';
require_once __DIR__ . '/app/Controllers/ApiDepartmentController.php';
require_once __DIR__ . '/app/Controllers/ApiAdminController.php';
require_once __DIR__ . '/app/Controllers/ApiGlobalController.php';

require_once __DIR__ . '/app/routes.php';

App::getInstance()->run();

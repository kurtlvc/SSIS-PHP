# CuyoSIS - CuyoTech University Student Information System

CuyoSIS is a comprehensive, modular Student Information System designed for CuyoTech University. Built using an Agile iterative approach, it centralizes academic, financial, and administrative workflows into a secure, role-based ecosystem.

## Core Modules

| Module | Description |
|--------|------------|
| **Student Portal** | Profile, grades, schedule, document requests |
| **Registrar** | Enrollment, grade encoding, student records |
| **Cashier** | Payments, receipts, daily collection summaries |
| **Department** | Clearance processing, department sign-offs |
| **Admin** | User management, RBAC, system settings |
| **Document Request** | TOR, COR, certifications queue & processing |

## Project Stack

- **Backend:** Plain PHP with custom router
- **Database:** MySQL / MariaDB (utf8mb4)
- **Frontend:** Bootstrap 6, jQuery

## Directory Structure

```
sis/
├── public/                 # Web root (nginx config)
├── index.php               # Application entry point
├── config/
│   ├── config.php          # Environment & app config
│   └── database.php        # Database connection (PDO singleton)
├── app/
│   ├── Core/
│   │   ├── App.php         # Application core (sessions, views, flash)
│   │   ├── Router.php      # Simple PHP router
│   │   └── Middleware.php  # Auth & RBAC middleware
│   ├── Models/
│   │   ├── UserModel.php
│   │   ├── StudentModel.php
│   │   ├── EnrolmentModel.php
│   │   ├── PaymentModel.php
│   │   ├── ClearanceModel.php
│   │   └── DocumentRequestModel.php
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── StudentController.php
│   │   ├── RegistrarController.php
│   │   ├── CashierController.php
│   │   ├── DepartmentController.php
│   │   ├── AdminController.php
│   │   └── ApiController.php
│   ├── Views/
│   │   ├── layouts/
│   │   ├── auth/
│   │   ├── student/
│   │   ├── registrar/
│   │   ├── cashier/
│   │   ├── department/
│   │   ├── admin/
│   │   └── errors/
│   └── routes.php
├── schema.sql              # Complete database schema
├── seed.sql                # Development seed data
├── .env.example
└── assets/css/style.css
```

## Setup

### 1. Database

```sql
CREATE DATABASE cuyosis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
mysql --default-character-set=utf8mb4 cuyosis < schema.sql
mysql --default-character-set=utf8mb4 cuyosis < seed.sql
```

### 2. Environment

```bash
cp .env.example .env
# Edit .env with your DB credentials
```

### 3. Nginx Configuration

```nginx
server {
    listen 80;
    server_name localhost;
    root /path/to/sis;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\. {
        deny all;
    }
}
```

## Role-Based Access Control

| Role | Modules Accessible |
|------|--------------------|
| `student` | Student Portal, Document Requests |
| `faculty` | Registrar (own students), Grade Encoding |
| `chair` | Registrar, Department Clearance |
| `dean` | Registrar, Department, Admin (read) |
| `admin` | All modules |

## Security

- Passwords hashed with `password_hash()` (bcrypt)
- CSRF tokens on all POST forms
- Session-based authentication with RBAC
- IDOR protection: backend verifies resource ownership
- Rate limiting via `rate_limits` table

## License

CuyoTech University SIS

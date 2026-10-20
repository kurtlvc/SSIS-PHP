<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CuyoTech University Student Services Information System">
    <meta name="theme-color" content="#1e4f3f">
    <link rel="icon" type="image/webp" href="/ssis/assets/cuyotech-logo-900x900.webp">
    <title><?= htmlspecialchars($pageTitle ?? ($config['system_title'] ?? 'CuyoSSIS')) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/ssis/assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --cuyo-teal: #0d9488;
            --cuyo-teal-dark: #0f766e;
            --cuyo-navy: #0f172a;
            --cuyo-navy-light: #1e293b;
            --cuyo-gray: #f8fafc;
            --cuyo-border: #e2e8f0;
        }
        * { box-sizing: border-box; }
        .app-shell { min-height: 100vh; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--cuyo-navy) 0%, var(--cuyo-navy-light) 100%);
            color: #e2e8f0;
            z-index: 1020;
            display: flex;
            flex-direction: column;
        }
        .sidebar .brand-link {
            display: flex;
            align-items: center;
            gap: .6rem;
            color: var(--cuyo-teal-light);
            font-weight: 700;
            font-size: 1.15rem;
            text-decoration: none;
        }
        .sidebar .brand-link img { width: 32px; height: 32px; }
        .sidebar-nav { flex: 1; overflow-y: auto; }
        .sidebar .nav-link {
            color: #cbd5e1;
            border-radius: .45rem;
            padding: .6rem .9rem;
            font-size: .9rem;
            gap: .6rem;
            transition: all .15s;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255 255 255 / .1);
            color: #fff;
        }
        .sidebar .nav-link.active { color: var(--cuyo-teal-light); }
        .main-wrap { margin-left: 250px; }
        .header {
            position: sticky;
            top: 0;
            z-index: 1010;
            background: #fff;
            border-bottom: 1px solid var(--cuyo-border);
        }
        .main-content { padding: 1.5rem 2rem; }
        .user-avatar { width: 34px; height: 34px; object-fit: cover; }
        @media (max-width: 768px) {
            .main-wrap { margin-left: 0; }
            .sidebar { position: relative; width: 100%; min-height: auto; }
        }
        .stat-card {
            border-radius: 1rem;
            border: 1px solid var(--cuyo-border);
            transition: transform .1s, box-shadow .15s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(15,23,42,.08); }
    </style>
</head>
<body class="bg-cuyo-gray">
<a href="#main-content" class="skip-link position-absolute top-0 start-0 p-2 m-2 z-3 bg-white border border-2 rounded">Skip to main content</a>

<?php if (isset($_SESSION['account_id'])): ?>
<div class="app-shell d-flex">

    <!-- Sidebar -->
    <aside class="sidebar d-flex flex-column" role="navigation" aria-label="Sidebar navigation">
        <div class="sidebar-header p-3 border-bottom border-secondary">
            <?php
            $role = $_SESSION['account_role'] ?? 'student';
            $home = '/ssis/' . ($role === 'student' ? 'student/dashboard' : ($role === 'admin' ? 'admin/dashboard' : ($role === 'cashier' ? 'cashier/dashboard' : ($role === 'dean' || $role === 'chair' ? 'registrar/dashboard' : 'department/dashboard'))));
            ?>
            <a class="brand-link" href="<?= $home ?>">
                <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="CuyoSSIS">
                <span>CuyoSSIS</span>
            </a>
        </div>

        <nav class="sidebar-nav nav nav-pills flex-column mx-2 my-4 gap-1">
            <?php if ($role === 'student'): ?>
                <a class="nav-link" href="/ssis/student/dashboard"><i class="bi bi-speedometer2"></i>Dashboard</a>
                <a class="nav-link" href="/ssis/student/grades"><i class="bi bi-journal-text"></i>Grades</a>
                <a class="nav-link" href="/ssis/student/schedule"><i class="bi bi-calendar-event"></i>Schedule</a>
                <a class="nav-link" href="/ssis/student/documents/request"><i class="bi bi-folder2-open"></i>Documents</a>
                <a class="nav-link" href="/ssis/student/documents/history"><i class="bi bi-clock"></i>Request History</a>
                <a class="nav-link" href="/ssis/student/clearance"><i class="bi bi-clipboard-check"></i>Clearance</a>
                <a class="nav-link" href="/ssis/student/profile"><i class="bi bi-person"></i>Profile</a>
            <?php elseif (in_array($role, ['faculty', 'chair', 'dean'])): ?>
                <a class="nav-link" href="/ssis/registrar/dashboard"><i class="bi bi-speedometer2"></i>Dashboard</a>
                <a class="nav-link" href="/ssis/registrar/students"><i class="bi bi-people"></i>Students</a>
                <a class="nav-link" href="/ssis/registrar/enrollments"><i class="bi bi-journal-check"></i>Enrollments</a>
                <a class="nav-link" href="/ssis/registrar/grades/encode"><i class="bi bi-pencil-square"></i>Grade Encoding</a>
                <a class="nav-link" href="/ssis/registrar/documents/queue"><i class="bi bi-folder2-open"></i>Document Queue</a>
            <?php elseif ($role === 'cashier'): ?>
                <a class="nav-link" href="/ssis/cashier/dashboard"><i class="bi bi-speedometer2"></i>Dashboard</a>
                <a class="nav-link" href="/ssis/cashier/payments/process"><i class="bi bi-credit-card-2-front"></i>Process Payment</a>
                <a class="nav-link" href="/ssis/cashier/reports/daily"><i class="bi bi-bar-chart-steps"></i>Daily Report</a>
            <?php elseif ($role === 'admin'): ?>
                <a class="nav-link" href="/ssis/admin/dashboard"><i class="bi bi-speedometer2"></i>Dashboard</a>
                <a class="nav-link" href="/ssis/admin/users"><i class="bi bi-people"></i>Users</a>
                <a class="nav-link" href="/ssis/admin/roles"><i class="bi bi-shield-lock"></i>Roles &amp; Permissions</a>
                <a class="nav-link" href="/ssis/admin/system-settings"><i class="bi bi-gear"></i>System Settings</a>
                <a class="nav-link" href="/ssis/admin/audit-logs"><i class="bi bi-clipboard-data"></i>Audit Logs</a>
                <a class="nav-link" href="/ssis/registrar/students"><i class="bi bi-person-badge"></i>Student Records</a>
                <a class="nav-link" href="/ssis/department/clearance"><i class="bi bi-clipboard-check"></i>Clearance Queue</a>
            <?php endif; ?>
        </nav>

        <div class="p-3 border-top border-secondary">
            <a class="nav-link text-warning" href="/ssis/logout">
                <i class="bi bi-box-arrow-right"></i>Logout
            </a>
        </div>
    </aside>

    <!-- Main -->
    <div class="main-wrap flex-grow-1 d-flex flex-column">
        <header class="header d-flex align-items-center justify-content-between px-4 py-2">
            <h1 class="h5 fw-bold mb-0"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
            <div class="dropdown">
                <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="User menu"
                   class="d-flex align-items-center text-decoration-none text-dark gap-2">
                    <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="Avatar" class="user-avatar rounded-circle border">
                    <span class="d-none d-sm-inline fw-medium"><?= htmlspecialchars($_SESSION['account_login'] ?? 'User') ?></span>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <?php if ($role === 'student'): ?>
                        <li><a class="dropdown-item" href="/ssis/student/profile">Profile</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="/ssis/logout">Logout</a></li>
                </ul>
            </div>
        </header>

        <main id="main-content" class="main-content flex-grow-1 overflow-y-auto" role="main">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $key => $msg): ?>
                    <?php if (in_array($key, ['error', 'success'])): ?>
                        <div class="alert alert-<?= $key === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert" aria-live="polite">
                            <i class="bi bi-<?= $key === 'error' ? 'exclamation-circle' : 'check-circle' ?> me-1" aria-hidden="true"></i>
                            <?= htmlspecialchars($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>
    </div>
</div>
<?php else: ?>
    <main id="main-content" class="main-content" role="main">
        <?= $content ?? '' ?>
    </main>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>

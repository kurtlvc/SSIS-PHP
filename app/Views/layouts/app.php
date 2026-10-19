<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? $config['system_title'] ?? 'CuyoSIS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/sis/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-light">
<?php if (isset($_SESSION['account_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $config['site_url'] ?>/<?= $module === 'student' ? 'student/dashboard' : ($module === 'registrar' ? 'registrar/enrollments' : ($module === 'cashier' ? 'cashier/dashboard' : ($module === 'department' ? 'department/clearance' : 'admin/users'))) ?>">CuyoSIS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if ($_SESSION['account_role'] === 'student'): ?>
                    <li class="nav-item"><a class="nav-link" href="/sis/student/dashboard">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/student/grades">Grades</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/student/schedule">Schedule</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/student/documents/request">Documents</a></li>
                <?php elseif (in_array($_SESSION['account_role'], ['faculty', 'chair', 'dean'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/sis/registrar/enrollments">Enrollments</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/registrar/grades/encode">Grade Encoding</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/registrar/documents/queue">Document Queue</a></li>
                <?php elseif (in_array($_SESSION['account_role'], ['cashier', 'admin'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/sis/cashier/dashboard">Cashier</a></li>
                <?php endif; ?>
                <?php if ($_SESSION['account_role'] === 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="/sis/admin/users">Users</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/admin/roles">Roles</a></li>
                    <li class="nav-item"><a class="nav-link" href="/sis/admin/system-settings">Settings</a></li>
                <?php endif; ?>
                <?php if (in_array($_SESSION['account_role'], ['chair', 'dean', 'admin'])): ?>
                    <li class="nav-item"><a class="nav-link" href="/sis/department/clearance">Clearance</a></li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($_SESSION['account_login']) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="/sis/logout">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="container-fluid px-4">
    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $key => $msg): ?>
            <?php if (in_array($key, ['error', 'success'])): ?>
                <div class="alert alert-<?= $key === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show">
                    <?= htmlspecialchars($msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <?php if (isset($content)): ?>
        <?= $content ?>
    <?php endif; ?>
</main>

<script type="module" src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV03r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>

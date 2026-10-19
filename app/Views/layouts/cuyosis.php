<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle ?? $config['system_title'] ?? 'CuyoSSIS') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="/ssis/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-cuyo-gray">
<a href="#main-content" class="skip-link position-absolute top-0 start-0 p-2 m-2 z-3 bg-white border border-2 rounded">Skip to main content</a>

<?php if (isset($_SESSION['account_id'])): ?>
<div class="cuyosis-layout d-flex flex-column min-vh-100">

    <!-- Top Navigation Bar -->
    <nav class="navbar navbar-light bg-white border-bottom px-3 px-lg-4 py-2 flex-shrink-0" role="navigation" aria-label="Main navigation">
        <div class="container-fluid px-0">
            <div class="d-flex align-items-center gap-3">
                <a class="navbar-brand mb-0 d-flex align-items-center" href="<?= $config['site_url'] ?>/student/dashboard">
                    <img src="/ssis/assets/cuyotech-header-908x160.webp" alt="CuyoTech" width="32" height="32" class="d-none d-sm-inline-block me-2 img-fluid" style="max-width: 32px;">
                    <span class="fw-bold text-cuyo-green">CuyoSSIS</span>
                </a>
                <span class="navbar-text text-muted d-none d-md-block">|</span>
                <span class="text-muted small d-none d-md-block">CuyoTech University</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="d-none d-sm-inline text-muted small">
                    Welcome, <?= htmlspecialchars($_SESSION['account_login'] ?? 'User') ?>
                </span>
                <button class="btn btn-link text-muted p-1" type="button" title="Notifications">
                    <i class="bi bi-bell fs-6"" aria-hidden="true"></i>
                </button>
                <button class="btn btn-link text-muted p-1" type="button" title="Messages">
                    <i class="bi bi-envelope fs-6"" aria-hidden="true"></i>
                </button>
                <button class="btn btn-link text-muted p-1" type="button" title="Settings">
                    <i class="bi bi-gear fs-6"" aria-hidden="true"></i>
                </button>
                <div class="dropdown">
                    <a href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="User menu">
                        <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="Avatar" width="36" height="36" class="rounded-circle border object-fit-cover">
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#" data-dropdown="profile">Profile</a></li>
                        <li><a class="dropdown-item" href="#" data-dropdown="settings">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="/ssis/logout">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Three-Column Body -->
    <div class="cuyosis-body flex-grow-1 d-flex px-3 px-lg-4 py-4 overflow-hidden">

        <!-- Slim Vertical Sidebar -->
        <aside class="cuyo-sidebar d-none d-lg-flex flex-column flex-shrink-0 border-end bg-white py-4 me-4">
            <div class="px-3 text-center mb-4">
                <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="Emblem" width="56" height="56" class="mb-2 img-fluid" style="max-width: 56px;">
                <div class="small fw-medium text-cuyo-green">CuyoTech University</div>
            </div>
            <nav class="nav nav-pills flex-column mx-2 gap-1" role="navigation" aria-label="Sidebar navigation">
                <a class="nav-link active" href="/ssis/student/dashboard">
                    <i class="bi bi-speedometer2 me-2" aria-hidden="true"></i>Dashboard
                </a>
                <a class="nav-link text-muted" href="/ssis/student/grades">
                    <i class="bi bi-journal-text me-2" aria-hidden="true"></i>Grades
                </a>
                <a class="nav-link text-muted" href="/ssis/student/schedule">
                    <i class="bi bi-calendar-event me-2" aria-hidden="true"></i>Schedule
                </a>
                <a class="nav-link text-muted" href="/ssis/student/documents/request">
                    <i class="bi bi-folder2-open me-2" aria-hidden="true"></i>Documents
                </a>
                <a class="nav-link text-muted" href="#">
                    <i class="bi bi-credit-card-2-front me-2" aria-hidden="true"></i>Payments
                </a>
                <a class="nav-link text-muted" href="#">
                    <i class="bi bi-clipboard-data me-2" aria-hidden="true"></i>Reports
                </a>
                <a class="nav-link text-muted" href="#">
                    <i class="bi bi-question-circle me-2" aria-hidden="true"></i>Help &amp; Support
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main id="main-content" class="cuyo-main flex-grow-1 overflow-y-auto pe-4" role="main">
            <?php if (isset($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $key => $msg): ?>
                    <?php if (in_array($key, ['error', 'success'])): ?>
                        <div class="alert alert-<?= $key === 'error' ? 'danger' : 'success' ?> alert-dismissible fade show" role="alert" aria-live="polite">
                            <?= htmlspecialchars($msg) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <?= $content ?? '' ?>
        </main>

        <!-- Right Column (Dashboard-specific) -->
        <?php if (isset($rightColumn) && $rightColumn): ?>
            <aside class="cuyo-right d-none d-xl-flex flex-column flex-shrink-0 ms-4">
                <?= $rightColumn ?>
            </aside>
        <?php endif; ?>

    </div>
</div>
<?php endif; ?>

<?php if (!isset($_SESSION['account_id'])): ?>
    <main id="main-content" class="container-fluid px-4" role="main">
        <?= $content ?? '' ?>
    </main>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-1a/pXj49ZQ1aHEmrJ+gMw1otqoVsYwlEnlD8mIfY2TV003r20Y0CN7uqx1tQogjPL" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>

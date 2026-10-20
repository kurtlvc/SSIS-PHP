<?php
$config = $config ?? [];
$systemTitle = $config['system_title'] ?? $system_title ?? 'CuyoSSIS';
$loginUrl = ($config['site_url'] ?? '') . '/login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CuyoTech University Student Services Information System">
    <meta name="theme-color" content="#1e4f3f">
    <link rel="icon" type="image/webp" href="/ssis/assets/cuyotech-logo-900x900.webp">
    <title>Access Denied - <?= htmlspecialchars($systemTitle) ?></title>
    <link href="/ssis/assets/vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/ssis/assets/vendor/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family:'Inter','Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f1f5f9; }
        .denied-card { max-width: 480px; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center min-vh-100">
    <main id="main-content" class="container" role="main">
        <div class="text-center">
            <i class="bi bi-lock display-1 text-cuyo-green mb-3 fs-4rem" aria-hidden="true"></i>
            <h1 class="display-4 fw-bold text-navy mb-2">403 — Access Denied</h1>
            <p class="lead text-muted">
                You are signed in, but your role does not have permission to view this page.
            </p>
            <p class="text-muted small mb-4">
                If you believe this is an error, please contact the
                <a href="mailto:registrar@cuytech.edu">IT Administrator</a>.
            </p>
            <a href="<?= htmlspecialchars($loginUrl) ?>" class="btn btn-cuyo-green text-white rounded-pill px-4">
                <i class="bi bi-box-arrow-right me-1"></i>Return to Login
            </a>
        </div>
    </main>
    <script src="/ssis/assets/vendor/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

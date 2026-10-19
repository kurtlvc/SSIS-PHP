<?php
$config = $config ?? [];
$pageTitle = $pageTitle ?? '404 - Page Not Found';
$module = $module ?? 'student';
?>
<div class="d-flex align-items-center justify-content-center min-vh-100">
    <div class="text-center">
        <h1 class="display-1 fw-bold text-primary">404</h1>
        <h2 class="h4 mb-3">Page Not Found</h2>
        <p class="text-muted mb-4">The page you're looking for doesn't exist or you don't have permission to access it.</p>
        <?php if (isset($_SESSION['account_id'])): ?>
            <a href="/ssis/<?= ($_SESSION['account_role'] === 'student') ? 'student/dashboard' : (($_SESSION['account_role'] === 'admin') ? 'admin/users' : 'registrar/enrollments') ?>" class="btn btn-primary">
                Go to Dashboard
            </a>
        <?php else: ?>
            <a href="/ssis/login" class="btn btn-primary">Go to Login</a>
        <?php endif; ?>
    </div>
</div>

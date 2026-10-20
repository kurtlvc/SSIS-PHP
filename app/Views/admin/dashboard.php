<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Admin Dashboard</h1>
</div>

<?php if (isset($counts)): ?>
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3"><div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
            <i class="bi bi-people display-6 text-primary mb-2" aria-hidden="true"></i>
            <h2 class="fw-bold mb-0"><?= (int) ($counts['students'] ?? 0) ?></h2>
            <p class="text-muted small mb-0">Students</p></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
            <i class="bi bi-person-badge display-6 text-info mb-2" aria-hidden="true"></i>
            <h2 class="fw-bold mb-0"><?= (int) ($counts['personnel'] ?? 0) ?></h2>
            <p class="text-muted small mb-0">Personnel</p></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
            <i class="bi bi-shield-lock display-6 text-success mb-2" aria-hidden="true"></i>
            <h2 class="fw-bold mb-0"><?= (int) ($counts['accounts'] ?? 0) ?></h2>
            <p class="text-muted small mb-0">Accounts</p></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
            <i class="bi bi-envelope-check display-6 text-warning mb-2" aria-hidden="true"></i>
            <h2 class="fw-bold mb-0"><?= (int) ($counts['pending_requests'] ?? 0) ?></h2>
            <p class="text-muted small mb-0">Pending Requests</p></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
            <i class="bi bi-journal-check display-6 text-secondary mb-2" aria-hidden="true"></i>
            <h2 class="fw-bold mb-0"><?= (int) ($counts['approved_enrolments'] ?? 0) ?></h2>
            <p class="text-muted small mb-0">Approved Enrolments</p></div></div></div>
        <div class="col-6 col-lg-3"><div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
            <i class="bi bi-cash-coin display-6 text-success mb-2" aria-hidden="true"></i>
            <h2 class="fw-bold mb-0">₱<?= number_format((float) ($counts['today_collection'] ?? 0), 2) ?></h2>
            <p class="text-muted small mb-0">Today's Collection</p></div></div></div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 bg-white shadow-sm h-100"><div class="card-header bg-white"><span class="fw-bold">Quick Links</span></div><div class="card-body">
            <div class="d-grid gap-2">
                <a href="/ssis/admin/users" class="btn btn-outline-primary btn-sm"><i class="bi bi-people me-1"></i>User Management</a>
                <a href="/ssis/admin/users/create" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus me-1"></i>Create User</a>
                <a href="/ssis/admin/system-settings" class="btn btn-outline-primary btn-sm"><i class="bi bi-gear me-1"></i>System Settings</a>
                <a href="/ssis/admin/audit-logs" class="btn btn-outline-primary btn-sm"><i class="bi bi-clipboard-data me-1"></i>Audit Logs</a>
                <a href="/ssis/registrar/enrollments" class="btn btn-outline-primary btn-sm"><i class="bi bi-journal-check me-1"></i>Enrollment Management</a>
                <a href="/ssis/cashier/dashboard" class="btn btn-outline-primary btn-sm"><i class="bi bi-cash-coin me-1"></i>Cashier Dashboard</a>
                <a href="/ssis/department/clearance" class="btn btn-outline-primary btn-sm"><i class="bi bi-clipboard-check me-1"></i>Clearance Queue</a>
            </div></div></div>
    </div>
</div>

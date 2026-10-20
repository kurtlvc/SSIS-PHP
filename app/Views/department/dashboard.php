<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Department Dashboard</h1>
</div>

<?php if (isset($counts)): ?>
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
                <i class="bi bi-hourglass-split display-6 text-warning mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= (int) ($counts['pending'] ?? 0) ?></h2>
                <p class="text-muted small mb-0">Pending</p></div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
                <i class="bi bi-clipboard-check display-6 text-success mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= (int) ($counts['cleared'] ?? 0) ?></h2>
                <p class="text-muted small mb-0">Cleared</p></div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
                <i class="bi bi-pause-circle display-6 text-info mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= (int) ($counts['on_hold'] ?? 0) ?></h2>
                <p class="text-muted small mb-0">On-Hold</p></div></div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center"><div class="card-body py-4">
                <i class="bi bi-diagram-2 display-6 text-primary mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= (int) ($counts['departments'] ?? 0) ?></h2>
                <p class="text-muted small mb-0">Departments</p></div></div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 bg-white shadow-sm h-100"><div class="card-header bg-white"><span class="fw-bold">Quick Links</span></div><div class="card-body">
            <div class="d-grid gap-2">
                <a href="/ssis/department/clearance" class="btn btn-outline-primary btn-sm"><i class="bi bi-clipboard-data me-1"></i>Clearance Queue</a>
                <a href="/ssis/registrar/students" class="btn btn-outline-primary btn-sm"><i class="bi bi-people me-1"></i>Students</a>
            </div></div></div>
    </div>
</div>

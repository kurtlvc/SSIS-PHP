<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Registrar Dashboard</h1>
</div>

<?php if (isset($counts)): ?>
    <div class="row g-4 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <i class="bi bi-people display-6 text-primary mb-2" aria-hidden="true"></i>
                    <h2 class="fw-bold mb-0"><?= (int) ($counts['students'] ?? 0) ?></h2>
                    <p class="text-muted small mb-0">Students</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <i class="bi bi-person-badge display-6 text-info mb-2" aria-hidden="true"></i>
                    <h2 class="fw-bold mb-0"><?= (int) ($counts['approved_enrolments'] ?? 0) ?></h2>
                    <p class="text-muted small mb-0">Approved Enrolments</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <i class="bi bi-clipboard-data display-6 text-warning mb-2" aria-hidden="true"></i>
                    <h2 class="fw-bold mb-0"><?= (int) ($counts['pending_assessment'] ?? 0) ?></h2>
                    <p class="text-muted small mb-0">Pending Assessment</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 bg-white h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <i class="bi bi-folder2-open display-6 text-secondary mb-2" aria-hidden="true"></i>
                    <h2 class="fw-bold mb-0"><?= (int) ($counts['doc_queue'] ?? 0) ?></h2>
                    <p class="text-muted small mb-0">Doc. Queue</p>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 bg-white shadow-sm h-100">
            <div class="card-header bg-white"><span class="fw-bold">Quick Links</span></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/ssis/registrar/students" class="btn btn-outline-primary btn-sm"><i class="bi bi-people me-1"></i>Manage Students</a>
                    <a href="/ssis/registrar/enrollments" class="btn btn-outline-primary btn-sm"><i class="bi bi-journal-check me-1"></i>Enrollment Management</a>
                    <a href="/ssis/registrar/grades/encode" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil-square me-1"></i>Grade Encoding</a>
                    <a href="/ssis/registrar/documents/queue" class="btn btn-outline-primary btn-sm"><i class="bi bi-folder2-open me-1"></i>Document Queue</a>
                </div>
            </div>
        </div>
    </div>
</div>

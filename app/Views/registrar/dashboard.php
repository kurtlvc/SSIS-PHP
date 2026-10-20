<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
    <h1 class="h3 mb-0 fw-bold" style="color: var(--dash-text-primary);">Registrar Dashboard</h1>
    <span class="small fst-italic" style="color: var(--dash-text-tertiary);"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
        <li class="breadcrumb-item"><a href="/ssis/registrar/dashboard" style="color: var(--dash-text-tertiary); text-decoration: none;">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page" style="color: var(--dash-text-muted);">Registrar</li>
    </ol>
</nav>

<?php if (isset($counts)): ?>
    <?php
    $students = (int) ($counts['students'] ?? 0);
    $approvedEnrolments = (int) ($counts['approved_enrolments'] ?? 0);
    $pendingAssessment = (int) ($counts['pending_assessment'] ?? 0);
    $docQueue = (int) ($counts['doc_queue'] ?? 0);
    ?>
<div class="dashboard-grid" role="group" aria-label="Registrar metrics">
    <article class="dash-card dash-card--users" tabindex="0">
        <div class="dash-card__icon dash-card__icon--success" aria-hidden="true">
            <i class="bi bi-people-fill" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Total Students</h2>
            <p class="dash-card__subtitle">Enrolled students</p>
            <div class="dash-card__value"><?= number_format($students) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__trend dash-card__trend--positive" aria-label="Student count trend">
                    <i class="bi bi-arrow-up-right me-1"></i>Active this term
                </span>
            </div>
        </div>
        <a href="/ssis/registrar/students" class="dash-card__action" aria-label="View all students">Manage Students</a>
    </article>

    <article class="dash-card dash-card--documents" tabindex="0">
        <div class="dash-card__icon dash-card__icon--info" aria-hidden="true">
            <i class="bi bi-journal-check" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Approved Enrolments</h2>
            <p class="dash-card__subtitle">Confirmed registrations</p>
            <div class="dash-card__value"><?= number_format($approvedEnrolments) ?></div>
            <div class="dash-card__meta">Current academic year</div>
        </div>
        <a href="/ssis/registrar/enrollments" class="dash-card__action" aria-label="Manage enrolments">Manage</a>
    </article>

    <article class="dash-card dash-card--warnings" tabindex="0">
        <div class="dash-card__icon dash-card__icon--warning" aria-hidden="true">
            <i class="bi bi-clipboard-data" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Pending Assessment</h2>
            <p class="dash-card__subtitle">Awaiting review</p>
            <div class="dash-card__value"><?= number_format($pendingAssessment) ?></div>
            <div class="dash-card__meta">
                <?php if ($pendingAssessment > 0): ?>
                    <span class="dash-card__badge dash-card__badge--critical"><?= $pendingAssessment ?> Items</span>
                <?php else: ?>
                    <span style="color: var(--dash-accent-green);">All current</span>
                <?php endif; ?>
            </div>
        </div>
        <a href="/ssis/registrar/enrollments" class="dash-card__action" aria-label="Review pending">Review</a>
    </article>

    <article class="dash-card dash-card--status" tabindex="0">
        <div class="dash-card__icon dash-card__icon--cyan" aria-hidden="true">
            <i class="bi bi-folder2-open" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Document Queue</h2>
            <p class="dash-card__subtitle">Pending requests</p>
            <div class="dash-card__value"><?= number_format($docQueue) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__status-dot" aria-hidden="true"></span>
                Processing within 24h
            </div>
        </div>
        <a href="/ssis/registrar/documents/queue" class="dash-card__action" aria-label="View document queue">View Queue</a>
    </article>
</div>
<?php endif; ?>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold">Quick Links</div>
            <div class="card-body p-4">
                <div class="d-grid gap-2" id="quickLinkList">
                    <a href="/ssis/registrar/students" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-people me-1"></i>Manage Students</a>
                    <a href="/ssis/registrar/enrollments" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-journal-check me-1"></i>Enrollment Management</a>
                    <a href="/ssis/registrar/grades/encode" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-pencil-square me-1"></i>Grade Encoding</a>
                    <a href="/ssis/registrar/documents/queue" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-folder2-open me-1"></i>Document Queue</a>
                    <a href="/ssis/registrar/student-form" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-person-plus me-1"></i>Student Form</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="search-filter-bar mt-4">
    <label for="quickLinkSearch" class="form-label mb-2 fw-semibold small text-uppercase">Search or Filter Items</label>
    <div class="input-group">
        <span class="input-group-text" id="search-addon">
            <i class="bi bi-search" role="img" aria-hidden="true"></i>
        </span>
        <input type="search" id="quickLinkSearch" class="form-control" placeholder="Type to search..." aria-label="Search quick links" aria-describedby="search-addon">
    </div>
    <div id="noQuickMatch" class="small mt-1 d-none" role="status" aria-live="polite"></div>
</div>
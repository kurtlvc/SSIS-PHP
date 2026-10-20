<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
    <h1 class="h3 mb-0 fw-bold" style="color: var(--dash-text-primary);">Student Dashboard</h1>
    <span class="small fst-italic" style="color: var(--dash-text-tertiary);"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard" style="color: var(--dash-text-tertiary); text-decoration: none;">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page" style="color: var(--dash-text-muted);">Student</li>
    </ol>
</nav>

<?php
$studentName = ($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '');
$studentId = $student['ref_no'] ?? '';
$program = $student['program_desc'] ?? '';
$gpa = $gpa ?? 0;
$enrolments = $enrolments ?? [];
$requests = $requests ?? [];
$grades = $grades ?? [];
$recentGrades = $recentGrades ?? [];

$pendingRequests = 0;
if (!empty($requests)) {
    foreach ($requests as $r) {
        if (($r['status'] ?? '') === 'pending') $pendingRequests++;
    }
}

$passedSubjects = 0;
if (!empty($grades)) {
    foreach ($grades as $g) {
        if (($g['final_grade'] ?? 0) >= 75) $passedSubjects++;
    }
}
?>

<div class="dashboard-grid" role="group" aria-label="Student metrics">
    <article class="dash-card dash-card--users" tabindex="0">
        <div class="dash-card__icon dash-card__icon--success" aria-hidden="true">
            <i class="bi bi-graph-up-arrow" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Current GPA</h2>
            <p class="dash-card__subtitle">Academic performance</p>
            <div class="dash-card__value"><?= number_format($gpa, 2) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__trend dash-card__trend--positive" aria-label="GPA trend">
                    <i class="bi bi-arrow-up-right me-1"></i>Target: 3.00+
                </span>
            </div>
        </div>
        <a href="/ssis/student/grades" class="dash-card__action" aria-label="View all grades">View Grades</a>
    </article>

    <article class="dash-card dash-card--documents" tabindex="0">
        <div class="dash-card__icon dash-card__icon--info" aria-hidden="true">
            <i class="bi bi-journal-check" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Active Enrolments</h2>
            <p class="dash-card__subtitle">Current semester</p>
            <div class="dash-card__value"><?= count($enrolments) ?></div>
            <div class="dash-card__meta">Semester: <?= htmlspecialchars($currentSemester['name'] ?? 'Current') ?></div>
        </div>
        <a href="/ssis/student/schedule" class="dash-card__action" aria-label="View schedule">View Schedule</a>
    </article>

    <article class="dash-card dash-card--warnings" tabindex="0">
        <div class="dash-card__icon dash-card__icon--warning" aria-hidden="true">
            <i class="bi bi-folder2-open" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Doc. Requests</h2>
            <p class="dash-card__subtitle">Total submitted</p>
            <div class="dash-card__value"><?= count($requests) ?></div>
            <div class="dash-card__meta">
                <?php if ($pendingRequests > 0): ?>
                    <span class="dash-card__badge dash-card__badge--critical"><?= $pendingRequests ?> Pending</span>
                <?php else: ?>
                    <span style="color: var(--dash-accent-green);">All processed</span>
                <?php endif; ?>
            </div>
        </div>
        <a href="/ssis/student/documents/request" class="dash-card__action" aria-label="Request document">Request New</a>
    </article>

    <article class="dash-card dash-card--status" tabindex="0">
        <div class="dash-card__icon dash-card__icon--cyan" aria-hidden="true">
            <i class="bi bi-trophy" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Passed Subjects</h2>
            <p class="dash-card__subtitle">Cumulative record</p>
            <div class="dash-card__value"><?= $passedSubjects ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__status-dot" aria-hidden="true"></span>
                On track for graduation
            </div>
        </div>
        <a href="/ssis/student/profile" class="dash-card__action" aria-label="View profile">View Profile</a>
    </article>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold">Quick Actions</div>
            <div class="card-body p-4">
                <div class="d-grid gap-2" id="quickLinkList">
                    <a href="/ssis/student/grades" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-journal-text me-1"></i>View All Grades</a>
                    <a href="/ssis/student/schedule" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-calendar-event me-1"></i>My Schedule</a>
                    <a href="/ssis/student/documents/request" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-folder2-open me-1"></i>Request Document</a>
                    <a href="/ssis/student/documents/history" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-clock me-1"></i>Request History</a>
                    <a href="/ssis/student/clearance" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-clipboard-check me-1"></i>Clearance Status</a>
                    <a href="/ssis/student/profile" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-person me-1"></i>My Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold">Recent Grades</div>
            <div class="card-body p-4">
                <?php if (!empty($recentGrades)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" aria-label="Recent grades" style="color: var(--dash-text-primary);">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--dash-card-border); color: var(--dash-text-tertiary);">
                                    <th class="border-0 small">Subject</th>
                                    <th class="border-0 small">Code</th>
                                    <th class="border-0 small text-end">Grade</th>
                                    <th class="border-0 small">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentGrades as $grade): ?>
                                    <tr style="border-bottom: 1px solid var(--dash-card-border);">
                                        <td><?= htmlspecialchars($grade['subject'] ?? $grade['code'] ?? '') ?></td>
                                        <td class="small" style="color: var(--dash-text-tertiary);"><?= htmlspecialchars($grade['code'] ?? '') ?></td>
                                        <td class="text-end fw-medium"><?= number_format($grade['final_grade'] ?? 0, 2) ?></td>
                                        <td>
                                            <?php $rm = $grade['remarks'] ?? ''; ?>
                                            <span class="dash-card__badge" style="background: <?= ($rm === 'Passed' || $rm === 'passed') ? 'rgba(16, 185, 129, 0.15)' : 'rgba(239, 68, 68, 0.15)' ?>; color: <?= ($rm === 'Passed' || $rm === 'passed') ? 'var(--dash-accent-green)' : 'var(--dash-accent-red)' ?>; border-color: <?= ($rm === 'Passed' || $rm === 'passed') ? 'rgba(16, 185, 129, 0.3)' : 'rgba(239, 68, 68, 0.3)' ?>;">
                                                <?= htmlspecialchars($rm) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="small mb-0" style="color: var(--dash-text-disabled);">No grades available yet.</p>
                <?php endif; ?>
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
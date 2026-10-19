<?php
$config = $config ?? [];
$pageTitle = $pageTitle ?? 'Student Dashboard';
$module = $module ?? 'student';
$studentName = ($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '');
$studentId = $student['ref_no'] ?? '';
$program = $student['program_desc'] ?? '';

$dateInfo = $dateInfo ?? [
    'weekday' => date('l'),
    'day' => date('j'),
    'month' => date('F'),
    'year' => date('Y'),
];

$weatherInfo = $weatherInfo ?? [
    'icon' => 'bi-sun',
    'temperature' => '24°',
    'condition' => 'Partly Cloudy',
    'humidity' => '65%',
    'wind' => '8 km/h',
    'forecast' => [],
];

ob_start();
?>

<!-- Date Card (Dark Green) -->
<div class="card border-0 text-white cuyo-date-card mb-4 shadow-sm">
    <div class="card-body text-center py-4">
        <i class="bi bi-calendar3 display-5 mb-2 opacity-75"></i>
        <h3 class="fw-bold mb-1"><?= $dateInfo['day'] ?></h3>
        <p class="mb-1 small text-uppercase letter-spacing-1"><?= $dateInfo['month'] ?></p>
        <p class="mb-0 small"><?= $dateInfo['weekday'] ?></p>
        <p class="mt-2 small opacity-75 mb-0"><?= $dateInfo['year'] ?></p>
    </div>
</div>

<!-- Weather Card (Light) -->
<div class="card border-0 bg-white cuyo-weather-card shadow-sm">
    <div class="card-body text-center py-3">
        <i class="bi <?= $weatherInfo['icon'] ?> display-4 text-warning mb-2"></i>
        <h2 class="fw-bold mb-1"><?= $weatherInfo['temperature'] ?></h2>
        <p class="small text-muted mb-2"><?= $weatherInfo['condition'] ?></p>
        <div class="row g-1 small text-center">
            <div class="col-6">
                <span class="d-block text-muted small">Humidity</span>
                <span class="fw-medium"><?= $weatherInfo['humidity'] ?></span>
            </div>
            <div class="col-6">
                <span class="d-block text-muted small">Wind</span>
                <span class="fw-medium"><?= $weatherInfo['wind'] ?></span>
            </div>
        </div>
        <?php if (!empty($weatherInfo['forecast'])): ?>
            <div class="mt-3 pt-2 border-top border-light">
                <?php foreach ($weatherInfo['forecast'] as $f): ?>
                    <div class="d-flex justify-content-between align-items-center mb-1 small">
                        <span class="text-muted w-30"><?= $f['day'] ?></span>
                        <i class="bi <?= $f['icon'] ?> text-muted"></i>
                        <div class="text-end">
                            <span class="fw-medium"><?= $f['high'] ?>/<?= $f['low'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$rightColumn = ob_get_clean();
?>

<!-- Welcome Banner -->
<div class="cuyo-banner card border-0 text-white mb-4 shadow-sm overflow-hidden position-relative" style="background-image: url('/sis/assets/cuyotech-bg-1200x675.webp'); background-size: cover; background-position: center;">
    <div class="card-body position-relative text-center py-5 py-md-6">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-overlay"></div>
        <div class="position-relative z-2">
            <h1 class="display-5 fw-bold mb-2">Welcome back, <?= htmlspecialchars($studentName) ?>!</h1>
            <p class="lead mb-4 opacity-90">
                Access your grades, schedule, and document requests in one place.
            </p>
            <a href="/sis/student/documents/request" class="btn btn-light btn-cuyo-action rounded-pill px-4 py-2 small">
                <i class="bi bi-folder2-open me-1"></i>Request a Document
            </a>
        </div>
    </div>
</div>

<!-- Dashboard Heading -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h4 fw-bold mb-0">Dashboard Overview</h2>
    <span class="text-muted small">Updated: <?= $dateInfo['time'] ?? date('g:i A') ?></span>
</div>

<!-- Statistic Cards Grid -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3 col-xl-2">
        <div class="card border-0 bg-white h-100 shadow-sm cuyo-stat-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-graph-up-arrow display-5 text-cuyo-green mb-2"></i>
                <h3 class="fw-bold mb-0 text-cuyo-green"><?= number_format($gpa ?? 0, 2) ?></h3>
                <p class="text-muted small mb-0">Current GPA</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-xl-2">
        <div class="card border-0 bg-white h-100 shadow-sm cuyo-stat-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-journal-check display-5 text-cuyo-green mb-2"></i>
                <h3 class="fw-bold mb-0 text-cuyo-green"><?= count($enrolments ?? []) ?></h3>
                <p class="text-muted small mb-0">Active Enrolments</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-xl-2">
        <div class="card border-0 bg-white h-100 shadow-sm cuyo-stat-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-folder2-open display-5 text-cuyo-green mb-2"></i>
                <h3 class="fw-bold mb-0 text-cuyo-green"><?= count($requests ?? []) ?></h3>
                <p class="text-muted small mb-0">Doc. Requests</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-xl-2">
        <div class="card border-0 bg-white h-100 shadow-sm cuyo-stat-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-clock display-5 text-cuyo-green mb-2"></i>
                <h3 class="fw-bold mb-0 text-cuyo-green">
                    <?php
                    $pending = 0;
                    if (!empty($requests)) {
                        foreach ($requests as $r) {
                            if (($r['status'] ?? '') === 'pending') $pending++;
                        }
                    }
                    echo $pending;
                    ?>
                </h3>
                <p class="text-muted small mb-0">Pending Requests</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-xl-2">
        <div class="card border-0 bg-white h-100 shadow-sm cuyo-stat-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-person-circle display-5 text-cuyo-green mb-2"></i>
                <h3 class="fw-bold mb-0 text-cuyo-green d-none d-xl-block" style="font-size: 1.25rem;"><?= htmlspecialchars(substr($studentName, 0, 1)) ?></h3>
                <h3 class="fw-bold mb-0 text-cuyo-green d-xl-none" style="font-size: 1.25rem;"><?= htmlspecialchars($studentName) ?></h3>
                <p class="text-muted small mb-0">Student Profile</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 col-xl-2">
        <div class="card border-0 bg-white h-100 shadow-sm cuyo-stat-card">
            <div class="card-body text-center py-3">
                <i class="bi bi-trophy display-5 text-cuyo-green mb-2"></i>
                <h3 class="fw-bold mb-0 text-cuyo-green">
                    <?php
                    $passed = 0;
                    if (!empty($grades)) {
                        foreach ($grades as $g) {
                            if (($g['final_grade'] ?? 0) >= 75) $passed++;
                        }
                    }
                    echo $passed;
                    ?>
                </h3>
                <p class="text-muted small mb-0">Passed Subjects</p>
            </div>
        </div>
    </div>
</div>

<!-- Recent Grades Table -->
<div class="card border-0 bg-white shadow-sm mb-4">
    <div class="card-body">
        <h3 class="h6 fw-bold mb-3">Recent Grades</h3>
        <?php if (!empty($grades)): ?>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead>
                        <tr>
                            <th class="border-0 text-muted small">Subject</th>
                            <th class="border-0 text-muted small">Code</th>
                            <th class="border-0 text-muted small text-end">Grade</th>
                            <th class="border-0 text-muted small">Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentGrades as $grade): ?>
                            <tr>
                                <td><?= htmlspecialchars($grade['subject'] ?? $grade['code'] ?? '') ?></td>
                                <td class="text-muted small"><?= htmlspecialchars($grade['code'] ?? '') ?></td>
                                <td class="text-end fw-medium"><?= number_format($grade['final_grade'] ?? 0, 2) ?></td>
                                <td>
                                    <?php $rm = $grade['remarks'] ?? ''; ?>
                                    <span class="badge bg-<?= ($rm === 'Passed' || $rm === 'passed') ? 'success' : 'danger' ?>-subtle text-<?= ($rm === 'Passed' || $rm === 'passed') ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars($rm) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted small mb-0">No grades available yet.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Quick Actions -->
<div class="card border-0 bg-white shadow-sm mb-4 d-lg-none">
    <div class="card-body">
        <h3 class="h6 fw-bold mb-3">Quick Actions</h3>
        <div class="d-grid gap-2">
            <a href="/sis/student/grades" class="btn btn-outline-cuyo-green btn-sm">View All Grades</a>
            <a href="/sis/student/schedule" class="btn btn-outline-cuyo-green btn-sm">My Schedule</a>
            <a href="/sis/student/documents/request" class="btn btn-outline-cuyo-green btn-sm">Request Document</a>
        </div>
    </div>
</div>

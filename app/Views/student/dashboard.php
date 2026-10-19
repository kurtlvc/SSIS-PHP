<?php
$config = $config ?? [];
$pageTitle = $pageTitle ?? 'Student Dashboard';
$module = $module ?? 'student';
$studentName = ($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Student Dashboard</h1>
    <div class="text-end">
        <span class="text-muted">Welcome,</span>
        <strong><?= htmlspecialchars($studentName) ?></strong>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary h-100">
            <div class="card-body text-center">
                <i class="bi bi-person-circle display-5 text-primary mb-2"></i>
                <h5 class="card-title">Profile</h5>
                <p class="card-text text-muted small">Student ID: <?= htmlspecialchars($student['ref_no'] ?? '') ?></p>
                <p class="card-text text-muted small">Program: <?= htmlspecialchars($student['program_desc'] ?? '') ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up-arrow display-5 text-success mb-2"></i>
                <h5 class="card-title">Current GPA</h5>
                <p class="display-6 fw-bold text-success mb-0"><?= number_format($gpa ?? 0, 2) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info h-100">
            <div class="card-body text-center">
                <i class="bi bi-journal-check display-5 text-info mb-2"></i>
                <h5 class="card-title">Enrolments</h5>
                <p class="display-6 fw-bold text-info mb-0"><?= count($enrolments ?? []) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-folder2-open display-5 text-warning mb-2"></i>
                <h5 class="card-title">Doc. Requests</h5>
                <p class="display-6 fw-bold text-warning mb-0"><?= count($requests ?? []) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between">
                <span class="fw-bold">Recent Enrolments</span>
            </div>
            <div class="card-body">
                <?php if (!empty($enrolments)): ?>
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr><th>AY</th><th>Semester</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($enrolments, 0, 5) as $enr): ?>
                                <tr>
                                    <td><?= htmlspecialchars($enr['ay_name']) ?></td>
                                    <td><?= htmlspecialchars($enr['semester_name']) ?></td>
                                    <td><?= htmlspecialchars($enr['enrol_status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No enrolments found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <span class="fw-bold">Recent Grades</span>
            </div>
            <div class="card-body">
                <?php if (!empty($grades)): ?>
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr><th>Course</th><th>Grade</th><th>Remarks</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?= htmlspecialchars($grade['code']) ?></td>
                                    <td><?= number_format($grade['final_grade'], 2) ?></td>
                                    <td><?= htmlspecialchars($grade['remarks']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No grades available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Quick Actions</span>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3">
                <a href="/sis/student/grades" class="btn btn-outline-primary w-100">View All Grades</a>
            </div>
            <div class="col-md-3">
                <a href="/sis/student/schedule" class="btn btn-outline-primary w-100">My Schedule</a>
            </div>
            <div class="col-md-3">
                <a href="/sis/student/documents/request" class="btn btn-outline-primary w-100">Request Document</a>
            </div>
        </div>
    </div>
</div>

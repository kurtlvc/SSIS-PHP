<?php
$studentName = ($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '');
$passedCount = 0;
$failedCount = 0;
if (!empty($grades)) {
    foreach ($grades as $g) {
        if ($g['final_grade'] !== null && $g['final_grade'] >= 3.0) {
            $passedCount++;
        } else {
            $failedCount++;
        }
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Grades & Academic History</h1>
    <div class="text-end">
        <span class="text-muted">Student:</span>
        <strong><?= htmlspecialchars($studentName) ?></strong>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <span class="fw-bold">Summary</span>
    </div>
    <div class="card-body">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <h2 class="display-6 fw-bold text-primary"><?= number_format($gpa ?? 0, 2) ?></h2>
                <p class="text-muted small mb-0">Cumulative GPA</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-6 fw-bold text-success"><?= count($grades ?? []) ?></h2>
                <p class="text-muted small mb-0">Total Courses</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-6 fw-bold text-info"><?= $passedCount ?></h2>
                <p class="text-muted small mb-0">Passed</p>
            </div>
            <div class="col-md-3">
                <h2 class="display-6 fw-bold text-danger"><?= $failedCount ?></h2>
                <p class="text-muted small mb-0">Failed/Incomplete</p>
            </div>
        </div>
    </div>
</div>

<?php
$passed = [];
$failed = [];
if (!empty($grades)) {
    foreach ($grades as $g) {
        if ($g['remarks'] === 'Passed' || $g['final_grade'] >= 3.0) {
            $passed[] = $g;
        } else {
            $failed[] = $g;
        }
    }
}
?>

<div class="card">
    <div class="card-header bg-white">
        <span class="fw-bold">Academic History</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($grades)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>A/Y</th>
                            <th>Semester</th>
                            <th>Code</th>
                            <th>Course</th>
                            <th>Units</th>
                            <th>Grade</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr>
                                <td><?= htmlspecialchars($grade['ay_name']) ?></td>
                                <td><?= htmlspecialchars($grade['semester_name']) ?></td>
                                <td><?= htmlspecialchars($grade['code']) ?></td>
                                <td><?= htmlspecialchars($grade['description']) ?></td>
                                <td><?= number_format($grade['units'], 2) ?></td>
                                <td><?= $grade['final_grade'] !== null ? number_format($grade['final_grade'], 2) : '—' ?></td>
                                <td>
                                    <?php if ($grade['remarks'] === 'Passed' || $grade['final_grade'] >= 3.0): ?>
                                        <span class="badge bg-success"><?= htmlspecialchars($grade['remarks']) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><?= htmlspecialchars($grade['remarks']) ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No grades available.</p>
        <?php endif; ?>
    </div>
</div>

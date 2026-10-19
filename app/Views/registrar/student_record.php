<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Student Master Record</h1>
    <a href="/ssis/registrar/enrollments" class="btn btn-outline-secondary btn-sm">Back to Enrolments</a>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <span class="fw-bold">Student Information</span>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-borderless" aria-label="Student information">
                    <tr>
                        <td class="fw-bold text-end">Student ID:</td>
                        <td><?= htmlspecialchars($student['ref_no'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Name:</td>
                        <td><?= htmlspecialchars(($student['lname'] ?? '') . ', ' . ($student['fname'] ?? '') . ' ' . ($student['mname'] ?? '')) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Email:</td>
                        <td><?= htmlspecialchars($student['emailaddress'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">LRN:</td>
                        <td><?= htmlspecialchars($student['LRN'] ?? '') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless" aria-label="Student information">
                    <tr>
                        <td class="fw-bold text-end">Program:</td>
                        <td><?= htmlspecialchars($student['program_code'] ?? '') ?> - <?= htmlspecialchars($student['program_desc'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Year Level:</td>
                        <td><?= htmlspecialchars($student['year'] ?? '') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Student Type:</td>
                        <td><?= $student['is_regular'] ? 'Regular' : 'Irregular' ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Status:</td>
                        <td><?= $student['is_active'] ? 'Active' : 'Inactive' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <span class="fw-bold">Enrolment History</span>
            </div>
            <div class="card-body">
                <?php if (!empty($enrolments)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm" aria-label="Enrolment history">
                            <thead>
                                <tr><th>A/Y</th><th>Semester</th><th>Units</th><th>Status</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($enrolments as $enr): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($enr['ay_name']) ?></td>
                                        <td><?= htmlspecialchars($enr['semester_name']) ?></td>
                                        <td><?= $enr['total_units'] ?></td>
                                        <td><?= htmlspecialchars($enr['enrol_status']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No enrolment records.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold">Academic Record</span>
                <span class="text-muted small">GPA: <?= number_format($gpa ?? 0, 2) ?></span>
            </div>
            <div class="card-body">
                <?php if (!empty($grades)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm" aria-label="Enrolment history">
                            <thead>
                                <tr><th>A/Y</th><th>Sem</th><th>Code</th><th>Grade</th><th>Remarks</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($grades as $g): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($g['ay_name']) ?></td>
                                        <td><?= htmlspecialchars($g['semester_name']) ?></td>
                                        <td><?= htmlspecialchars($g['code']) ?></td>
                                        <td><?= $g['final_grade'] !== null ? number_format($g['final_grade'], 2) : '—' ?></td>
                                        <td><?= htmlspecialchars($g['remarks']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No grades recorded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

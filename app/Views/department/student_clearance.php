<?php
function clearBadge($status) {
    return $status === 'Cleared' ? 'bg-success' : ($status === 'On-Hold' ? 'bg-danger' : 'bg-warning');
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Student Clearance</h1>
    <a href="/ssis/department/clearance" class="btn btn-outline-secondary btn-sm">Back to Queue</a>
</div>

<?php if (isset($error) && !empty(trim($error))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($success) && !empty(trim($success))): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($student): ?>
    <div class="card mb-4">
        <div class="card-header">
            <span class="fw-bold">Student Information</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless" aria-label="Student information">
                        <tr>
                            <td class="fw-bold text-end">Name:</td>
                            <td><?= htmlspecialchars(($student['lname'] ?? '') . ', ' . ($student['fname'] ?? '') . ' ' . ($student['mname'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">Ref No:</td>
                            <td><?= htmlspecialchars($student['ref_no']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">Email:</td>
                            <td><?= htmlspecialchars($student['emailaddress']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless" aria-label="Student information">
                        <tr>
                            <td class="fw-bold text-end">Semester:</td>
                            <td>
                                    <form method="GET" class="d-inline-block">
                                    <select name="sem_id" id="sem_id" class="form-select form-select-sm" onchange="this.form.submit()" aria-label="Select semester">
                                        <?php foreach ($semesters as $sem): ?>
                                            <option value="<?= $sem['sem_id'] ?>" <?= $sem['sem_id'] == $activeSemester ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($sem['semester_name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <span class="fw-bold">Department Clearance Status</span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($clearanceData)): ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle" aria-label="Department clearance status">
                        <thead class="table-light">
                            <tr>
                                <th>Department</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clearanceData as $cd): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($cd['dept_name']) ?></strong>
                                        <?php if ($cd['cleared_at']): ?>
                                            <small class="text-muted d-block">Cleared: <?= date('M d, Y h:i A', strtotime($cd['cleared_at'])) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= clearBadge($cd['status']) ?>"><?= $cd['status'] ?></span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($cd['clearance_id']): ?>
                                            <form method="POST" action="/ssis/department/clearance/<?= $student['stud_id'] ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                <input type="hidden" name="record_id" value="<?= $cd['clearance_id'] ?>">
                                                <input type="hidden" name="sem_id" value="<?= $activeSemester ?>">
                                                <select name="status" class="form-select form-select-sm d-inline-block w-auto" aria-label="Update clearance status">
                                                    <option value="Cleared" <?= $cd['status'] == 'Cleared' ? 'selected' : '' ?>>Cleared</option>
                                                    <option value="On-Hold" <?= $cd['status'] == 'On-Hold' ? 'selected' : '' ?>>On-Hold</option>
                                                    <option value="Pending" <?= $cd['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary">Update</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="POST" action="/ssis/department/clearance/<?= $student['stud_id'] ?>" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                <input type="hidden" name="department_id" value="<?= $cd['dept_id'] ?>">
                                                <input type="hidden" name="sem_id" value="<?= $activeSemester ?>">
                                                <input type="hidden" name="status" value="Cleared">
                                                <button type="submit" name="action" value="create" class="btn btn-sm btn-success">Sign Off</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-3 mb-0">No clearance records found.</p>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>
        Student not found. Please verify the student ID.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

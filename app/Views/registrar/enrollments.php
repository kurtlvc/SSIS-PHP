<?php
function badgeClass($status) {
    return match($status) {
        'Approved', 'Cleared' => 'bg-success',
        'Pending', 'Pending Approval' => 'bg-warning',
        'Rejected', 'Denied' => 'bg-danger',
        default => 'bg-secondary',
    };
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Enrollment Management</h1>
    <a href="/ssis/registrar/grades/encode" class="btn btn-outline-primary btn-sm">Grade Encoding</a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="ay_id" class="form-label small">Academic Year</label>
                <select name="ay_id" id="ay_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['ay_id'] ?>" <?= (isset($_GET['ay_id']) && $_GET['ay_id'] == $ay['ay_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ay['ay_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="sem_id" class="form-label small">Semester</label>
                <select name="sem_id" id="sem_id" class="form-select form-select-sm">
                    <option value="">All</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($_GET['sem_id']) && $_GET['sem_id'] == $sem['sem_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['semester_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($enrolments)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" aria-label="Enrollment list">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Ref No</th>
                            <th>Program</th>
                            <th>A/Y</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolments as $enr): ?>
                            <tr>
                                <td><?= htmlspecialchars($enr['lname'] . ', ' . $enr['fname']) ?></td>
                                <td><?= htmlspecialchars($enr['ref_no']) ?></td>
                                <td><?= htmlspecialchars($enr['program_code']) ?></td>
                                <td><?= htmlspecialchars($enr['ay_name']) ?></td>
                                <td><?= htmlspecialchars($enr['semester_name']) ?></td>
                                <td><?= htmlspecialchars($enr['enrol_status'] ?? 'N/A') ?></td>
                                <td class="text-center">
                                    <a href="/ssis/registrar/students/<?= $enr['stud_id'] ?>" class="btn btn-sm btn-outline-primary">View Record</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No enrolments found for the selected criteria.</p>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Student Clearance Status</h1>
    <a href="/ssis/department/clearance" class="btn btn-outline-secondary">
        <i class="bi bi-table me-1"></i>Table View
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/department/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/department/clearance">Clearance Processing</a></li>
        <li class="breadcrumb-item active" aria-current="page">Student View</li>
    </ol>
</nav>

<?php if (isset($success) && !empty(trim($success))): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($error) && !empty(trim($error))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="sem_id" class="form-label small">Semester</label>
                <select name="sem_id" id="sem_id" class="form-select form-select-sm">
                    <option value="">All Semesters</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($_GET['sem_id']) && $_GET['sem_id'] == $sem['sem_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['semester_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="search" class="form-label small">Search Student</label>
                <input type="text" name="search" id="search" class="form-control form-control-sm" placeholder="Name or Ref No" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Student clearance overview">
                    <thead>
                        <tr>
                            <th class="small">Student</th>
                            <th class="small">Ref No</th>
                            <th class="small">Program</th>
                            <th class="small">Overall Status</th>
                            <?php foreach ($departments as $dept): ?>
                                <th class="small text-center" style="min-width: 100px;">
                                    <span class="badge bg-secondary"><?= htmlspecialchars($dept['dept_name']) ?></span>
                                </th>
                            <?php endforeach; ?>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($student['lname'] . ', ' . $student['fname']) ?></td>
                                <td class="small text-tertiary font-monospace"><?= htmlspecialchars($student['ref_no']) ?></td>
                                <td class="small text-tertiary"><?= htmlspecialchars($student['program_code'] ?? '') ?></td>
                                <td>
                                    <?php
                                        $overall = $student['overall_status'] ?? 'Pending';
                                        $cls = $overall === 'Cleared' ? 'bg-success' : ($overall === 'On-Hold' ? 'bg-danger' : 'bg-warning');
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= $overall ?></span>
                                </td>
                                <?php foreach ($departments as $dept): ?>
                                    <?php
                                        $deptStatus = $student['clearance'][$dept['dept_id']] ?? 'Pending';
                                        $cls = $deptStatus === 'Cleared' ? 'bg-success' : ($deptStatus === 'On-Hold' ? 'bg-danger' : 'bg-warning');
                                    ?>
                                    <td class="text-center">
                                        <span class="badge <?= $cls ?>" style="min-width: 80px;"><?= $deptStatus ?></span>
                                    </td>
                                <?php endforeach; ?>
                                <td class="text-center">
                                    <a href="/ssis/department/clearance/<?= $student['stud_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>Manage
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No students found for the selected semester.</p>
        <?php endif; ?>
    </div>
</div>
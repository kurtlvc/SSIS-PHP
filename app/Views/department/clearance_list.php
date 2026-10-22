<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Clearance Processing</h1>
    <div class="d-flex gap-2">
        <a href="/ssis/department/clearance/student-view" class="btn btn-outline-secondary">
            <i class="bi bi-view-stacked me-1"></i>Student View
        </a>
        <a href="/ssis/department/clearance" class="btn btn-outline-secondary btn-sm">Refresh</a>
    </div>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/department/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Clearance Processing</li>
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
            <div class="col-md-5">
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
            <div class="col-md-5">
                <label for="dept_id" class="form-label small">Department</label>
                <select name="dept_id" id="dept_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['dept_id'] ?>" <?= (isset($_GET['dept_id']) && $_GET['dept_id'] == $dept['dept_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['dept_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($records)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Clearance records">
                    <thead>
                        <tr>
                            <th class="small">Student</th>
                            <th class="small">Ref No</th>
                            <th class="small">Department</th>
                            <th class="small">Semester</th>
                            <th class="small">Status</th>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $rec): ?>
                            <?php
                                $cls = $rec['status'] === 'Cleared' ? 'bg-success' : ($rec['status'] === 'On-Hold' ? 'bg-danger' : 'bg-warning');
                            ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($rec['lname'] . ', ' . $rec['fname']) ?></td>
                                <td class="small text-tertiary font-monospace"><?= htmlspecialchars($rec['ref_no']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $cls ?>"><?= htmlspecialchars($rec['dept_name']) ?></span>
                                </td>
                                <td class="small text-tertiary"><?= htmlspecialchars($rec['semester_name']) ?></td>
                                <td>
                                    <span class="badge <?= $cls ?>"><?= $rec['status'] ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="/ssis/department/clearance/<?= $rec['stud_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>Process
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No clearance records found.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Department Statistics -->
<div class="card mb-4">
    <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
        <span>Department Clearance Statistics</span>
        <span class="badge bg-primary"><?= count($departments) ?> Departments</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 table-dark-theme" aria-label="Department clearance statistics">
                <thead>
                    <tr>
                        <th class="small">Department</th>
                        <th class="small text-center">Total Students</th>
                        <th class="small text-center">Cleared</th>
                        <th class="small text-center">Pending</th>
                        <th class="small text-center">On Hold</th>
                        <th class="small text-center">Clearance Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deptStats as $stat): ?>
                        <?php
                            $rate = $stat['total'] > 0 ? round(($stat['cleared'] / $stat['total']) * 100, 1) : 0;
                            $rateColor = $rate >= 80 ? 'success' : ($rate >= 50 ? 'warning' : 'danger');
                        ?>
                        <tr>
                            <td class="fw-medium">
                                <span class="badge bg-secondary"><?= htmlspecialchars($stat['dept_name']) ?></span>
                            </td>
                            <td class="small text-center"><?= $stat['total'] ?></td>
                            <td class="small text-center text-success"><?= $stat['cleared'] ?></td>
                            <td class="small text-center text-warning"><?= $stat['pending'] ?></td>
                            <td class="small text-center text-danger"><?= $stat['on_hold'] ?></td>
                            <td class="text-center">
                                <span class="badge bg-<?= $rateColor ?>"><?= $rate ?>%</span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Clearance Processing</h1>
    <a href="/sis/department/clearance" class="btn btn-outline-secondary btn-sm">Refresh</a>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label small">Semester</label>
                <select name="sem_id" class="form-select form-select-sm">
                    <option value="">All Semesters</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($_GET['sem_id']) && $_GET['sem_id'] == $sem['sem_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['semester_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label small">Department</label>
                <select name="dept_id" class="form-select form-select-sm">
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
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Ref No</th>
                            <th>Department</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($records as $rec): ?>
                            <tr>
                                <td><?= htmlspecialchars($rec['lname'] . ', ' . $rec['fname']) ?></td>
                                <td><?= htmlspecialchars($rec['ref_no']) ?></td>
                                <td><?= htmlspecialchars($rec['dept_name']) ?></td>
                                <td><?= htmlspecialchars($rec['semester_name']) ?></td>
                                <td>
                                    <?php
                                        $cls = $rec['status'] === 'Cleared' ? 'bg-success' : ($rec['status'] === 'On-Hold' ? 'bg-danger' : 'bg-warning');
                                    ?>
                                    <span class="badge <?= $cls ?>"><?= $rec['status'] ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="/sis/department/clearance/<?= $rec['stud_id'] ?>" class="btn btn-sm btn-outline-primary">Process</a>
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

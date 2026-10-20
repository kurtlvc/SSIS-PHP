<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Clearance</h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <span class="fw-bold">Clearance Departments</span>
            <select id="semFilter" class="form-select form-select-sm w-auto d-none">
                <?php if (!empty($semesters)): ?>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($activeSemester) && $activeSemester == $sem['sem_id']) ? 'selected' : '' ?>><?= htmlspecialchars($sem['semester_name']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($clearance)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" aria-label="Student clearance">
                    <thead class="table-light">
                        <tr>
                            <th>Department</th>
                            <th class="text-center">Status</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clearance as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['dept_name'] ?? '') ?></td>
                                <td class="text-center">
                                    <?php $status = $item['status'] ?? ''; ?>
                                    <span class="badge bg-<?= match($status) { 'Cleared' => 'success', 'On-Hold' => 'warning', default => 'secondary' } ?>">
                                        <?= htmlspecialchars($status ?: 'Pending') ?>
                                    </span>
                                </td>
                                <td><span class="text-muted small">---</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No clearance records found for the selected semester.</p>
        <?php endif; ?>
    </div>
</div>

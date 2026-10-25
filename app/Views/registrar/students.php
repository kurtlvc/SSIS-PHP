<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Students</h1>
    <a href="/ssis/registrar/students/new" class="btn btn-primary btn-sm"><i class="bi bi-plus me-1"></i>Add Student</a>
</div>

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

<div class="card border-0 shadow-sm">
    <div class="card-header"><span class="fw-bold">All Students</span></div>
    <div class="card-body p-0">
        <?php if (!empty($students)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" aria-label="Students list">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 140px;">Ref No</th>
                            <th>Name</th>
                            <th>Program</th>
                            <th class="text-end" style="width: 160px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $s): ?>
                            <?php
                                $programText = trim(($s['program_code'] ?? '') . (!empty($s['description']) ? ' - ' . $s['description'] : ''));
                                if (empty($programText)) {
                                    $programText = '<span class="text-muted fst-italic">Not Assigned</span>';
                                } else {
                                    $programText = htmlspecialchars($programText);
                                }
                            ?>
                            <tr>
                                <td class="font-monospace fw-semibold text-primary"><?= htmlspecialchars($s['ref_no'] ?? '') ?></td>
                                <td class="fw-medium"><?= htmlspecialchars(trim($s['lname'] . ', ' . $s['fname'] . ' ' . ($s['mname'] ?? ''))) ?></td>
                                <td><?= $programText ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="/ssis/registrar/students/<?= $s['stud_id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>View</a>
                                    <a href="/ssis/registrar/students/<?= $s['stud_id'] ?>/edit" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil me-1"></i>Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No students found.</p>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Edit Grades</h1>
    <a href="/faculty/grade-submissions/<?= $submission['submission_id'] ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/faculty/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/faculty/grade-submissions">Grade Submissions</a></li>
        <li class="breadcrumb-item"><a href="/faculty/grade-submissions/<?= $submission['submission_id'] ?>">View</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
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
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-bold"><?= htmlspecialchars($submission['code'] ?? '') ?> - <?= htmlspecialchars($submission['description'] ?? '') ?></span>
            <span class="badge bg-<?= match($submission['status']) {
                'draft' => 'secondary',
                'submitted' => 'warning',
                'reviewed' => 'info',
                default => 'secondary',
            } ?> ms-2"><?= ucfirst($submission['status']) ?></span>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="/faculty/grade-submissions/<?= $submission['submission_id'] ?>/edit" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= App::csrf_token() ?>">
            
            <div class="col-12">
                <h6 class="text-muted mb-3">Student Grades</h6>
            </div>
            
            <?php foreach ($items as $item): ?>
                <?php
                    $isFinal = !empty($item['is_final']);
                    $gradeValue = $item['grade_value'] !== null ? $item['grade_value'] : '';
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="fw-medium"><?= htmlspecialchars($item['lname'] . ', ' . $item['fname']) ?></span>
                            <span class="badge bg-<?= $isFinal ? 'success' : 'secondary' ?>"><?= $isFinal ? 'Final' : 'Draft' ?></span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label small">Grade (0-100)</label>
                                <input type="number" name="grades[<?= $item['item_id'] ?>]" class="form-control" value="<?= htmlspecialchars($gradeValue) ?>" min="0" max="100" step="0.01" placeholder="Enter grade">
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">Remarks</label>
                                <input type="text" name="remarks[<?= $item['item_id'] ?>]" class="form-control" value="<?= htmlspecialchars($item['remarks'] ?? '') ?>" placeholder="Optional remarks">
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_final[<?= $item['item_id'] ?>]" value="1" id="is_final_<?= $item['item_id'] ?>" <?= $isFinal ? 'checked' : '' ?>>
                                <label class="form-check-label small" for="is_final_<?= $item['item_id'] ?>">Mark as Final</label>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save Grades
                </button>
                <a href="/faculty/grade-submissions/<?= $submission['submission_id'] ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
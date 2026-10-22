<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Review Grade Submission</h1>
    <a href="/ssis/registrar/grades/submission" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Submissions
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/registrar/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/registrar/grades/submission">Grade Submissions</a></li>
        <li class="breadcrumb-item"><a href="/ssis/registrar/grades/submission/<?= $submission['submission_id'] ?>">View</a></li>
        <li class="breadcrumb-item active" aria-current="page">Review</li>
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
                'submitted' => 'warning',
                'reviewed' => 'info',
                default => 'secondary',
            } ?> ms-2"><?= ucfirst($submission['status']) ?></span>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="/ssis/registrar/grades/submission/<?= $submission['submission_id'] ?>/review" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= App::csrf_token() ?>">
            
            <div class="col-12">
                <h6 class="text-muted mb-3">Review Notes (required for rejection)</h6>
                <textarea name="review_notes" class="form-control" rows="3" placeholder="Enter your review comments..."></textarea>
            </div>
            
            <div class="col-12">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="review_action" id="approve" value="approve" checked>
                    <label class="form-check-label fw-medium" for="approve">
                        <i class="bi bi-check-circle-fill text-success me-1"></i>Approve
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="review_action" id="reject" value="reject">
                    <label class="form-check-label fw-medium" for="reject">
                        <i class="bi bi-x-circle-fill text-danger me-1"></i>Reject
                    </label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="review_action" id="request_revision" value="request_revision">
                    <label class="form-check-label fw-medium" for="request_revision">
                        <i class="bi bi-arrow-counterclockwise text-warning me-1"></i>Request Revision
                    </label>
                </div>
            </div>
            
            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send-check me-1"></i>Submit Review
                </button>
                <a href="/ssis/registrar/grades/submission/<?= $submission['submission_id'] ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header fw-semibold">Student Grades to Review</div>
    <div class="card-body p-0">
        <?php if (!empty($items)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Student grades for review">
                    <thead>
                        <tr>
                            <th class="small">Student</th>
                            <th class="small">Ref No</th>
                            <th class="small text-center">Grade</th>
                            <th class="small">Remarks</th>
                            <th class="small">Current Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php
                                $isFinal = !empty($item['is_final']);
                                $gradeDisplay = $item['grade_value'] !== null ? number_format($item['grade_value'], 2) : '—';
                            ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($item['lname'] . ', ' . $item['fname']) ?></td>
                                <td class="small text-tertiary font-monospace"><?= htmlspecialchars($item['ref_no'] ?? '') ?></td>
                                <td class="fw-bold text-center"><?= $gradeDisplay ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $isFinal ? 'success' : 'secondary' ?>"><?= $isFinal ? 'Final' : 'Draft' ?></span>
                                </td>
                                <td class="small text-tertiary"><?= htmlspecialchars($item['remarks'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No grade items in this submission.</p>
        <?php endif; ?>
    </div>
</div>
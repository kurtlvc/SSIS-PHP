<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Grade Submission Details</h1>
    <a href="/ssis/faculty/grade-submissions" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Submissions
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/faculty/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/faculty/grade-submissions">Grade Submissions</a></li>
        <li class="breadcrumb-item active" aria-current="page">Submission Details</li>
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

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <span class="fw-bold"><?= htmlspecialchars($submission['code'] ?? '') ?></span>
                    <span class="badge bg-<?= match($submission['status']) {
                        'draft' => 'secondary',
                        'submitted' => 'warning',
                        'reviewed' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'posted' => 'primary',
                        default => 'secondary',
                    } ?> ms-2"><?= ucfirst($submission['status']) ?></span>
                </div>
                <div class="text-end">
                    <small class="text-muted">Submitted: <?= $submission['submitted_at'] ? date('M d, Y H:i', strtotime($submission['submitted_at'])) : 'Not submitted' ?></small>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Course</h6>
                        <p class="mb-0 fw-bold"><?= htmlspecialchars($submission['code'] ?? '') ?> - <?= htmlspecialchars($submission['description'] ?? '') ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Class / Schedule</h6>
                        <p class="mb-0"><?= htmlspecialchars($submission['class_no'] ?? '') ?> | <?= htmlspecialchars($submission['day_name'] ?? '') ?> <?= htmlspecialchars($submission['time_start'] ?? '') ?>-<?= htmlspecialchars($submission['time_end'] ?? '') ?> | <?= htmlspecialchars($submission['room_name'] ?? '') ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Faculty</h6>
                        <p class="mb-0"><?= htmlspecialchars(($submission['faculty_fname'] ?? '') . ' ' . ($submission['faculty_lname'] ?? '')) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Academic Term</h6>
                        <p class="mb-0"><?= htmlspecialchars($submission['ay_name'] ?? '') ?> - <?= htmlspecialchars($submission['semester_name'] ?? '') ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Students in Submission</h6>
                        <p class="mb-0 fw-bold"><?= $submission['student_count'] ?? 0 ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Current Status</h6>
                        <p class="mb-0">
                            <span class="badge bg-<?= match($submission['status']) {
                                'draft' => 'secondary',
                                'submitted' => 'warning',
                                'reviewed' => 'info',
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'posted' => 'primary',
                                default => 'secondary',
                            } ?>"><?= ucfirst($submission['status']) ?></span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header fw-bold">Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <?php if ($submission['status'] === 'draft'): ?>
                        <form method="POST" action="/ssis/faculty/grade-submissions/<?= $submission['submission_id'] ?>/submit" class="d-inline" onsubmit="return confirm('Submit this draft for review?');">
                            <input type="hidden" name="csrf_token" value="<?= App::csrf_token() ?>">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-send-check me-1"></i>Submit for Review
                            </button>
                        </form>
                    <?php elseif ($submission['status'] === 'submitted' || $submission['status'] === 'reviewed'): ?>
                        <a href="/ssis/faculty/grade-submissions/<?= $submission['submission_id'] ?>/edit" class="btn btn-primary w-100">
                            <i class="bi bi-pencil me-1"></i>Edit Grades
                        </a>
                    <?php elseif ($submission['status'] === 'approved'): ?>
                        <form method="POST" action="/ssis/faculty/grade-submissions/<?= $submission['submission_id'] ?>/post" class="d-inline" onsubmit="return confirm('Post these grades to student records? This action cannot be undone.');">
                            <input type="hidden" name="csrf_token" value="<?= App::csrf_token() ?>">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-upload me-1"></i>Post Grades
                            </button>
                        </form>
                    <?php elseif ($submission['status'] === 'rejected'): ?>
                        <div class="alert alert-danger mb-0">
                            <i class="bi bi-x-circle me-1"></i>This submission was rejected and cannot be modified.
                        </div>
                    <?php elseif ($submission['status'] === 'posted'): ?>
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle me-1"></i>Grades have been posted to student records.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">Student Grades</div>
            <div class="card-body p-0">
                <?php if (!empty($items)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-dark-theme" aria-label="Student grades in submission">
                            <thead>
                                <tr>
                                    <th class="small">Student</th>
                                    <th class="small">Ref No</th>
                                    <th class="small text-center">Grade</th>
                                    <th class="small">Remarks</th>
                                    <th class="small">Final</th>
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
                                        <td class="small text-tertiary text-center"><?= htmlspecialchars($item['remarks'] ?? '—') ?></td>
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
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header fw-semibold">Student Grades</div>
            <div class="card-body p-0">
                <?php if (!empty($items)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-dark-theme" aria-label="Student grades in submission">
                            <thead>
                                <tr>
                                    <th class="small">Student</th>
                                    <th class="small">Ref No</th>
                                    <th class="small text-center">Grade</th>
                                    <th class="small">Remarks</th>
                                    <th class="small">Final</th>
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
                                        <td class="small text-tertiary text-center"><?= htmlspecialchars($item['remarks'] ?? '—') ?></td>
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
    </div>
</div>

<?php if (!empty($reviewHistory)): ?>
    <!-- Prominent Review Feedback Card -->
    <div class="card mt-4 border-<?= match($reviewHistory[0]['action']) {
        'approved' => 'success',
        'rejected' => 'danger',
        'revision_requested' => 'warning',
        default => 'info',
    } ?>">
        <div class="card-header bg-<?= match($reviewHistory[0]['action']) {
            'approved' => 'success',
            'rejected' => 'danger',
            'revision_requested' => 'warning',
            default => 'info',
        } ?> text-white d-flex justify-content-between align-items-center">
            <span class="fw-bold">
                <i class="bi bi-<?= match($reviewHistory[0]['action']) {
                    'approved' => 'check-circle-fill',
                    'rejected' => 'x-circle-fill',
                    'revision_requested' => 'arrow-counterclockwise',
                    default => 'clipboard-check',
                } ?> me-2"></i>
                Review Feedback: <?= ucfirst($reviewHistory[0]['action']) ?>
            </span>
            <small class="text-white-50">Reviewed by <?= htmlspecialchars($reviewHistory[0]['reviewer_name'] ?? '') ?> on <?= date('M d, Y H:i', strtotime($reviewHistory[0]['created_at'])) ?></small>
        </div>
        <div class="card-body">
            <?php foreach ($reviewHistory as $review): ?>
                <div class="mb-3 p-3 bg-light rounded">
                    <div class="d-flex align-items-start gap-3">
                        <div class="timeline-marker bg-<?= match($review['action']) {
                            'approved' => 'success',
                            'rejected' => 'danger',
                            'revision_requested' => 'warning',
                            default => 'info',
                        } ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-<?= match($review['action']) {
                                'approved' => 'check-circle-fill',
                                'rejected' => 'x-circle-fill',
                                'revision_requested' => 'arrow-counterclockwise',
                                default => 'clipboard-check',
                            } ?> fs-5"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">
                                <?= ucfirst($review['action']) ?>
                                <span class="badge bg-<?= match($review['action']) {
                                    'approved' => 'success',
                                    'rejected' => 'danger',
                                    'revision_requested' => 'warning',
                                    default => 'secondary',
                                } ?> ms-2"><?= ucfirst($review['action']) ?></span>
                            </div>
                            <div class="text-muted small mb-2">
                                <i class="bi bi-person-badge me-1"></i>
                                <?= htmlspecialchars($review['reviewer_name'] ?? '') ?>
                                <i class="bi bi-calendar-event ms-3 me-1"></i>
                                <?= date('M d, Y H:i', strtotime($review['created_at'])) ?>
                            </div>
                            <?php if (!empty($review['notes'])): ?>
                                <div class="alert alert-light border mt-2 mb-0 p-3">
                                    <i class="bi bi-chat-text me-1"></i>
                                    <strong>Reviewer Comments:</strong>
                                    <div class="mt-2"><?= nl2br(htmlspecialchars($review['notes'])) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <!-- No review history yet -->
    <div class="card mt-4 border-secondary">
        <div class="card-header bg-light">
            <i class="bi bi-clock-history me-2"></i>
            <span class="fw-semibold">Review Status</span>
            <span class="badge bg-secondary ms-2">No Review Yet</span>
        </div>
        <div class="card-body text-center py-4">
            <i class="bi bi-clock-history display-4 text-muted mb-3"></i>
            <h5 class="text-muted">No Review History Yet</h5>
            <p class="text-muted mb-0">This submission has not been reviewed yet. You will see feedback here once it's reviewed.</p>
        </div>
    </div>
<?php endif; ?>

(End of file - total 221 lines)
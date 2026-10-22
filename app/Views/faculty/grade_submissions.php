<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Grade Submissions</h1>
    <a href="/faculty/grade-submissions/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>New Submission
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/faculty/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Grade Submissions</li>
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
    <div class="col-md-3">
        <div class="card stat-card stat-card--users">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-card__icon stat-card__icon--secondary">
                    <i class="bi bi-file-earmark-text" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <div class="stat-card__value"><?= $stats['draft'] ?? 0 ?></div>
                    <div class="stat-card__label">Draft</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card--warnings">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-card__icon stat-card__icon--warning">
                    <i class="bi bi-eye" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <div class="stat-card__value"><?= $stats['submitted'] ?? 0 ?></div>
                    <div class="stat-card__label">Submitted for Review</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card--documents">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-card__icon stat-card__icon--info">
                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <div class="stat-card__value"><?= $stats['approved'] ?? 0 ?></div>
                    <div class="stat-card__label">Approved</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card stat-card--status">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-card__icon stat-card__icon--success">
                    <i class="bi bi-check-circle" style="font-size: 1.5rem;"></i>
                </div>
                <div>
                    <div class="stat-card__value"><?= $stats['posted'] ?? 0 ?></div>
                    <div class="stat-card__label">Posted</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form method="GET" class="row g-3 align-items-end flex-grow-1">
            <div class="col-md-3">
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
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label for="status_filter" class="form-label small">Status</label>
                <select name="status_filter" id="status_filter" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="draft" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'draft') ? 'selected' : '' ?>>Draft</option>
                    <option value="submitted" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'submitted') ? 'selected' : '' ?>>Submitted</option>
                    <option value="reviewed" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'reviewed') ? 'selected' : '' ?>>Reviewed</option>
                    <option value="approved" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'approved') ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'rejected') ? 'selected' : '' ?>>Rejected</option>
                    <option value="posted" <?= (isset($_GET['status_filter']) && $_GET['status_filter'] == 'posted') ? 'selected' : '' ?>>Posted</option>
                </select>
            </div>
            <div class="col-md-3 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
        <a href="/faculty/grade-submissions/create" class="btn btn-primary btn-sm">
            <i class="bi bi-plus me-1"></i>New Submission
        </a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($submissions)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Grade submissions">
                    <thead>
                        <tr>
                            <th class="small">Schedule</th>
                            <th class="small">Course</th>
                            <th class="small">Faculty</th>
                            <th class="small">Students</th>
                            <th class="small">Status</th>
                            <th class="small">Submitted</th>
                            <th class="small">Reviewed By</th>
                            <th class="small">Review Feedback</th>
                            <th class="small text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $sub): ?>
                            <?php
                                $statusClass = match($sub['status']) {
                                    'draft' => 'bg-secondary',
                                    'submitted' => 'bg-warning',
                                    'reviewed' => 'bg-info',
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    'posted' => 'bg-primary',
                                    default => 'bg-secondary',
                                };
                                $hasReviewFeedback = !empty($sub['review_notes']) || !empty($sub['reviewed_at']);
                            ?>
                            <tr>
                                <td class="small text-tertiary"><?= htmlspecialchars($sub['class_no'] ?? '') ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($sub['code'] ?? '') ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($sub['description'] ?? '') ?></small>
                                </td>
                                <td class="small text-tertiary"><?= htmlspecialchars(($sub['faculty_fname'] ?? '') . ' ' . ($sub['faculty_lname'] ?? '')) ?></td>
                                <td class="text-center fw-bold"><?= $sub['student_count'] ?? 0 ?></td>
                                <td>
                                    <span class="badge <?= $statusClass ?>"><?= ucfirst($sub['status']) ?></span>
                                </td>
                                <td class="small text-tertiary"><?= $sub['submitted_at'] ? date('M d, Y', strtotime($sub['submitted_at'])) : '—' ?></td>
                                <td class="small text-tertiary"><?= !empty($sub['reviewed_by_name']) ? htmlspecialchars($sub['reviewed_by_name']) : '—' ?></td>
                                <td>
                                    <?php if ($sub['status'] === 'reviewed' || $sub['status'] === 'approved' || $sub['status'] === 'rejected'): ?>
                                        <?php if (!empty($sub['review_notes'])): ?>
                                            <span class="badge bg-info text-dark" title="<?= htmlspecialchars($sub['review_notes']) ?>">
                                                <i class="bi bi-chat-text me-1"></i>Has Feedback
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No Feedback</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="/faculty/grade-submissions/<?= $sub['submission_id'] ?>" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if ($sub['status'] === 'draft'): ?>
                                            <form method="POST" action="/faculty/grade-submissions/<?= $sub['submission_id'] ?>/submit" class="d-inline" onsubmit="return confirm('Submit this draft for review?');">
                                                <input type="hidden" name="csrf_token" value="<?= App::csrf_token() ?>">
                                                <button type="submit" class="btn btn-outline-success" title="Submit for Review">
                                                    <i class="bi bi-send"></i>
                                                </button>
                                            </form>
                                        <?php elseif ($sub['status'] === 'submitted' || $sub['status'] === 'reviewed'): ?>
                                            <a href="/faculty/grade-submissions/<?= $sub['submission_id'] ?>/edit" class="btn btn-outline-primary" title="Edit Grades">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        <?php elseif ($sub['status'] === 'approved'): ?>
                                            <span class="text-success small">Approved - Ready to Post</span>
                                        <?php elseif ($sub['status'] === 'rejected'): ?>
                                            <span class="text-danger small">Rejected</span>
                                        <?php elseif ($sub['status'] === 'posted'): ?>
                                            <span class="text-success small">Posted</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No grade submissions found for the selected criteria.</p>
        <?php endif; ?>
    </div>
</div>
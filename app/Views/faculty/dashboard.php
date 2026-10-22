<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Faculty Dashboard</h1>
    <span class="small fst-italic text-tertiary"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/faculty/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Faculty</li>
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
    <div class="col-6 col-sm-12 col-lg-3 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4 text-center">
                <i class="bi bi-book display-6 text-primary mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= $stats['total_classes'] ?? 0 ?></h2>
                <p class="text-muted small mb-0">Classes This Term</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-12 col-lg-3 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4 text-center">
                <i class="bi bi-people display-6 text-info mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= $stats['total_students'] ?? 0 ?></h2>
                <p class="text-muted small mb-0">Total Students</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-12 col-lg-3 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4 text-center">
                <i class="bi bi-pencil-square display-6 text-warning mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= $stats['pending_grades'] ?? 0 ?></h2>
                <p class="text-muted small mb-0">Pending Grades</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-12 col-lg-3 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4 text-center">
                <i class="bi bi-check-circle display-6 text-success mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= $stats['submitted_grades'] ?? 0 ?></h2>
                <p class="text-muted small mb-0">Submitted</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">My Classes This Term</span>
                <span class="badge bg-primary"><?= count($schedules) ?> Classes</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($schedules)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-dark-theme" aria-label="My classes">
                            <thead>
                                <tr>
                                    <th class="small">Course</th>
                                    <th class="small">Section</th>
                                    <th class="small">Schedule</th>
                                    <th class="small">Room</th>
                                    <th class="small text-center">Students</th>
                                    <th class="small text-center">Grade Status</th>
                                    <th class="small text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedules as $sched): ?>
                                    <?php
                                        $gradeStatus = 'draft';
                                        $gradeStatusLabel = 'Not Started';
                                        if (isset($sched['submission_status'])) {
                                            $gradeStatus = $sched['submission_status'];
                                            $gradeStatusLabel = ucfirst($gradeStatus);
                                        }
                                        $statusClass = match($gradeStatus) {
                                            'draft' => 'bg-secondary',
                                            'submitted' => 'bg-warning',
                                            'reviewed' => 'bg-info',
                                            'approved' => 'bg-success',
                                            'rejected' => 'bg-danger',
                                            'posted' => 'bg-primary',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <tr>
                                        <td class="fw-medium">
                                            <?= htmlspecialchars($sched['code'] ?? '') ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($sched['description'] ?? '') ?></small>
                                        </td>
                                        <td class="small text-center text-tertiary"><?= htmlspecialchars($sched['class_no'] ?? '') ?></td>
                                        <td class="small text-tertiary">
                                            <?= htmlspecialchars($sched['day_name'] ?? '') ?>
                                            <?= htmlspecialchars($sched['time_start'] ?? '') ?>-<?= htmlspecialchars($sched['time_end'] ?? '') ?>
                                            <br><small><?= htmlspecialchars($sched['room_name'] ?? '') ?></small>
                                        </td>
                                        <td class="small text-tertiary text-center"><?= htmlspecialchars($sched['room_name'] ?? '') ?></td>
                                        <td class="small text-center fw-bold"><?= $sched['enrolled_count'] ?? 0 ?></td>
                                        <td class="text-center">
                                            <span class="badge <?= $statusClass ?>"><?= $gradeStatusLabel ?></span>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="/faculty/grade-submissions/<?= $sched['submission_id'] ?? 0 ?>" class="btn btn-outline-primary btn-sm" title="View Grades">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if (!isset($sched['submission_id'])): ?>
                                                    <a href="/faculty/grade-submissions/create/confirm?sched_id=<?= $sched['sched_id'] ?>" class="btn btn-outline-success" title="Create Submission">
                                                        <i class="bi bi-plus-circle"></i>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="/faculty/grade-submissions/<?= $sched['submission_id'] ?>" class="btn btn-outline-primary" title="Edit Grades">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 mb-0">No classes assigned for the current term.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">Recent Announcements</div>
            <div class="card-body">
                <?php if (!empty($announcements)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($announcements as $ann): ?>
                            <div class="list-group-item px-0 border-0 border-bottom">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-medium"><?= htmlspecialchars($ann['ann_title']) ?></span>
                                    <small class="text-muted"><?= date('M d, Y', strtotime($ann['ann_id'] ?? '')) ?></small>
                                </div>
                                <p class="mb-0 small text-muted"><?= htmlspecialchars($ann['ann_body']) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3 mb-0">No announcements at this time.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-semibold">Quick Actions</div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="/faculty/grade-submissions" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i>Grade Submissions
                    </a>
                    <a href="/faculty/grade-submissions/create" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>New Grade Submission
                    </a>
                    <a href="/faculty/schedule" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-event me-1"></i>My Schedule
                    </a>
                    <a href="/faculty/profile" class="btn btn-outline-secondary">
                        <i class="bi bi-person me-1"></i>My Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
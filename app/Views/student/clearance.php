<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Clearance</h1>
    <a href="/ssis/student/dashboard" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">My Clearance</li>
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

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label for="semFilter" class="form-label small">Semester</label>
                <select name="sem_id" id="semFilter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($activeSemester) && $activeSemester == $sem['sem_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['semester_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($clearance)): ?>
            <div class="row g-3 p-3">
                <?php foreach ($clearance as $item): ?>
                    <?php
                        $status = $item['status'] ?? '';
                        $statusInfo = match($status) {
                            'Cleared' => ['label' => 'Cleared', 'icon' => 'bi-check-circle-fill', 'color' => 'success', 'step' => 3],
                            'On-Hold' => ['label' => 'On Hold', 'icon' => 'bi-pause-circle-fill', 'color' => 'warning', 'step' => 2],
                            'Pending' => ['label' => 'Pending', 'icon' => 'bi-clock', 'color' => 'secondary', 'step' => 1],
                            default => ['label' => 'Pending', 'icon' => 'bi-clock', 'color' => 'secondary', 'step' => 1],
                        };
                        $deptLabels = $item['dept_name'] ?? 'Unknown Department';
                    ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="fw-bold"><?= $deptLabels ?></span>
                                    <span class="badge bg-<?= $statusInfo['color'] ?> ms-2"><?= $statusInfo['label'] ?></span>
                                </div>
                                <small class="text-muted"><?= $item['semester_name'] ?? '' ?></small>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Department</h6>
                                        <p class="mb-0"><?= htmlspecialchars($item['dept_name'] ?? '') ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-1">Current Status</h6>
                                        <p class="mb-0">
                                            <span class="badge bg-<?= $statusInfo['color'] ?>"><?= $statusInfo['label'] ?></span>
                                        </p>
                                    </div>
                                </div>
                                
                                <?php if (!empty($item['remarks'])): ?>
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Remarks</h6>
                                        <p class="mb-0"><?= htmlspecialchars($item['remarks']) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($item['cleared_by_name'])): ?>
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Processed By</h6>
                                        <p class="mb-0"><?= htmlspecialchars($item['cleared_by_name']) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($item['cleared_at'])): ?>
                                    <div class="mb-3">
                                        <h6 class="text-muted mb-1">Processed On</h6>
                                        <p class="mb-0"><?= htmlspecialchars(date('M d, Y H:i', strtotime($item['cleared_at']))) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Status Progress -->
                                <div class="workflow-timeline">
                                    <h6 class="text-muted mb-3">Progress</h6>
                                    <div class="timeline">
                                        <!-- Step 1: Requested -->
                                        <div class="timeline-item completed">
                                            <div class="timeline-marker">
                                                <i class="bi bi-send"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title">Clearance Requested</div>
                                                <div class="timeline-meta">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    Requested
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 2: Under Review -->
                                        <div class="timeline-item <?= $statusInfo['step'] >= 2 ? 'completed' : '' ?>">
                                            <div class="timeline-marker">
                                                <i class="bi bi-search"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title">Under Review</div>
                                                <?php if ($statusInfo['step'] >= 2): ?>
                                                    <div class="timeline-meta">
                                                        <i class="bi bi-person-badge me-1"></i>
                                                        Reviewed by department
                                                        <br>
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        <?= $item['cleared_at'] ? date('M d, Y', strtotime($item['cleared_at'])) : 'Pending' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="timeline-meta text-muted">Awaiting department review...</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Step 3: Cleared/On Hold -->
                                        <div class="timeline-item <?= $statusInfo['step'] >= 3 ? 'completed' : '' ?>">
                                            <div class="timeline-marker">
                                                <i class="bi bi-<?= $statusInfo['step'] >= 3 ? 'check-circle-fill' : 'clock' ?>"></i>
                                            </div>
                                            <div class="timeline-content">
                                                <div class="timeline-title"><?= $statusInfo['label'] ?></div>
                                                <?php if ($statusInfo['step'] >= 3): ?>
                                                    <div class="timeline-meta">
                                                        <i class="bi bi-calendar-event me-1"></i>
                                                        <?= $item['cleared_at'] ? date('M d, Y', strtotime($item['cleared_at'])) : 'Processing' ?>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="timeline-meta text-muted">Awaiting department decision...</div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-file-earmark-text display-1 text-muted mb-3"></i>
                    <h4 class="text-muted mb-2">No clearance records found</h4>
                    <p class="text-muted mb-4">No clearance records found for the selected semester.</p>
                    <a href="/ssis/student/dashboard" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
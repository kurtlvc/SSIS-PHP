<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Document Request History</h1>
    <a href="/ssis/student/documents/request" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>New Request
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Document History</li>
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

<?php if (!empty($requests)): ?>
    <div class="row g-4">
        <?php foreach ($requests as $r): ?>
            <?php
                $status = $r['status'] ?? 'Pending';
                $statusInfo = match($status) {
                    'Released' => ['label' => 'Released', 'icon' => 'bi-check-circle-fill', 'color' => 'success', 'step' => 4],
                    'Ready for Pickup' => ['label' => 'Ready for Pickup', 'icon' => 'bi-box-seam', 'color' => 'info', 'step' => 3],
                    'Processing' => ['label' => 'Processing', 'icon' => 'bi-hourglass-split', 'color' => 'warning', 'step' => 2],
                    'Pending' => ['label' => 'Pending', 'icon' => 'bi-clock', 'color' => 'secondary', 'step' => 1],
                    default => ['label' => 'Pending', 'icon' => 'bi-clock', 'color' => 'secondary', 'step' => 1],
                };
                $docLabels = ['TOR' => 'Transcript of Records', 'COR' => 'Certificate of Registration', 'Good Moral' => 'Good Moral', 'Certification' => 'Certification'];
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold"><?= $docLabels[$r['document_type']] ?? $r['document_type'] ?></span>
                            <span class="badge bg-<?= $statusInfo['color'] ?> ms-2"><?= $statusInfo['label'] ?></span>
                        </div>
                        <small class="text-muted"><?= htmlspecialchars(date('M d, Y H:i', strtotime($r['request_date'] ?? ''))) ?></small>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Reference No</h6>
                                <p class="mb-0 font-monospace"><?= htmlspecialchars($r['request_id'] ?? '') ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Type</h6>
                                <p class="mb-0"><?= $docLabels[$r['document_type']] ?? $r['document_type'] ?></p>
                            </div>
                        </div>
                        <?php if (!empty($r['purpose'])): ?>
                            <div class="mb-3">
                                <h6 class="text-muted mb-1">Purpose</h6>
                                <p class="mb-0"><?= htmlspecialchars($r['purpose']) ?></p>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Workflow Timeline -->
                        <div class="workflow-timeline">
                            <h6 class="text-muted mb-3">Status Progress</h6>
                            <div class="timeline">
                                <!-- Step 1: Pending -->
                                <div class="timeline-item <?= $statusInfo['step'] >= 1 ? 'completed' : '' ?>">
                                    <div class="timeline-marker">
                                        <i class="bi bi-clock"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Request Submitted</div>
                                        <div class="timeline-meta">
                                            <i class="bi bi-calendar-event me-1"></i>
                                            <?= htmlspecialchars(date('M d, Y H:i', strtotime($r['request_date'] ?? ''))) ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Step 2: Processing -->
                                <div class="timeline-item <?= $statusInfo['step'] >= 2 ? 'completed' : '' ?>">
                                    <div class="timeline-marker">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Under Review</div>
                                        <?php if ($statusInfo['step'] >= 2 && !empty($r['processed_date'])): ?>
                                            <div class="timeline-meta">
                                                <i class="bi bi-person-badge me-1"></i>
                                                Processed by: <?= htmlspecialchars(($r['processed_fname'] ?? '') . ' ' . ($r['processed_lname'] ?? '')) ?>
                                                <br>
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= htmlspecialchars(date('M d, Y H:i', strtotime($r['processed_date'] ?? ''))) ?>
                                            </div>
                                        <?php elseif ($statusInfo['step'] < 2): ?>
                                            <div class="timeline-meta text-muted">Awaiting registrar review...</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Step 3: Ready for Pickup -->
                                <div class="timeline-item <?= $statusInfo['step'] >= 3 ? 'completed' : '' ?>">
                                    <div class="timeline-marker">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Ready for Pickup</div>
                                        <?php if ($statusInfo['step'] >= 3 && !empty($r['ready_date'])): ?>
                                            <div class="timeline-meta">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                Available since: <?= htmlspecialchars(date('M d, Y', strtotime($r['ready_date'] ?? ''))) ?>
                                            </div>
                                        <?php elseif ($statusInfo['step'] < 3): ?>
                                            <div class="timeline-meta text-muted">Awaiting document preparation...</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <!-- Step 4: Released -->
                                <div class="timeline-item <?= $statusInfo['step'] >= 4 ? 'completed' : '' ?>">
                                    <div class="timeline-marker">
                                        <i class="bi bi-check-circle-fill"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-title">Released</div>
                                        <?php if ($statusInfo['step'] >= 4 && !empty($r['released_date'])): ?>
                                            <div class="timeline-meta">
                                                <i class="bi bi-person-badge me-1"></i>
                                                Released by: <?= htmlspecialchars(($r['released_fname'] ?? '') . ' ' . ($r['released_lname'] ?? '')) ?>
                                                <br>
                                                <i class="bi bi-calendar-event me-1"></i>
                                                <?= htmlspecialchars(date('M d, Y H:i', strtotime($r['released_date'] ?? ''))) ?>
                                            </div>
                                        <?php elseif ($statusInfo['step'] < 4): ?>
                                            <div class="timeline-meta text-muted">Awaiting pickup/release...</div>
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
            <h4 class="text-muted mb-2">No document requests yet</h4>
            <p class="text-muted mb-4">Submit your first document request to get started.</p>
            <a href="/ssis/student/documents/request" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Request Document
            </a>
        </div>
    </div>
<?php endif; ?>
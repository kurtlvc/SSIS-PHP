<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Enrollment History</h1>
    <a href="/ssis/student/enrollment/cart" class="btn btn-primary">
        <i class="bi bi-cart me-1"></i>Current Cart
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Enrollment History</li>
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

<?php if (!empty($enrolments)): ?>
    <div class="card mb-4">
        <div class="card-header fw-semibold">Previous Enrollments</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Enrollment history">
                    <thead>
                        <tr>
                            <th class="small">Academic Year</th>
                            <th class="small">Semester</th>
                            <th class="small">Status</th>
                            <th class="small">Subjects</th>
                            <th class="small">Total Units</th>
                            <th class="small">Date</th>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolments as $enrolment): ?>
                            <tr>
                                <td class="small text-tertiary"><?= htmlspecialchars($enrolment['ay_name'] ?? '') ?></td>
                                <td class="small text-tertiary"><?= htmlspecialchars($enrolment['semester_name'] ?? '') ?></td>
                                <td class="small">
                                    <?php
                                        $status = $enrolment['enrol_status'] ?? 'Unknown';
                                        $badgeClass = match($status) {
                                            'Approved' => 'bg-success',
                                            'Pending' => 'bg-warning',
                                            'Rejected' => 'bg-danger',
                                            'Dropped' => 'bg-secondary',
                                            default => 'bg-secondary',
                                        };
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($status) ?></span>
                                </td>
                                <td class="small text-center"><?= $enrolment['total_course'] ?? 0 ?></td>
                                <td class="small text-center fw-bold"><?= $enrolment['total_units'] ?? 0 ?></td>
                                <td class="small text-tertiary"><?= $enrolment['registration_date'] ? date('M d, Y', strtotime($enrolment['registration_date'])) : 'N/A' ?></td>
                                <td class="text-center">
                                    <a href="/ssis/student/enrollment/view/<?= $enrolment['enrolment_id'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clock-history display-1 text-muted mb-3"></i>
            <h4 class="text-muted mb-2">No enrollment history</h4>
            <p class="text-muted mb-4">You haven't enrolled in any subjects yet.</p>
            <a href="/ssis/student/enrollment/available" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Browse Subjects
            </a>
        </div>
    </div>
<?php endif; ?>
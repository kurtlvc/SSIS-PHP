<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Available Subjects for Enrollment</h1>
    <a href="/ssis/student/enrollment/cart" class="btn btn-outline-secondary">
        <i class="bi bi-cart me-1"></i>View Cart
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/student/enrollment/cart">My Cart</a></li>
        <li class="breadcrumb-item active" aria-current="page">Add Subjects</li>
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

<?php if (!empty($availableSchedules)): ?>
    <div class="card mb-4">
        <div class="card-header fw-semibold">Available Schedules for <?= htmlspecialchars($currentSemester ?? 'Current Semester') ?></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Available schedules">
                    <thead>
                        <tr>
                            <th class="small">Subject</th>
                            <th class="small">Code</th>
                            <th class="small">Units</th>
                            <th class="small">Schedule</th>
                            <th class="small">Professor</th>
                            <th class="small">Class Size</th>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($availableSchedules as $schedule): ?>
                            <?php
                                $inCart = false;
                                if (isset($cartItems)) {
                                    foreach ($cartItems as $item) {
                                        if ($item['sched_id'] == $schedule['sched_id']) {
                                            $inCart = true;
                                            break;
                                        }
                                    }
                                }
                                $alreadyEnrolled = false;
                                if (isset($enrolledSchedIds)) {
                                    $alreadyEnrolled = in_array($schedule['sched_id'], $enrolledSchedIds);
                                }
                            ?>
                            <tr class="<?= $inCart ? 'table-warning' : '' ?>">
                                <td class="fw-medium"><?= htmlspecialchars($schedule['description']) ?></td>
                                <td class="small text-tertiary font-monospace"><?= htmlspecialchars($schedule['code']) ?></td>
                                <td class="small text-center fw-bold"><?= $schedule['units'] ?></td>
                                <td class="small text-tertiary">
                                    <?= htmlspecialchars($schedule['schedule_info'] ?? 'TBA') ?>
                                </td>
                                <td class="small text-tertiary">
                                    <?= htmlspecialchars(($schedule['prof_fname'] ?? '') . ' ' . ($schedule['prof_lname'] ?? '')) ?>
                                </td>
                                <td class="small text-center">
                                    <?php if ($schedule['class_size'] > 0): ?>
                                        <span class="badge bg-success"><?= $schedule['class_size'] ?> slots</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Full</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($inCart): ?>
                                        <span class="badge bg-warning text-dark">In Cart</span>
                                    <?php elseif ($alreadyEnrolled): ?>
                                        <span class="badge bg-success">Enrolled</span>
                                    <?php elseif ($schedule['class_size'] > 0): ?>
                                        <form method="POST" action="/ssis/student/enrollment/cart/add" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <input type="hidden" name="sched_id" value="<?= $schedule['sched_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-primary" title="Add to cart">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled title="Class is full">
                                            <i class="bi bi-x-circle"></i> Full
                                        </button>
                                    <?php endif; ?>
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
            <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
            <h4 class="text-muted mb-2">No available schedules</h4>
            <p class="text-muted mb-0">There are no available schedules for your curriculum this semester.</p>
        </div>
    </div>
<?php endif; ?>
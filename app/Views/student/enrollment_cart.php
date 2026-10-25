<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Enrollment Cart</h1>
    <div class="d-flex gap-2">
        <a href="/ssis/student/enrollment/available" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i>Add Subjects
        </a>
        <a href="/ssis/student/enrollment/history" class="btn btn-outline-secondary">
            <i class="bi bi-clock-history me-1"></i>Enrollment History
        </a>
    </div>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Enrollment Cart</li>
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

<?php if (isset($cart) && $cart): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-bold">Your Enrollment Cart</span>
            <span class="badge bg-primary"><?= count($cartItems) ?> subjects, <?= $cart['total_units'] ?> units</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Enrollment cart items">
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
                        <?php foreach ($cartItems as $item): ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($item['description']) ?></td>
                                <td class="small text-tertiary font-monospace"><?= htmlspecialchars($item['code']) ?></td>
                                <td class="small text-center fw-bold"><?= $item['units'] ?></td>
                                <td class="small text-tertiary">
                                    <?= htmlspecialchars($item['schedule_info'] ?? 'TBA') ?>
                                </td>
                                <td class="small text-tertiary">
                                    <?= htmlspecialchars(($item['prof_fname'] ?? '') . ' ' . ($item['prof_lname'] ?? '')) ?>
                                </td>
                                <td class="small text-center text-tertiary"><?= $item['class_size'] ?> slots</td>
                                <td class="text-center">
                                    <form method="POST" action="/ssis/student/enrollment/cart/remove" class="d-inline" onsubmit="return confirm('Remove this subject from cart?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <input type="hidden" name="cart_id" value="<?= $cart['cart_id'] ?>">
                                        <input type="hidden" name="sched_id" value="<?= $item['sched_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php if ($cart['status'] === 'draft'): ?>
        <div class="d-flex justify-content-end gap-2 mb-4">
            <form method="POST" action="/ssis/student/enrollment/cart/submit" class="d-inline" onsubmit="return confirm('Submit cart for assessment? You cannot modify it after submission until assessed.');">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <input type="hidden" name="cart_id" value="<?= $cart['cart_id'] ?>">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-send-check me-1"></i>Submit for Assessment
                </button>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($cart['status'] === 'submitted'): ?>
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-1"></i>
            Your enrollment has been submitted. Please wait for the registrar to assess it.
        </div>
    <?php endif; ?>

    <?php if ($cart['status'] === 'assessed'): ?>
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-1"></i>
            Your enrollment has been assessed. You may now view and pay your assessment.
        </div>
    <?php endif; ?>

    <?php if ($cart['status'] === 'paid'): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill me-1"></i>
            Your enrollment is confirmed and paid!
        </div>
    <?php endif; ?>

    <?php if (isset($assessment) && $assessment): ?>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="fw-bold">Assessment Details</span>
                <span class="badge bg-<?= $assessment['status'] === 'paid' ? 'success' : 'warning' ?>">
                    <?= ucfirst($assessment['status'] ?: 'pending') ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Assessment #</h6>
                        <p class="mb-0 fw-bold">#<?= $assessment['assessment_id'] ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Term</h6>
                        <p class="mb-0"><?= htmlspecialchars($assessment['semester_name'] . ' ' . $assessment['ay_name']) ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Program</h6>
                        <p class="mb-0"><?= htmlspecialchars($assessment['program_code']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Assessed On</h6>
                        <p class="mb-0"><?= date('M d, Y', strtotime($assessment['assessed_at'])) ?></p>
                    </div>
                </div>
                <hr>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Tuition Fee</h6>
                        <p class="mb-0 fw-bold text-primary">₱ <?= number_format($assessment['tuition_fee'], 2) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Misc Fee</h6>
                        <p class="mb-0 fw-bold text-primary">₱ <?= number_format($assessment['misc_fee'], 2) ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Lab Fee</h6>
                        <p class="mb-0 fw-bold text-primary">₱ <?= number_format($assessment['lab_fee'], 2) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Other Fees</h6>
                        <p class="mb-0 fw-bold text-primary">₱ <?= number_format($assessment['other_fees'], 2) ?></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1">Discount</h6>
                        <p class="mb-0 fw-bold text-danger">- ₱ <?= number_format($assessment['discount_amount'], 2) ?></p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted mb-1"><strong>Net Amount</strong></h6>
                        <p class="mb-0 fw-bold text-success fs-5">₱ <?= number_format($assessment['net_amount'], 2) ?></p>
                    </div>
                </div>
                <hr>
                <div class="d-grid gap-2">
                    <?php if ($assessment['status'] === 'paid'): ?>
                        <button class="btn btn-success" disabled>
                            <i class="bi bi-check-circle me-1"></i>Paid
                        </button>
                    <?php else: ?>
                        <form method="GET" action="/ssis/student/payment/pay" class="d-inline">
                            <input type="hidden" name="assessment_id" value="<?= $assessment['assessment_id'] ?>">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-credit-card me-1"></i>Pay Now
                            </button>
                        </form>
                    <?php endif; ?>
                    <?php if ($assessment['status'] === 'paid'): ?>
                        <a href="/ssis/student/payment/history/<?= $assessment['assessment_id'] ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-receipt me-1"></i>View Receipt
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted mb-3"></i>
            <h4 class="text-muted mb-2">Your enrollment cart is empty</h4>
            <p class="text-muted mb-4">Add subjects from the available schedules to build your schedule.</p>
            <a href="/ssis/student/enrollment/available" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Browse Available Subjects
            </a>
        </div>
    </div>
<?php endif; ?>
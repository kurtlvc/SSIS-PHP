<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Payment Assessments</h1>
    <a href="/ssis/student/enrollment/cart" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Cart
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/student/enrollment/cart">Enrollment Cart</a></li>
        <li class="breadcrumb-item active" aria-current="page">Payment Assessments</li>
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

<?php if (!empty($assessments)): ?>
    <div class="row g-4 mb-4">
        <?php foreach ($assessments as $assessment): ?>
            <?php
                $isPaid = $assessment['status'] === 'paid';
                $statusClass = $isPaid ? 'bg-success' : ($assessment['status'] === 'partial' ? 'bg-warning' : 'bg-warning');
            ?>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-bold">Assessment #<?= $assessment['assessment_id'] ?></span>
                            <span class="badge bg-<?= $isPaid ? 'success' : 'warning' ?> ms-2">
                                <?= ucfirst($assessment['status'] ?: 'pending') ?>
                            </span>
                        </div>
                        <small class="text-muted"><?= htmlspecialchars($assessment['ay_name'] . ' - ' . $assessment['semester_name']) ?></small>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Program</h6>
                                <p class="mb-0"><?= htmlspecialchars($assessment['program_code']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Term</h6>
                                <p class="mb-0"><?= htmlspecialchars($assessment['semester_name'] . ' ' . $assessment['ay_name']) ?></p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Student</h6>
                                <p class="mb-0 fw-bold"><?= htmlspecialchars($assessment['lname'] . ', ' . $assessment['fname']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-1">Ref No</h6>
                                <p class="mb-0 font-monospace"><?= htmlspecialchars($assessment['ref_no'] ?? '') ?></p>
                            </div>
                        </div>
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
                                <h6 class="text-muted mb-1">Net Amount</h6>
                                <p class="mb-0 fw-bold text-success fs-5">₱ <?= number_format($assessment['net_amount'], 2) ?></p>
                            </div>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <?php if ($isPaid): ?>
                                <button class="btn btn-success" disabled>
                                    <i class="bi bi-check-circle me-1"></i>Paid
                                </button>
                            <?php else: ?>
                                <form method="GET" action="/ssis/student/payment/pay" class="d-inline">
                                    <input type="hidden" name="assessment_id" value="<?= $assessment['assessment_id'] ?>">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-credit-card me-1"></i>Pay Now
                                    </button>
                                </form>
                            <?php endif; ?>
                            <a href="/ssis/cashier/receipts/<?= $assessment['assessment_id'] ?>" class="btn btn-outline-secondary btn-sm" target="_blank" title="View Receipt">
                                <i class="bi bi-receipt me-1"></i>View Receipt
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-credit-card display-1 text-muted mb-3"></i>
            <h4 class="text-muted mb-2">No Assessments Yet</h4>
            <p class="text-muted mb-4">Submit your enrollment cart for assessment to see payment options.</p>
            <a href="/ssis/student/enrollment/cart" class="btn btn-primary">
                <i class="bi bi-cart-plus me-1"></i>Go to Enrollment Cart
            </a>
        </div>
    </div>
<?php endif; ?>
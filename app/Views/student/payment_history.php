<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Payment History</h1>
    <a href="/ssis/student/payment/assessments" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Assessments
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/student/payment/assessments">Payment Assessments</a></li>
        <li class="breadcrumb-item active" aria-current="page">Payment History</li>
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
    <div class="card-header">
        <span class="fw-bold">Assessment Details</span>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Assessment #</h6>
                <p class="mb-0 fw-bold">#<?= $assessment['assessment_id'] ?></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Status</h6>
                <p class="mb-0">
                    <span class="badge bg-<?= $assessment['status'] === 'paid' ? 'success' : 'warning' ?>">
                        <?= ucfirst($assessment['status'] ?: 'pending') ?>
                    </span>
                </p>
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
                <h6 class="text-muted mb-1">Program</h6>
                <p class="mb-0"><?= htmlspecialchars($assessment['program_code'] ?? '') ?></p>
            </div>
            <div class="col-md-6">
                <h6 class="text-muted mb-1">Term</h6>
                <p class="mb-0"><?= htmlspecialchars($assessment['semester_name'] . ' ' . $assessment['ay_name']) ?></p>
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
                <p class="mb-0 fw-bold text-success fs-4">₱ <?= number_format($assessment['net_amount'], 2) ?></p>
            </div>
        </div>
        <hr>
        <h6 class="text-muted mb-3">Payment History</h6>
        <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Payment history">
                    <thead>
                        <tr>
                            <th class="small">Receipt No</th>
                            <th class="small">Date</th>
                            <th class="small">Gateway</th>
                            <th class="small text-end">Amount</th>
                            <th class="small text-center">Status</th>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td class="fw-medium font-monospace"><?= htmlspecialchars($payment['receipt_no'] ?? '') ?></td>
                                <td class="small text-tertiary"><?= date('M d, Y H:i', strtotime($payment['payment_date'])) ?></td>
                                <td class="small text-tertiary">
                                    <span class="badge bg-<?= match($payment['gateway_name']) {
                                        'gcash' => 'success',
                                        'paymaya' => 'info',
                                        'bank' => 'primary',
                                        default => 'secondary',
                                    } ?>"><?= ucfirst($payment['gateway_name']) ?></span>
                                </td>
                                <td class="fw-bold text-end">₱ <?= number_format($payment['amount_paid'], 2) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $payment['status'] === 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($payment['status']) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="/ssis/student/payment/receipt/<?= urlencode($payment['receipt_no']) ?>" class="btn btn-sm btn-outline-primary" title="View Receipt">
                                        <i class="bi bi-receipt me-1"></i>View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle me-1"></i>
                No payment records found for this assessment.
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex justify-content-end gap-2 mt-4">
    <a href="/ssis/student/payment/assessments" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Assessments
    </a>
</div>
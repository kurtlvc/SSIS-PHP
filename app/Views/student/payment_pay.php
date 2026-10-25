<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Pay Assessment</h1>
    <a href="/ssis/student/payment/assessments" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Assessments
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/student/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/student/payment/assessments">Payment Assessments</a></li>
        <li class="breadcrumb-item active" aria-current="page">Pay Assessment</li>
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

<div class="row">
    <div class="col-md-8">
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
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <span class="fw-bold">Payment Options</span>
            </div>
            <div class="card-body">
                <form method="POST" action="/ssis/student/payment/pay">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <input type="hidden" name="assessment_id" value="<?= $assessment['assessment_id'] ?>">

                    <div class="mb-3">
                        <label for="gateway" class="form-label">Payment Gateway</label>
                        <select name="gateway" id="gateway" class="form-select" required>
                            <option value="simulated">Simulated Gateway</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Amount to Pay</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" name="amount_paid" id="amount_paid" class="form-control form-control-lg"
                                   step="0.01" min="1" max="<?= $assessment['net_amount'] ?>"
                                   value="<?= number_format($assessment['net_amount'], 2, '.', '') ?>" required>
                        </div>
                        <div class="form-text">Maximum: ₱ <?= number_format($assessment['net_amount'], 2) ?>
                            <?php if (isset($assessment['balance']) && $assessment['balance'] < $assessment['net_amount']): ?>
                                | Remaining balance: ₱ <?= number_format($assessment['balance'], 2) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card me-1"></i>Pay ₱ <span id="payAmount"><?= number_format($assessment['net_amount'], 2) ?></span>
                        </button>
                    </div>
                </form>

                <div class="mt-4 p-3 bg-light rounded">
                    <h6 class="mb-2">Payment Information</h6>
                    <ul class="small text-muted mb-0">
                        <li>Payments are processed securely through our payment gateway partners.</li>
                        <li>You will receive an email confirmation upon successful payment.</li>
                        <li>Official receipt will be available for download after payment.</li>
                        <li>For Bank Transfer, please use the reference number provided after payment.</li>
                        <li>Partial payments are allowed. You can make multiple payments until the full amount is settled.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('amount_paid').addEventListener('input', function() {
    document.getElementById('payAmount').textContent = parseFloat(this.value || 0).toFixed(2);
});
</script>
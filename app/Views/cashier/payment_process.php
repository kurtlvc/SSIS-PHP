<?php
function cashBadge($status) {
    return match($status) {
        'Approved', 'Cleared' => 'bg-success',
        'Pending', 'enrolled' => 'bg-warning',
        default => 'bg-secondary',
    };
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Process Payment</h1>
    <a href="/ssis/cashier/dashboard" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
</div>

<?php if (isset($error) && !empty(trim($error))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($success) && !empty(trim($success))): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($enrolment): ?>
    <div class="card mb-4">
        <div class="card-header">
            <span class="fw-bold">Student & Enrolment Details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless" aria-label="Student and enrolment details">
                        <tr>
                            <td class="fw-bold text-end">Student:</td>
                            <td><?= htmlspecialchars($enrolment['lname'] . ', ' . $enrolment['fname'] . ' ' . ($enrolment['mname'] ?? '')) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">Ref No:</td>
                            <td><?= htmlspecialchars($enrolment['ref_no']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">Email:</td>
                            <td><?= htmlspecialchars($enrolment['emailaddress']) ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-sm table-borderless" aria-label="Student and enrolment details">
                        <tr>
                            <td class="fw-bold text-end">Program:</td>
                            <td><?= htmlspecialchars($enrolment['program_code']) ?> - <?= htmlspecialchars($enrolment['program_desc']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">A/Y:</td>
                            <td><?= htmlspecialchars($enrolment['ay_name']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">Semester:</td>
                            <td><?= htmlspecialchars($enrolment['semester_name']) ?></td>
                        </tr>
                        <tr>
                            <td class="fw-bold text-end">Units:</td>
                            <td><?= $enrolment['total_units'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <form method="POST" action="/ssis/cashier/payments/process">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <input type="hidden" name="enrolment_id" value="<?= $enrolment['enrolment_id'] ?>">
        <div class="card">
            <div class="card-header">
                <span class="fw-bold">Payment Details</span>
            </div>
            <div class="card-body">
                <?php if (!empty($assessment)): ?>
                    <div class="alert alert-info">
                        Assessment #<?= (int) $assessment['assessment_id'] ?> &mdash;
                        Net amount: <strong>₱ <?= number_format($assessment['net_amount'], 2) ?></strong><br>
                        Remaining balance: <strong>₱ <?= number_format($balance ?? 0, 2) ?></strong>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">No assessment exists for this enrolment. Ask the registrar to assess it first.</div>
                <?php endif; ?>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="amount_paid" class="form-label">Amount to Pay (₱)</label>
                        <input type="number" step="0.01" min="0.01" max="<?= htmlspecialchars((string) ($balance ?? 0)) ?>" name="amount_paid" id="amount_paid" class="form-control" value="<?= htmlspecialchars((string) ($balance ?? 0)) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="gateway" class="form-label">Payment Gateway</label>
                        <select name="gateway" id="gateway" class="form-select">
                            <option value="simulated">Simulated Gateway</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                            <option value="bank">Bank Transfer</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-3" <?= empty($assessment) ? 'disabled' : '' ?>>Process Payment</button>
            </div>
        </div>
    </form>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <span class="fw-bold">Find Student Enrolment</span>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="enrolment_id" class="form-label">Search by Enrolment ID</label>
                    <input type="number" name="enrolment_id" id="enrolment_id" class="form-control" placeholder="Enter enrolment ID">
                </div>
                <div class="col-md-8 d-grid align-items-end">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

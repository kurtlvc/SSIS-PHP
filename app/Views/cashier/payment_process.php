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
    <a href="/sis/cashier/dashboard" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if (isset($success)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<?php if ($enrolment): ?>
    <div class="card mb-4">
        <div class="card-header bg-white">
            <span class="fw-bold">Student & Enrolment Details</span>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-sm table-borderless">
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
                    <table class="table table-sm table-borderless">
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

    <form method="POST" action="/sis/cashier/payments/process">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
        <input type="hidden" name="enrolment_id" value="<?= $enrolment['enrolment_id'] ?>">
        <input type="hidden" name="ay_id" value="<?= $enrolment['ay_id'] ?>">
        <div class="card">
            <div class="card-header bg-white">
                <span class="fw-bold">Payment Details</span>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Amount to Pay (₱)</label>
                        <input type="number" step="0.01" name="amount_paid" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Or Number</label>
                        <input type="text" name="or_no" class="form-control" placeholder="Optional">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="Cash">Cash</option>
                            <option value="Bank Deposit">Bank Deposit</option>
                            <option value="Online Transfer">Online Transfer</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks" class="form-control" placeholder="Optional notes">
                </div>
                <button type="submit" class="btn btn-success">Process Payment</button>
            </div>
        </div>
    </form>
<?php else: ?>
    <div class="card">
        <div class="card-header bg-white">
            <span class="fw-bold">Find Student Enrolment</span>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search by Enrolment ID</label>
                    <input type="number" name="enrolment_id" class="form-control" placeholder="Enter enrolment ID">
                </div>
                <div class="col-md-8 d-grid align-items-end">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

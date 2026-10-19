<?php
$studentName = ($receipt['lname'] ?? '') . ', ' . ($receipt['fname'] ?? '');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Official Receipt</h1>
    <a href="/ssis/cashier/dashboard" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
        <i class="bi bi-printer"" aria-hidden="true"></i> Print
    </button>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <div class="text-center mb-4 pb-3 border-bottom">
            <h2 class="fw-bold text-primary mb-0">CuyoTech University</h2>
            <p class="text-muted mb-0">Official Receipt</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless" aria-label="Receipt details">
                    <tr>
                        <td class="fw-bold text-end">Receipt No:</td>
                        <td><?= htmlspecialchars($receipt['receipt_no']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Date:</td>
                        <td><?= date('F d, Y h:i A', strtotime($receipt['payment_date'])) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Student:</td>
                        <td><?= htmlspecialchars($studentName) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Student ID:</td>
                        <td><?= htmlspecialchars($receipt['ref_no']) ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-sm table-borderless" aria-label="Receipt details">
                    <tr>
                        <td class="fw-bold text-end">Program:</td>
                        <td><?= htmlspecialchars($receipt['program_code']) ?> - <?= htmlspecialchars($receipt['description']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Academic Year:</td>
                        <td><?= htmlspecialchars($receipt['ay_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Semester:</td>
                        <td><?= htmlspecialchars($receipt['semester_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Cashier:</td>
                        <td><?= htmlspecialchars($receipt['cashier_fname'] . ', ' . $receipt['cashier_lname']) ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <table class="table table-bordered" aria-label="Payment breakdown">
                <thead class="table-light">
                    <tr>
                        <th>Description</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Tuition & Fees (Units: <?= $receipt['total_units'] ?>)</td>
                        <td class="text-end">₱ <?= number_format($receipt['amount_paid'], 2) ?></td>
                    </tr>
                    <tr class="table-active">
                        <td class="fw-bold">Total Amount Paid</td>
                        <td class="text-end fw-bold">₱ <?= number_format($receipt['amount_paid'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="text-center border-top pt-3">
            <p class="text-muted mb-0">Thank you for your payment.</p>
            <p class="text-muted small">This is a computer-generated receipt. No signature required.</p>
        </div>
    </div>
</div>

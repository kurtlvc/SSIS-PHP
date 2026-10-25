<?php
$studentName = ($receipt['lname'] ?? '') . ', ' . ($receipt['fname'] ?? '');
$verificationUrl = rtrim(App::getInstance()->getConfig('app_url') ?? '', '/') . '/verify/receipt/' . urlencode($receipt['receipt_no']);
$qrData = json_encode([
    'receipt_no' => $receipt['receipt_no'],
    'student' => $receipt['ref_no'],
    'amount' => $receipt['amount_paid'],
    'date' => $receipt['payment_date'],
    'verify' => $verificationUrl
]);
$gatewayName = $receipt['gateway_name'] ?? 'cash';
$gatewayLabel = match ($gatewayName) {
    'gcash' => 'GCash',
    'paymaya' => 'PayMaya',
    'bank' => 'Bank Transfer',
    'simulated' => 'Online (Simulated)',
    default => 'Cash'
};
$pdfMode = $pdf_mode ?? false;
?>
<?php if (!$pdfMode): ?>
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <h1 class="h3 mb-0">Official Receipt</h1>
    <div class="d-flex gap-2">
        <a href="<?= ($module ?? '') === 'student' ? '/ssis/student/payment/assessments' : '/ssis/cashier/dashboard' ?>" class="btn btn-outline-secondary btn-sm">Back</a>
        <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer" aria-hidden="true"></i> Print
        </button>
        <a href="/ssis/cashier/receipts/<?= urlencode($receipt['receipt_no']) ?>/pdf" class="btn btn-outline-success btn-sm" target="_blank">
            <i class="bi bi-file-earmark-pdf" aria-hidden="true"></i> PDF
        </a>
    </div>
</div>
<?php endif; ?>

<div class="card shadow-sm receipt-card">
    <div class="card-body p-4">
        <div class="text-center mb-4 pb-3 border-bottom">
            <img src="/ssis/assets/cuyotech-logo-900x900.webp" alt="CuyoTech University Logo" class="mb-2" style="height: 70px; width: auto;">
            <h2 class="fw-bold text-primary mb-1">CuyoTech University</h2>
            <p class="text-muted mb-1">Official Receipt</p>
            <p class="text-muted small mb-0">CuyoTech University, Brgy. Poblacion, Cuyo, Palawan 5318</p>
            <p class="text-muted small mb-0">Tel: (048) 234-5678 | Email: registrar@cuyotech.edu.ph</p>
            <p class="text-muted small mb-0">TIN: 000-123-456-000 | BIR Permit No.: 1234567890</p>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless" aria-label="Receipt details">
                    <tr>
                        <td class="fw-bold text-end" style="width: 40%;">Receipt No:</td>
                        <td class="fw-bold"><?= htmlspecialchars($receipt['receipt_no']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Date & Time:</td>
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
                        <td class="fw-bold text-end" style="width: 40%;">Program:</td>
                        <td><?= htmlspecialchars($receipt['program_code']) ?> - <?= htmlspecialchars($receipt['program_desc'] ?? '') ?></td>
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
                        <td class="fw-bold text-end">Payment Mode:</td>
                        <td>
                            <span class="badge bg-info text-dark"><?= $gatewayLabel ?></span>
                            <?php if (!empty($receipt['gateway_reference'])): ?>
                                <br><small class="text-muted">Ref: <?= htmlspecialchars($receipt['gateway_reference']) ?></small>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <table class="table table-bordered table-sm" aria-label="Payment breakdown">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60%;">Description</th>
                        <th class="text-end" style="width: 20%;">Units</th>
                        <th class="text-end" style="width: 20%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $assessment = $receipt['assessment'] ?? null;
                    $hasBreakdown = $assessment && isset($assessment['tuition_fee'], $assessment['misc_fee'], $assessment['lab_fee']);
                    if ($hasBreakdown):
                        $tuition = (float) $assessment['tuition_fee'];
                        $misc = (float) $assessment['misc_fee'];
                        $lab = (float) $assessment['lab_fee'];
                        $other = (float) ($assessment['other_fees'] ?? 0);
                        $discount = (float) ($assessment['discount_amount'] ?? 0);
                        $net = (float) $assessment['net_amount'];
                    ?>
                    <tr>
                        <td>Tuition Fee (<?= $receipt['total_units'] ?> units × ₱<?= number_format($tuition / max(1, $receipt['total_units']), 2) ?>/unit)</td>
                        <td class="text-end"><?= $receipt['total_units'] ?></td>
                        <td class="text-end">₱ <?= number_format($tuition, 2) ?></td>
                    </tr>
                    <?php if ($misc > 0): ?>
                    <tr>
                        <td>Miscellaneous Fee</td>
                        <td class="text-end">-</td>
                        <td class="text-end">₱ <?= number_format($misc, 2) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($lab > 0): ?>
                    <tr>
                        <td>Laboratory Fee</td>
                        <td class="text-end">-</td>
                        <td class="text-end">₱ <?= number_format($lab, 2) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($other > 0): ?>
                    <tr>
                        <td>Other Fees</td>
                        <td class="text-end">-</td>
                        <td class="text-end">₱ <?= number_format($other, 2) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($discount > 0): ?>
                    <tr class="table-success">
                        <td>Discount</td>
                        <td class="text-end">-</td>
                        <td class="text-end text-success">- ₱ <?= number_format($discount, 2) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="table-active">
                        <td class="fw-bold">Net Assessment</td>
                        <td class="text-end">-</td>
                        <td class="text-end fw-bold">₱ <?= number_format($net, 2) ?></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td>Tuition & Fees (Units: <?= $receipt['total_units'] ?>)</td>
                        <td class="text-end"><?= $receipt['total_units'] ?></td>
                        <td class="text-end">₱ <?= number_format($receipt['amount_paid'], 2) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr class="table-primary">
                        <td class="fw-bold">Amount Paid This Transaction</td>
                        <td class="text-end">-</td>
                        <td class="text-end fw-bold fs-5">₱ <?= number_format($receipt['amount_paid'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <?php if (!empty($receipt['auth_code'])): ?>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td class="fw-bold text-end">Authorization Code:</td>
                        <td><code><?= htmlspecialchars($receipt['auth_code']) ?></code></td>
                    </tr>
                    <?php if (!empty($receipt['completed_at']) && $receipt['completed_at'] !== $receipt['payment_date']): ?>
                    <tr>
                        <td class="fw-bold text-end">Completed At:</td>
                        <td><?= date('F d, Y h:i A', strtotime($receipt['completed_at'])) ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
            <div class="col-md-6 text-md-end">
                <div class="qr-container d-inline-block p-2 border rounded bg-white">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=<?= urlencode($qrData) ?>" alt="QR Code for verification" class="qr-code" style="max-width: 120px;">
                    <div class="text-center small text-muted mt-1">Scan to verify</div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="border-top pt-3">
            <div class="row mb-3">
                <div class="col-md-6">
                    <p class="mb-1"><strong>Received by:</strong> <?= $receipt['cashier_fname'] ? htmlspecialchars($receipt['cashier_fname'] . ' ' . $receipt['cashier_lname']) : 'Online Payment System' ?></p>
                    <p class="mb-0 small text-muted">Cashier / Payment Gateway</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-1"><strong>Verified by:</strong> _________________________</p>
                    <p class="mb-0 small text-muted">Authorized Signatory</p>
                </div>
            </div>
            <hr>
            <p class="text-muted small mb-1 text-center">Thank you for your payment. This receipt serves as proof of payment for enrollment purposes.</p>
            <p class="text-muted small mb-0 text-center">This is a computer-generated receipt. No signature required for online payments.</p>
            <p class="text-muted small mb-0 text-center">
                Verify online: <a href="<?= $verificationUrl ?>" target="_blank"><?= $verificationUrl ?></a>
            </p>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print { display: none !important; }
    .receipt-card { box-shadow: none !important; border: 1px solid #000 !important; }
    .qr-code { display: block !important; }
}
.qr-code { max-width: 120px; }
@media screen {
    .qr-container { display: inline-block; }
}
</style>

<script>
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        window.print();
    }
});
</script>

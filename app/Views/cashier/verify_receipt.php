<?php
$studentName = ($receipt['lname'] ?? '') . ', ' . ($receipt['fname'] ?? '');
$gatewayName = $receipt['gateway_name'] ?? 'cash';
$gatewayLabel = match ($gatewayName) {
    'gcash' => 'GCash',
    'paymaya' => 'PayMaya',
    'bank' => 'Bank Transfer',
    'simulated' => 'Online (Simulated)',
    default => 'Cash'
};
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-body p-5 text-center">
                    <?php if ($verified): ?>
                        <div class="mb-4">
                            <div class="text-success" style="font-size: 4rem;">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <h2 class="fw-bold text-success mb-1">Receipt Verified</h2>
                            <p class="text-muted">This receipt is valid and confirmed.</p>
                        </div>
                    <?php else: ?>
                        <div class="mb-4">
                            <div class="text-danger" style="font-size: 4rem;">
                                <i class="bi bi-x-circle-fill"></i>
                            </div>
                            <h2 class="fw-bold text-danger mb-1">Invalid Receipt</h2>
                            <p class="text-muted">This receipt could not be verified.</p>
                        </div>
                    <?php endif; ?>

                    <?php if ($receipt): ?>
                        <div class="text-start mt-4 p-4 bg-light rounded">
                            <h5 class="fw-bold mb-3 border-bottom pb-2">Receipt Details</h5>
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-bold text-end" style="width: 40%;">Receipt No:</td>
                                    <td class="fw-bold text-success"><?= htmlspecialchars($receipt['receipt_no']) ?></td>
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
                                <tr>
                                    <td class="fw-bold text-end">Program:</td>
                                    <td><?= htmlspecialchars($receipt['program_code'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-end">Academic Year:</td>
                                    <td><?= htmlspecialchars($receipt['ay_name'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-end">Semester:</td>
                                    <td><?= htmlspecialchars($receipt['semester_name'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-end">Amount Paid:</td>
                                    <td class="fw-bold text-primary fs-5">₱ <?= number_format($receipt['amount_paid'], 2) ?></td>
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
                                <?php if (!empty($receipt['auth_code'])): ?>
                                <tr>
                                    <td class="fw-bold text-end">Authorization Code:</td>
                                    <td><code><?= htmlspecialchars($receipt['auth_code']) ?></code></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <div class="mt-4 text-muted small">
                            <p class="mb-1">CuyoTech University, Brgy. Poblacion, Cuyo, Palawan 5318</p>
                            <p class="mb-0">Tel: (048) 234-5678 | Email: registrar@cuyotech.edu.ph</p>
                        </div>
                    <?php else: ?>
                        <div class="text-muted">
                            <p>The receipt number <code><?= htmlspecialchars($receiptNo ?? '') ?></code> was not found in our records.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Cashier Dashboard</h1>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-primary h-100">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack display-5 text-primary mb-2"" aria-hidden="true"></i>
                <h5 class="card-title">Today's Collections</h5>
                <h2 class="fw-bold text-primary">₱ <?= number_format($summary ? array_sum(array_column($summary, 'total_amount')) : 0, 2) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-success h-100">
            <div class="card-body text-center">
                <i class="bi bi-receipt display-5 text-success mb-2"" aria-hidden="true"></i>
                <h5 class="card-title">Total Transactions</h5>
                <h2 class="fw-bold text-success"><?= $summary ? array_sum(array_column($summary, 'total_transactions')) : 0 ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-bottom display-5 text-warning mb-2"" aria-hidden="true"></i>
                <h5 class="card-title">Pending Payments</h5>
                <h2 class="fw-bold text-warning"><?= count($pendingPayments ?? []) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-info h-100">
            <div class="card-body text-center">
                <i class="bi bi-graph-up-arrow display-5 text-info mb-2"" aria-hidden="true"></i>
                <h5 class="card-title">Avg. per Tx</h5>
                <h2 class="fw-bold text-info">
                    ₱ <?= number_format($summary ? (array_sum(array_column($summary, 'total_amount')) / max(array_sum(array_column($summary, 'total_transactions')), 1)) : 0, 2) ?>
                </h2>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Pending Payment Queue</span>
        <a href="/ssis/cashier/payments/process" class="btn btn-sm btn-primary">New Payment</a>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($pendingPayments)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" aria-label="Pending payment queue">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Ref No</th>
                            <th>Program</th>
                            <th>Units</th>
                            <th>A/Y</th>
                            <th>Semester</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingPayments as $pp): ?>
                            <tr>
                                <td><?= htmlspecialchars($pp['lname'] . ', ' . $pp['fname']) ?></td>
                                <td><?= htmlspecialchars($pp['ref_no']) ?></td>
                                <td><?= htmlspecialchars($pp['program_code']) ?></td>
                                <td><?= $pp['total_units'] ?></td>
                                <td><?= htmlspecialchars($pp['ay_name']) ?></td>
                                <td><?= htmlspecialchars($pp['semester_name']) ?></td>
                                <td class="text-center">
                                    <a href="/ssis/cashier/payments/process?enrolment_id=<?= $pp['enrolment_id'] ?>" class="btn btn-sm btn-success">Collect</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-3 mb-0">No pending payments for the selected period.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <span class="fw-bold">Recent Transactions</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($summary)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" aria-label="Pending payment queue">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Transactions</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($summary as $day): ?>
                            <?php if (isset($day['total_amount']) && $day['total_amount'] > 0): ?>
                                <tr>
                                    <td><?= date('M d, Y', strtotime($day['payment_date'])) ?></td>
                                    <td><?= $day['total_transactions'] ?></td>
                                    <td>₱ <?= number_format($day['total_amount'], 2) ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-3 mb-0">No transactions recorded.</p>
        <?php endif; ?>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Daily Collection Report</h1>
    <a href="/ssis/cashier/dashboard" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back to Dashboard</a>
</div>

<form method="GET" class="row g-3 align-items-end mb-4">
    <div class="col-auto">
        <label for="date" class="form-label small">Date</label>
        <input type="date" name="date" id="date" class="form-control form-control-sm" value="<?= htmlspecialchars($date ?? '') ?>">
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
    </div>
</form>

<div class="row g-4 mb-4 text-center">
    <div class="col-6 col-lg-3 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4">
                <i class="bi bi-receipt display-6 text-primary mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= (int) ($count ?? 0) ?></h2>
                <p class="text-muted small mb-0">Transactions</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4">
                <i class="bi bi-cash-coin display-6 text-success mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0">₱<?= number_format((float) ($total ?? 0), 2) ?></h2>
                <p class="text-muted small mb-0">Total Amount</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><span class="fw-bold">Transactions for <?= htmlspecialchars(date('M d, Y', strtotime($date ?? ''))) ?></span></div>
    <div class="card-body p-0">
        <?php if (!empty($transactions)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0" aria-label="Daily transactions">
                    <thead class="table-light">
                        <tr>
                            <th>Receipt No</th>
                            <th>Student</th>
                            <th>Cashier</th>
                            <th class="text-end">Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td><?= htmlspecialchars($t['receipt_no'] ?? '') ?></td>
                                <td><?= htmlspecialchars(($t['lname'] ?? '') . ', ' . ($t['fname'] ?? '')) ?></td>
                                <td><?= htmlspecialchars(($t['cashier_lname'] ?? '') . ', ' . ($t['cashier_fname'] ?? '')) ?></td>
                                <td class="text-end">₱<?= number_format((float) ($t['amount_paid'] ?? 0), 2) ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($t['payment_date'] ?? ''))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No transactions for this date.</p>
        <?php endif; ?>
    </div>
</div>

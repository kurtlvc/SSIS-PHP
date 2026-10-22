<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
    <h1 class="h3 mb-0 fw-bold text-primary">Cashier Dashboard</h1>
    <span class="small fst-italic text-tertiary"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/cashier/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Cashier</li>
    </ol>
</nav>

<?php
$summary = $summary ?? [];
$pendingPayments = $pendingPayments ?? [];

$totalCollections = $summary ? array_sum(array_column($summary, 'total_amount')) : 0;
$totalTransactions = $summary ? array_sum(array_column($summary, 'total_transactions')) : 0;
$pendingCount = count($pendingPayments);
$avgPerTx = $totalTransactions > 0 ? $totalCollections / $totalTransactions : 0;
?>

<div class="row g-4 mb-4 text-center">
    <div class="col-6 col-sm-12 col-lg-6 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4">
                <i class="bi bi-receipt display-6 text-primary mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= number_format($totalTransactions) ?></h2>
                <p class="text-muted small mb-0">Transactions</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-12 col-lg-6 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4">
                <i class="bi bi-cash-coin display-6 text-success mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0">₱ <?= number_format($totalCollections, 2) ?></h2>
                <p class="text-muted small mb-0">Total Amount</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4 text-center">
    <div class="col-6 col-sm-12 col-lg-6 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4">
                <i class="bi bi-hourglass-bottom display-6 text-warning mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0"><?= number_format($pendingCount) ?></h2>
                <p class="text-muted small mb-0">Pending Payments</p>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-12 col-lg-6 mx-auto">
        <div class="card border-0 bg-white h-100 shadow-sm">
            <div class="card-body py-4">
                <i class="bi bi-graph-up-arrow display-6 text-info mb-2" aria-hidden="true"></i>
                <h2 class="fw-bold mb-0">₱ <?= number_format($avgPerTx, 2) ?></h2>
                <p class="text-muted small mb-0">Avg. per Transaction</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold d-flex justify-content-between align-items-center">
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-hourglass-split text-warning"></i>
                    <span>Pending Payment Queue</span>
                </span>
                <a href="/ssis/cashier/payments/process" class="btn btn-sm btn-primary-blue d-flex align-items-center gap-1">
                    <i class="bi bi-plus"></i>
                    <span>New Payment</span>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($pendingPayments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-dark-theme" aria-label="Pending payment queue">
                            <thead>
                                <tr>
                                    <th class="small">Student</th>
                                    <th class="small">Ref No</th>
                                    <th class="small">Program</th>
                                    <th class="small">Units</th>
                                    <th class="small">A/Y</th>
                                    <th class="small">Semester</th>
                                    <th class="small text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingPayments as $pp): ?>
                                    <tr>
                                        <td class="fw-medium"><?= htmlspecialchars($pp['lname'] . ', ' . $pp['fname']) ?></td>
                                        <td class="small text-tertiary font-monospace"><?= htmlspecialchars($pp['ref_no']) ?></td>
                                        <td class="small text-tertiary"><span class="badge bg-light text-dark"><?= htmlspecialchars($pp['program_code']) ?></span></td>
                                        <td class="small text-tertiary text-center"><?= $pp['total_units'] ?></td>
                                        <td class="small text-tertiary"><?= htmlspecialchars($pp['ay_name']) ?></td>
                                        <td class="small text-tertiary"><?= htmlspecialchars($pp['semester_name']) ?></td>
                                        <td class="text-center">
                                            <a href="/ssis/cashier/payments/process?enrolment_id=<?= $pp['enrolment_id'] ?>" class="btn btn-sm btn-success-green d-flex align-items-center justify-content-center gap-1" style="min-width: 90px;">
                                                <i class="bi bi-cash-coin"></i>
                                                <span>Collect</span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-check-circle-fill text-success display-4 mb-3"></i>
                        <h5 class="text-muted mb-2">No Pending Payments</h5>
                        <p class="text-disabled small mb-0">All students have cleared their payments for the selected period.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold">Recent Transactions</div>
            <div class="card-body p-4">
                <?php if (!empty($summary)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 table-dark-theme" aria-label="Recent transactions">
                            <thead>
                                <tr>
                                    <th class="small">Date</th>
                                    <th class="small">Transactions</th>
                                    <th class="small">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary as $day): ?>
                                    <?php if (isset($day['total_amount']) && $day['total_amount'] > 0): ?>
                                        <tr>
                                            <td class="small text-tertiary"><?= date('M d, Y', strtotime($day['payment_date'])) ?></td>
                                            <td class="small text-tertiary"><?= $day['total_transactions'] ?></td>
                                            <td>₱ <?= number_format($day['total_amount'], 2) ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="small mb-0 text-disabled">No transactions recorded.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="search-filter-bar mt-4">
    <label for="quickLinkSearch" class="form-label mb-2 fw-semibold small text-uppercase">Search or Filter Items</label>
    <div class="input-group">
        <span class="input-group-text" id="search-addon">
            <i class="bi bi-search" role="img" aria-hidden="true"></i>
        </span>
        <input type="search" id="quickLinkSearch" class="form-control" placeholder="Type to search..." aria-label="Search quick links" aria-describedby="search-addon">
    </div>
    <div id="noQuickMatch" class="small mt-1 d-none" role="status" aria-live="polite"></div>
</div>
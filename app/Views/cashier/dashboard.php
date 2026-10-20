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

<div class="dashboard-grid" role="group" aria-label="Cashier metrics">
    <article class="dash-card dash-card--users" tabindex="0">
        <div class="dash-card__icon dash-card__icon--success" aria-hidden="true">
            <i class="bi bi-cash-stack icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Today's Collections</h2>
            <p class="dash-card__subtitle">Total revenue</p>
            <div class="dash-card__value">₱ <?= number_format($totalCollections, 2) ?></div>
            <div class="dash-card__meta">As of <?= date('g:i A') ?></div>
        </div>
        <a href="/ssis/cashier/reports/daily" class="dash-card__action" aria-label="View daily report">View Report</a>
    </article>

    <article class="dash-card dash-card--documents" tabindex="0">
        <div class="dash-card__icon dash-card__icon--info" aria-hidden="true">
            <i class="bi bi-receipt icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Total Transactions</h2>
            <p class="dash-card__subtitle">Completed today</p>
            <div class="dash-card__value"><?= number_format($totalTransactions) ?></div>
            <div class="dash-card__meta">Successful payments</div>
        </div>
        <a href="/ssis/cashier/reports/daily" class="dash-card__action" aria-label="View transactions">Details</a>
    </article>

    <article class="dash-card dash-card--warnings" tabindex="0">
        <div class="dash-card__icon dash-card__icon--warning" aria-hidden="true">
            <i class="bi bi-hourglass-bottom icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Pending Payments</h2>
            <p class="dash-card__subtitle">Awaiting collection</p>
            <div class="dash-card__value"><?= number_format($pendingCount) ?></div>
            <div class="dash-card__meta">
                <?php if ($pendingCount > 0): ?>
                    <span class="dash-card__badge dash-card__badge--critical"><?= $pendingCount ?> Students</span>
                <?php else: ?>
                    <span class="text-success-custom">Queue clear</span>
                <?php endif; ?>
            </div>
        </div>
        <a href="/ssis/cashier/payments/process" class="dash-card__action" aria-label="Process payments">Process</a>
    </article>

    <article class="dash-card dash-card--status" tabindex="0">
        <div class="dash-card__icon dash-card__icon--cyan" aria-hidden="true">
            <i class="bi bi-graph-up-arrow icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Avg. per Transaction</h2>
            <p class="dash-card__subtitle">Mean payment value</p>
            <div class="dash-card__value">₱ <?= number_format($avgPerTx, 2) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__status-dot" aria-hidden="true"></span>
                Based on today's data
            </div>
        </div>
        <a href="/ssis/cashier/reports/daily" class="dash-card__action" aria-label="View analytics">Analytics</a>
    </article>
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
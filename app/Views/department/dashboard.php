<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
    <h1 class="h3 mb-0 fw-bold" style="color: var(--dash-text-primary);">Department Dashboard</h1>
    <span class="small fst-italic" style="color: var(--dash-text-tertiary);"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
        <li class="breadcrumb-item"><a href="/ssis/department/dashboard" style="color: var(--dash-text-tertiary); text-decoration: none;">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page" style="color: var(--dash-text-muted);">Department</li>
    </ol>
</nav>

<?php if (isset($counts)): ?>
    <?php
    $pending = (int) ($counts['pending'] ?? 0);
    $cleared = (int) ($counts['cleared'] ?? 0);
    $onHold = (int) ($counts['on_hold'] ?? 0);
    $departments = (int) ($counts['departments'] ?? 0);
    ?>
<div class="dashboard-grid" role="group" aria-label="Department metrics">
    <article class="dash-card dash-card--warnings" tabindex="0">
        <div class="dash-card__icon dash-card__icon--warning" aria-hidden="true">
            <i class="bi bi-hourglass-split" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Pending Clearance</h2>
            <p class="dash-card__subtitle">Awaiting action</p>
            <div class="dash-card__value"><?= number_format($pending) ?></div>
            <div class="dash-card__meta">
                <?php if ($pending > 0): ?>
                    <span class="dash-card__badge dash-card__badge--critical"><?= $pending ?> Students</span>
                <?php else: ?>
                    <span style="color: var(--dash-accent-green);">All current</span>
                <?php endif; ?>
            </div>
        </div>
        <a href="/ssis/department/clearance" class="dash-card__action" aria-label="View pending clearance">Review</a>
    </article>

    <article class="dash-card dash-card--users" tabindex="0">
        <div class="dash-card__icon dash-card__icon--success" aria-hidden="true">
            <i class="bi bi-clipboard-check" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Cleared Students</h2>
            <p class="dash-card__subtitle">Completed clearance</p>
            <div class="dash-card__value"><?= number_format($cleared) ?></div>
            <div class="dash-card__meta">This academic term</div>
        </div>
        <a href="/ssis/department/clearance" class="dash-card__action" aria-label="View cleared students">View List</a>
    </article>

    <article class="dash-card dash-card--documents" tabindex="0">
        <div class="dash-card__icon dash-card__icon--info" aria-hidden="true">
            <i class="bi bi-pause-circle" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">On Hold</h2>
            <p class="dash-card__subtitle">Requires attention</p>
            <div class="dash-card__value"><?= number_format($onHold) ?></div>
            <div class="dash-card__meta">
                <?php if ($onHold > 0): ?>
                    <span class="dash-card__badge dash-card__badge--critical"><?= $onHold ?> Items</span>
                <?php else: ?>
                    <span style="color: var(--dash-accent-green);">None pending</span>
                <?php endif; ?>
            </div>
        </div>
        <a href="/ssis/department/clearance" class="dash-card__action" aria-label="View on-hold items">Review</a>
    </article>

    <article class="dash-card dash-card--status" tabindex="0">
        <div class="dash-card__icon dash-card__icon--cyan" aria-hidden="true">
            <i class="bi bi-diagram-2" style="font-size: 1.5rem;"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Departments</h2>
            <p class="dash-card__subtitle">Managed units</p>
            <div class="dash-card__value"><?= number_format($departments) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__status-dot" aria-hidden="true"></span>
                Active departments
            </div>
        </div>
        <a href="/ssis/registrar/students" class="dash-card__action" aria-label="View student records">Students</a>
    </article>
</div>
<?php endif; ?>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold">Quick Links</div>
            <div class="card-body p-4">
                <div class="d-grid gap-2" id="quickLinkList">
                    <a href="/ssis/department/clearance" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-clipboard-data me-1"></i>Clearance Queue</a>
                    <a href="/ssis/registrar/students" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2" style="padding: 12px 16px;"><i class="bi bi-people me-1"></i>Student Records</a>
                </div>
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
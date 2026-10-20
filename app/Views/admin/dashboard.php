<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
    <h1 class="h3 mb-0 fw-bold text-primary">Admin Dashboard</h1>
    <span class="small fst-italic text-tertiary"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Admin</li>
    </ol>
</nav>

<?php if (isset($counts)): ?>
<div class="dashboard-grid" role="group" aria-label="System metrics">
    <?php
    $studentsCount = (int)($counts['students'] ?? 0);
    $personnelCount = (int)($counts['personnel'] ?? 0);
    $accountsCount = (int)($counts['accounts'] ?? 0);
    $totalUsers = $studentsCount + $personnelCount + $accountsCount;
    $pendingRequests = (int)($counts['pending_requests'] ?? 0);
    $approvedEnrolments = (int)($counts['approved_enrolments'] ?? 0);
    $todayCollection = number_format((float)($counts['today_collection'] ?? 0), 2);
    ?>
    
    <article class="dash-card dash-card--users" tabindex="0">
        <div class="dash-card__icon dash-card__icon--success" aria-hidden="true">
            <i class="bi bi-people-fill icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Total Users</h2>
            <p class="dash-card__subtitle">Active users in system</p>
            <div class="dash-card__value"><?= number_format($totalUsers) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__trend dash-card__trend--positive" aria-label="12 percent increase from last month">
                    <i class="bi bi-arrow-up-right me-1"></i>12% from last month
                </span>
            </div>
        </div>
        <a href="/ssis/admin/users" class="dash-card__action" aria-label="View all users">View All</a>
    </article>

    <article class="dash-card dash-card--documents" tabindex="0">
        <div class="dash-card__icon dash-card__icon--info" aria-hidden="true">
            <i class="bi bi-file-earmark-fill icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Total Documents</h2>
            <p class="dash-card__subtitle">Files uploaded</p>
            <div class="dash-card__value"><?= number_format($approvedEnrolments) ?></div>
            <div class="dash-card__meta">Last updated 2 hours ago</div>
        </div>
        <a href="/ssis/registrar/documents/queue" class="dash-card__action" aria-label="Manage documents">Manage</a>
    </article>

    <article class="dash-card dash-card--warnings" tabindex="0">
        <div class="dash-card__icon dash-card__icon--warning" aria-hidden="true">
            <i class="bi bi-exclamation-triangle-fill icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">Pending Items</h2>
            <p class="dash-card__subtitle">Items awaiting action</p>
            <div class="dash-card__value"><?= number_format($pendingRequests) ?></div>
            <div class="dash-card__meta">
                <span class="dash-card__badge dash-card__badge--critical">3 Critical</span>
            </div>
        </div>
        <a href="/ssis/admin/users" class="dash-card__action" aria-label="Review pending items">Review</a>
    </article>

    <article class="dash-card dash-card--status" tabindex="0">
        <div class="dash-card__icon dash-card__icon--cyan" aria-hidden="true">
            <i class="bi bi-info-circle-fill icon-1_5rem"></i>
        </div>
        <div class="dash-card__content">
            <h2 class="dash-card__title">System Status</h2>
            <p class="dash-card__subtitle">Current system health</p>
            <div class="dash-card__value dash-card__value--text">Operational</div>
            <div class="dash-card__meta">
                <span class="dash-card__status-dot" aria-hidden="true"></span>
                Uptime: 99.9% | Last check: 1 min ago
            </div>
        </div>
        <a href="/ssis/admin/audit-logs" class="dash-card__action" aria-label="View system details">Details</a>
    </article>
</div>
<?php endif; ?>

<div class="row g-4 mt-4">
    <div class="col-12">
        <div class="card border-0 h-100 quick-links-card">
            <div class="card-header fw-semibold">Quick Links</div>
            <div class="card-body p-4">
                <div class="d-grid gap-2" id="quickLinkList">
                    <a href="/ssis/admin/users" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-people me-1"></i>User Management</a>
                    <a href="/ssis/admin/users/create" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-plus me-1"></i>Create User</a>
                    <a href="/ssis/admin/system-settings" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-gear me-1"></i>System Settings</a>
                    <a href="/ssis/admin/audit-logs" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-clipboard-data me-1"></i>Audit Logs</a>
                    <a href="/ssis/registrar/enrollments" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-journal-check me-1"></i>Enrollment Management</a>
                    <a href="/ssis/cashier/dashboard" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-cash-coin me-1"></i>Cashier Dashboard</a>
                    <a href="/ssis/department/clearance" class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center gap-2 btn-padding-md"><i class="bi bi-clipboard-check me-1"></i>Clearance Queue</a>
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
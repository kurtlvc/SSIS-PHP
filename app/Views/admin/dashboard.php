<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 gap-2">
    <h1 class="h3 mb-0 fw-bold">Admin Dashboard</h1>
    <span class="text-muted small fst-italic"><?= htmlspecialchars(date('F j, Y')) ?></span>
</div>

<?php if (isset($counts)): ?>
    <div class="row g-3 mb-4" role="group" aria-label="System metrics">
        <?php
        $metrics = [
            ['value' => (int)($counts['students'] ?? 0),            'label' => 'Students',             'icon' => 'bi-people',        'type' => 'positive'],
            ['value' => (int)($counts['personnel'] ?? 0),            'label' => 'Personnel',            'icon' => 'bi-person-badge',  'type' => 'positive'],
            ['value' => (int)($counts['accounts'] ?? 0),             'label' => 'Accounts',             'icon' => 'bi-shield-lock',   'type' => 'positive'],
            ['value' => (int)($counts['pending_requests'] ?? 0),     'label' => 'Pending Requests',     'icon' => 'bi-clock',         'type' => 'pending'],
            ['value' => (int)($counts['approved_enrolments'] ?? 0),  'label' => 'Approved Enrolments',  'icon' => 'bi-journal-check', 'type' => 'positive'],
            ['value' => number_format((float)($counts['today_collection'] ?? 0), 2), 'label' => "Today's Collection", 'icon' => 'bi-cash-coin','prefix' => '₱', 'type' => 'positive'],
        ];
        foreach ($metrics as $m):
            $accent = $m['type'] === 'pending' ? 'text-warning' : 'text-success';
        ?>
        <div class="col-12 col-sm-6 col-xl-3 d-flex align-items-stretch">
            <div class="dash-card card h-100 w-100">
                <div class="card-body d-flex flex-column align-items-center text-center p-4">
                    <i class="bi <?= $m['icon'] ?> dash-card-icon <?= $accent ?> mb-2" role="img" aria-hidden="true"></i>
                    <div class="dash-card-value fw-bold mb-1">
                        <?= (isset($m['prefix']) ? $m['prefix'] : '') . htmlspecialchars((string) $m['value']) ?>
                    </div>
                    <div class="dash-card-label"><?= htmlspecialchars($m['label']) ?></div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-12">
        <div class="card border-0 bg-white shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Quick Links</div>
            <div class="card-body">
                <div class="d-grid gap-2" id="quickLinkList">
                    <a href="/ssis/admin/users" class="btn btn-outline-primary btn-sm"><i class="bi bi-people me-2"></i>User Management</a>
                    <a href="/ssis/admin/users/create" class="btn btn-outline-primary btn-sm"><i class="bi bi-plus me-2"></i>Create User</a>
                    <a href="/ssis/admin/system-settings" class="btn btn-outline-primary btn-sm"><i class="bi bi-gear me-2"></i>System Settings</a>
                    <a href="/ssis/admin/audit-logs" class="btn btn-outline-primary btn-sm"><i class="bi bi-clipboard-data me-2"></i>Audit Logs</a>
                    <a href="/ssis/registrar/enrollments" class="btn btn-outline-primary btn-sm"><i class="bi bi-journal-check me-2"></i>Enrollment Management</a>
                    <a href="/ssis/cashier/dashboard" class="btn btn-outline-primary btn-sm"><i class="bi bi-cash-coin me-2"></i>Cashier Dashboard</a>
                    <a href="/ssis/department/clearance" class="btn btn-outline-primary btn-sm"><i class="bi bi-clipboard-check me-2"></i>Clearance Queue</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="search-filter-bar mt-4">
    <label for="quickLinkSearch" class="form-label mb-2 fw-semibold small text-uppercase text-secondary">Search or Filter Items</label>
    <div class="input-group">
        <span class="input-group-text bg-white border-secondary" id="search-addon">
            <i class="bi bi-search text-muted" role="img" aria-hidden="true"></i>
        </span>
        <input type="search" id="quickLinkSearch" class="form-control border-secondary" placeholder="Type to search..." aria-label="Search quick links" aria-describedby="search-addon">
    </div>
    <div id="noQuickMatch" class="text-muted small mt-1 d-none" role="status" aria-live="polite"></div>
</div>

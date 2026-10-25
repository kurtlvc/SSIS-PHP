<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Audit Logs</h1>
        <p class="text-muted small mb-0">System events, records creation, status updates, and administrative activities.</p>
    </div>
    <a href="/ssis/admin/dashboard" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
</div>

<?php if (isset($success) && !empty(trim($success))): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <span class="fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Recent Activity History</span>
        <span class="badge bg-light text-dark border"><?= count($logs ?? []) ?> events recorded</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($logs)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" aria-label="Audit logs">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width: 140px;">Timestamp</th>
                            <th>User (Actor)</th>
                            <th>Action</th>
                            <th>Target / Module</th>
                            <th>Summary & Details</th>
                            <th class="text-muted">Origin</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <?php
                                $meta = !empty($log['meta']) ? (is_array($log['meta']) ? $log['meta'] : json_decode($log['meta'], true)) : [];
                                
                                // Action badge colors
                                $action = strtoupper($log['action'] ?? 'UNKNOWN');
                                $actionBadgeClass = match($action) {
                                    'CREATE' => 'bg-success',
                                    'UPDATE' => 'bg-primary',
                                    'DELETE' => 'bg-danger',
                                    'LOGIN'  => 'bg-info text-dark',
                                    'LOGOUT' => 'bg-secondary',
                                    default  => 'bg-secondary',
                                };

                                // Readable Target Entity
                                $entity = htmlspecialchars($log['entity'] ?? 'Record');
                                $entityId = htmlspecialchars($log['entity_id'] ?? '');

                                // Build friendly changes summary
                                $summary = '';
                                if (!empty($meta['new']) && is_array($meta['new'])) {
                                    $changes = [];
                                    foreach ($meta['new'] as $k => $v) {
                                        if (is_array($v)) $v = json_encode($v);
                                        if ($k === 'is_active') {
                                            $changes[] = 'Status changed to <strong>' . ($v ? 'Active' : 'Inactive') . '</strong>';
                                        } elseif ($k === 'password' || $k === 'password_hash') {
                                            $changes[] = 'Password updated';
                                        } else {
                                            $oldVal = isset($meta['old'][$k]) ? (is_array($meta['old'][$k]) ? json_encode($meta['old'][$k]) : $meta['old'][$k]) : null;
                                            if ($oldVal !== null) {
                                                $changes[] = htmlspecialchars($k) . ': <span class="text-decoration-line-through text-muted">' . htmlspecialchars((string)$oldVal) . '</span> &rarr; <span class="text-primary fw-medium">' . htmlspecialchars((string)$v) . '</span>';
                                            } else {
                                                $changes[] = htmlspecialchars($k) . ' = <span class="fw-medium">' . htmlspecialchars((string)$v) . '</span>';
                                            }
                                        }
                                    }
                                    $summary = implode(', ', $changes);
                                } elseif ($action === 'DELETE') {
                                    $summary = '<span class="text-danger">Record removed</span>';
                                } elseif ($action === 'CREATE') {
                                    $summary = '<span class="text-success">New record created</span>';
                                }
                            ?>
                            <tr>
                                <td>
                                    <div class="fw-semibold text-nowrap"><?= htmlspecialchars(date('M d, Y', strtotime($log['created_at'] ?? 'now'))) ?></div>
                                    <div class="small text-muted text-nowrap"><i class="bi bi-clock me-1"></i><?= htmlspecialchars(date('h:i:s A', strtotime($log['created_at'] ?? 'now'))) ?></div>
                                </td>
                                <td>
                                    <?php if (!empty($log['actor_login'])): ?>
                                        <div class="fw-bold"><?= htmlspecialchars($log['actor_login']) ?></div>
                                        <div class="small text-muted">Role: <?= htmlspecialchars(ucfirst($log['actor_role'] ?? 'user')) ?> (ID #<?= htmlspecialchars($log['actor_id'] ?? '') ?>)</div>
                                    <?php elseif (!empty($log['actor_id'])): ?>
                                        <div class="fw-bold">User #<?= htmlspecialchars($log['actor_id']) ?></div>
                                    <?php else: ?>
                                        <span class="text-muted fst-italic">System</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge <?= $actionBadgeClass ?>"><?= $action ?></span>
                                </td>
                                <td>
                                    <div class="fw-medium text-capitalize"><?= str_replace('_', ' ', $entity) ?></div>
                                    <?php if ($entityId !== ''): ?>
                                        <span class="badge bg-light text-dark border">#<?= $entityId ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($summary !== ''): ?>
                                        <div class="small"><?= $summary ?></div>
                                    <?php else: ?>
                                        <span class="text-muted small">&mdash;</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="small text-muted text-nowrap">
                                        <i class="bi bi-globe me-1"></i><?= htmlspecialchars($meta['ip'] ?? 'Local') ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No audit log entries recorded yet.</p>
        <?php endif; ?>
    </div>
</div>

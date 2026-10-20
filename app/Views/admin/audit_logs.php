<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Audit Logs</h1>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible"><i class="bi bi-check-circle me-1"></i><?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white"><span class="fw-bold">Activity Log</span></div>
    <div class="card-body p-0">
        <?php if (!empty($logs)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-sm" aria-label="Audit logs">
                    <thead class="table-light">
                        <tr>
                            <th>Time</th>
                            <th>Actor ID</th>
                            <th>Action</th>
                            <th>Entity</th>
                            <th>Entity ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($log['created_at'] ?? ''))) ?></td>
                                <td><?= htmlspecialchars($log['actor_id'] ?? '') ?></td>
                                <td><?= htmlspecialchars($log['action'] ?? '') ?></td>
                                <td><?= htmlspecialchars($log['entity'] ?? '') ?></td>
                                <td><?= htmlspecialchars($log['entity_id'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No audit log entries.</p>
        <?php endif; ?>
    </div>
</div>

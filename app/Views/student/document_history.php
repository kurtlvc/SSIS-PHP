<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Document Request History</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header">
        <span class="fw-bold">My Requests</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($requests)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" aria-label="Document request history">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Processed By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars(date('M d, Y', strtotime($r['request_date'] ?? ''))) ?></td>
                                <td><?= htmlspecialchars($r['document_type'] ?? '') ?></td>
                                <td>
                                    <?php $status = $r['status'] ?? ''; ?>
                                    <span class="badge bg-<?= match($status) {
                                        'Released' => 'success',
                                        'Ready for Pickup' => 'info',
                                        'Processing' => 'warning',
                                        'Pending' => 'secondary',
                                        default => 'secondary',
                                    } ?>">
                                        <?= htmlspecialchars($status ?: 'Pending') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(($r['processed_lname'] ?? '') . ', ' . ($r['processed_fname'] ?? '')) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">You have no document requests.</p>
        <?php endif; ?>
    </div>
</div>

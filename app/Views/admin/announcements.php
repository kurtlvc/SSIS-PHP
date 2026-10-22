<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Announcements</h1>
    <a href="/ssis/admin/announcements/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Announcement
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Announcements</li>
    </ol>
</nav>

<?php if (isset($success) && !empty(trim($success))): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($error) && !empty(trim($error))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header fw-semibold">All Announcements</div>
    <div class="card-body p-0">
        <?php if (!empty($announcements)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="All announcements">
                    <thead>
                        <tr>
                            <th class="small">Title</th>
                            <th class="small">Priority</th>
                            <th class="small">Target Roles</th>
                            <th class="small">Status</th>
                            <th class="small">Order</th>
                            <th class="small">Created</th>
                            <th class="small text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($announcements as $ann): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($ann['ann_title']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars(mb_substr($ann['ann_body'], 0, 100)) ?>...</small>
                                </td>
                                <td class="small">
                                    <span class="badge bg-<?= match($ann['priority']) {
                                        'urgent' => 'danger',
                                        'high' => 'warning',
                                        'normal' => 'info',
                                        'low' => 'secondary',
                                        default => 'secondary',
                                    } ?>">
                                        <?= ucfirst($ann['priority'] ?? 'normal') ?>
                                    </span>
                                </td>
                                <td class="small text-tertiary">
                                    <?php if (!empty($ann['target_roles'])): ?>
                                        <?php foreach (json_decode($ann['target_roles'], true) as $role): ?>
                                            <span class="badge bg-secondary me-1"><?= htmlspecialchars($role) ?></span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted">All</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small text-center">
                                    <span class="badge bg-<?= ($ann['is_active'] && $ann['ann_status']) ? 'success' : 'secondary' ?>">
                                        <?= ($ann['is_active'] && $ann['ann_status']) ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="small text-tertiary text-center"><?= $ann['ann_index'] ?? 0 ?></td>
                                <td class="small text-tertiary"><?= $ann['created_at'] ? date('M d, Y', strtotime($ann['created_at'])) : '—' ?></td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="/ssis/admin/announcements/<?= $ann['ann_id'] ?>/edit" class="btn btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="/ssis/admin/announcements/<?= $ann['ann_id'] ?>" class="btn btn-outline-primary" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form method="POST" action="/ssis/admin/announcements/<?= $ann['ann_id'] ?>/delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No announcements found.</p>
        <?php endif; ?>
    </div>
</div>
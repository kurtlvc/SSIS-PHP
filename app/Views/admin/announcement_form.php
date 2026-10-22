<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $announcement ? 'Edit' : 'Create' ?> Announcement</h1>
    <a href="/ssis/admin/announcements" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/admin/announcements">Announcements</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $announcement ? 'Edit' : 'Create' ?></li>
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
    <div class="card-header fw-semibold">Announcement Details</div>
    <div class="card-body">
        <form method="POST" action="<?= $announcement ? '/ssis/admin/announcements/' . $announcement['ann_id'] . '/edit' : '/ssis/admin/announcements/create' ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-8">
                    <label for="ann_title" class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="ann_title" id="ann_title" class="form-control" required
                           value="<?= htmlspecialchars($announcement['ann_title'] ?? '') ?>" placeholder="Enter announcement title">
                </div>
                <div class="col-md-4">
                    <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                    <select name="priority" id="priority" class="form-select" required>
                        <option value="low" <?= ($announcement['priority'] ?? '') === 'low' ? 'selected' : '' ?>>Low</option>
                        <option value="normal" <?= ($announcement['priority'] ?? '') === 'normal' ? 'selected' : '' ?>>Normal</option>
                        <option value="high" <?= ($announcement['priority'] ?? '') === 'high' ? 'selected' : '' ?>>High</option>
                        <option value="urgent" <?= ($announcement['priority'] ?? '') === 'urgent' ? 'selected' : '' ?>>Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="ann_body" class="form-label">Content <span class="text-danger">*</span></label>
                <textarea name="ann_body" id="ann_body" class="form-control" rows="6" required placeholder="Enter announcement content..."><?= htmlspecialchars($announcement['ann_body'] ?? '') ?></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="target_roles" class="form-label">Target Roles</label>
                    <select name="target_roles[]" id="target_roles" class="form-select" multiple>
                        <option value="student" <?= in_array('student', $targetRoles ?? []) ? 'selected' : '' ?>>Student</option>
                        <option value="faculty" <?= in_array('faculty', $targetRoles ?? []) ? 'selected' : '' ?>>Faculty</option>
                        <option value="chair" <?= in_array('chair', $targetRoles ?? []) ? 'selected' : '' ?>>Chair</option>
                        <option value="dean" <?= in_array('dean', $targetRoles ?? []) ? 'selected' : '' ?>>Dean</option>
                        <option value="admin" <?= in_array('admin', $targetRoles ?? []) ? 'selected' : '' ?>>Admin</option>
                        <option value="cashier" <?= in_array('cashier', $targetRoles ?? []) ? 'selected' : '' ?>>Cashier</option>
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple. Leave empty for all roles.</div>
                </div>
                <div class="col-md-3">
                    <label for="ann_status" class="form-label">Status</label>
                    <select name="ann_status" id="ann_status" class="form-select">
                        <option value="1" <?= ($announcement['ann_status'] ?? 1) ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= !($announcement['ann_status'] ?? 1) ? 'selected' : '' ?>>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="ann_index" class="form-label">Display Order</label>
                    <input type="number" name="ann_index" id="ann_index" class="form-control" min="0"
                           value="<?= htmlspecialchars($announcement['ann_index'] ?? 0) ?>" placeholder="0">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($announcement['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save Announcement
                </button>
                <a href="/ssis/admin/announcements" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
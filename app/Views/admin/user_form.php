<?php
$isEdit = !empty($user);
$action = $isEdit ? '/ssis/admin/users/' . $user['id'] . '/edit' : '/ssis/admin/users/create';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $isEdit ? 'Edit User' : 'Create New User' ?></h1>
    <a href="/ssis/admin/users" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
<?php endif; ?>

<form method="POST" action="<?= $action ?>" autocomplete="new-password">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><span class="fw-bold">Account Details</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Login / Username *</label>
                    <input type="text" name="login" class="form-control" value="<?= htmlspecialchars($user['login'] ?? '') ?>" required autofocus>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="8" autocomplete="new-password"
                           placeholder="<?= $isEdit ? 'Leave blank to keep current' : 'Minimum 8 characters' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Role *</label>
                    <select name="role" class="form-select" required>
                        <?php foreach (['student','faculty','chair','dean','admin','cashier'] as $r): ?>
                            <option value="<?= $r ?>" <?= (!empty($user) && ($user['role'] ?? '') === $r) ? 'selected' : '' ?>><?= ucfirst($r) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                               <?= empty($user) || ($user['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner Type *</label>
                    <select name="owner_type" class="form-select" required>
                        <option value="personnel" <?= (!empty($user) && ($user['owner_type'] ?? 'personnel') === 'personnel') ? 'selected' : '' ?>>Personnel</option>
                        <option value="student" <?= (!empty($user) && ($user['owner_type'] ?? '') === 'student') ? 'selected' : '' ?>>Student</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Owner ID *</label>
                    <input type="number" name="owner_id" class="form-control" value="<?= htmlspecialchars($user['owner_id'] ?? '') ?>" required>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= $isEdit ? 'Update User' : 'Create User' ?></button></div>
    </div>
</form>

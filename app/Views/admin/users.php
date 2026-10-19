<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">User Management</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-plus"></i> Create User
    </button>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white">
        <span class="fw-bold">All Accounts</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($accounts)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Login</th>
                            <th>Role</th>
                            <th>Owner Type</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accounts as $acc): ?>
                            <?php
                                $name = '';
                                if ($acc['owner_type'] === 'student') {
                                    $name = ($acc['student_fname'] ?? '') . ' ' . ($acc['student_lname'] ?? '');
                                } else {
                                    $name = ($acc['personnel_fname'] ?? '') . ' ' . ($acc['personnel_lname'] ?? '');
                                }
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($acc['login']) ?></td>
                                <td>
                                    <span class="badge bg-<?= match($acc['role']) {
                                        'admin' => 'danger',
                                        'dean' => 'warning',
                                        'chair' => 'info',
                                        'faculty' => 'primary',
                                        'student' => 'success',
                                        default => 'secondary',
                                    } ?>">
                                        <?= ucfirst($acc['role']) ?>
                                    </span>
                                </td>
                                <td><?= ucfirst($acc['owner_type']) ?></td>
                                <td><?= htmlspecialchars($name) ?></td>
                                <td><?= htmlspecialchars($acc['email'] ?? '') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $acc['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $acc['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="/sis/admin/users/update/<?= $acc['id'] ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <select name="role" class="form-select form-select-sm d-inline-block w-auto">
                                            <option value="student" <?= $acc['role'] == 'student' ? 'selected' : '' ?>>Student</option>
                                            <option value="faculty" <?= $acc['role'] == 'faculty' ? 'selected' : '' ?>>Faculty</option>
                                            <option value="chair" <?= $acc['role'] == 'chair' ? 'selected' : '' ?>>Chair</option>
                                            <option value="dean" <?= $acc['role'] == 'dean' ? 'selected' : '' ?>>Dean</option>
                                            <option value="admin" <?= $acc['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                        <select name="is_active" class="form-select form-select-sm d-inline-block w-auto">
                                            <option value="1" <?= $acc['is_active'] ? 'selected' : '' ?>>Active</option>
                                            <option value="0" <?= !$acc['is_active'] ? 'selected' : '' ?>>Inactive</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No accounts found.</p>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="createUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/sis/admin/users/create">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Create New User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Login / Username</label>
                        <input type="text" name="login" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="chair">Chair</option>
                            <option value="dean">Dean</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner Type</label>
                        <select name="owner_type" class="form-select" required>
                            <option value="personnel">Personnel</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Owner ID</label>
                        <input type="number" name="owner_id" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

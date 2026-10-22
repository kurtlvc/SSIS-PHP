<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">User Management</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
        <i class="bi bi-plus" aria-hidden="true"></i> Create User
    </button>
</div>

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

<div class="card quick-links-card">
    <div class="card-header fw-semibold">All Accounts</div>
    <div class="card-body p-0">
        <?php if (!empty($accounts)): ?>
            <div class="table-responsive table-user-management">
                <table class="table table-hover mb-0" aria-label="All accounts">
                    <thead>
                        <tr>
                            <th>Login</th>
                            <th>Role</th>
                            <th>Owner Type</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-100px">Actions</th>
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
                                $roleClass = match($acc['role']) {
                                    'admin' => 'bg-danger',
                                    'dean' => 'bg-warning',
                                    'chair' => 'bg-info',
                                    'faculty' => 'bg-primary',
                                    'student' => 'bg-success',
                                    default => 'bg-secondary',
                                };
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($acc['login']) ?></td>
                                <td>
                                    <span class="badge <?= $roleClass ?>">
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
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="User actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="/ssis/admin/users/<?= $acc['id'] ?>">
                                                    <i class="bi bi-eye me-2"></i>View
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="/ssis/admin/users/<?= $acc['id'] ?>/edit">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="/ssis/admin/users/<?= $acc['id'] ?>/toggle-status" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                    <input type="hidden" name="is_active" value="<?= $acc['is_active'] ? '0' : '1' ?>">
                                                    <button type="submit" class="dropdown-item text-<?= $acc['is_active'] ? 'danger' : 'success' ?>">
                                                        <i class="bi bi-<?= $acc['is_active'] ? 'x-circle' : 'check-circle' ?> me-2"></i>
                                                        <?= $acc['is_active'] ? 'Deactivate' : 'Activate' ?>
                                                    </button>
                                                </form>
                                            </li>
                                            <li>
                                                <form method="POST" action="/ssis/admin/users/<?= $acc['id'] ?>/delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Delete
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
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

<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/ssis/admin/users/create">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="login" class="form-label">Login / Username</label>
                        <input type="text" name="login" id="login" class="form-control" required autocomplete="username">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required minlength="8" autocomplete="new-password">
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select" required>
                            <option value="student">Student</option>
                            <option value="faculty">Faculty</option>
                            <option value="chair">Chair</option>
                            <option value="dean">Dean</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="owner_type" class="form-label">Owner Type</label>
                        <select name="owner_type" id="owner_type" class="form-select" required>
                            <option value="personnel">Personnel</option>
                            <option value="student">Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="owner_id" class="form-label">Owner ID</label>
                        <input type="number" name="owner_id" id="owner_id" class="form-control" required min="1">
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
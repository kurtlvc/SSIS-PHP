<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">User Account</h1>
    <a href="/ssis/admin/users" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php
$ownerType = $user['owner_type'] ?? '';
$ownerId = $user['owner_id'] ?? null;
$ownerName = '';
if ($ownerType === 'student') {
    $ownerName = ($user['student_fname'] ?? '') . ' ' . ($user['student_lname'] ?? '');
} elseif ($ownerType === 'personnel') {
    $ownerName = ($user['personnel_fname'] ?? '') . ' ' . ($user['personnel_lname'] ?? '');
}
?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white"><span class="fw-bold">Account Details</span></div>
    <div class="card-body">
        <table class="table table-sm table-borderless mb-0">
            <tbody>
                <tr><td class="fw-bold w-25">Login</td><td><?= htmlspecialchars($user['login'] ?? '') ?></td></tr>
                <tr><td class="fw-bold">Email</td><td><?= htmlspecialchars($user['email'] ?? '') ?></td></tr>
                <tr><td class="fw-bold">Role</td><td><span class="badge bg-secondary"><?= ucfirst($user['role'] ?? '') ?></span></td></tr>
                <tr><td class="fw-bold">Owner Type</td><td><?= ucfirst($ownerType) ?></td></tr>
                <tr><td class="fw-bold">Owner ID</td><td><?= htmlspecialchars($ownerId ?? '') ?></td></tr>
                <tr><td class="fw-bold">Owner Name</td><td><?= htmlspecialchars($ownerName) ?></td></tr>
                <tr><td class="fw-bold">Status</td><td><?= ($user['is_active'] ?? 1) ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td></tr>
                <tr><td class="fw-bold">Created</td><td><?= htmlspecialchars($user['created_at'] ?? '') ?></td></tr>
            </tbody>
        </table>
    </div>
</div>

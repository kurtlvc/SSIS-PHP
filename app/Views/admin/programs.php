<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Program Management</h1>
    <a href="/ssis/admin/programs/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Program
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Programs</li>
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
    <div class="card-header fw-semibold">All Programs</div>
    <div class="card-body p-0">
        <?php if (!empty($programs)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="All programs">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Campus</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($programs as $program): ?>
                            <tr>
                                <td class="fw-medium font-monospace"><?= htmlspecialchars($program['program_code']) ?></td>
                                <td><?= htmlspecialchars($program['description']) ?></td>
                                <td class="small text-tertiary"><?= htmlspecialchars($program['campus_name'] ?? '') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $program['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $program['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Program actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="/ssis/admin/programs/<?= $program['program_id'] ?>/edit">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="/ssis/admin/programs/<?= $program['program_id'] ?>/delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this program?');">
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
            <p class="text-muted text-center py-4 mb-0">No programs found.</p>
        <?php endif; ?>
    </div>
</div>
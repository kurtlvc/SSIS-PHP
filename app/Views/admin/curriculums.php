<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Curriculum Management</h1>
    <a href="/ssis/admin/curriculums/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Curriculum
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Curriculums</li>
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
    <div class="card-header fw-semibold">All Curriculums</div>
    <div class="card-body p-0">
        <?php if (!empty($curriculums)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="All curriculums">
                    <thead>
                        <tr>
                            <th>Program</th>
                            <th>Effective Year</th>
                            <th>Description</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($curriculums as $curriculum): ?>
                            <tr>
                                <td class="fw-medium"><?= htmlspecialchars($curriculum['program_code']) ?></td>
                                <td class="small text-tertiary font-monospace"><?= htmlspecialchars($curriculum['effective_year']) ?></td>
                                <td><?= htmlspecialchars($curriculum['description']) ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $curriculum['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $curriculum['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Curriculum actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="/ssis/admin/curriculums/<?= $curriculum['curr_id'] ?>/edit">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="/ssis/admin/curriculums/<?= $curriculum['curr_id'] ?>/delete" class="d-inline" onsubmit="return confirm('Are you sure you want to deactivate this curriculum?');">
                                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="bi bi-trash me-2"></i>Deactivate
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
            <p class="text-muted text-center py-4 mb-0">No curriculums found.</p>
        <?php endif; ?>
    </div>
</div>
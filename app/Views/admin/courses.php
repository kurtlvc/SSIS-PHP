<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Course Management</h1>
    <a href="/ssis/admin/courses/create" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Course
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Courses</li>
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

<div class="card mb-4">
    <div class="card-header">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="curr_id" class="form-label small">Filter by Curriculum</label>
                <select name="curr_id" id="curr_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Curriculums</option>
                    <?php foreach ($curriculums as $curriculum): ?>
                        <option value="<?= $curriculum['curr_id'] ?>" <?= (isset($activeCurriculum) && $activeCurriculum == $curriculum['curr_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($curriculum['program_code'] . ' - ' . $curriculum['description'] . ' (' . $curriculum['effective_year'] . ')') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label small">Search</label>
                <input type="search" name="search" id="search" class="form-control form-control-sm" placeholder="Code or Description" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($courses)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="All courses">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Description</th>
                            <th>Curriculum</th>
                            <th class="text-center">Units</th>
                            <th class="text-center">Lec</th>
                            <th class="text-center">Lab</th>
                            <th class="text-center">Prerequisite</th>
                            <th class="text-center">Status</th>
                            <th class="text-center w-100px">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td class="fw-medium font-monospace"><?= htmlspecialchars($course['code']) ?></td>
                                <td><?= htmlspecialchars($course['description']) ?></td>
                                <td class="small text-tertiary"><?= htmlspecialchars($course['program_code'] . ' - ' . $course['effective_year']) ?></td>
                                <td class="small text-center fw-bold"><?= $course['units'] ?></td>
                                <td class="small text-center"><?= $course['lec_units'] ?></td>
                                <td class="small text-center"><?= $course['lab_units'] ?></td>
                                <td class="small text-tertiary text-center"><?= htmlspecialchars($course['prerequisite'] ?? '-') ?></td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $course['is_active'] ? 'success' : 'secondary' ?>">
                                        <?= $course['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Course actions">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item" href="/ssis/admin/courses/<?= $course['course_id'] ?>/edit">
                                                    <i class="bi bi-pencil me-2"></i>Edit
                                                </a>
                                            </li>
                                            <li>
                                                <form method="POST" action="/ssis/admin/courses/<?= $course['course_id'] ?>/delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this course?');">
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
            <p class="text-muted text-center py-4 mb-0">No courses found.</p>
        <?php endif; ?>
    </div>
</div>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $curriculum ? 'Edit' : 'Create' ?> Curriculum</h1>
    <a href="/ssis/admin/curriculums" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/admin/curriculums">Curriculums</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $curriculum ? 'Edit' : 'Create' ?></li>
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
    <div class="card-header fw-semibold">Curriculum Information</div>
    <div class="card-body">
        <form method="POST" action="<?= $curriculum ? '/ssis/admin/curriculums/' . $curriculum['curr_id'] . '/edit' : '/ssis/admin/curriculums/create' ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="program_id" class="form-label">Program <span class="text-danger">*</span></label>
                    <select name="program_id" id="program_id" class="form-select" required>
                        <option value="">Select Program</option>
                        <?php foreach ($programs as $program): ?>
                            <option value="<?= $program['program_id'] ?>" <?= ($curriculum['program_id'] ?? '') == $program['program_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($program['program_code'] . ' - ' . $program['description']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="effective_year" class="form-label">Effective Year <span class="text-danger">*</span></label>
                    <input type="number" name="effective_year" id="effective_year" class="form-control" required min="2000" max="2100"
                           value="<?= htmlspecialchars($curriculum['effective_year'] ?? date('Y')) ?>" placeholder="e.g., 2026">
                </div>
                <div class="col-md-5">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" id="description" class="form-control" required
                           value="<?= htmlspecialchars($curriculum['description'] ?? '') ?>" placeholder="e.g., BS Computer Science Curriculum 2026">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($curriculum['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save Curriculum
                </button>
                <a href="/ssis/admin/curriculums" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
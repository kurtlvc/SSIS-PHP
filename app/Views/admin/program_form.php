<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $program ? 'Edit' : 'Create' ?> Program</h1>
    <a href="/ssis/admin/programs" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/admin/programs">Programs</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $program ? 'Edit' : 'Create' ?></li>
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
    <div class="card-header fw-semibold">Program Information</div>
    <div class="card-body">
        <form method="POST" action="<?= $program ? '/ssis/admin/programs/' . $program['program_id'] . '/edit' : '/ssis/admin/programs/create' ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label for="program_code" class="form-label">Program Code <span class="text-danger">*</span></label>
                    <input type="text" name="program_code" id="program_code" class="form-control" required
                           value="<?= htmlspecialchars($program['program_code'] ?? '') ?>" placeholder="e.g., BSCS">
                </div>
                <div class="col-md-6">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" id="description" class="form-control" required
                           value="<?= htmlspecialchars($program['description'] ?? '') ?>" placeholder="e.g., Bachelor of Science in Computer Science">
                </div>
                <div class="col-md-2">
                    <label for="campus_id" class="form-label">Campus <span class="text-danger">*</span></label>
                    <select name="campus_id" id="campus_id" class="form-select" required>
                        <option value="">Select Campus</option>
                        <?php foreach ($campuses as $campus): ?>
                            <option value="<?= $campus['campus_id'] ?>" <?= ($program['campus_id'] ?? '') == $campus['campus_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($campus['campus_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($program['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save Program
                </button>
                <a href="/ssis/admin/programs" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
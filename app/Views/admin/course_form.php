<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $course ? 'Edit' : 'Create' ?> Course</h1>
    <a href="/ssis/admin/courses" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to List
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/admin/courses">Courses</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $course ? 'Edit' : 'Create' ?></li>
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
    <div class="card-header fw-semibold">Course Information</div>
    <div class="card-body">
        <form method="POST" action="<?= $course ? '/ssis/admin/courses/' . $course['course_id'] . '/edit' : '/ssis/admin/courses/create' ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <label for="curr_id" class="form-label">Curriculum <span class="text-danger">*</span></label>
                    <select name="curr_id" id="curr_id" class="form-select" required>
                        <option value="">Select Curriculum</option>
                        <?php foreach ($curriculums as $curriculum): ?>
                            <option value="<?= $curriculum['curr_id'] ?>" <?= ($course['curr_id'] ?? '') == $curriculum['curr_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($curriculum['program_code'] . ' - ' . $curriculum['description'] . ' (' . $curriculum['effective_year'] . ')') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="code" class="form-label">Course Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" id="code" class="form-control" required
                           value="<?= htmlspecialchars($course['code'] ?? '') ?>" placeholder="e.g., CS101">
                </div>
                <div class="col-md-6">
                    <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                    <input type="text" name="description" id="description" class="form-control" required
                           value="<?= htmlspecialchars($course['description'] ?? '') ?>" placeholder="e.g., Introduction to Computer Science">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-2">
                    <label for="units" class="form-label">Units <span class="text-danger">*</span></label>
                    <input type="number" name="units" id="units" class="form-control" required min="1" max="6"
                           value="<?= htmlspecialchars($course['units'] ?? '') ?>" placeholder="3">
                </div>
                <div class="col-md-2">
                    <label for="lec_units" class="form-label">Lec Units <span class="text-danger">*</span></label>
                    <input type="number" name="lec_units" id="lec_units" class="form-control" required min="0" max="6"
                           value="<?= htmlspecialchars($course['lec_units'] ?? '') ?>" placeholder="2">
                </div>
                <div class="col-md-2">
                    <label for="lab_units" class="form-label">Lab Units <span class="text-danger">*</span></label>
                    <input type="number" name="lab_units" id="lab_units" class="form-control" required min="0" max="6"
                           value="<?= htmlspecialchars($course['lab_units'] ?? '') ?>" placeholder="1">
                </div>
                <div class="col-md-6">
                    <label for="prerequisite" class="form-label">Prerequisite</label>
                    <input type="text" name="prerequisite" id="prerequisite" class="form-control"
                           value="<?= htmlspecialchars($course['prerequisite'] ?? '') ?>" placeholder="e.g., CS100">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <div class="form-check form-switch mt-4">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" <?= ($course['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <label class="form-check-label fw-medium" for="is_active">Active</label>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save me-1"></i>Save Course
                </button>
                <a href="/ssis/admin/courses" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Cancel
                </a>
            </div>
        </form>
    </div>
</div>
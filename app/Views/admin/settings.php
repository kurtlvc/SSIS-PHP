<?php
$activeAY = null;
$activeSem = null;
if ($settings) {
    foreach ($academicYears as $ay) {
        if ($ay['ay_id'] == $settings['ay_id']) {
            $activeAY = $ay['ay_name'];
            break;
        }
    }
    foreach ($semesters as $sem) {
        if ($sem['sem_id'] == $settings['sem_id']) {
            $activeSem = $sem['semester_name'];
            break;
        }
    }
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">System Settings</h1>
</div>

<?php if (isset($success)): ?>
    <div class="alert alert-success alert-dismissible">
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible">
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <span class="fw-bold">General Settings</span>
            </div>
            <div class="card-body">
                <form method="POST" action="/ssis/admin/settings/update">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="mb-3">
                        <label for="system_title" class="form-label">System Title</label>
                        <input type="text" name="system_title" id="system_title" class="form-control"
                               value="<?= htmlspecialchars($settings['system_title'] ?? 'CuyoSSIS') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="ay_id" class="form-label">Active Academic Year</label>
                        <select name="ay_id" id="ay_id" class="form-select">
                            <?php foreach ($academicYears as $ay): ?>
                                <option value="<?= $ay['ay_id'] ?>" <?= ($settings['ay_id'] ?? '') == $ay['ay_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ay['ay_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="sem_id" class="form-label">Active Semester</label>
                        <select name="sem_id" id="sem_id" class="form-select">
                            <?php foreach ($semesters as $sem): ?>
                                <option value="<?= $sem['sem_id'] ?>" <?= ($settings['sem_id'] ?? '') == $sem['sem_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sem['semester_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="upload_max_mb" class="form-label">Upload Max (MB)</label>
                        <input type="number" name="upload_max_mb" id="upload_max_mb" class="form-control"
                               value="<?= $settings['upload_max_mb'] ?? 10 ?>" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="rate_limit_per_minute" class="form-label">Rate Limit Per Minute</label>
                        <input type="number" name="rate_limit_per_minute" id="rate_limit_per_minute" class="form-control"
                               value="<?= $settings['rate_limit_per_minute'] ?? 60 ?>" min="1">
                    </div>
                    <div class="mb-3">
                        <label for="rate_limit_login" class="form-label">Login Rate Limit</label>
                        <input type="number" name="rate_limit_login" id="rate_limit_login" class="form-control"
                               value="<?= $settings['rate_limit_login'] ?? 10 ?>" min="1">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="rate_limit_strict" value="1"
                               class="form-check-input" <?= ($settings['rate_limit_strict'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label">Enable Strict Rate Limiting</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white">
                <span class="fw-bold">Current Configuration</span>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless" aria-label="Current configuration">
                    <tr>
                        <td class="fw-bold text-end">System Title:</td>
                        <td><?= htmlspecialchars($settings['system_title'] ?? 'CuyoSSIS') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Active Academic Year:</td>
                        <td><?= htmlspecialchars($activeAY ?? 'Not Set') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Active Semester:</td>
                        <td><?= htmlspecialchars($activeSem ?? 'Not Set') ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Upload Limit:</td>
                        <td><?= $settings['upload_max_mb'] ?? 10 ?> MB</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Rate Limit (/min):</td>
                        <td><?= $settings['rate_limit_per_minute'] ?? 60 ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Login Limit:</td>
                        <td><?= $settings['rate_limit_login'] ?? 10 ?> attempts</td>
                    </tr>
                    <tr>
                        <td class="fw-bold text-end">Strict Mode:</td>
                        <td><?= ($settings['rate_limit_strict'] ?? 0) ? 'Enabled' : 'Disabled' ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">New Grade Submission</h1>
    <a href="/ssis/registrar/grades/submission" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Back to Submissions
    </a>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/registrar/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/ssis/registrar/grades/submission">Grade Submissions</a></li>
        <li class="breadcrumb-item active" aria-current="page">New Submission</li>
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
        <span class="fw-bold">Select Schedule</span>
    </div>
    <div class="card-body">
        <form method="GET" action="/ssis/registrar/grades/submission/create" class="row g-3 align-items-end mb-4">
            <div class="col-md-4">
                <label for="ay_id" class="form-label small">Academic Year</label>
                <select name="ay_id" id="ay_id" class="form-select form-select-sm">
                    <option value="">Select Academic Year</option>
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['ay_id'] ?>" <?= (isset($_GET['ay_id']) && $_GET['ay_id'] == $ay['ay_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ay['ay_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="sem_id" class="form-label small">Semester</label>
                <select name="sem_id" id="sem_id" class="form-select form-select-sm">
                    <option value="">Select Semester</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($_GET['sem_id']) && $_GET['sem_id'] == $sem['sem_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['semester_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Load Schedules</button>
            </div>
        </form>
    </div>
</div>

<?php if (isset($schedules) && !empty($schedules)): ?>
    <div class="card">
        <div class="card-header">
            <span class="fw-bold">Available Schedules for Grade Submission</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Schedules for grade submission">
                    <thead>
                        <tr>
                            <th class="small">Class No</th>
                            <th class="small">Course</th>
                            <th class="small">Units</th>
                            <th class="small">Faculty</th>
                            <th class="small">Students Enrolled</th>
                            <th class="small">Schedule</th>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $sched): ?>
                            <?php
                                $hasSubmission = false;
                                if (isset($existingSubmissions)) {
                                    foreach ($existingSubmissions as $sub) {
                                        if ($sub['sched_id'] == $sched['sched_id']) {
                                            $hasSubmission = true;
                                            break;
                                        }
                                    }
                                }
                            ?>
                            <tr>
                                <td class="small text-tertiary"><?= htmlspecialchars($sched['class_no'] ?? '') ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($sched['code'] ?? '') ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($sched['description'] ?? '') ?></small>
                                </td>
                                <td class="small text-center"><?= $sched['units'] ?? 0 ?></td>
                                <td class="small text-tertiary"><?= htmlspecialchars(($sched['prof_fname'] ?? '') . ' ' . ($sched['prof_lname'] ?? '')) ?></td>
                                <td class="small text-center text-tertiary"><?= $sched['enrolled_count'] ?? 0 ?></td>
                                <td class="small text-tertiary">
                                    <?= htmlspecialchars($sched['day_name'] ?? '') ?>
                                    <?= htmlspecialchars($sched['time_start'] ?? '') ?>-<?= htmlspecialchars($sched['time_end'] ?? '') ?>
                                    <br>
                                    <small><?= htmlspecialchars($sched['room_name'] ?? '') ?></small>
                                </td>
                                <td class="text-center">
                                    <?php if ($hasSubmission): ?>
                                        <span class="badge bg-success">Submission Exists</span>
                                    <?php else: ?>
                                        <a href="/ssis/registrar/grades/submission/create/confirm?sched_id=<?= $sched['sched_id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>Create Draft
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
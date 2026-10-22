<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Schedule Management</h1>
    <div class="d-flex gap-2">
        <a href="/ssis/admin/schedules?course_id=<?= $activeCourse ?? '' ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrows-clockwise me-1"></i>Refresh
        </a>
    </div>
</div>

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="breadcrumb mb-0 breadcrumb-custom">
        <li class="breadcrumb-item"><a href="/ssis/admin/dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Schedules</li>
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
                <label for="course_id" class="form-label small">Filter by Course</label>
                <select name="course_id" id="course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Courses</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['course_id'] ?>" <?= ($activeCourse ?? '') == $course['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['code'] . ' - ' . $course['description']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($schedules)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-dark-theme" aria-label="Course schedules">
                    <thead>
                        <tr>
                            <th class="small">Course</th>
                            <th class="small">Section</th>
                            <th class="small">Day</th>
                            <th class="small">Time</th>
                            <th class="small">Room</th>
                            <th class="small text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $schedule): ?>
                            <tr>
                                <td class="fw-medium">
                                    <?= htmlspecialchars($schedule['code'] ?? '') ?>
                                    <br><small class="text-muted"><?= htmlspecialchars($schedule['description'] ?? '') ?></small>
                                </td>
                                <td class="small text-center text-tertiary"><?= htmlspecialchars($schedule['class_no'] ?? '') ?></td>
                                <td class="small text-tertiary"><?= htmlspecialchars($schedule['day_name'] ?? '') ?></td>
                                <td class="small text-tertiary text-center">
                                    <?= htmlspecialchars($schedule['time_start'] ?? '') ?> - <?= htmlspecialchars($schedule['time_end'] ?? '') ?>
                                </td>
                                <td class="small text-tertiary"><?= htmlspecialchars($schedule['room_name'] ?? '') ?></td>
                                <td class="text-center">
                                    <form method="POST" action="/ssis/admin/schedules/<?= $schedule['sd_id'] ?>/delete" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this schedule?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">
                <?php if ($activeCourse): ?>
                    No schedules for the selected course.
                <?php else: ?>
                    Select a course to view its schedules.
                <?php endif; ?>
            </p>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="fw-bold">Add New Schedule</span>
    </div>
    <div class="card-body">
        <form method="POST" action="/ssis/admin/schedules/create" class="row g-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">

            <div class="col-md-3">
                <label for="course_id" class="form-label">Course <span class="text-danger">*</span></label>
                <select name="course_id" id="course_id" class="form-select" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= $course['course_id'] ?>" <?= ($activeCourse ?? '') == $course['course_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['code'] . ' - ' . $course['description']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="day_id" class="form-label">Day <span class="text-danger">*</span></label>
                <select name="day_id" id="day_id" class="form-select" required>
                    <option value="">Select Day</option>
                    <?php foreach ($days as $day): ?>
                        <option value="<?= $day['day_id'] ?>"><?= htmlspecialchars($day['day_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="time_start_id" class="form-label">Start Time <span class="text-danger">*</span></label>
                <select name="time_start_id" id="time_start_id" class="form-select" required>
                    <option value="">Select Start</option>
                    <?php foreach ($times as $time): ?>
                        <option value="<?= $time['time_id'] ?>"><?= htmlspecialchars($time['time_start']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="time_end_id" class="form-label">End Time <span class="text-danger">*</span></label>
                <select name="time_end_id" id="time_end_id" class="form-select" required>
                    <option value="">Select End</option>
                    <?php foreach ($times as $time): ?>
                        <option value="<?= $time['time_id'] ?>"><?= htmlspecialchars($time['time_end']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="room_id" class="form-label">Room <span class="text-danger">*</span></label>
                <select name="room_id" id="room_id" class="form-select" required>
                    <option value="">Select Room</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['room_id'] ?>"><?= htmlspecialchars($room['room_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Add Schedule
                </button>
            </div>
        </form>
    </div>
</div>
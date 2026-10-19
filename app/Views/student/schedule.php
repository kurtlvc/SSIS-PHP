<?php
$studentName = ($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '');
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Schedule</h1>
    <div class="text-end">
        <span class="text-muted">Student:</span>
        <strong><?= htmlspecialchars($studentName) ?></strong>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-bold">Active Enrolment</span>
        <?php if ($activeEnrolment): ?>
            <span class="text-muted small">
                A/Y: <?= htmlspecialchars($activeEnrolment['ay_name']) ?> |
                Semester: <?= htmlspecialchars($activeEnrolment['semester_name']) ?>
            </span>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <?php if ($activeEnrolment): ?>
            <div class="row g-3 text-center">
                <div class="col-md-3">
                    <h4 class="fw-bold text-primary"><?= $activeEnrolment['total_units'] ?></h4>
                    <p class="text-muted small mb-0">Total Units</p>
                </div>
                <div class="col-md-3">
                    <h4 class="fw-bold text-success"><?= $activeEnrolment['total_course'] ?? 0 ?></h4>
                    <p class="text-muted small mb-0">Courses</p>
                </div>
                <div class="col-md-3">
                    <h4 class="fw-bold text-info"><?= htmlspecialchars($activeEnrolment['enrol_status']) ?></h4>
                    <p class="text-muted small mb-0">Status</p>
                </div>
                <div class="col-md-3">
                    <h4 class="fw-bold text-warning"><?= $activeEnrolment['is_cleared'] ? 'Cleared' : 'Pending' ?></h4>
                    <p class="text-muted small mb-0">Clearance</p>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted">No active enrolment found.</p>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($enrolments)): ?>
    <div class="card mb-4">
        <div class="card-header bg-white">
            <span class="fw-bold">Enrolment History</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Academic Year</th>
                            <th>Semester</th>
                            <th>Units</th>
                            <th>Status</th>
                            <th>Registration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($enrolments as $enr): ?>
                            <tr>
                                <td><?= htmlspecialchars($enr['ay_name']) ?></td>
                                <td><?= htmlspecialchars($enr['semester_name']) ?></td>
                                <td><?= $enr['total_units'] ?></td>
                                <td><?= htmlspecialchars($enr['enrol_status']) ?></td>
                                <td><?= $enr['registration_date'] ? date('M d, Y', strtotime($enr['registration_date'])) : '—' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header bg-white">
        <span class="fw-bold">Class Schedule</span>
    </div>
    <div class="card-body">
        <?php if (!empty($schedule)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Day</th>
                            <th>Time</th>
                            <th>Course</th>
                            <th>Units</th>
                            <th>Instructor</th>
                            <th>Room</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedule as $sched): ?>
                            <tr>
                                <td><?= htmlspecialchars($sched['day_name'] ?? '—') ?></td>
                                <td><?= htmlspecialchars(date('g:i a', strtotime($sched['time_start']))) ?> - <?= htmlspecialchars(date('g:i a', strtotime($sched['time_end']))) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($sched['code']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($sched['description']) ?></small>
                                </td>
                                <td><?= number_format($sched['units'], 2) ?></td>
                                <td><?= htmlspecialchars(($sched['prof_lname'] ?? '') . ', ' . ($sched['prof_fname'] ?? '')) ?></td>
                                <td><?= htmlspecialchars($sched['room_name'] ?? '—') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No schedule available. Please contact the registrar.</p>
        <?php endif; ?>
    </div>
</div>

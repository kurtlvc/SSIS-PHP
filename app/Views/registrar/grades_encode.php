<?php
function gradeBadge($grade) {
    if ($grade === null) return 'bg-secondary';
    if ($grade >= 3.0) return 'text-success fw-bold';
    if ($grade >= 2.0) return 'text-primary fw-bold';
    return 'text-danger fw-bold';
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Grades Encoding</h1>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="ay_id" class="form-label small">Academic Year</label>
                <select name="ay_id" id="ay_id" class="form-select form-select-sm">
                    <option value="">All</option>
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
                    <option value="">All</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem['sem_id'] ?>" <?= (isset($_GET['sem_id']) && $_GET['sem_id'] == $sem['sem_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($sem['semester_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
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

<div class="card">
    <div class="card-header bg-white">
        <span class="fw-bold">Students Requiring Grade Input</span>
    </div>
    <div class="card-body">
        <?php if (!empty($gradesToEncode)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="gradesTable" aria-label="Grades to encode">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Ref No</th>
                            <th>Course</th>
                            <th>A/Y</th>
                            <th>Sem</th>
                            <th>Units</th>
                            <th>Final Grade</th>
                            <th>Remarks</th>
                            <th class="text-center">Save</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($gradesToEncode as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['lname'] . ', ' . $item['fname']) ?></td>
                                <td><?= htmlspecialchars($item['ref_no']) ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($item['code']) ?></strong><br>
                                    <small class="text-muted"><?= htmlspecialchars($item['description']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($item['ay_name']) ?></td>
                                <td><?= htmlspecialchars($item['semester_name']) ?></td>
                                <td><?= number_format($item['units'], 2) ?></td>
                                <td>
                                    <input type="number" step="0.01" name="final_grade"
                                           class="form-control form-control-sm grade-input"
                                           data-enrolment="<?= $item['enrolment_id'] ?>"
                                           data-course="<?= $item['course_id'] ?>">
                                </td>
                                <td>
                                    <input type="text" name="remarks"
                                           class="form-control form-control-sm remarks-input"
                                           data-enrolment="<?= $item['enrolment_id'] ?>"
                                           data-course="<?= $item['course_id'] ?>"
                                           placeholder="Passed/Failed">
                                </td>
                                <td class="text-center">
                                    <button type="button"
                                            class="btn btn-success btn-sm btn-save"
                                            data-enrolment="<?= $item['enrolment_id'] ?>"
                                            data-course="<?= $item['course_id'] ?>">
                                        <i class="bi bi-save"" aria-hidden="true"></i> Save
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No grades to encode for the selected criteria.</p>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.btn-save').on('click', function() {
        var enrolmentId = $(this).data('enrolment');
        var courseId = $(this).data('course');
        var grade = $('input[name="final_grade"][data-enrolment="'+enrolmentId+'"][data-course="'+courseId+'"]').val();
        var remarks = $('input[name="remarks"][data-enrolment="'+enrolmentId+'"][data-course="'+courseId+'"]').val();

        if (grade === '') {
            alert('Please enter a final grade.');
            return;
        }

        $.post('/ssis/registrar/grades/encode', {
            csrf_token: '<?= $csrf_token ?>',
            enrolment_id: enrolmentId,
            course_id: courseId,
            final_grade: grade,
            remarks: remarks
        }, function(response) {
            alert('Grade saved successfully!');
            location.reload();
        });
    });
});
</script>

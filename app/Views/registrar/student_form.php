<?php
$isEdit = !empty($student);
$action = $isEdit ? '/ssis/registrar/students/' . $student['stud_id'] . '/edit' : '/ssis/registrar/students/new';
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0"><?= $isEdit ? 'Edit Student' : 'Add New Student' ?></h1>
    <a href="/ssis/registrar/students" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<?php if (isset($error)): ?>
    <div class="alert alert-danger alert-dismissible"><i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
<?php endif; ?>

<form method="POST" action="<?= $action ?>" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white"><span class="fw-bold">Student Information</span></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Ref. No. *</label>
                    <input type="text" name="ref_no" class="form-control" value="<?= htmlspecialchars($student['ref_no'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Last Name *</label>
                    <input type="text" name="lname" class="form-control" value="<?= htmlspecialchars($student['lname'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">First Name *</label>
                    <input type="text" name="fname" class="form-control" value="<?= htmlspecialchars($student['fname'] ?? '') ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="mname" class="form-control" value="<?= htmlspecialchars($student['mname'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Ext. Name</label>
                    <input type="text" name="ext" class="form-control" value="<?= htmlspecialchars($student['ext'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">LRN</label>
                    <input type="text" name="LRN" class="form-control" value="<?= htmlspecialchars($student['LRN'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="emailaddress" class="form-control" value="<?= htmlspecialchars($student['emailaddress'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact No.</label>
                    <input type="text" name="contactno" class="form-control" value="<?= htmlspecialchars($student['contactno'] ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Curriculum ID *</label>
                    <input type="number" name="curr_id" class="form-control" value="<?= htmlspecialchars($student['curr_id'] ?? '') ?>" required>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white"><button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i><?= $isEdit ? 'Update Student' : 'Create Student' ?></button></div>
    </div>
</form>

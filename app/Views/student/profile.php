<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">My Profile</h1>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <?php if (!empty($student)): ?>
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="bg-light border border-secondary d-inline-flex align-items-center justify-content-center rounded-circle mb-2 avatar-120">
                        <i class="bi bi-person-fill display-4 text-muted"></i>
                    </div>
                    <h3 class="h5 fw-bold mb-1"><?= htmlspecialchars(($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '')) ?></h3>
                    <p class="text-muted small mb-0">Ref. No.: <?= htmlspecialchars($student['ref_no'] ?? '') ?></p>
                </div>
                <div class="col-md-8">
                    <table class="table table-borderless table-sm mb-0">
                        <tbody>
                            <tr>
                                <th class="text-muted w-35">Full Name</th>
                                <td><?= htmlspecialchars(($student['lname'] ?? '') . ', ' . ($student['fname'] ?? '') . ' ' . ($student['mname'] ?? '') . ' ' . ($student['ext'] ?? '')) ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">LRN</th>
                                <td><?= htmlspecialchars($student['LRN'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Email</th>
                                <td><?= htmlspecialchars($student['emailaddress'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Contact No.</th>
                                <td><?= htmlspecialchars($student['contactno'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Address</th>
                                <td><?= htmlspecialchars($student['address_house'] ?? '') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Program</th>
                                <td><?= htmlspecialchars(($student['program_code'] ?? '') . ' (' . ($student['program_desc'] ?? '') . ')') ?></td>
                            </tr>
                            <tr>
                                <th class="text-muted">Year Level</th>
                                <td><?= htmlspecialchars($student['year'] ?? '') ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted mb-0">No profile information available.</p>
        <?php endif; ?>
    </div>
</div>

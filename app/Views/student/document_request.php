<?php
$studentName = ($student['fname'] ?? '') . ' ' . ($student['lname'] ?? '');
$docTypes = ['TOR' => 'Transcript of Records', 'COR' => 'Certificate of Registration', 'Good Moral' => 'Good Moral Certificate', 'Certification' => 'Other Certification'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Document Request</h1>
    <div class="text-end">
        <span class="text-muted">Student:</span>
        <strong><?= htmlspecialchars($studentName) ?></strong>
    </div>
</div>

<div class="row">
    <div class="col-md-5 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <span class="fw-bold">Request New Document</span>
            </div>
            <div class="card-body">
<?php if (isset($error) && !empty(trim($error))): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($success) && !empty(trim($success))): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert" aria-live="polite">
        <i class="bi bi-check-circle me-1" aria-hidden="true"></i>
        <?= htmlspecialchars($success) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
                <form method="POST" action="/ssis/student/documents/request">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token ?? '') ?>">
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Document Type</label>
                        <select name="document_type" id="document_type" class="form-select" required
                               aria-describedby="docHelp">
                            <option value="">-- Select Document --</option>
                            <?php foreach ($docTypes as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div id="docHelp" class="form-text small text-muted mt-1">Choose the document type you need to request.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-7">
        <div class="card h-100">
            <div class="card-header">
                <span class="fw-bold">My Requests</span>
            </div>
            <div class="card-body">
                <?php if (!empty($requests)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover" aria-label="My document requests">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Processed</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($requests as $req): ?>
                                    <tr>
                                        <td><?= date('M d, Y', strtotime($req['request_date'])) ?></td>
                                        <td>
                                            <?php 
                                                $labels = ['TOR' => 'Transcript of Records', 'COR' => 'Certificate of Registration', 'Good Moral' => 'Good Moral Certificate', 'Certification' => 'Other Certification'];
                                                echo $labels[$req['document_type']] ?? $req['document_type'];
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                                $badgeClass = match($req['status']) {
                                                    'Released' => 'bg-success',
                                                    'Ready for Pickup' => 'bg-info',
                                                    'Processing' => 'bg-warning',
                                                    default => 'bg-secondary',
                                                };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $req['status'] ?></span>
                                        </td>
                                        <td><?= $req['processed_at'] ? date('M d, Y', strtotime($req['processed_at'])) : '—' ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">No document requests yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

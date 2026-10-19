<?php
function reqBadge($status) {
    return match($status) {
        'Released' => 'bg-success',
        'Ready for Pickup' => 'bg-info',
        'Processing' => 'bg-warning',
        default => 'bg-secondary',
    };
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Document Request Queue</h1>
    <a href="/sis/registrar/enrollments" class="btn btn-outline-secondary btn-sm">Back to Enrolments</a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <span class="fw-bold">Pending Document Requests</span>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($requests)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date Requested</th>
                            <th>Student</th>
                            <th>Ref No</th>
                            <th>Program</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><?= date('M d, Y', strtotime($req['request_date'])) ?></td>
                                <td><?= htmlspecialchars($req['lname'] . ', ' . $req['fname']) ?></td>
                                <td><?= htmlspecialchars($req['ref_no']) ?></td>
                                <td><?= htmlspecialchars($req['program_code']) ?></td>
                                <td>
                                    <?php
                                        $labels = ['TOR' => 'Transcript of Records', 'COR' => 'Certificate of Registration', 'Good Moral' => 'Good Moral', 'Certification' => 'Certification'];
                                        echo $labels[$req['document_type']] ?? $req['document_type'];
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?= reqBadge($req['status']) ?>"><?= $req['status'] ?></span>
                                </td>
                                <td class="text-center">
                                    <form method="POST" action="/sis/registrar/documents/process/<?= $req['request_id'] ?>" class="d-inline">
                                        <input type="hidden" name="csrf_token" value="<?= App::csrf_token() ?>">
                                        <select name="status" class="form-select form-select-sm d-inline-block w-auto">
                                            <option value="Processing">Processing</option>
                                            <option value="Ready for Pickup">Ready for Pickup</option>
                                            <option value="Released">Released</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center py-4 mb-0">No document requests pending.</p>
        <?php endif; ?>
    </div>
</div>

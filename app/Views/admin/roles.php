<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Roles & Permissions</h1>
    <a href="/ssis/admin/users" class="btn btn-outline-secondary btn-sm">Back to Users</a>
</div>

<div class="card mb-4">
    <div class="card-header">
        <span class="fw-bold">Role Definitions</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" aria-label="Role definitions">
                <thead class="table-light">
                    <tr>
                        <th>Role</th>
                        <th>Description</th>
                        <th>Access Level</th>
                        <th>Can Access Modules</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><span class="badge bg-danger">Admin</span></td>
                        <td>Full system access. User account management, global settings, all modules.</td>
                        <td>99 (Highest)</td>
                        <td>All Modules</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-warning">Dean</span></td>
                        <td>Dean-level access. Registrar functions, department clearance, user oversight.</td>
                        <td>80</td>
                        <td>Registrar, Department, Admin</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-info">Chair</span></td>
                        <td>Program Chair. Grade encoding, student records, clearance processing.</td>
                        <td>70</td>
                        <td>Registrar, Department</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-primary">Faculty</span></td>
                        <td>Teaching faculty. Grade encoding, student records within assigned courses.</td>
                        <td>60</td>
                        <td>Registrar</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-success">Student</span></td>
                        <td>End-user student. Portal access, grades, schedule, document requests.</td>
                        <td>50</td>
                        <td>Student Portal</td>
                    </tr>
                    <tr>
                        <td><span class="badge bg-success">Cashier</span></td>
                        <td>Cashier. Payment processing, receipt generation, daily collections.</td>
                        <td>65</td>
                        <td>Cashier</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <span class="fw-bold">Permission Matrix</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered text-center" aria-label="Permission matrix">
                <thead class="table-light">
                    <tr>
                        <th>Module / Action</th>
                        <th>Student</th>
                        <th>Faculty</th>
                        <th>Chair</th>
                        <th>Dean</th>
                        <th>Cashier</th>
                        <th>Admin</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>View Dashboard</td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>Enrollment Management</td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-warning">Own Courses</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>Grade Encoding</td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>Process Payments</td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>Clearance Processing</td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>User Management</td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>System Settings</td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-danger">No</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                    <tr>
                        <td>Document Requests</td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                        <td><span class="badge bg-success">Yes</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

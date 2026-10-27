<?php
$pageTitle = 'Users & permissions'; $activeNav = 'users';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Users & permissions', 'Employees, roles and what each role can do. Final role names and approval delegation are confirmed with the client.', action_btn('Import employees', 'upload', 'impModal', 'btn-outline-ink') . action_btn('Add user', 'plus-lg', 'userModal'));
$users = [
 ['E-1042','Sneha Kapoor','Projects Manager','Projects','Host / Employee','Pune Plant','Active'],['E-1107','Vikram Shah','Procurement Head','Procurement','Department Head / Approver','Pune Plant','Active'],
 ['E-0988','Arjun Menon','Maintenance Lead','Maintenance','Host / Employee','Chakan Factory','Active'],['E-1210','A. Jadhav','Security Guard','Security','Security Guard','Pune Plant','Active'],
 ['E-1305','Neha Rane','HR Executive','HR','Receptionist','Mumbai HQ','Active'],['E-0871','R. Patwardhan','Stores Officer','Stores','Stores / Material Officer','Nashik Warehouse','Inactive'],
];
$perms = ['Visitors'=>[1,1,1,0,0],'Appointments'=>[1,1,1,1,1],'Approvals'=>[1,0,1,1,0],'Gate check-in / out'=>[1,0,1,0,0],'Material passes'=>[1,1,1,1,1],'Reports'=>[1,0,0,0,1],'Audit log'=>[1,0,0,0,0]];
?>
<div class="panel">
  <ul class="nav nav-tabs-x nav-tabs px-3 pt-1">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#uList">Employees &amp; users</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#uRoles">Roles &amp; permissions</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#uAct">Login activity</button></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="uList">
      <?php table_tools('userTable', 'Search name, ID or department'); ?>
      <div class="table-responsive"><table class="table table-hover" id="userTable"><thead><tr><th>ID</th><th>Name</th><th>Department</th><th>Role</th><th>Site</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
      <?php foreach ($users as $u): ?><tr><td class="mono"><?= e($u[0]) ?></td><td><b><?= e($u[1]) ?></b><span class="sub"><?= e($u[2]) ?></span></td><td><?= e($u[3]) ?></td><td><?= e($u[4]) ?></td><td><?= e($u[5]) ?></td><td><?= badge($u[6]) ?></td>
        <td class="text-end"><?= table_actions('users', $u[0], ['name' => $u[1]]) ?></td></tr><?php endforeach; ?></tbody></table></div>
    </div>
    <div class="tab-pane fade" id="uRoles"><div class="panel-body">
      <div class="d-flex gap-2 align-items-center mb-3 flex-wrap"><label class="form-label m-0">Role</label><select class="form-select w-auto"><?php foreach ($ROLES as $r): ?><option><?= e($r) ?></option><?php endforeach; ?></select></div>
      <div class="table-responsive"><table class="table perm-grid"><thead><tr><th>Area</th><th>View</th><th>Create</th><th>Edit</th><th>Approve</th><th>Export</th></tr></thead><tbody>
      <?php foreach ($perms as $a => $p): ?><tr><td><?= e($a) ?></td><?php foreach ($p as $i => $on): ?><td><input class="form-check-input" type="checkbox" <?= $on ? 'checked' : '' ?> aria-label="<?= e($a) ?> permission <?= $i ?>"></td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table></div>
      <button class="btn btn-ink" data-toast="Permissions saved">Save permissions</button></div></div>
    <div class="tab-pane fade" id="uAct"><div class="table-responsive"><table class="table"><thead><tr><th>Time</th><th>User</th><th>Event</th><th>IP address</th></tr></thead><tbody>
      <tr><td class="mono">10:02</td><td>Vikram Shah</td><td>Signed in</td><td class="mono">10.4.2.18</td></tr><tr><td class="mono">09:41</td><td>A. Jadhav</td><td>Signed in (Gate 2)</td><td class="mono">10.4.9.4</td></tr><tr><td class="mono">09:12</td><td>Unknown</td><td><?= badge('Blocked') ?> Failed sign-in ×3</td><td class="mono">103.21.4.77</td></tr></tbody></table></div></div>
  </div>
</div>
<?php
modal_open('userModal', 'User details', 'modal-lg', 'Save', 'User saved');
?>
<div class="row g-3"><div class="col-md-4"><label class="form-label req">Employee ID</label><input class="form-control" required></div><div class="col-md-8"><label class="form-label req">Full name</label><input class="form-control" required></div>
<div class="col-md-4"><label class="form-label">Designation</label><input class="form-control"></div><div class="col-md-4"><label class="form-label req">Department</label><select class="form-select" required><option value="">Select</option><option>Procurement</option><option>Production</option><option>Maintenance</option><option>HR</option><option>Stores</option></select></div>
<div class="col-md-4"><label class="form-label">Reporting manager</label><input class="form-control"></div>
<div class="col-md-6"><label class="form-label req">Email</label><input type="email" class="form-control" required></div><div class="col-md-6"><label class="form-label req">Phone</label><input class="form-control" required></div>
<div class="col-md-6"><label class="form-label req">Role</label><select class="form-select" required><option value="">Select</option><?php foreach ($ROLES as $r): ?><option><?= e($r) ?></option><?php endforeach; ?></select></div>
<div class="col-md-6"><label class="form-label req">Sites allowed</label><select class="form-select" multiple><?php foreach ($SITES as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></div>
<div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked id="uAct1"><label class="form-check-label" for="uAct1">Active</label></div></div></div>
<?php modal_close('Save user');
modal_open('impModal', 'Import employees', 'modal-md', 'Validate file', 'File validated – 120 rows OK, 3 need fixing');
?>
<a href="#" class="btn btn-sm btn-outline-ink mb-3" data-toast="Template downloaded"><i class="bi bi-download me-1"></i>Download template</a>
<input type="file" class="form-control" accept=".csv,.xlsx" required><p class="small text-secondary mt-2 mb-0">You'll see validation results for each row before anything is imported.</p>
<?php modal_close('Validate file');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

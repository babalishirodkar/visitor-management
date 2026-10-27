<?php
$pageTitle = 'Contractors'; $activeNav = 'contractors';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Contractors & temporary workforce', 'Companies, workers, work orders and document validity. Workers with expired authorisation are blocked at the gate.', action_btn('Bulk import', 'upload', 'importModal', 'btn-outline-ink') . action_btn('Add contractor', 'plus-lg', 'ctModal'));
?>
<div class="row g-3 mb-3"><?php stat_tile('Contractor companies', 14, 'building'); stat_tile('Workers on site', 11, 'person-workspace'); stat_tile('Documents expiring', 3, 'file-earmark-x', 'within 30 days', 'warn'); stat_tile('Blocked workers', 1, 'slash-circle', '', 'alert'); ?></div>
<div class="panel">
  <ul class="nav nav-tabs-x nav-tabs px-3 pt-1">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#cCo">Companies</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cWk">Workers</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#cLog">Gate log</button></li>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="cCo">
      <?php table_tools('coTable', 'Search company, project or work order'); ?>
      <div class="table-responsive"><table class="table table-hover" id="coTable">
        <thead><tr><th>Company</th><th>Project</th><th>Site</th><th>Work order</th><th>Valid till</th><th>Documents</th><th>Induction</th><th>Workers</th><th class="text-end">Actions</th></tr></thead><tbody>
        <?php foreach ($CONTRACTORS as $c): ?>
          <tr><td><b><?= e($c['company']) ?></b><span class="sub"><?= e($c['rep']) ?></span></td><td><?= e($c['project']) ?></td><td><?= e($c['site']) ?></td><td class="mono"><?= e($c['wo']) ?></td><td><?= e($c['valid']) ?></td><td><?= badge($c['docs']) ?></td><td><?= badge($c['induction']) ?></td><td><?= (int)$c['workers'] ?></td><td class="text-end"><?= table_actions('contractors', $c['wo'], ['name' => $c['company']]) ?></td></tr>
        <?php endforeach; ?></tbody></table></div>
    </div>
    <div class="tab-pane fade" id="cWk">
      <?php table_tools('wkTable', 'Search worker'); ?>
      <div class="table-responsive"><table class="table table-hover" id="wkTable">
        <thead><tr><th>Worker</th><th>Company</th><th>ID</th><th>Trade</th><th>Medical valid till</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
        <?php foreach ($WORKERS as $w): ?>
          <tr><td><b><?= e($w['name']) ?></b></td><td><?= e($w['company']) ?></td><td class="mono"><?= e($w['id']) ?></td><td><?= e($w['trade']) ?></td><td><?= e($w['medical']) ?></td><td><?= badge($w['status']) ?></td>
            <td class="text-end"><?= table_actions('contractors', $w['id'], ['name' => $w['name']]) ?></td></tr>
        <?php endforeach; ?></tbody></table></div>
    </div>
    <div class="tab-pane fade" id="cLog"><div class="p-3"><ul class="timeline">
      <li class="done"><b>Salim Ansari – exit</b><small>Gate 2 · 06:02 · Kalpataru Civil</small></li>
      <li class="done"><b>Ramesh Gaikwad, Santosh Bhosale – entry</b><small>Gate 2 · 07:15 · Shree Electricals</small></li>
      <li class="now"><b>Pravin Thorat – entry refused</b><small>Gate 2 · 07:31 · Medical certificate expired 02 Oct</small></li></ul></div></div>
  </div>
</div>
<?php
modal_open('ctModal', 'Add contractor', 'modal-lg', 'Save', 'Contractor saved – site authorisation requested');
?>
<div class="row g-3">
  <div class="col-md-6"><label class="form-label req">Company</label><input class="form-control" required></div><div class="col-md-6"><label class="form-label req">Authorised representative</label><input class="form-control" required></div>
  <div class="col-md-4"><label class="form-label req">Work order no.</label><input class="form-control" required></div><div class="col-md-4"><label class="form-label req">Valid from</label><input type="date" class="form-control" required></div><div class="col-md-4"><label class="form-label req">Valid till</label><input type="date" class="form-control" required></div>
  <div class="col-md-4"><label class="form-label req">Site</label><select class="form-select"><?php foreach ($SITES as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-4"><label class="form-label">Department</label><select class="form-select"><option>Maintenance</option><option>Production</option><option>Projects</option></select></div>
  <div class="col-md-4"><label class="form-label">Shift</label><select class="form-select"><option>General (08:00–17:00)</option><option>Shift A</option><option>Shift B</option><option>Night</option></select></div>
  <div class="col-12"><label class="form-label">Allowed areas</label><div class="d-flex flex-wrap gap-3"><?php foreach (['Production floor','Warehouse','Paint shop','Utilities','Admin block'] as $a): ?><div class="form-check"><input class="form-check-input" type="checkbox" id="ar<?= e($a) ?>"><label class="form-check-label" for="ar<?= e($a) ?>"><?= e($a) ?></label></div><?php endforeach; ?></div></div>
  <div class="col-12"><label class="form-label">Documents (insurance, licence, work order)</label><input type="file" multiple class="form-control"></div>
</div>
<?php modal_close('Save contractor');
modal_open('importModal', 'Bulk import workers', 'modal-md', 'Validate file', 'File validated – 18 rows OK, 2 need fixing');
?>
<p class="small text-secondary">Use the agreed template. You'll see row-by-row results before anything is saved.</p>
<a href="#" class="btn btn-sm btn-outline-ink mb-3" data-toast="Template downloaded"><i class="bi bi-download me-1"></i>Download template</a>
<input type="file" class="form-control" accept=".csv,.xlsx" required>
<?php modal_close('Validate file');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

<?php
$pageTitle = 'Organization & sites'; $activeNav = 'organization';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Organization & sites', 'Company profile, sites, gates, visitor policy and pass templates.');
$sites = [['Pune Plant','Plant','Pune, MH','3','06:00–22:00','GP-{YY}-{#####}'],['Mumbai HQ','Office','Mumbai, MH','2','08:00–20:00','GP-{YY}-{#####}'],['Nashik Warehouse','Warehouse','Nashik, MH','2','06:00–22:00','GP-{YY}-{#####}'],['Chakan Factory','Plant','Chakan, MH','4','24 hours','GP-{YY}-{#####}']];
?>
<div class="panel">
  <ul class="nav nav-tabs-x nav-tabs px-3 pt-1">
    <?php foreach (['Profile'=>'oProfile','Sites'=>'oSites','Gates & areas'=>'oGates','Visitor policy'=>'oPolicy','Pass template'=>'oTpl','Master data'=>'oMaster'] as $l => $id): ?>
    <li class="nav-item"><button class="nav-link <?= $id === 'oProfile' ? 'active' : '' ?>" data-bs-toggle="tab" data-bs-target="#<?= $id ?>"><?= e($l) ?></button></li><?php endforeach; ?>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="oProfile"><form class="panel-body needs-validation" novalidate data-toast="Organization profile saved"><div class="row g-3">
      <div class="col-md-8"><label class="form-label req">Organization name</label><input class="form-control" required value="Sahyadri Engineering Pvt Ltd"></div>
      <div class="col-md-4"><label class="form-label">Logo</label><input type="file" class="form-control" accept="image/*"></div>
      <div class="col-12"><label class="form-label">Registered address</label><textarea class="form-control" rows="2">Plot 14, MIDC Bhosari, Pune 411026</textarea></div>
      <div class="col-md-4"><label class="form-label">Contact person</label><input class="form-control" value="Admin Desk"></div><div class="col-md-4"><label class="form-label">Phone</label><input class="form-control" value="020 5550 1122"></div><div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control" value="security@sahyadri.example"></div>
      <div class="col-12"><button class="btn btn-ink" type="submit">Save profile</button></div></div></form></div>
    <div class="tab-pane fade" id="oSites"><div class="d-flex justify-content-end p-3 pb-0"><?= action_btn('Add site', 'plus-lg', 'siteModal') ?></div>
      <div class="table-responsive"><table class="table"><thead><tr><th>Site</th><th>Type</th><th>Location</th><th>Gates</th><th>Operating hours</th><th>Pass numbering</th><th>Active</th></tr></thead><tbody>
      <?php foreach ($sites as $s): ?><tr><td><b><?= e($s[0]) ?></b></td><td><?= e($s[1]) ?></td><td><?= e($s[2]) ?></td><td><?= e($s[3]) ?></td><td><?= e($s[4]) ?></td><td class="mono"><?= e($s[5]) ?></td><td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked aria-label="Active <?= e($s[0]) ?>"></div></td></tr><?php endforeach; ?></tbody></table></div></div>
    <div class="tab-pane fade" id="oGates"><div class="panel-body"><div class="row g-3">
      <div class="col-md-6"><h3 class="h6">Gates – Pune Plant</h3><ul class="list-group"><li class="list-group-item d-flex justify-content-between">Gate 1 – Main (visitors) <?= badge('Active') ?></li><li class="list-group-item d-flex justify-content-between">Gate 2 – Goods &amp; contractors <?= badge('Active') ?></li><li class="list-group-item d-flex justify-content-between">Gate 3 – Emergency exit <?= badge('Cancelled') ?></li></ul></div>
      <div class="col-md-6"><h3 class="h6">Reception desks &amp; buildings</h3><ul class="list-group"><li class="list-group-item">Reception – Admin block</li><li class="list-group-item">Production hall A, B</li><li class="list-group-item">Stores &amp; dispatch</li><li class="list-group-item">Restricted: Paint shop, Chemical store</li></ul></div></div></div></div>
    <div class="tab-pane fade" id="oPolicy"><form class="panel-body needs-validation" novalidate data-toast="Visitor policy saved"><div class="row g-3">
      <div class="col-md-4"><label class="form-label">Default pass validity</label><select class="form-select"><option>Same day</option><option>Up to 3 days</option><option>Until work order end</option></select></div>
      <div class="col-md-4"><label class="form-label">Photo capture</label><select class="form-select"><option>Required</option><option>Optional</option><option>Off</option></select></div>
      <div class="col-md-4"><label class="form-label">Max badge reprints</label><input type="number" class="form-control" value="2"></div>
      <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked id="pAppr"><label class="form-check-label" for="pAppr">Visitors need host approval before entry</label></div></div>
      <div class="col-12"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="pWl"><label class="form-check-label" for="pWl">Deny entry automatically on watchlist match</label></div></div>
      <div class="col-12"><label class="form-label">Emergency contacts printed on badge</label><input class="form-control" value="Security 020 5550 1122 · Fire 101"></div>
      <div class="col-12"><button class="btn btn-ink" type="submit">Save policy</button></div></div></form></div>
    <div class="tab-pane fade" id="oTpl"><div class="panel-body"><div class="row g-3"><div class="col-md-5"><label class="form-label">Show on badge</label>
      <?php foreach (['Photograph','Company','Host and department','Purpose','Destination','Validity','QR code'] as $f): ?><div class="form-check"><input class="form-check-input" type="checkbox" checked id="tf<?= e($f) ?>"><label class="form-check-label" for="tf<?= e($f) ?>"><?= e($f) ?></label></div><?php endforeach; ?>
      <div class="mt-3"><label class="form-label">Badge size</label><select class="form-select"><option>CR80 (credit-card size)</option><option>A6</option><option>A4 gate pass</option></select></div>
      <button class="btn btn-ink mt-3" data-toast="Template saved">Save template</button></div>
      <div class="col-md-7"><a href="<?= page_url('passes', 'list') ?>" class="btn btn-outline-ink">Open badge preview</a></div></div></div></div>
    <div class="tab-pane fade" id="oMaster"><div class="panel-body"><div class="row g-3">
      <?php foreach (['Visit purposes'=>['Meeting','Interview','Delivery','Maintenance','Audit'],'Units of measure'=>['nos','kg','m','set','box','litre'],'Departments'=>['Procurement','Production','Maintenance','HR','Stores','Quality']] as $t => $vals): ?>
      <div class="col-md-4"><h3 class="h6"><?= e($t) ?></h3><div class="d-flex flex-wrap gap-1"><?php foreach ($vals as $v): ?><span class="status status-muted"><?= e($v) ?></span><?php endforeach; ?></div><button class="btn btn-sm btn-outline-ink mt-2" data-toast="Item added"><i class="bi bi-plus"></i> Add</button></div><?php endforeach; ?></div></div></div>
  </div>
</div>
<?php
modal_open('siteModal', 'Add site', 'modal-md', 'Save', 'Site added');
?>
<div class="row g-3"><div class="col-12"><label class="form-label req">Site name</label><input class="form-control" required></div><div class="col-md-6"><label class="form-label req">Type</label><select class="form-select"><option>Office</option><option>Plant</option><option>Factory</option><option>Warehouse</option></select></div><div class="col-md-6"><label class="form-label req">Operating hours</label><input class="form-control" required placeholder="06:00–22:00"></div><div class="col-12"><label class="form-label">Address</label><textarea class="form-control" rows="2"></textarea></div></div>
<?php modal_close('Add site');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

<?php
$pageTitle = 'Safety & restricted areas'; $activeNav = 'safety';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Safety & restricted areas', 'Induction rules, PPE confirmation and areas that need extra authorisation.', action_btn('Add restricted area', 'plus-lg', 'areaModal'));
$rules = [['Contractor','Full induction (45 min)','Helmet, shoes, vest, gloves','Every 6 months'],['Supplier','Short briefing (10 min)','Shoes, vest','Every 12 months'],['Customer','Safety video (5 min)','Visitor vest','Each visit'],['Delivery person','Gate briefing','Vest','Each visit'],['Interview candidate','None','None','—']];
$areas = [['Paint shop','Fire permit + EHS approval','Contractor, Maintenance','Active'],['Chemical store','Stores head approval, PPE level 2','Stores, EHS','Active'],['Server room','IT head approval, escort required','IT','Active'],['Hot-work zone','Work permit valid on the day','Contractor','Active']];
?>
<div class="row g-3 mb-3"><?php stat_tile('Inductions done today', 31, 'patch-check', '', 'good'); stat_tile('Pending inductions', 5, 'hourglass-split', '', 'warn'); stat_tile('Missing PPE check', 2, 'cone-striped', '', 'alert'); stat_tile('Restricted areas', count($areas), 'sign-stop'); ?></div>
<div class="row g-3">
  <div class="col-xl-7"><div class="panel">
    <div class="panel-head"><h2>Induction requirements by category</h2></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Category</th><th>Induction</th><th>PPE</th><th>Renew</th></tr></thead><tbody>
    <?php foreach ($rules as $r): ?><tr><td><b><?= e($r[0]) ?></b></td><td><?= e($r[1]) ?></td><td><?= e($r[2]) ?></td><td><?= e($r[3]) ?></td></tr><?php endforeach; ?></tbody></table></div>
  </div>
  <div class="panel mt-3"><div class="panel-head"><h2>Restricted areas</h2></div>
    <div class="table-responsive"><table class="table"><thead><tr><th>Area</th><th>Extra authorisation</th><th>Who may enter</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($areas as $a): ?><tr><td><b><?= e($a[0]) ?></b></td><td><?= e($a[1]) ?></td><td><?= e($a[2]) ?></td><td><?= badge($a[3]) ?></td><td class="text-end"><?= table_actions('safety', $a[0], ['name' => $a[0]]) ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
  <div class="col-xl-5">
    <div class="panel mb-3"><div class="panel-head"><h2>Record induction</h2></div>
      <form class="panel-body needs-validation" novalidate data-toast="Induction and PPE confirmation recorded">
        <div class="mb-2"><label class="form-label req">Person or pass</label><input class="form-control" required placeholder="GP-26-08799"></div>
        <div class="mb-2"><label class="form-label req">Induction</label><select class="form-select" required><option value="">Select</option><option>Full induction</option><option>Short briefing</option><option>Safety video</option></select></div>
        <div class="mb-2 d-grid gap-1">
          <?php foreach (['Helmet','Safety shoes','Vest','Gloves','Eye protection'] as $p): ?><div class="form-check"><input class="form-check-input" type="checkbox" id="ppe<?= e($p) ?>"><label class="form-check-label" for="ppe<?= e($p) ?>"><?= e($p) ?> confirmed</label></div><?php endforeach; ?>
          <div class="form-check"><input class="form-check-input" type="checkbox" id="decl" required><label class="form-check-label" for="decl">Declaration signed by the person</label></div></div>
        <button class="btn btn-ink w-100" type="submit">Save induction</button>
      </form></div>
    <div class="panel"><div class="panel-head"><h2>Exceptions</h2></div><ul class="list-group list-group-flush small">
      <li class="list-group-item"><b>Kalpataru Civil – 2 workers</b> entered before induction. Authorised by EHS Officer, 08:10.</li>
      <li class="list-group-item"><b>CleanServe Facilities</b> – 2 inductions pending, access limited to Admin block.</li></ul></div>
  </div>
</div>
<?php
modal_open('areaModal', 'Add restricted area', 'modal-md', 'Save', 'Restricted area saved');
?>
<div class="row g-3"><div class="col-12"><label class="form-label req">Area name</label><input class="form-control" required></div><div class="col-12"><label class="form-label req">Extra authorisation needed</label><input class="form-control" required></div><div class="col-12"><label class="form-label">Work permit required</label><select class="form-select"><option>No</option><option>Yes – check validity at the gate</option></select></div></div>
<?php modal_close('Save area');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

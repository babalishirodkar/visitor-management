<?php
$pageTitle = 'Vehicles & parking'; $activeNav = 'vehicles';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Vehicles & parking', 'Vehicle movements linked to visitor, contractor and material passes.', export_btns() . action_btn('Log vehicle', 'plus-lg', 'vehModal'));
?>
<div class="row g-3 mb-3"><?php stat_tile('Vehicles on site', 6, 'truck'); stat_tile('Entered today', 19, 'box-arrow-in-right'); stat_tile('Parking used', '14 / 40', 'p-square'); stat_tile('Invalid permits', 1, 'exclamation-octagon', '', 'alert'); ?></div>
<div class="panel">
  <?php table_tools('vehTable', 'Search registration or driver'); ?>
  <div class="table-responsive"><table class="table table-hover" id="vehTable">
    <thead><tr><th>Registration</th><th>Type</th><th>Driver</th><th>Linked to</th><th>Gate</th><th>In</th><th>Out</th><th>Parking</th><th>Permit</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($VEHICLES as $v): ?>
      <tr><td class="ref mono"><?= e($v['reg']) ?></td><td><?= e($v['type']) ?></td><td><?= e($v['driver']) ?></td><td><?= e($v['link']) ?></td><td><?= e($v['gate']) ?></td><td class="mono"><?= e($v['in']) ?></td><td class="mono"><?= e($v['out']) ?></td><td><?= e($v['parking']) ?></td><td><?= badge($v['permit']) ?></td>
        <td class="text-end"><?= table_actions('vehicles', $v['reg'], ['name' => $v['reg']]) ?></td></tr>
    <?php endforeach; ?></tbody></table></div>
</div>
<?php
modal_open('vehModal', 'Log vehicle movement', 'modal-lg', 'Save', 'Vehicle movement logged');
?>
<div class="row g-3"><div class="col-md-4"><label class="form-label req">Registration number</label><input class="form-control" required></div>
<div class="col-md-4"><label class="form-label req">Type</label><select class="form-select"><option>Car</option><option>Two-wheeler</option><option>Tempo</option><option>Truck</option></select></div>
<div class="col-md-4"><label class="form-label req">Driver</label><input class="form-control" required></div>
<div class="col-md-4"><label class="form-label req">Movement</label><select class="form-select"><option>Entry</option><option>Exit</option></select></div>
<div class="col-md-4"><label class="form-label">Linked pass</label><input class="form-control" placeholder="GP-… / IN-… / OUT-…"></div>
<div class="col-md-4"><label class="form-label">Parking area</label><select class="form-select"><option>Visitor P-1</option><option>Visitor P-2</option><option>Bay B-1</option><option>Bay B-4</option></select></div>
<div class="col-12"><label class="form-label">Security observation</label><input class="form-control"></div></div>
<?php modal_close('Save movement');
modal_open('vhistModal', 'Vehicle history', 'modal-md', 'Close');
?>
<table class="table table-sm"><thead><tr><th>Date</th><th>Gate</th><th>In</th><th>Out</th><th>Purpose</th></tr></thead><tbody>
<tr><td>26 Oct</td><td>Gate 2</td><td>10:12</td><td>—</td><td>Material inward</td></tr><tr><td>19 Oct</td><td>Gate 2</td><td>09:40</td><td>11:05</td><td>Material inward</td></tr><tr><td>05 Oct</td><td>Gate 2</td><td>14:20</td><td>15:00</td><td>Material inward</td></tr></tbody></table>
<?php modal_close('Close');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

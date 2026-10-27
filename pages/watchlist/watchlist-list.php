<?php
$pageTitle = 'Watchlist'; $activeNav = 'watchlist';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Watchlist', 'Entry restrictions that raise a warning at registration and at the gate. A match does not deny entry unless the action says so.', action_btn('Add restriction', 'plus-lg', 'wlModal'));
?>
<div class="panel">
  <?php table_tools('wlTable', 'Search name or company'); ?>
  <div class="table-responsive"><table class="table table-hover" id="wlTable">
    <thead><tr><th>Person</th><th>Reason</th><th>Added by</th><th>Expires</th><th>At the gate</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($WATCHLIST as $w): ?>
      <tr><td><b><?= e($w['name']) ?></b><span class="sub"><?= e($w['company']) ?></span></td>
        <td style="max-width:360px"><?php if (in_array($currentRole, ['super_admin','org_admin','security'], true)): ?><?= e($w['reason']) ?><?php else: ?><span class="text-secondary"><i class="bi bi-eye-slash me-1"></i>Restricted detail</span><?php endif; ?></td>
        <td><?= e($w['by']) ?><span class="sub">Added <?= e($w['added']) ?></span></td><td><?= e($w['expires']) ?></td><td><?= badge($w['action']) ?></td>
        <td class="text-end"><?= table_actions('watchlist', $w['name'], ['name' => $w['name']]) ?></td></tr>
    <?php endforeach; ?></tbody></table></div>
</div>
<?php
modal_open('wlModal', 'Add restriction', 'modal-md', 'Save', 'Restriction added – approval requested');
?>
<div class="row g-3"><div class="col-12"><label class="form-label req">Person</label><input class="form-control" list="wlv" required><datalist id="wlv"><?php foreach ($VISITORS as $v): ?><option value="<?= e($v['name']) ?>"><?php endforeach; ?></datalist></div>
<div class="col-12"><label class="form-label req">Reason</label><textarea class="form-control" rows="2" required></textarea></div>
<div class="col-md-6"><label class="form-label req">At the gate</label><select class="form-select"><option>Warn only</option><option>Deny entry</option></select></div>
<div class="col-md-6"><label class="form-label req">Expires on</label><input type="date" class="form-control" required></div></div>
<?php modal_close('Add restriction');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

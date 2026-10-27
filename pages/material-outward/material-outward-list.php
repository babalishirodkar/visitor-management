<?php
$pageTitle = 'Material outward'; $activeNav = 'outward';
$needsItems = true;
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Material outward', 'Tools, assets, finished goods, scrap and repair items leaving site. Release needs the configured approvals.', export_btns() . action_btn('New outward pass', 'plus-lg', 'outModal'));
?>
<div class="row g-3 mb-3"><?php stat_tile('Released today', 9, 'box-arrow-up'); stat_tile('Awaiting approval', 3, 'hourglass-split', '', 'warn'); stat_tile('Returnable out', 14, 'arrow-return-left'); stat_tile('Overdue', 2, 'clock-history', '', 'alert'); ?></div>
<div class="panel">
  <div class="table-tools">
    <div class="search-box"><i class="bi bi-search"></i><input type="search" class="form-control" placeholder="Search pass, destination or item" data-filter-table="outTable"></div>
    <select class="form-select w-auto" data-filter-col="4" data-filter-match="exact" data-filter-table="outTable" aria-label="Kind"><option value="">All pass kinds</option><option>Returnable</option><option>Non-returnable</option></select>
  </div>
  <div class="table-responsive"><table class="table table-hover" id="outTable">
    <thead><tr><th>Pass</th><th>Requested by</th><th>Destination</th><th>Items</th><th>Kind</th><th>Return due</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($OUTWARD as $m): ?>
      <tr><td class="ref mono"><?= e($m['id']) ?></td><td><?= e($m['requester']) ?></td><td><?= e($m['dest']) ?></td><td><?= e($m['items']) ?></td><td><?= e($m['kind']) ?></td><td><?= e($m['due']) ?></td><td><?= badge($m['status']) ?></td>
        <td class="text-end"><?= table_actions('material-outward', $m['id'], ['name' => $m['id']]) ?></td></tr>
    <?php endforeach; ?></tbody></table></div>
</div>
<?php
modal_open('outModal', 'New outward gate pass', 'modal-xl', 'Submit', 'Outward pass submitted for approval');
?>
<div class="row g-3">
  <div class="col-md-4"><label class="form-label req">Material type</label><select class="form-select" required><option value="">Select</option><option>Tools</option><option>Machinery</option><option>Company asset</option><option>Finished goods</option><option>Scrap</option><option>Samples</option><option>Repair item</option></select></div>
  <div class="col-md-4"><label class="form-label req">Department</label><select class="form-select" required><option value="">Select</option><option>Maintenance</option><option>Production</option><option>Quality</option><option>Stores</option></select></div>
  <div class="col-md-4"><label class="form-label req">Destination</label><input class="form-control" required></div>
  <div class="col-md-8"><label class="form-label req">Reason</label><input class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Challan / work order no.</label><input class="form-control"></div>
  <div class="col-md-4"><label class="form-label req">Pass kind</label><select class="form-select" id="kindSel"><option>Returnable</option><option>Non-returnable</option></select></div>
  <div class="col-md-4" id="dueWrap"><label class="form-label req">Expected return date</label><input type="date" class="form-control" id="dueInp" required></div>
  <div class="col-md-4" id="ownerWrap"><label class="form-label req">Responsible person</label><input class="form-control" id="ownerInp" required></div>
  <div class="col-12"><label class="form-label">Documents</label><input type="file" multiple class="form-control"></div>
</div>
<?php $itemsId = 'outItems'; include __DIR__ . '/../../includes/form-items.php'; ?>
<div class="small text-secondary">Approval route: Department → Stores → Finance (3 levels). Material cannot be released before all are complete.</div>
<?php modal_close('Submit for approval');
modal_open('releaseModal', 'Release at gate – OUT-0285', 'modal-lg', 'Confirm release', 'Release recorded');
?>
<table class="table table-sm align-middle"><thead><tr><th>Item</th><th>Approved</th><th>Actually released</th></tr></thead><tbody>
<tr><td>MS scrap</td><td>1,850 kg</td><td><input type="number" class="form-control form-control-sm" value="1850" style="width:110px"></td></tr></tbody></table>
<div class="row g-3"><div class="col-md-6"><label class="form-label req">Vehicle number</label><input class="form-control" required></div><div class="col-md-6"><label class="form-label">Remarks</label><input class="form-control"></div></div>
<?php modal_close('Confirm release');
require_once __DIR__ . '/../../includes/footer.php';
$extraScripts = <<<'JS'
<script>
kindSel.addEventListener('change', () => { const r = kindSel.value === 'Returnable';
  dueWrap.hidden = ownerWrap.hidden = !r; dueInp.required = ownerInp.required = r; });
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

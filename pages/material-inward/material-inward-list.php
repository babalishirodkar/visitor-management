<?php
$pageTitle = 'Material inward'; $activeNav = 'inward';
$needsItems = true;
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Material inward', 'Supplier deliveries, customer returns, repair items and samples coming into site.', export_btns() . action_btn('New inward pass', 'plus-lg', 'inModal'));
?>
<div class="row g-3 mb-3"><?php stat_tile('Received today', 7, 'box-arrow-in-down'); stat_tile('Awaiting approval', 2, 'hourglass-split', '', 'warn'); stat_tile('Shortage / damage', 2, 'exclamation-diamond', 'this week', 'alert'); stat_tile('Vehicles at dock', 3, 'truck'); ?></div>
<div class="panel">
  <?php table_tools('inTable', 'Search pass, supplier or PO'); ?>
  <div class="table-responsive"><table class="table table-hover" id="inTable">
    <thead><tr><th>Pass</th><th>Supplier</th><th>PO / challan</th><th>Items</th><th>Vehicle</th><th>Receiving dept</th><th>Received</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($INWARD as $m): ?>
      <tr><td class="ref mono"><?= e($m['id']) ?></td><td><?= e($m['supplier']) ?></td><td><?= e($m['ref']) ?></td><td><?= e($m['items']) ?></td><td class="mono"><?= e($m['vehicle']) ?></td><td><?= e($m['dept']) ?></td><td><?= e($m['received']) ?></td><td><?= badge($m['status']) ?></td>
        <td class="text-end"><?= table_actions('material-inward', $m['id'], ['name' => $m['id']]) ?></td></tr>
    <?php endforeach; ?></tbody></table></div>
</div>
<?php
modal_open('inModal', 'New inward gate pass', 'modal-xl', 'Create', 'Inward pass created');
?>
<div class="row g-3">
  <div class="col-md-4"><label class="form-label req">Type</label><select class="form-select" required><option>Supplier delivery</option><option>Customer return</option><option>Repair item</option><option>Sample</option><option>Tools / equipment</option></select></div>
  <div class="col-md-4"><label class="form-label req">Supplier</label><input class="form-control" required></div><div class="col-md-4"><label class="form-label">PO / reference</label><input class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Invoice / challan no.</label><input class="form-control"></div><div class="col-md-4"><label class="form-label">Vehicle number</label><input class="form-control"></div>
  <div class="col-md-4"><label class="form-label req">Receiving department</label><select class="form-select" required><option value="">Select</option><option>Stores</option><option>Production</option><option>Maintenance</option><option>Quality</option></select></div>
  <div class="col-md-6"><label class="form-label req">Recipient</label><input class="form-control" required></div><div class="col-md-6"><label class="form-label">Supporting documents</label><input type="file" multiple class="form-control"></div>
</div>
<?php $itemsId = 'inItems'; include __DIR__ . '/../../includes/form-items.php'; modal_close('Create pass');
modal_open('recvModal', 'Record received quantities', 'modal-lg', 'Confirm receipt', 'Receipt recorded and inward pass generated');
?>
<table class="table table-sm align-middle"><thead><tr><th>Item</th><th>Ordered</th><th>Received</th><th>Result</th></tr></thead><tbody>
<tr><td>Hydraulic hoses</td><td>40 nos</td><td><input type="number" class="form-control form-control-sm" value="40" style="width:90px"></td><td><select class="form-select form-select-sm"><option>Accepted</option><option>Shortage</option><option>Excess</option><option>Damaged</option><option>Rejected</option></select></td></tr></tbody></table>
<label class="form-label">Remarks</label><textarea class="form-control" rows="2"></textarea>
<div class="form-check mt-2"><input class="form-check-input" type="checkbox" id="ackRecv" required><label class="form-check-label" for="ackRecv">Receiving department has acknowledged the delivery.</label></div>
<div class="small text-secondary mt-2">GRN posting to ERP is outside this system.</div>
<?php modal_close('Confirm receipt');
require_once __DIR__ . '/../../includes/footer.php';
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

<?php
$pageTitle = 'Returnable items'; $activeNav = 'returns';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Returnable items', 'Issued, returned and outstanding quantities for returnable passes.', export_btns());
?>
<div class="row g-3 mb-3"><?php stat_tile('Open returnables', 14, 'box-seam'); stat_tile('Due this week', 4, 'calendar-week', '', 'warn'); stat_tile('Overdue', 2, 'clock-history', 'Oldest: 8 days', 'alert'); stat_tile('Closed this month', 21, 'check2-circle', '', 'good'); ?></div>
<div class="panel">
  <div class="table-tools">
    <div class="search-box"><i class="bi bi-search"></i><input type="search" class="form-control" placeholder="Search pass or item" data-filter-table="retTable"></div>
    <select class="form-select w-auto" data-filter-col="6" data-filter-table="retTable" aria-label="State"><option value="">All states</option><option>Open</option><option>Partial</option><option>Overdue</option><option>Closed</option></select>
  </div>
  <div class="table-responsive"><table class="table table-hover" id="retTable">
    <thead><tr><th>Pass</th><th>Item</th><th style="min-width:200px">Returned</th><th>Outstanding</th><th>Responsible</th><th>Due</th><th>State</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($RETURNS as $r): $out = $r['issued'] - $r['returned']; $pct = round($r['returned'] / $r['issued'] * 100); ?>
      <tr><td class="ref mono"><?= e($r['pass']) ?></td><td><?= e($r['item']) ?></td>
        <td><div class="d-flex justify-content-between small"><span><?= (int)$r['returned'] ?> of <?= (int)$r['issued'] ?> <?= e($r['unit']) ?></span><span><?= $pct ?>%</span></div><div class="progress"><div class="progress-bar <?= $r['state'] === 'Overdue' ? 'over' : '' ?>" style="width:<?= $pct ?>%"></div></div></td>
        <td class="fw-semibold"><?= $out ?> <?= e($r['unit']) ?></td><td><?= e($r['owner']) ?></td><td><?= e($r['due']) ?></td><td><?= badge($r['state']) ?></td>
        <td class="text-end"><?= table_actions('returns', $r['pass'], ['name' => $r['item']]) ?></td></tr>
    <?php endforeach; ?></tbody></table></div>
</div>
<?php
modal_open('retModal', 'Record return', 'modal-md', 'Save', 'Return recorded');
?>
<div class="mb-2"><b id="retTitle"></b><div class="small text-secondary">Outstanding: <span id="retOut"></span></div></div>
<div class="row g-3"><div class="col-6"><label class="form-label req">Quantity returned</label><input type="number" min="1" class="form-control" required id="retQty"></div>
<div class="col-6"><label class="form-label req">Condition</label><select class="form-select"><option>Good</option><option>Damaged</option><option>Needs repair</option></select></div>
<div class="col-12"><label class="form-label">Remarks</label><input class="form-control"></div>
<div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="closeEx"><label class="form-check-label" for="closeEx">Close with an authorised exception (items will not come back)</label></div></div></div>
<?php modal_close('Save return');
modal_open('extModal', 'Extend return date', 'modal-md', 'Extend', 'Return date extended');
?>
<div class="row g-3"><div class="col-12"><label class="form-label req">New return date</label><input type="date" class="form-control" required></div><div class="col-12"><label class="form-label req">Reason</label><textarea class="form-control" rows="2" required></textarea></div></div>
<?php modal_close('Extend date');
require_once __DIR__ . '/../../includes/footer.php';
$extraScripts = <<<'JS'
<script>
document.getElementById('retModal').addEventListener('show.bs.modal', e => { const b = e.relatedTarget; if(!b) return;
  retTitle.textContent = b.dataset.pass + ' · ' + b.dataset.item; retOut.textContent = b.dataset.out; retQty.max = b.dataset.out; });
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

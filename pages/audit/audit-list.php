<?php
$pageTitle = 'Audit log'; $activeNav = 'audit';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Audit log', 'Approvals, rejections, revocations, overrides, corrections and admin changes. Entries cannot be edited.', export_btns());
?>
<div class="panel">
  <div class="table-tools">
    <div class="search-box"><i class="bi bi-search"></i><input type="search" class="form-control" placeholder="Search actor, record or reason" data-filter-table="auditTable"></div>
    <select class="form-select w-auto" data-filter-col="3" data-filter-table="auditTable" aria-label="Action"><option value="">All actions</option><option>Approved</option><option>Rescheduled</option><option>Timestamp</option><option>override</option><option>revoked</option><option>reconciled</option></select>
    <input type="date" class="form-control w-auto" aria-label="From date"><input type="date" class="form-control w-auto" aria-label="To date">
  </div>
  <div class="table-responsive"><table class="table table-hover" id="auditTable">
    <thead><tr><th>Time</th><th>Actor</th><th>Role</th><th>Action</th><th>Record</th><th>Reason</th><th class="text-end">Actions</th></tr></thead><tbody>
    <?php foreach ($AUDIT as $a): ?>
      <tr><td class="mono text-nowrap"><?= e($a['time']) ?></td><td><?= e($a['actor']) ?></td><td><?= e($a['role']) ?></td><td><b><?= e($a['action']) ?></b></td><td class="ref mono"><?= e($a['ref']) ?></td><td><?= e($a['reason']) ?></td><td class="text-end"><?= table_actions('audit', $a['ref'], ['name' => $a['ref']]) ?></td></tr>
    <?php endforeach; ?></tbody></table></div>
</div>
<?php require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

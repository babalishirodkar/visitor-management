<?php
$pageTitle = 'Approvals'; $activeNav = 'approvals';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Approvals', 'Visitor, material and contractor requests assigned to you. Every decision is logged with a time and comment.', action_btn('Set delegate', 'person-check', 'delegateModal', 'btn-outline-ink') . action_btn('Workflow rules', 'diagram-3', 'rulesModal', 'btn-outline-ink'));
?>
<div class="row g-3">
  <div class="col-xl-8">
    <div class="panel">
      <div class="table-tools">
        <div class="search-box"><i class="bi bi-search"></i><input type="search" class="form-control" placeholder="Search requests" data-filter-table="apprTable"></div>
        <select class="form-select w-auto" data-filter-col="1" data-filter-table="apprTable" aria-label="Type"><option value="">All types</option><option>Visitor</option><option>Walk-in</option><option>Material</option><option>Contractor</option></select>
      </div>
      <div class="table-responsive"><table class="table table-hover" id="apprTable">
        <thead><tr><th>Request</th><th>Type</th><th>Requested by</th><th>Level</th><th>Waiting</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($APPROVALS as $a): ?>
          <tr>
            <td style="min-width:260px"><span class="ref mono"><?= e($a['id']) ?></span><span class="sub"><?= e($a['subject']) ?></span></td>
            <td><?= e($a['type']) ?></td><td><?= e($a['requester']) ?><span class="sub"><?= e($a['site']) ?></span></td>
            <td class="text-nowrap"><?= e($a['level']) ?></td><td><?= e($a['age']) ?><?php if ($a['due'] !== 'on time'): ?><br><?= badge($a['due']) ?><?php endif; ?></td>
            <td class="text-end"><?= table_actions('approvals', $a['id'], ['name' => $a['subject']]) ?></td>
          </tr>
        <?php endforeach; ?>
        </tbody></table></div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="panel mb-3"><div class="panel-head"><h2>Decision history</h2></div><div class="panel-body">
      <ul class="timeline">
        <li class="done"><b>APT-26-0413 approved</b><small>Vikram Shah · 10:41 · "Known supplier"</small></li>
        <li class="done"><b>OUT-0290 approved (3 of 3)</b><small>Finance · 09:20</small></li>
        <li class="done"><b>OUT-0290 approved (2 of 3)</b><small>Stores · 08:55</small></li>
        <li class="now"><b>AP-7783 escalated</b><small>No response in 24 h → Plant Head</small></li>
      </ul></div></div>
    <div class="panel"><div class="panel-head"><h2>Emergency override log</h2></div>
      <div class="panel-body small">GP-26-08790 · Fire inspector admitted at 09:15 without approval. Authorised by Plant Head. Reason: statutory inspection. Pending ratification.</div></div>
  </div>
</div>

<?php
modal_open('decisionModal', 'Record decision', 'modal-md', 'Submit', 'Decision recorded');
?>
<div class="mb-2 small text-secondary">Request <b id="decRef" class="text-body"></b></div>
<label class="form-label" id="decLabel">Comment</label><textarea class="form-control" rows="3" id="decComment"></textarea>
<div class="invalid-feedback">Add a reason when rejecting or asking for information.</div>
<?php modal_close('Submit decision');
modal_open('delegateModal', 'Set delegate approver', 'modal-md', 'Save', 'Delegate saved');
?>
<div class="row g-3"><div class="col-12"><label class="form-label req">Delegate</label><select class="form-select" required><option value="">Select person</option><option>Neha Rane</option><option>Arjun Menon</option></select></div>
<div class="col-6"><label class="form-label req">From</label><input type="date" class="form-control" required></div><div class="col-6"><label class="form-label req">Until</label><input type="date" class="form-control" required></div></div>
<?php modal_close('Save delegate');
modal_open('rulesModal', 'Approval workflow rules', 'modal-lg', 'Save rules', 'Workflow rules saved');
?>
<table class="table table-sm align-middle"><thead><tr><th>Request type</th><th>Route by</th><th>Levels</th><th>Reminder</th><th>Escalate after</th></tr></thead><tbody>
<tr><td>Visitor appointment</td><td>Host dept</td><td><select class="form-select form-select-sm"><option>1</option><option>2</option></select></td><td>4 h</td><td>24 h</td></tr>
<tr><td>Walk-in</td><td>Host</td><td><select class="form-select form-select-sm"><option>1</option></select></td><td>10 min</td><td>30 min</td></tr>
<tr><td>Material outward</td><td>Dept → Stores → Finance</td><td><select class="form-select form-select-sm"><option>3</option></select></td><td>4 h</td><td>24 h</td></tr>
<tr><td>Contractor</td><td>Site</td><td><select class="form-select form-select-sm"><option>2</option></select></td><td>1 day</td><td>2 days</td></tr></tbody></table>
<div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked id="reappr"><label class="form-check-label" for="reappr">Ask for re-approval when date, host or items change after approval</label></div>
<?php modal_close('Save rules');
require_once __DIR__ . '/../../includes/footer.php';
$extraScripts = <<<'JS'
<script>
document.getElementById('decisionModal').addEventListener('show.bs.modal', e => {
  const b = e.relatedTarget; if(!b) return;
  decRef.textContent = b.dataset.ref; decLabel.textContent = b.dataset.decision === 'Approve' ? 'Comment (optional)' : 'Reason (required)';
  decComment.required = b.dataset.decision !== 'Approve';
  document.querySelector('#decisionModal .modal-title').textContent = b.dataset.decision + ' ' + b.dataset.ref;
  document.querySelector('#decisionModal form').dataset.toast = b.dataset.decision === 'Approve' ? b.dataset.ref+' approved' : b.dataset.decision === 'Reject' ? b.dataset.ref+' rejected' : 'Information requested for '+b.dataset.ref;
});
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

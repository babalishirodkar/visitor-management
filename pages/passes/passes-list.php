<?php
$pageTitle = 'Gate passes & badges'; $activeNav = 'passes';
$needsQR = true;
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Gate passes & badges', 'Preview, print and track visitor passes. A pass is only issued once approvals and checks are complete.',
  '<button class="btn btn-outline-ink" onclick="window.print()"><i class="bi bi-printer me-1"></i>Print badge</button><button class="btn btn-amber" data-toast="PDF downloaded"><i class="bi bi-file-earmark-pdf me-1"></i>Download PDF</button>');
$passes = [
  ['GP-26-08812','Meera Iyer','Bosch Rexroth','Visitor','Active','26 Oct, 18:00',0],
  ['GP-26-08813','Rohan Deshmukh','Tata Projects','Visitor','Active','26 Oct, 18:00',0],
  ['GP-26-08799','Ramesh Gaikwad','Shree Electricals','Contractor','Active','31 Oct, 18:00',1],
  ['GP-26-08790','Fire inspector','Municipal Fire Dept','Official','Used','26 Oct, 12:00',0],
  ['GP-26-08760','Sunita Kulkarni','Self','Visitor','Expired','24 Oct, 18:00',0],
  ['GP-26-08751','Anil Pawar','Pawar Engineering','Visitor','Revoked','25 Oct, 18:00',2],
];
?>
<div class="badge-wrap">
  <div>
    <div class="gate-badge" id="badge">
      <div class="gb-head"><b>PUNE PLANT</b><span class="gb-type" id="gbType">VISITOR</span></div>
      <div class="gb-strip"></div>
      <div class="gb-body">
        <div class="gb-person"><div class="gb-photo"><i class="bi bi-person-fill"></i></div>
          <div><div class="gb-name" id="gbName">Meera Iyer</div><div class="gb-co" id="gbCo">Bosch Rexroth</div></div></div>
        <div class="gb-grid">
          <div><span>Host</span><b>Vikram Shah</b></div><div><span>Department</span><b>Procurement</b></div>
          <div><span>Purpose</span><b>Quarterly review</b></div><div><span>Destination</span><b>Admin block, Floor 2</b></div>
          <div><span>Date</span><b>26 Oct 2026</b></div><div><span>Valid</span><b>11:30 – 18:00</b></div>
        </div>
      </div>
      <div class="gb-foot"><div id="qrBox" role="img" aria-label="QR code for this pass"></div>
        <div><div class="gb-id" id="gbId">GP-26-08812</div><div class="gb-valid">Scan at the gate for entry and exit.<br>Reprints: 0</div></div></div>
      <div class="gb-note">Wear visibly at all times. Return at exit. Emergency: 020 5550 1122</div>
    </div>
  </div>

  <div class="flex-grow-1" style="min-width:320px">
    <div class="panel">
      <div class="panel-head"><h2>Passes issued</h2></div>
      <?php table_tools('passTable', 'Search pass number or name'); ?>
      <div class="table-responsive"><table class="table table-hover" id="passTable">
        <thead><tr><th>Pass</th><th>Holder</th><th>Type</th><th>Valid until</th><th>State</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($passes as $p): ?>
          <tr><td class="ref mono"><?= e($p[0]) ?></td><td><?= e($p[1]) ?><span class="sub"><?= e($p[2]) ?></span></td><td><?= e($p[3]) ?></td><td class="mono"><?= e($p[5]) ?></td><td><?= badge($p[4]) ?></td>
            <td class="text-end"><?= table_actions('passes', $p[0], ['name' => $p[1], 'qr' => true]) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    </div>
    <div class="small text-secondary mt-2">Pass states: Active, Used, Expired, Cancelled, Revoked. Reprints are limited by policy and each one is logged.</div>
  </div>
</div>

<?php
modal_open('reprintModal', 'Reprint badge', 'modal-md', 'Reprint', 'Badge sent to printer – reprint logged');
?><label class="form-label req">Reason for reprint</label><select class="form-select" required><option value="">Select</option><option>Badge lost</option><option>Badge damaged</option><option>Printer error</option></select>
<p class="small text-secondary mt-2 mb-0">Policy allows 2 reprints per pass. This is reprint 1.</p>
<?php modal_close('Reprint');
modal_open('revokeModal', 'Revoke pass', 'modal-md', 'Revoke', 'Pass revoked – it will fail at the gate');
?><label class="form-label req">Reason</label><textarea class="form-control" rows="3" required></textarea>
<p class="small text-secondary mt-2 mb-0">A revoked pass is rejected at the gate straight away. This cannot be undone.</p>
<?php modal_close('Revoke pass');
require_once __DIR__ . '/../../includes/footer.php';
$extraScripts = <<<'JS'
<script>
renderQR('#qrBox','https://gatedesk.example/verify/GP-26-08812');
document.querySelectorAll('[data-show-pass]').forEach(b => b.onclick = () => {
  const d = JSON.parse(b.dataset.showPass);
  document.getElementById('gbName').textContent = d.name; document.getElementById('gbCo').textContent = d.co;
  document.getElementById('gbId').textContent = d.id; document.getElementById('gbType').textContent = d.type;
  renderQR('#qrBox','https://gatedesk.example/verify/'+d.id);
  document.getElementById('badge').scrollIntoView({behavior:'smooth', block:'nearest'});
});
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

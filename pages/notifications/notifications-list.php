<?php
$pageTitle = 'Notifications'; $activeNav = 'notifications';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Notifications', 'Your alerts, plus the message templates and triggers that send them.', '<button class="btn btn-outline-ink" data-toast="All marked as read">Mark all as read</button>');
$inbox = [
 ['bi-hourglass-split','AP-7784 needs your decision','Walk-in: Vishal More, steel samples','4 min ago',1],
 ['bi-person-check','Meera Iyer has arrived','Checked in at Gate 1 · host: Vikram Shah','42 min ago',1],
 ['bi-arrow-return-left','Return overdue: OUT-0281','Torque wrenches, 3 of 8 still outstanding','1 h ago',1],
 ['bi-file-earmark-x','Contractor document expiring','Kalpataru Civil work order ends 15 Nov','3 h ago',0],
 ['bi-calendar-x','Appointment rescheduled','APT-26-0417 moved to 27 Oct 09:00','Yesterday',0],
];
$templates = [['Appointment confirmation','Visitor','Email + in-app','On approval'],['Appointment cancelled / rescheduled','Visitor, host','Email + in-app','On change'],['Visitor arrived','Host','In-app + email','On check-in'],['Approval pending','Approver','In-app + email','Reminder at 4 h, escalate at 24 h'],['Visitor overdue','Host, security','In-app','10 min after pass expiry'],['Return due / overdue','Responsible person','Email + in-app','2 days before, then daily'],['Contractor document expiring','Contractor Mgr','Email','30 and 7 days before']];
?>
<div class="panel">
  <ul class="nav nav-tabs-x nav-tabs px-3 pt-1">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#nInbox">Inbox <span class="status status-wait ms-1">3</span></button></li>
    <?php if (in_array($currentRole, ['super_admin','org_admin'], true)): ?>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#nTpl">Templates &amp; triggers</button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#nLog">Delivery log</button></li>
    <?php endif; ?>
  </ul>
  <div class="tab-content">
    <div class="tab-pane fade show active" id="nInbox"><ul class="list-group list-group-flush">
      <?php foreach ($inbox as $n): ?><li class="list-group-item d-flex gap-3 align-items-start <?= $n[4] ? 'bg-warning-subtle' : '' ?>"><i class="bi <?= e($n[0]) ?> fs-4"></i><div class="flex-grow-1"><b><?= e($n[1]) ?></b><span class="sub"><?= e($n[2]) ?></span></div><small class="text-secondary text-nowrap"><?= e($n[3]) ?></small></li><?php endforeach; ?></ul></div>
    <div class="tab-pane fade" id="nTpl"><div class="table-responsive"><table class="table">
      <thead><tr><th>Message</th><th>Sent to</th><th>Channel</th><th>Trigger</th><th>On</th><th class="text-end">Actions</th></tr></thead><tbody>
      <?php foreach ($templates as $i => $t): ?><tr><td><b><?= e($t[0]) ?></b></td><td><?= e($t[1]) ?></td><td><?= e($t[2]) ?></td><td><?= e($t[3]) ?></td><td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" checked aria-label="Enable <?= e($t[0]) ?>"></div></td><td class="text-end"><?= table_actions('notifications', $t[0], ['name' => $t[0]]) ?></td></tr><?php endforeach; ?></tbody></table></div>
      <div class="p-3 border-top small text-secondary"><i class="bi bi-info-circle me-1"></i>SMS and WhatsApp are not included. They need a separate decision, provider setup and per-message charges.</div></div>
    <div class="tab-pane fade" id="nLog"><div class="table-responsive"><table class="table"><thead><tr><th>Time</th><th>Message</th><th>To</th><th>Channel</th><th>Result</th></tr></thead><tbody>
      <tr><td class="mono">10:41</td><td>Appointment confirmation</td><td>meera.iyer@boschr.example</td><td>Email</td><td><?= badge('Active') ?> Delivered</td></tr>
      <tr><td class="mono">10:12</td><td>Visitor arrived</td><td>Vikram Shah</td><td>In-app</td><td><?= badge('Active') ?> Delivered</td></tr>
      <tr><td class="mono">09:30</td><td>Approval pending</td><td>anil@pawareng.example</td><td>Email</td><td><?= badge('Expired') ?> Bounced</td></tr></tbody></table></div></div>
  </div>
</div>
<?php
modal_open('tplModal', 'Edit message template', 'modal-lg', 'Save', 'Template saved');
?>
<div class="row g-3"><div class="col-12"><label class="form-label req">Subject</label><input class="form-control" required value="Your visit to {site} on {date} is confirmed"></div>
<div class="col-12"><label class="form-label req">Message</label><textarea class="form-control" rows="6" required>Hello {visitor_name},

Your visit to {site} on {date} at {time} is confirmed. Please carry a photo ID and show the QR pass at the gate.

Host: {host_name}
Gate: {gate}</textarea></div>
<div class="col-12 small text-secondary">Placeholders: {visitor_name} {host_name} {site} {gate} {date} {time} {pass_no}</div></div>
<?php modal_close('Save template');
require_once __DIR__ . '/../../includes/footer.php'; require_once __DIR__ . '/../../includes/footerscript.php'; ?>

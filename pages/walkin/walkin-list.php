<?php
$pageTitle = 'Walk-in registration'; $activeNav = 'walkin';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Walk-in registration', 'Register a visitor who has no appointment. The host is asked to approve before a pass is issued.');
?>
<div class="row g-3">
  <div class="col-xl-8">
    <form class="panel needs-validation" novalidate data-toast="Walk-in registered – host notified for approval" id="walkinForm">
      <div class="panel-head"><h2>Visitor details</h2><div class="search-box" style="max-width:300px"><i class="bi bi-search"></i><input class="form-control form-control-sm" placeholder="Find returning visitor" list="vl" id="prefill"><datalist id="vl"><?php foreach ($VISITORS as $v): ?><option value="<?= e($v['name']) ?>"><?php endforeach; ?></datalist></div></div>
      <div class="panel-body"><div class="row g-3">
        <div class="col-md-6"><label class="form-label req">Full name</label><input class="form-control" id="wName" required></div>
        <div class="col-md-6"><label class="form-label">Company</label><input class="form-control" id="wCo"></div>
        <div class="col-md-4"><label class="form-label req">Phone</label><input class="form-control" id="wPhone" required inputmode="tel"></div>
        <div class="col-md-4"><label class="form-label req">Category</label><select class="form-select" id="wCat" required><option value="">Select</option><option>Customer</option><option>Supplier</option><option>Vendor</option><option>Delivery person</option><option>Contractor</option><option>Official</option><option>Personal guest</option></select></div>
        <div class="col-md-4"><label class="form-label">Vehicle number</label><input class="form-control"></div>
        <div class="col-md-6"><label class="form-label req">Host</label><select class="form-select" required><option value="">Select host</option><option>Sneha Kapoor – Projects</option><option>Vikram Shah – Procurement</option><option>Neha Rane – HR</option><option>Arjun Menon – Maintenance</option></select></div>
        <div class="col-md-6"><label class="form-label">Destination</label><input class="form-control"></div>
        <div class="col-12"><label class="form-label req">Purpose</label><input class="form-control" required></div>
        <div class="col-md-4"><label class="form-label req">ID type</label><select class="form-select" required><option value="">Select</option><option>Aadhaar</option><option>Driving licence</option><option>Passport</option><option>Company ID</option></select></div>
        <div class="col-md-4"><label class="form-label req">ID number</label><input class="form-control" required></div>
        <div class="col-md-4"><label class="form-label">Photo</label><input type="file" accept="image/*" capture="user" class="form-control"></div>
        <div class="col-12"><div class="form-check"><input class="form-check-input" type="checkbox" id="ack" required><label class="form-check-label" for="ack">Visitor has read the site rules and safety instructions.</label></div></div>
      </div></div>
      <div class="p-3 border-top d-flex gap-2 justify-content-end"><button type="reset" class="btn btn-outline-ink">Clear</button><button class="btn btn-amber" type="submit"><i class="bi bi-send me-1"></i>Request approval</button></div>
    </form>
  </div>
  <div class="col-xl-4">
    <div class="panel mb-3">
      <div class="panel-head"><h2>Open walk-ins</h2></div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item"><div class="d-flex justify-content-between"><b>Vishal More</b><?= badge('Pending approval') ?></div><span class="sub">Vendor · steel samples · waiting 4 min</span></li>
        <li class="list-group-item"><div class="d-flex justify-content-between"><b>Suresh Wagh</b><?= badge('Approved') ?></div><span class="sub">Delivery · ready for pass</span> <a href="<?= page_url('passes', 'list') ?>" class="small">Issue pass</a></li>
        <li class="list-group-item"><div class="d-flex justify-content-between"><b>Rakesh Dubey</b><?= badge('Blocked') ?></div><span class="sub">Rejected by host · "Not expected"</span></li>
      </ul>
    </div>
    <div class="panel"><div class="panel-body small">
      <b>How walk-ins work</b>
      <ol class="mb-0 mt-2 ps-3"><li>Register the visitor.</li><li>The host gets a request and responds.</li><li>If approved, Security verifies the ID and records entry.</li><li>If rejected, the reason is saved and entry is not allowed.</li></ol>
    </div></div>
  </div>
</div>
<?php
require_once __DIR__ . '/../../includes/footer.php';
$extraScripts = <<<'JS'
<script>
const V = {'Rohan Deshmukh':['Tata Projects','98220 41177','Customer'],'Meera Iyer':['Bosch Rexroth','98907 22314','Supplier'],'Imran Shaikh':['SwiftCourier','90110 55621','Delivery person'],'Anil Pawar':['Pawar Engineering','99600 71230','Vendor']};
document.getElementById('prefill').addEventListener('change', e => { const v = V[e.target.value]; if(!v) return;
  wName.value = e.target.value; wCo.value=v[0]; wPhone.value=v[1]; wCat.value=v[2]; toast('Details filled from visitor master'); });
document.getElementById('walkinForm').addEventListener('submit', e => { /* app.js handles toast */ });
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

<?php
$pageTitle = 'Reports'; $activeNav = 'reports';
$needsCharts = true;
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Reports', 'Pick a report, set filters, then export. Data is limited to the sites and roles you can see.', export_btns());
$reports = [
 ['Visitor register','Who visited, when, and who they met','people'],['Appointments','Booked, attended, cancelled and no-shows','calendar2-check'],['Approvals','Decisions, delays and escalations','check2-square'],
 ['Occupancy','People on site by hour and gate','door-open'],['Material inward / outward','Movements and quantities','box-seam'],['Outstanding returns','Open and overdue returnables','arrow-return-left'],
 ['Contractors','Attendance and document validity','hammer'],['Vehicles','Movements and permit issues','truck'],['Exceptions','Denials, overrides and corrections','exclamation-triangle'],['Audit trail','Who changed what, and why','journal-check'],
];
$rows = [
 ['26 Oct 11:34','Meera Iyer','Bosch Rexroth','Vikram Shah','Procurement','Pune Plant','Gate 1','Supplier','Inside'],
 ['26 Oct 09:52','Imran Shaikh','SwiftCourier','Stores Dept','Stores','Pune Plant','Gate 2','Delivery person','Inside'],
 ['26 Oct 08:40','Prakash Nair','Nair Logistics','Stores Dept','Stores','Nashik Warehouse','Gate 2','Supplier','Inside'],
 ['25 Oct 15:10','Rohan Deshmukh','Tata Projects','Sneha Kapoor','Projects','Pune Plant','Gate 1','Customer','Closed'],
 ['25 Oct 10:00','Anil Pawar','Pawar Engineering','Arjun Menon','Maintenance','Pune Plant','Gate 1','Vendor','No-show'],
];
?>
<div class="row g-3">
  <div class="col-xl-3"><div class="panel"><div class="panel-head"><h2>Report types</h2></div>
    <div class="list-group list-group-flush" id="repList">
      <?php foreach ($reports as $i => $r): ?><button class="list-group-item list-group-item-action d-flex gap-2 align-items-start <?= $i === 0 ? 'active' : '' ?>" data-report="<?= e($r[0]) ?>"><i class="bi bi-<?= e($r[2]) ?> mt-1"></i><span><b><?= e($r[0]) ?></b><span class="sub"><?= e($r[1]) ?></span></span></button><?php endforeach; ?>
    </div></div></div>
  <div class="col-xl-9">
    <div class="panel mb-3"><div class="panel-body"><div class="row g-2 align-items-end">
      <div class="col-6 col-md-2"><label class="form-label">From</label><input type="date" class="form-control" value="2026-10-20"></div>
      <div class="col-6 col-md-2"><label class="form-label">To</label><input type="date" class="form-control" value="2026-10-26"></div>
      <div class="col-6 col-md-2"><label class="form-label">Site</label><select class="form-select"><option>All</option><?php foreach ($SITES as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></div>
      <div class="col-6 col-md-2"><label class="form-label">Gate</label><select class="form-select"><option>All</option><option>Gate 1</option><option>Gate 2</option></select></div>
      <div class="col-6 col-md-2"><label class="form-label">Category</label><select class="form-select"><option>All</option><option>Customer</option><option>Supplier</option><option>Vendor</option></select></div>
      <div class="col-6 col-md-2"><label class="form-label">Status</label><select class="form-select"><option>All</option><option>Inside</option><option>Closed</option><option>No-show</option></select></div>
      <div class="col-12 text-end"><button class="btn btn-ink" data-toast="Report refreshed"><i class="bi bi-funnel me-1"></i>Apply filters</button></div>
    </div></div></div>
    <div class="row g-3 mb-3">
      <div class="col-md-6"><div class="panel"><div class="panel-head"><h2>Visits by category</h2></div><div class="panel-body"><div style="height:220px"><canvas id="catChart" role="img" aria-label="Visits by category"></canvas></div></div></div></div>
      <div class="col-md-6"><div class="panel"><div class="panel-head"><h2>People on site by hour</h2></div><div class="panel-body"><div style="height:220px"><canvas id="hourChart" role="img" aria-label="Occupancy by hour"></canvas></div></div></div></div>
    </div>
    <div class="panel"><div class="panel-head"><h2 id="repTitle">Visitor register</h2><span class="text-secondary small">20–26 Oct 2026 · 1,126 records</span></div>
      <div class="table-responsive"><table class="table table-hover"><thead><tr><th>Time in</th><th>Visitor</th><th>Company</th><th>Host</th><th>Department</th><th>Site</th><th>Gate</th><th>Category</th><th>Status</th><th class="text-end">Actions</th></tr></thead><tbody>
      <?php foreach ($rows as $r): ?><tr><?php foreach ($r as $i => $c): ?><td class="<?= $i === 0 ? 'mono' : '' ?>"><?= $i === 8 ? badge($c) : e($c) ?></td><?php endforeach; ?><td class="text-end"><?= table_actions('reports', $r[1], ['name' => $r[1]]) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
  </div>
</div>
<?php
require_once __DIR__ . '/../../includes/footer.php';
$extraScripts = <<<'JS'
<script>
new Chart(catChart,{type:'bar',data:{labels:['Supplier','Customer','Vendor','Delivery','Contractor','Other'],datasets:[{data:[312,264,198,171,126,55],backgroundColor:'#367f70',borderRadius:4}]},options:{indexAxis:'y',maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{x:{grid:{color:'#eef0f1'}},y:{grid:{display:false}}}}});
new Chart(hourChart,{type:'line',data:{labels:['6','8','10','12','14','16','18','20'],datasets:[{label:'On site',data:[8,24,41,38,33,29,12,5],borderColor:'#367f70',backgroundColor:'rgba(139,182,170,.2)',fill:true,tension:.3,pointBackgroundColor:'#367f70'}]},options:{maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,grid:{color:'#eef0f1'}},x:{grid:{display:false}}}}});
document.querySelectorAll('[data-report]').forEach(b=>b.onclick=()=>{document.querySelectorAll('[data-report]').forEach(x=>x.classList.remove('active'));b.classList.add('active');repTitle.textContent=b.dataset.report;toast(b.dataset.report+' loaded (sample data)');});
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

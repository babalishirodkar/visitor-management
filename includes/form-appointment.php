<?php /* Shared appointment form body (used in modals on dashboard and appointments page). */ ?>
<div class="row g-3">
  <div class="col-md-6"><label class="form-label req">Visitor name</label><input class="form-control" required placeholder="Search existing or type a new name" list="visitorList">
    <datalist id="visitorList"><?php foreach ($VISITORS as $v): ?><option value="<?= e($v['name']) ?>"><?php endforeach; ?></datalist></div>
  <div class="col-md-6"><label class="form-label">Company</label><input class="form-control" placeholder="Company or 'Self'"></div>
  <div class="col-md-4"><label class="form-label req">Category</label><select class="form-select" required><option value="">Select</option><option>Customer</option><option>Supplier</option><option>Vendor</option><option>Interview candidate</option><option>Delivery person</option><option>Contractor</option><option>Official</option><option>Personal guest</option></select></div>
  <div class="col-md-4"><label class="form-label req">Phone</label><input class="form-control" required inputmode="tel"></div>
  <div class="col-md-4"><label class="form-label">Email</label><input type="email" class="form-control"></div>
  <div class="col-md-4"><label class="form-label req">Date</label><input type="date" class="form-control" required value="2026-10-27"></div>
  <div class="col-md-4"><label class="form-label req">From</label><input type="time" class="form-control" required value="10:00"></div>
  <div class="col-md-4"><label class="form-label req">Duration</label><select class="form-select"><option>30 min</option><option selected>1 hour</option><option>2 hours</option><option>Half day</option><option>Full day</option></select></div>
  <div class="col-md-4"><label class="form-label req">Site</label><select class="form-select"><?php foreach ($SITES as $s): ?><option><?= e($s) ?></option><?php endforeach; ?></select></div>
  <div class="col-md-4"><label class="form-label req">Host</label><select class="form-select" required><option value="">Select host</option><option>Sneha Kapoor – Projects</option><option>Vikram Shah – Procurement</option><option>Neha Rane – HR</option><option>Arjun Menon – Maintenance</option></select></div>
  <div class="col-md-4"><label class="form-label">Destination</label><input class="form-control" placeholder="Building / room"></div>
  <div class="col-12"><label class="form-label req">Purpose of visit</label><input class="form-control" required></div>
  <div class="col-md-4"><label class="form-label">Accompanying persons</label><input type="number" min="0" value="0" class="form-control"></div>
  <div class="col-md-4"><label class="form-label">Vehicle number</label><input class="form-control" placeholder="MH12 AB 1234"></div>
  <div class="col-md-4"><label class="form-label">Attach document</label><input type="file" class="form-control"></div>
</div>

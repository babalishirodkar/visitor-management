<?php
$pageTitle = 'Visitor Directory';
$activeNav = 'visitors';

require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';

$cats = [
    ['Customer', 'Photo ID', 'Host approval'],
    ['Supplier', 'Photo ID, PO reference', 'Host approval'],
    ['Vendor', 'Photo ID, company letter', 'Host + dept approval'],
    ['Interview candidate', 'Photo ID, interview letter', 'HR approval'],
    ['Delivery person', 'Photo ID', 'Auto-approved'],
    ['Contractor', 'Photo ID, work order, induction', 'Contractor Mgr + EHS'],
    ['Official', 'Photo ID, authority letter', 'Host approval'],
    ['Personal guest', 'Photo ID', 'Host approval']
];

$actions = export_btns() . ' '
         . '<a href="' . page_url('visitors', 'add') . '" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">'
         . '<i class="bi bi-person-plus"></i><span>Register Visitor</span></a>';

page_header('Visitor Directory', 'Reusable visitor master profiles, category rules and compliance records.', $actions, [
    ['label' => 'Gate & visitors'],
    ['label' => 'Visitor Master']
]);
?>

<div class="panel mb-4">
  <ul class="nav nav-tabs px-3 pt-2 border-bottom" role="tablist">
    <li class="nav-item">
      <button class="nav-link active py-2 px-3 fw-medium" data-bs-toggle="tab" data-bs-target="#tVisitors">
        <i class="bi bi-people me-1"></i>All Visitors
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link py-2 px-3 fw-medium" data-bs-toggle="tab" data-bs-target="#tCats">
        <i class="bi bi-card-checklist me-1"></i>Categories &amp; Requirements
      </button>
    </li>
    <li class="nav-item">
      <button class="nav-link py-2 px-3 fw-medium" data-bs-toggle="tab" data-bs-target="#tRetention">
        <i class="bi bi-clock-history me-1"></i>Data Retention Policy
      </button>
    </li>
  </ul>

  <div class="tab-content">
    <!-- Visitors Tab -->
    <div class="tab-pane fade show active" id="tVisitors">
      <div class="table-filter-bar">
        <div class="table-filter-search">
          <i class="bi bi-search"></i>
          <input type="search" class="form-control" placeholder="Search by name, company, email or phone..." data-filter-table="visTable">
        </div>
        <div class="ms-auto">
          <select class="form-select w-auto" data-filter-col="3" data-filter-match="exact" data-filter-table="visTable" aria-label="Visitor category">
            <option value="">All Categories</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= e($c[0]) ?>"><?= e($c[0]) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table-clean table" id="visTable">
          <thead>
            <tr>
              <th>ID</th>
              <th>Visitor Name</th>
              <th>Company</th>
              <th>Category</th>
              <th>Phone</th>
              <th>Total Visits</th>
              <th>Last Visit</th>
              <th class="text-end">Actions</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($VISITORS as $v): 
            $viewUrl = page_url('visitors', 'add', ['id' => $v['id'], 'mode' => 'view']);
            $editUrl = page_url('visitors', 'add', ['id' => $v['id'], 'mode' => 'edit']);
          ?>
            <tr>
              <td><span class="ref-pill"><?= e($v['id']) ?></span></td>
              <td>
                <a href="<?= e($viewUrl) ?>" class="fw-semibold text-dark text-decoration-none">
                  <?= e($v['name']) ?>
                </a>
                <?php if (!empty($v['flag'])): ?>
                  <span class="badge bg-danger-subtle text-danger border border-danger-subtle small ms-1">
                    <i class="bi bi-shield-exclamation me-1"></i>Watchlist
                  </span>
                <?php endif; ?>
                <span class="table-subtext"><?= e($v['email'] ?? '') ?></span>
              </td>
              <td><span class="text-dark"><?= e($v['company']) ?></span></td>
              <td><span class="badge bg-light text-dark border"><?= e($v['category']) ?></span></td>
              <td><span class="mono text-muted small"><?= e($v['phone']) ?></span></td>
              <td><span class="fw-medium text-dark"><?= (int)$v['visits'] ?></span></td>
              <td><span class="text-muted small"><?= e($v['last']) ?></span></td>
              <td class="text-end">
                <?= table_actions('visitors', $v['id'], ['name' => $v['name']]) ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Categories Tab -->
    <div class="tab-pane fade" id="tCats">
      <div class="table-responsive">
        <table class="table-clean table">
          <thead>
            <tr>
              <th>Visitor Category</th>
              <th>Mandatory ID Documents</th>
              <th>Approval Requirement</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($cats as $c): ?>
            <tr>
              <td><strong class="text-dark"><?= e($c[0]) ?></strong></td>
              <td><span class="text-muted"><?= e($c[1]) ?></span></td>
              <td><span class="badge bg-light text-dark border"><?= e($c[2]) ?></span></td>
              <td>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" checked>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Retention Tab -->
    <div class="tab-pane fade" id="tRetention">
      <div class="p-4" style="max-width: 680px;">
        <h3 class="h6 fw-bold mb-2">Automated Privacy &amp; Data Retention</h3>
        <p class="text-muted small mb-4">
          Government and corporate compliance guidelines require confidential ID photos and proofs to be securely purged after policy duration.
        </p>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">Retain ID Documents &amp; Photos For</label>
            <select class="form-select">
              <option>90 days (Recommended)</option>
              <option>180 days</option>
              <option>1 year</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Retain Visit Movement Records For</label>
            <select class="form-select">
              <option>3 years</option>
              <option selected>5 years (Standard)</option>
              <option>7 years</option>
            </select>
          </div>
          <div class="col-12 mt-4">
            <button class="btn btn-primary" data-toast="Retention policy saved successfully">
              Save Retention Policy
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
require_once __DIR__ . '/../../includes/footerscript.php'; 
?>

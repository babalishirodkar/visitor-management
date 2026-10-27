<?php
require_once __DIR__ . '/../../includes/helpers.php';
$id = trim($_GET['id'] ?? '');
$mode = $_GET['mode'] ?? ($id ? 'view' : 'new');
$modTitle = 'Material Inward';
$pageTitle = ($mode === 'view') ? "$modTitle Details" : (($mode === 'edit') ? "Edit $modTitle" : "New $modTitle");
$activeNav = 'inward';

require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';

$actions = '<a href="' . page_url('material-inward', 'list') . '" class="btn btn-outline-slate"><i class="bi bi-arrow-left me-1"></i>Back to list</a>';
page_header($pageTitle, "Manage and record $modTitle transactions.", $actions, [
    ['label' => $modTitle, 'url' => page_url('material-inward', 'list')],
    ['label' => ($mode === 'view' ? 'View Details' : ($mode === 'edit' ? 'Edit' : 'Create New'))]
]);
?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="detail-card">
      <div class="detail-section-title">
        <i class="bi bi-file-earmark-text text-primary"></i>
        <span><?= e($modTitle) ?> Information</span>
        <?php if ($id): ?>
          <span class="ms-auto ref-pill"><?= e($id) ?></span>
        <?php endif; ?>
      </div>
      
      <form method="POST" action="<?= page_url('material-inward', 'list') ?>" class="needs-validation" novalidate data-toast="Record saved successfully">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label req">Reference / Identifier</label>
            <input type="text" class="form-control" name="ref" value="<?= e($id ?: strtoupper(substr('material-inward', 0, 3)) . '-' . date('y') . '-' . rand(1000, 9999)) ?>" <?= $mode === 'view' ? 'readonly' : 'required' ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label req">Site Location</label>
            <select class="form-select" <?= $mode === 'view' ? 'disabled' : 'required' ?>>
              <?php foreach ($SITES as $s): ?>
                <option <?= $s === $currentSite ? 'selected' : '' ?>><?= e($s) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label req">Description / Subject</label>
            <input type="text" class="form-control" name="description" placeholder="Enter record details..." <?= $mode === 'view' ? 'readonly' : 'required' ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Responsible Person / Officer</label>
            <input type="text" class="form-control" name="officer" value="<?= e($currentUser['name'] ?? 'Officer') ?>" <?= $mode === 'view' ? 'readonly' : '' ?>>
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select class="form-select" <?= $mode === 'view' ? 'disabled' : '' ?>>
              <option>Active / Valid</option>
              <option>Pending Approval</option>
              <option>Under Review</option>
              <option>Completed</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Notes &amp; Observations</label>
            <textarea class="form-control" rows="3" <?= $mode === 'view' ? 'readonly' : '' ?> placeholder="Security observations or compliance checklist..."></textarea>
          </div>
          
          <div class="col-12 mt-4 pt-2 border-top d-flex gap-2">
            <?php if ($mode === 'view'): ?>
              <?php if (!is_readonly()): ?>
                <a href="<?= page_url('material-inward', 'add') ?>?id=<?= urlencode($id) ?>&mode=edit" class="btn btn-primary px-4"><i class="bi bi-pencil me-1"></i>Edit Record</a>
              <?php endif; ?>
              <a href="<?= page_url('material-inward', 'list') ?>" class="btn btn-outline-slate">Close</a>
            <?php else: ?>
              <button type="submit" class="btn btn-primary px-4"><?= $mode === 'edit' ? 'Save Changes' : 'Create Record' ?></button>
              <a href="<?= page_url('material-inward', 'list') ?>" class="btn btn-outline-slate">Cancel</a>
            <?php endif; ?>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="col-lg-4 module-context">
    <div class="detail-card">
      <div class="detail-section-title">
        <i class="bi bi-info-circle text-primary"></i>
        <span>Module Information</span>
      </div>
      <p class="text-muted small">
        Centralized management for <?= strtolower($modTitle) ?> under enterprise compliance policies and site security procedures.
      </p>
      <div class="py-2 px-3 bg-light rounded small">
        <div class="d-flex justify-content-between mb-1">
          <span class="text-muted">Current Site:</span>
          <span class="fw-semibold text-dark"><?= e($currentSite) ?></span>
        </div>
        <div class="d-flex justify-content-between">
          <span class="text-muted">Logged In:</span>
          <span class="fw-semibold text-dark"><?= e($currentUser['name'] ?? 'Admin') ?></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
require_once __DIR__ . '/../../includes/footer.php';
require_once __DIR__ . '/../../includes/footerscript.php';
?>
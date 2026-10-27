<?php
require_once __DIR__ . '/../../includes/helpers.php';

$id = trim($_GET['id'] ?? '');
$mode = $_GET['mode'] ?? ($id ? 'view' : 'new');

// Find visitor in data
$visitor = null;
foreach ($VISITORS as $v) {
    if (strcasecmp($v['id'], $id) === 0) {
        $visitor = $v;
        break;
    }
}

// Fallback dummy for demonstration if not found
if ($id && !$visitor) {
    $visitor = [
        'id'       => $id,
        'name'     => 'Unknown Visitor',
        'company'  => 'Unknown',
        'category' => 'Visitor',
        'phone'    => '—',
        'email'    => '—',
        'visits'   => 1,
        'last'     => 'Today',
        'flag'     => ''
    ];
}

$pageTitle = ($mode === 'view') 
    ? 'Visitor ' . ($visitor['name'] ?? '') 
    : (($mode === 'edit') ? 'Edit Visitor Profile' : 'New Visitor Registration');

$activeNav = 'visitors';

require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
?>

<?php if ($mode === 'view' && $visitor): ?>
  <?php
    $actions = '<a href="' . page_url('visitors', 'list') . '" class="btn btn-outline-slate"><i class="bi bi-arrow-left me-1"></i>Back to list</a> ';
    if (!is_readonly()) {
        $actions .= '<a href="' . page_url('visitors', 'add', ['id' => $visitor['id'], 'mode' => 'edit']) . '" class="btn btn-outline-slate"><i class="bi bi-pencil me-1"></i>Edit Profile</a> ';
    }
    $actions .= '<a href="' . page_url('appointments', 'add', ['visitor' => $visitor['name']]) . '" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Schedule Visit</a>';

    page_header(
        $visitor['name'],
        'Visitor Profile ID: ' . $visitor['id'] . ' · ' . $visitor['company'],
        $actions,
        [
            ['label' => 'Visitor Master', 'url' => page_url('visitors', 'list')],
            ['label' => $visitor['name']]
        ]
    );
  ?>

  <div class="row g-4">
    <div class="col-lg-8">
      <div class="detail-card">
        <div class="detail-section-title">
          <i class="bi bi-person-lines-fill text-primary"></i>
          <span>Profile Details</span>
          <?php if (!empty($visitor['flag'])): ?>
            <span class="badge bg-danger-subtle text-danger border border-danger-subtle ms-auto">
              <i class="bi bi-shield-exclamation me-1"></i>Watchlist Alert
            </span>
          <?php endif; ?>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Full Name</span>
              <span class="detail-value"><?= e($visitor['name']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Company</span>
              <span class="detail-value"><?= e($visitor['company']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Category</span>
              <span class="detail-value"><?= e($visitor['category']) ?></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Phone</span>
              <span class="detail-value mono"><?= e($visitor['phone']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Email</span>
              <span class="detail-value"><?= e($visitor['email']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Total Visits</span>
              <span class="detail-value fw-bold text-primary"><?= (int)$visitor['visits'] ?> time(s)</span>
            </div>
          </div>
        </div>
      </div>

      <div class="detail-card">
        <div class="detail-section-title">
          <i class="bi bi-clock-history text-primary"></i>
          <span>Recent Site Visits History</span>
        </div>
        <div class="table-responsive">
          <table class="table-clean table">
            <thead>
              <tr>
                <th>Date</th>
                <th>Host</th>
                <th>Purpose</th>
                <th>Site</th>
                <th>Duration</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><?= e($visitor['last']) ?></td>
                <td>Sneha Kapoor</td>
                <td>Quarterly Business Review</td>
                <td>Pune Plant</td>
                <td>2 h 15 m</td>
                <td><span class="status-badge status-ok"><i class="bi bi-check-circle-fill"></i>Completed</span></td>
              </tr>
              <tr>
                <td>12 Sep 2026</td>
                <td>Vikram Shah</td>
                <td>Technical Inspection</td>
                <td>Pune Plant</td>
                <td>3 h 40 m</td>
                <td><span class="status-badge status-ok"><i class="bi bi-check-circle-fill"></i>Completed</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="pass-badge-preview">
        <div class="avatar-badge mx-auto mb-3" style="width: 64px; height: 64px; font-size: 1.5rem;">
          <?= strtoupper(substr($visitor['name'], 0, 1)) ?>
        </div>
        <h3 class="h5 fw-bold mb-1"><?= e($visitor['name']) ?></h3>
        <p class="text-muted small mb-3"><?= e($visitor['company']) ?></p>

        <div class="p-3 bg-light rounded text-start small mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Master ID:</span>
            <span class="fw-bold mono"><?= e($visitor['id']) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">ID Verification:</span>
            <span class="text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Last Checked In:</span>
            <span><?= e($visitor['last']) ?></span>
          </div>
        </div>

        <a href="<?= page_url('appointments', 'add', ['visitor' => $visitor['name']]) ?>" class="btn btn-primary w-100 py-2">
          <i class="bi bi-calendar-plus me-1"></i>Schedule New Visit
        </a>
      </div>
    </div>
  </div>

<?php elseif ($mode === 'edit' && $visitor): ?>
  <?php
    $actions = '<a href="' . page_url('visitors', 'add', ['id' => $visitor['id'], 'mode' => 'view']) . '" class="btn btn-outline-slate">Cancel</a>';
    page_header(
        'Edit Visitor · ' . $visitor['name'],
        'Update visitor contact details, company or verification documents.',
        $actions,
        [
            ['label' => 'Visitor Master', 'url' => page_url('visitors', 'list')],
            ['label' => $visitor['name'], 'url' => page_url('visitors', 'add', ['id' => $visitor['id'], 'mode' => 'view'])],
            ['label' => 'Edit']
        ]
    );
  ?>

  <form method="POST" action="<?= page_url('visitors', 'list') ?>" class="needs-validation" novalidate data-toast="Visitor profile updated successfully">
    <div class="detail-card">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label req">Full Name</label>
          <input type="text" class="form-control" value="<?= e($visitor['name']) ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label req">Company</label>
          <input type="text" class="form-control" value="<?= e($visitor['company']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label req">Category</label>
          <select class="form-select" required>
            <?php foreach (['Customer', 'Supplier', 'Vendor', 'Interview candidate', 'Delivery person', 'Contractor', 'Official', 'Personal guest'] as $cat): ?>
              <option value="<?= e($cat) ?>" <?= $visitor['category'] === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label req">Phone</label>
          <input type="text" class="form-control" value="<?= e($visitor['phone']) ?>" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" value="<?= e($visitor['email']) ?>">
        </div>
        <div class="col-12 mt-4">
          <button type="submit" class="btn btn-primary px-4 me-2">Save Profile</button>
          <a href="<?= page_url('visitors', 'add', ['id' => $visitor['id'], 'mode' => 'view']) ?>" class="btn btn-outline-slate">Cancel</a>
        </div>
      </div>
    </div>
  </form>

<?php else: ?>
  <?php
    $actions = '<a href="' . page_url('visitors', 'list') . '" class="btn btn-outline-slate"><i class="bi bi-arrow-left me-1"></i>Back to list</a>';
    page_header(
        'Register New Visitor',
        'Create a reusable master visitor profile with verified identification documents.',
        $actions,
        [
            ['label' => 'Visitor Master', 'url' => page_url('visitors', 'list')],
            ['label' => 'New Visitor']
        ]
    );
  ?>

  <form method="POST" action="<?= page_url('visitors', 'list') ?>" class="needs-validation" novalidate data-toast="Visitor profile created successfully">
    <div class="detail-card">
      <div class="detail-section-title">
        <i class="bi bi-person-plus text-primary"></i>
        <span>Visitor Identification Details</span>
      </div>
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label req">Full Name</label>
          <input type="text" class="form-control" placeholder="e.g. Meera Iyer" required autofocus>
        </div>
        <div class="col-md-6">
          <label class="form-label req">Company / Organization</label>
          <input type="text" class="form-control" placeholder="e.g. Bosch Rexroth" required>
        </div>
        <div class="col-md-4">
          <label class="form-label req">Category</label>
          <select class="form-select" required>
            <option value="">Select category...</option>
            <option value="Customer">Customer</option>
            <option value="Supplier">Supplier</option>
            <option value="Vendor">Vendor</option>
            <option value="Interview candidate">Interview candidate</option>
            <option value="Contractor">Contractor</option>
            <option value="Delivery person">Delivery person</option>
            <option value="Official">Official</option>
            <option value="Personal guest">Personal guest</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label req">Mobile Phone</label>
          <input type="tel" class="form-control" placeholder="e.g. 98907 22314" required>
        </div>
        <div class="col-md-4">
          <label class="form-label">Email Address</label>
          <input type="email" class="form-control" placeholder="visitor@company.com">
        </div>
        <div class="col-md-6">
          <label class="form-label">Identity Document Type</label>
          <select class="form-select">
            <option>Aadhaar Card / National ID</option>
            <option>Driver's License</option>
            <option>Passport</option>
            <option>Company ID Card</option>
          </select>
        </div>
        <div class="col-md-6">
          <label class="form-label">Document ID Number</label>
          <input type="text" class="form-control" placeholder="e.g. XXXX-XXXX-1234">
        </div>
        <div class="col-12 mt-4">
          <button type="submit" class="btn btn-primary px-4 me-2">Save Visitor Profile</button>
          <a href="<?= page_url('visitors', 'list') ?>" class="btn btn-outline-slate">Cancel</a>
        </div>
      </div>
    </div>
  </form>
<?php endif; ?>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
require_once __DIR__ . '/../../includes/footerscript.php'; 
?>

<?php
require_once __DIR__ . '/../../includes/helpers.php';
require_auth();

// Check access before processing writes, rather than waiting for the page header.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (is_readonly() || !can_see('appointments'))) {
    http_response_code(403);
    $pageTitle = 'Action unavailable';
    $activeNav = 'appointments';
    require_once __DIR__ . '/../../includes/header.php';
    page_header('Action unavailable', 'Your current role cannot change appointment records.',
        '<a class="btn btn-outline-slate" href="' . page_url('appointments', 'list') . '">Back to appointments</a>');
    require_once __DIR__ . '/../../includes/footer.php';
    require_once __DIR__ . '/../../includes/footerscript.php';
    exit;
}

// Handle POST submissions (Create, Update, Check-in)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['form_action'] ?? 'create';
    $ref = trim($_POST['ref'] ?? '');

    if ($action === 'create') {
        $visitor = trim($_POST['visitor'] ?? '');
        $company = trim($_POST['company'] ?? 'Independent');
        $group = max(1, (int)($_POST['group'] ?? 1));
        $visitorDisplay = ($group > 1) ? $visitor . " (+" . ($group - 1) . ")" : $visitor;

        $datePart = $_POST['date'] ?? date('Y-m-d');
        $timePart = $_POST['time'] ?? date('H:i');
        $whenDisplay = date('d M, H:i', strtotime("$datePart $timePart"));

        $newRef = save_new_appointment([
            'visitor'      => $visitorDisplay,
            'company'      => $company,
            'category'     => $_POST['category'] ?? 'Visitor',
            'phone'        => $_POST['phone'] ?? '',
            'email'        => $_POST['email'] ?? '',
            'host'         => $_POST['host'] ?? 'Sneha Kapoor',
            'dept'         => $_POST['dept'] ?? 'Administration',
            'site'         => $_POST['site'] ?? 'Pune Plant',
            'when'         => $whenDisplay,
            'purpose'      => $_POST['purpose'] ?? 'Site visit',
            'status'       => 'Approved',
            'group'        => $group,
            'vehicle'      => $_POST['vehicle'] ?? '',
            'vehicle_type' => $_POST['vehicle_type'] ?? '',
            'notes'        => $_POST['notes'] ?? '',
        ]);

        header("Location: " . page_url('appointments', 'add', ['id' => $newRef, 'mode' => 'view', 'created' => 1]));
        exit;
    } elseif ($action === 'update' && $ref) {
        $datePart = $_POST['date'] ?? date('Y-m-d');
        $timePart = $_POST['time'] ?? date('H:i');
        $whenDisplay = date('d M, H:i', strtotime("$datePart $timePart"));

        update_appointment_record($ref, [
            'visitor'      => $_POST['visitor'] ?? '',
            'company'      => $_POST['company'] ?? '',
            'category'     => $_POST['category'] ?? 'Visitor',
            'phone'        => $_POST['phone'] ?? '',
            'email'        => $_POST['email'] ?? '',
            'host'         => $_POST['host'] ?? '',
            'dept'         => $_POST['dept'] ?? '',
            'site'         => $_POST['site'] ?? '',
            'when'         => $whenDisplay,
            'purpose'      => $_POST['purpose'] ?? '',
            'status'       => $_POST['status'] ?? 'Approved',
            'group'        => (int)($_POST['group'] ?? 1),
            'vehicle'      => $_POST['vehicle'] ?? '',
            'vehicle_type' => $_POST['vehicle_type'] ?? '',
            'notes'        => $_POST['notes'] ?? '',
        ]);

        header("Location: " . page_url('appointments', 'add', ['id' => $ref, 'mode' => 'view', 'updated' => 1]));
        exit;
    } elseif ($action === 'checkin' && $ref) {
        update_appointment_record($ref, [
            'status' => 'Checked in'
        ]);
        header("Location: " . page_url('appointments', 'add', ['id' => $ref, 'mode' => 'view', 'checkedin' => 1]));
        exit;
    }
}

// Determine mode & current appointment
$id = trim($_GET['id'] ?? '');
$mode = $_GET['mode'] ?? ($id ? 'view' : 'new');
$appointment = $id ? get_appointment_by_ref($id) : null;

// If an ID was passed but not found, fallback to new
if ($id && !$appointment && $mode !== 'new') {
    header("Location: " . page_url('appointments', 'list', ['not_found' => 1]));
    exit;
}

$pageTitle = ($mode === 'view') 
    ? 'Appointment ' . ($appointment['ref'] ?? '') 
    : (($mode === 'edit') ? 'Edit Appointment' : 'New Appointment');

$activeNav = 'appointments';
$needsQR = $mode === 'view' && $appointment !== null;

require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
?>

<?php if ($mode === 'view' && $appointment): ?>
  <!-- ======================================================================
       VIEW MODE: Minimalist Comprehensive Detail & Digital Badge View
       ====================================================================== -->
  <?php
    $ref = $appointment['ref'];
    $actions = '<a href="' . page_url('appointments', 'list') . '" class="btn btn-outline-slate"><i class="bi bi-arrow-left me-1"></i>Back to list</a> ';
    if (!is_readonly()) {
        $actions .= '<a href="' . page_url('appointments', 'add', ['id' => $ref, 'mode' => 'edit']) . '" class="btn btn-outline-slate"><i class="bi bi-pencil me-1"></i>Edit</a> ';
    }
    $actions .= '<a href="' . page_url('passes', 'add', ['ref' => $ref]) . '" class="btn btn-primary"><i class="bi bi-printer me-1"></i>Print Pass</a>';

    page_header(
        $appointment['visitor'] . ' · ' . $ref,
        'Scheduled visit at ' . $appointment['site'] . ' with ' . $appointment['host'],
        $actions,
        [
            ['label' => 'Appointments', 'url' => page_url('appointments', 'list')],
            ['label' => $ref]
        ]
    );
  ?>

  <?php if (isset($_GET['created'])): ?>
    <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
      <i class="bi bi-check-circle-fill text-success fs-5"></i>
      <div>Appointment <strong><?= e($ref) ?></strong> was created and approved successfully. A digital pass QR has been generated.</div>
    </div>
  <?php elseif (isset($_GET['updated'])): ?>
    <div class="alert alert-info d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
      <i class="bi bi-info-circle-fill text-info fs-5"></i>
      <div>Appointment <strong><?= e($ref) ?></strong> was updated successfully.</div>
    </div>
  <?php elseif (isset($_GET['checkedin'])): ?>
    <div class="alert alert-success d-flex align-items-center gap-2 border-0 shadow-sm mb-4">
      <i class="bi bi-door-open-fill text-success fs-5"></i>
      <div>Visitor checked in successfully. Current status updated to <strong>Inside</strong>.</div>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Left Column: Visit Information Cards -->
    <div class="col-lg-8">
      <!-- Visitor Profile Card -->
      <div class="detail-card">
        <div class="detail-section-title">
          <i class="bi bi-person-badge text-primary"></i>
          <span>Visitor Profile</span>
          <span class="ms-auto"><?= badge($appointment['status']) ?></span>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Full Name</span>
              <span class="detail-value"><?= e($appointment['visitor']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Organization</span>
              <span class="detail-value"><?= e($appointment['company']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Category</span>
              <span class="detail-value"><?= e($appointment['category'] ?? 'Visitor') ?></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Phone</span>
              <span class="detail-value"><?= e($appointment['phone'] ?? '+91 98220 41177') ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Email</span>
              <span class="detail-value"><?= e($appointment['email'] ?? 'visitor@example.com') ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Group Size</span>
              <span class="detail-value">
                <span class="badge bg-light text-dark border"><?= (int)($appointment['group'] ?? 1) ?> Person(s)</span>
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- Schedule & Host Destination Card -->
      <div class="detail-card">
        <div class="detail-section-title">
          <i class="bi bi-calendar2-event text-primary"></i>
          <span>Schedule &amp; Destination</span>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Date &amp; Time</span>
              <span class="detail-value text-primary"><?= e($appointment['when']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Site / Branch</span>
              <span class="detail-value"><?= e($appointment['site']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Meeting Purpose</span>
              <span class="detail-value"><?= e($appointment['purpose']) ?></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Host Employee</span>
              <span class="detail-value"><?= e($appointment['host']) ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Department</span>
              <span class="detail-value"><?= e($appointment['dept'] ?? 'General Administration') ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Allowed Area</span>
              <span class="detail-value">Main Office &amp; Conference Block</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Logistics & Vehicle Details -->
      <div class="detail-card">
        <div class="detail-section-title">
          <i class="bi bi-truck text-primary"></i>
          <span>Vehicle &amp; Safety Compliance</span>
        </div>
        <div class="row g-3">
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Vehicle Number</span>
              <span class="detail-value mono"><?= e(!empty($appointment['vehicle']) ? $appointment['vehicle'] : 'None (Pedestrian)') ?></span>
            </div>
            <div class="detail-row">
              <span class="detail-label">Vehicle Type</span>
              <span class="detail-value"><?= e(!empty($appointment['vehicle_type']) ? $appointment['vehicle_type'] : 'N/A') ?></span>
            </div>
          </div>
          <div class="col-md-6">
            <div class="detail-row">
              <span class="detail-label">Safety Induction</span>
              <span class="detail-value text-success"><i class="bi bi-check-circle me-1"></i>Completed</span>
            </div>
            <div class="detail-row">
              <span class="detail-label">PPE Status</span>
              <span class="detail-value text-muted">Standard safety helmet &amp; shoes</span>
            </div>
          </div>
          <?php if (!empty($appointment['notes'])): ?>
            <div class="col-12 mt-2">
              <div class="p-2 bg-light rounded text-muted small">
                <strong>Host Instructions:</strong> <?= e($appointment['notes']) ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Audit & Workflow Log -->
      <div class="detail-card">
        <div class="detail-section-title">
          <i class="bi bi-clock-history text-primary"></i>
          <span>Workflow &amp; Audit Trail</span>
        </div>
        <div class="d-flex flex-column gap-3">
          <div class="d-flex align-items-center gap-3">
            <div class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-check-lg"></i></div>
            <div>
              <div class="fw-semibold text-dark small">Appointment Pre-registered</div>
              <div class="text-muted small">Initiated by <?= e($appointment['host']) ?> · Notification sent to visitor email</div>
            </div>
            <span class="ms-auto text-muted small">Created</span>
          </div>
          <div class="d-flex align-items-center gap-3">
            <div class="badge bg-success-subtle text-success p-2 rounded-circle"><i class="bi bi-shield-check"></i></div>
            <div>
              <div class="fw-semibold text-dark small">Host Approval Verified</div>
              <div class="text-muted small">Status confirmed for <?= e($appointment['site']) ?></div>
            </div>
            <span class="ms-auto text-muted small">Completed</span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column: Digital Pass Preview & Gate Operations -->
    <div class="col-lg-4">
      <div class="pass-badge-preview">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <span class="fw-bold text-dark small text-uppercase tracking-wider">GatePass Digital Badge</span>
          <span class="badge bg-primary text-white small"><?= e($appointment['site']) ?></span>
        </div>

        <div class="qr-box-wrap" id="badgeQrBox"></div>

        <div class="fw-bold text-dark fs-5 mb-0"><?= e($appointment['visitor']) ?></div>
        <div class="text-muted small mb-2"><?= e($appointment['company']) ?></div>

        <div class="py-2 px-3 bg-light rounded text-start small mb-3">
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Pass ID:</span>
            <span class="fw-bold mono"><?= e($ref) ?></span>
          </div>
          <div class="d-flex justify-content-between mb-1">
            <span class="text-muted">Valid On:</span>
            <span class="fw-semibold"><?= e($appointment['when']) ?></span>
          </div>
          <div class="d-flex justify-content-between">
            <span class="text-muted">Visiting:</span>
            <span class="fw-semibold"><?= e($appointment['host']) ?></span>
          </div>
        </div>

        <?php if (!is_readonly()): ?>
          <?php if (strtolower($appointment['status']) !== 'checked in'): ?>
            <form method="POST" action="<?= page_url('appointments', 'add') ?>" class="mb-2">
              <input type="hidden" name="form_action" value="checkin">
              <input type="hidden" name="ref" value="<?= e($ref) ?>">
              <button type="submit" class="btn btn-success w-100 py-2 fw-semibold">
                <i class="bi bi-box-arrow-in-right me-1"></i>Record Gate Check-in
              </button>
            </form>
          <?php else: ?>
            <div class="p-2 bg-success-subtle text-success rounded text-center small fw-semibold mb-2">
              <i class="bi bi-check-circle-fill me-1"></i>Visitor is Currently Inside
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <a href="<?= page_url('passes', 'add', ['ref' => $ref]) ?>" class="btn btn-outline-slate w-100 py-2">
          <i class="bi bi-printer me-1"></i>Print Official Badge
        </a>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.renderQR) {
        window.renderQR('#badgeQrBox', <?= json_encode('GATEDESK:' . $ref . ':' . $appointment['visitor'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>);
      }
    });
  </script>

<?php elseif ($mode === 'edit' && $appointment): ?>
  <!-- ======================================================================
       EDIT MODE: Pre-filled form with Save & Cancel actions
       ====================================================================== -->
  <?php
    $ref = $appointment['ref'];
    $actions = '<a href="' . page_url('appointments', 'add', ['id' => $ref, 'mode' => 'view']) . '" class="btn btn-outline-slate"><i class="bi bi-x-lg me-1"></i>Cancel</a>';
    page_header(
        'Edit Appointment · ' . $ref,
        'Modify visit details, host, destination or reschedule arrival.',
        $actions,
        [
            ['label' => 'Appointments', 'url' => page_url('appointments', 'list')],
            ['label' => $ref, 'url' => page_url('appointments', 'add', ['id' => $ref, 'mode' => 'view'])],
            ['label' => 'Edit']
        ]
    );
  ?>

  <form method="POST" action="<?= page_url('appointments', 'add') ?>" class="needs-validation" novalidate>
    <input type="hidden" name="form_action" value="update">
    <input type="hidden" name="ref" value="<?= e($ref) ?>">

    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Visitor Card -->
        <div class="detail-card">
          <div class="detail-section-title">
            <i class="bi bi-person text-primary"></i>
            <span>Visitor Information</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label req">Visitor Full Name</label>
              <input type="text" class="form-control" name="visitor" value="<?= e($appointment['visitor']) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label req">Company / Organization</label>
              <input type="text" class="form-control" name="company" value="<?= e($appointment['company']) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Category</label>
              <select class="form-select" name="category">
                <?php foreach (['Customer', 'Supplier', 'Vendor', 'Interview candidate', 'Delivery person', 'Contractor', 'Official', 'Personal guest'] as $cat): ?>
                  <option value="<?= e($cat) ?>" <?= ($appointment['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Phone Number</label>
              <input type="text" class="form-control" name="phone" value="<?= e($appointment['phone'] ?? '') ?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" value="<?= e($appointment['email'] ?? '') ?>">
            </div>
          </div>
        </div>

        <!-- Schedule & Location Card -->
        <div class="detail-card">
          <div class="detail-section-title">
            <i class="bi bi-geo-alt text-primary"></i>
            <span>Host &amp; Schedule Details</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label req">Host Employee</label>
              <input type="text" class="form-control" name="host" value="<?= e($appointment['host']) ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label req">Department</label>
              <input type="text" class="form-control" name="dept" value="<?= e($appointment['dept'] ?? 'General') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Site Location</label>
              <select class="form-select" name="site" required>
                <?php foreach ($SITES as $site): ?>
                  <option value="<?= e($site) ?>" <?= $appointment['site'] === $site ? 'selected' : '' ?>><?= e($site) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Date</label>
              <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Time</label>
              <input type="time" class="form-control" name="time" value="11:00" required>
            </div>
            <div class="col-md-8">
              <label class="form-label req">Purpose of Visit</label>
              <input type="text" class="form-control" name="purpose" value="<?= e($appointment['purpose']) ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Group Count</label>
              <input type="number" class="form-control" name="group" min="1" max="50" value="<?= (int)($appointment['group'] ?? 1) ?>">
            </div>
          </div>
        </div>

        <!-- Logistics Card -->
        <div class="detail-card">
          <div class="detail-section-title">
            <i class="bi bi-truck text-primary"></i>
            <span>Vehicle &amp; Notes</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Vehicle Registration No.</label>
              <input type="text" class="form-control" name="vehicle" value="<?= e($appointment['vehicle'] ?? '') ?>" placeholder="e.g. MH12 AB 1234">
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Type</label>
              <input type="text" class="form-control" name="vehicle_type" value="<?= e($appointment['vehicle_type'] ?? '') ?>" placeholder="Car, Two-wheeler, Truck">
            </div>
            <div class="col-12">
              <label class="form-label">Special Notes / Security Briefing</label>
              <textarea class="form-control" name="notes" rows="2"><?= e($appointment['notes'] ?? '') ?></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column Controls -->
      <div class="col-lg-4">
        <div class="detail-card sticky-top" style="top: 80px;">
          <div class="detail-section-title">
            <i class="bi bi-sliders text-primary"></i>
            <span>Status &amp; Actions</span>
          </div>
          <div class="mb-3">
            <label class="form-label">Appointment Status</label>
            <select class="form-select" name="status">
              <?php foreach (['Approved', 'Pending approval', 'Checked in', 'Info requested', 'Rescheduled', 'No-show', 'Cancelled'] as $st): ?>
                <option value="<?= e($st) ?>" <?= strtolower($appointment['status']) === strtolower($st) ? 'selected' : '' ?>><?= e($st) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <hr class="my-3">
          <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-2">
            <i class="bi bi-check-lg me-1"></i>Save Changes
          </button>
          <a href="<?= page_url('appointments', 'add', ['id' => $ref, 'mode' => 'view']) ?>" class="btn btn-outline-slate w-100 py-2">
            Cancel
          </a>
        </div>
      </div>
    </div>
  </form>

<?php else: ?>
  <!-- ======================================================================
       ADD / CREATE MODE: Modern Step-Free Appointment Form
       ====================================================================== -->
  <?php
    $actions = '<a href="' . page_url('appointments', 'list') . '" class="btn btn-outline-slate"><i class="bi bi-arrow-left me-1"></i>Back to list</a>';
    page_header(
        'Create New Appointment',
        'Pre-register an upcoming visitor, vendor, or contractor visit to generate instant gate pass.',
        $actions,
        [
            ['label' => 'Appointments', 'url' => page_url('appointments', 'list')],
            ['label' => 'New Appointment']
        ]
    );
  ?>

  <form method="POST" action="<?= page_url('appointments', 'add') ?>" class="needs-validation" novalidate id="newAppointmentForm">
    <input type="hidden" name="form_action" value="create">

    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Visitor Card -->
        <div class="detail-card">
          <div class="detail-section-title">
            <i class="bi bi-person text-primary"></i>
            <span>1. Visitor Profile</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label req">Full Name</label>
              <input type="text" class="form-control" name="visitor" placeholder="e.g. Rahul Sharma" required autofocus>
              <div class="invalid-feedback">Please enter visitor's full name.</div>
            </div>
            <div class="col-md-6">
              <label class="form-label req">Company / Organization</label>
              <input type="text" class="form-control" name="company" placeholder="e.g. Tata Projects / Independent" required>
              <div class="invalid-feedback">Please specify organization.</div>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Category</label>
              <select class="form-select" name="category" required>
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
              <label class="form-label">Mobile Number</label>
              <input type="tel" class="form-control" name="phone" placeholder="e.g. 98220 12345">
            </div>
            <div class="col-md-4">
              <label class="form-label">Email Address</label>
              <input type="email" class="form-control" name="email" placeholder="visitor@company.com">
            </div>
            <div class="col-md-4">
              <label class="form-label">Accompanying Persons</label>
              <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-people"></i></span>
                <input type="number" class="form-control" name="group" value="1" min="1" max="25">
              </div>
              <div class="form-text">Total number in party including primary visitor</div>
            </div>
          </div>
        </div>

        <!-- Schedule & Location Card -->
        <div class="detail-card">
          <div class="detail-section-title">
            <i class="bi bi-calendar2-week text-primary"></i>
            <span>2. Schedule &amp; Host Details</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label req">Host Employee</label>
              <input type="text" class="form-control" name="host" placeholder="e.g. Sneha Kapoor" value="Sneha Kapoor" required>
            </div>
            <div class="col-md-6">
              <label class="form-label req">Department</label>
              <select class="form-select" name="dept" required>
                <option value="Projects">Projects</option>
                <option value="Procurement">Procurement</option>
                <option value="Maintenance">Maintenance</option>
                <option value="HR">Human Resources</option>
                <option value="Stores">Stores &amp; Logistics</option>
                <option value="Administration" selected>Administration</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Site Location</label>
              <select class="form-select" name="site" required>
                <?php foreach ($SITES as $site): ?>
                  <option value="<?= e($site) ?>" <?= $site === $currentSite ? 'selected' : '' ?>><?= e($site) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Arrival Date</label>
              <input type="date" class="form-control" name="date" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label req">Arrival Time</label>
              <input type="time" class="form-control" name="time" value="11:30" required>
            </div>
            <div class="col-12">
              <label class="form-label req">Purpose of Visit</label>
              <input type="text" class="form-control" name="purpose" placeholder="e.g. Quarterly review meeting / Machinery audit" required>
            </div>
          </div>
        </div>

        <!-- Vehicle & Compliance Card -->
        <div class="detail-card">
          <div class="detail-section-title">
            <i class="bi bi-shield-check text-primary"></i>
            <span>3. Vehicle &amp; Security Notes</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Vehicle Registration Plate</label>
              <input type="text" class="form-control" name="vehicle" placeholder="e.g. MH12 AB 9988">
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Category</label>
              <select class="form-select" name="vehicle_type">
                <option value="">No vehicle (Pedestrian)</option>
                <option value="Car / Sedan">Car / Sedan</option>
                <option value="Two-Wheeler">Two-Wheeler</option>
                <option value="Commercial LCV">Commercial LCV</option>
                <option value="Truck">Heavy Vehicle / Truck</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Special Instructions / Material Carried</label>
              <textarea class="form-control" name="notes" rows="2" placeholder="e.g. Carrying laptop, inspection kit, requires safety induction"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Right Column Summary & Actions -->
      <div class="col-lg-4">
        <div class="detail-card sticky-top" style="top: 80px;">
          <div class="detail-section-title">
            <i class="bi bi-send text-primary"></i>
            <span>Confirmation</span>
          </div>
          <p class="text-muted small mb-3">
            Creating an appointment pre-authorizes the visitor. The QR badge pass will be generated instantly and host notifications will be triggered.
          </p>

          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="sendInviteEmail" checked>
            <label class="form-check-label small text-dark" for="sendInviteEmail">
              Send QR pass invitation to visitor email
            </label>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input" type="checkbox" id="requireSafety" checked>
            <label class="form-check-label small text-dark" for="requireSafety">
              Require safety induction acknowledgement
            </label>
          </div>

          <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold mb-2 shadow-sm">
            <i class="bi bi-calendar2-check me-1"></i>Create Appointment
          </button>
          <a href="<?= page_url('appointments', 'list') ?>" class="btn btn-outline-slate w-100 py-2">
            Cancel
          </a>
        </div>
      </div>
    </div>
  </form>
<?php endif; ?>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
require_once __DIR__ . '/../../includes/footerscript.php'; 
?>

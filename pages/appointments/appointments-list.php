<?php
$pageTitle = 'Appointments';
$activeNav = 'appointments';

require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';

$appointments = get_all_appointments();

// Calculate metrics
$totalCount = count($appointments);
$approvedCount = $pendingCount = $checkedInCount = 0;
foreach ($appointments as $appointment) {
    $status = strtolower($appointment['status'] ?? '');
    $approvedCount += in_array($status, ['approved', 'checked in'], true) ? 1 : 0;
    $pendingCount += str_starts_with($status, 'pending') ? 1 : 0;
    $checkedInCount += $status === 'checked in' ? 1 : 0;
}

$actions = export_btns() . ' '
         . '<a href="' . page_url('appointments', 'add') . '" class="btn btn-primary d-inline-flex align-items-center gap-1 shadow-sm">'
         . '<i class="bi bi-plus-lg"></i><span>New Appointment</span>'
         . '</a>';

page_header('Appointments', 'Pre-registered visitors, scheduled meetings and expected arrivals.', $actions, [
    ['label' => 'Gate & visitors'],
    ['label' => 'Appointments']
]);
?>

<!-- Minimalist Summary KPI Tiles -->
<div class="row g-3 mb-4">
  <?php 
    stat_tile('Total Scheduled', $totalCount, 'calendar2-check', 'Active visit schedules', 'primary'); 
    stat_tile('Approved / Ready', $approvedCount, 'check-circle', 'Passes ready for entry', 'success'); 
    stat_tile('Pending Approvals', $pendingCount, 'hourglass-split', 'Awaiting host authorization', 'warning'); 
    stat_tile('Checked In Inside', $checkedInCount, 'door-open', 'Currently inside premises', 'info'); 
  ?>
</div>

<!-- Main Table Panel -->
<div class="panel">
  <!-- Table Header Filter Bar -->
  <div class="table-filter-bar">
    <div class="table-filter-search">
      <i class="bi bi-search"></i>
      <input type="search" class="form-control" placeholder="Search by reference, visitor, company, host..." data-filter-table="appointmentsTable" id="tableQuickSearch">
    </div>

    <div class="d-flex align-items-center gap-2 ms-auto">
      <select class="form-select w-auto" id="filterStatus" data-filter-col="7" data-filter-table="appointmentsTable" aria-label="Status filter">
        <option value="">All Statuses</option>
        <option value="Approved">Approved</option>
        <option value="Pending">Pending Approval</option>
        <option value="Checked in">Checked in</option>
        <option value="Info requested">Info Requested</option>
        <option value="Rescheduled">Rescheduled</option>
        <option value="No-show">No-show</option>
      </select>

      <select class="form-select w-auto" id="filterSite" data-filter-col="3" data-filter-table="appointmentsTable" aria-label="Site filter">
        <option value="">All Sites</option>
        <?php foreach ($SITES as $site): ?>
          <option value="<?= e($site) ?>"><?= e($site) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <!-- Responsive Table -->
  <div class="table-responsive">
    <table class="table-clean table" id="appointmentsTable">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Visitor &amp; Company</th>
          <th>Host Employee</th>
          <th>Site Location</th>
          <th>Schedule</th>
          <th>Purpose</th>
          <th>Group</th>
          <th>Status</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
      <?php if (empty($appointments)): ?>
        <tr>
          <td colspan="9" class="text-center py-5 text-muted">
            <i class="bi bi-calendar-x fs-1 d-block mb-2 text-subtle"></i>
            No appointments registered yet. Click <strong>New Appointment</strong> to create one.
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($appointments as $item): 
          $ref = $item['ref'];
          $viewUrl = page_url('appointments', 'add', ['id' => $ref, 'mode' => 'view']);
          $editUrl = page_url('appointments', 'add', ['id' => $ref, 'mode' => 'edit']);
          $passUrl = page_url('passes', 'add', ['ref' => $ref]);
        ?>
          <tr>
            <td>
              <a href="<?= e($viewUrl) ?>" class="ref-pill" title="View details"><?= e($ref) ?></a>
            </td>
            <td>
              <a href="<?= e($viewUrl) ?>" class="fw-semibold text-dark text-decoration-none hover-primary">
                <?= e($item['visitor']) ?>
              </a>
              <span class="table-subtext"><?= e($item['company']) ?></span>
            </td>
            <td>
              <span class="fw-medium text-dark"><?= e($item['host']) ?></span>
              <span class="table-subtext"><?= e($item['dept'] ?? 'General') ?></span>
            </td>
            <td>
              <span class="text-dark small"><i class="bi bi-geo-alt text-muted me-1"></i><?= e($item['site']) ?></span>
            </td>
            <td>
              <span class="fw-medium text-dark"><?= e($item['when']) ?></span>
            </td>
            <td>
              <span class="text-muted"><?= e($item['purpose']) ?></span>
            </td>
            <td>
              <span class="badge bg-light text-dark border">
                <i class="bi bi-people-fill text-muted me-1"></i><?= (int)($item['group'] ?? 1) ?>
              </span>
            </td>
            <td>
              <?= badge($item['status']) ?>
            </td>
            <td class="text-end">
              <?= table_actions('appointments', $item['ref'], ['name' => $item['visitor'], 'qr' => $passUrl]) ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php 
require_once __DIR__ . '/../../includes/footer.php'; 
require_once __DIR__ . '/../../includes/footerscript.php'; 
?>

<?php
$pageTitle = 'Dashboard';
$activeNav = 'dashboard';
$needsCharts = true;

require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Site overview', 'A clear view of your people, visits, and daily operations.',
    can_see('appointments') && !is_readonly()
      ? '<a href="' . page_url('appointments', 'add') . '" class="btn btn-primary"><i class="bi bi-plus-lg me-2" aria-hidden="true"></i>New appointment</a>'
      : '');
?>

<section class="command-hero" aria-label="Site command overview">
  <div class="command-hero-copy">
    <span class="eyebrow"><i class="bi bi-compass" aria-hidden="true"></i> Your day, in perspective</span>
    <h2>Welcome back, <?= e(explode(' ', $currentUser['name'] ?? 'Ved')[0]) ?>.</h2>
    <p>Every arrival, every detail. Keep your people moving and your site connected at <?= e($currentSite) ?>.</p>
    <div class="hero-meta">
      <span><i class="bi bi-geo-alt" aria-hidden="true"></i> <?= e($currentSite) ?></span>
      <span><i class="bi bi-calendar3" aria-hidden="true"></i> <?= date('l, d M') ?></span>
    </div>
  </div>
  <div class="command-hero-visual" aria-hidden="true">
    <?php include __DIR__ . '/../../includes/site-illustration.php'; ?>
  </div>
</section>

<!-- Minimalist KPI Stat Grid -->
<div class="dashboard-section-label">People &amp; visits <span class="status status-muted">Sample data</span></div>
<div class="row g-3 mb-4">
  <?php
    stat_tile('People on site now', 27, 'people-fill', '14 visitors · 11 contractors · 2 delivery', 'info');
    stat_tile('Expected today', 38, 'calendar2-check', '12 arrived · 26 remaining', 'primary');
    stat_tile('Pending approvals', count($APPROVALS), 'hourglass-split', '1 escalated request', 'warning');
    stat_tile('Overdue visits', 3, 'clock-history', 'Exceeded pass validity limit', 'danger');
  ?>
</div>

<div class="dashboard-section-label">Materials &amp; workforce</div>
<div class="row g-3 mb-4">
  <?php
    stat_tile('Material out today', 9, 'box-arrow-up', '4 returnable items tracked', 'primary');
    stat_tile('Overdue returns', 2, 'arrow-return-left', 'OUT-0281 is 8 days overdue', 'danger');
    stat_tile('Contractor documents', 3, 'file-earmark-check', '1 expired · 2 expiring soon', 'warning');
    stat_tile('Vehicles on site', 6, 'truck', '1 permit verification pending', 'info');
  ?>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
  <div class="col-lg-8">
    <div class="panel h-100">
      <div class="panel-header">
        <h2 class="panel-title">Weekly Check-in Volume</h2>
        <div class="d-flex align-items-center gap-2">
          <span class="badge bg-light text-muted border">Gate 1 &amp; Gate 2</span>
        </div>
      </div>
      <div class="panel-body">
        <div style="height: 250px;">
          <canvas id="weekChart" aria-label="Check-ins by day" role="img"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4">
    <div class="panel h-100">
      <div class="panel-header">
        <h2 class="panel-title">On-Site Distribution</h2>
      </div>
      <div class="panel-body d-flex flex-column justify-content-center">
        <div style="height: 190px;">
          <canvas id="mixChart" aria-label="On-site breakdown" role="img"></canvas>
        </div>
        <div class="d-flex justify-content-around text-center mt-3 pt-2 border-top small">
          <div><div class="fw-bold text-dark">14</div><div class="text-muted">Visitors</div></div>
          <div><div class="fw-bold text-dark">11</div><div class="text-muted">Contractors</div></div>
          <div><div class="fw-bold text-dark">2</div><div class="text-muted">Deliveries</div></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Lower Sections: Live Occupancy & Approvals -->
<div class="row g-4">
  <div class="col-xl-7">
    <div class="panel h-100">
      <div class="panel-header">
        <div>
          <h2 class="panel-title">Currently Inside Premises</h2>
          <div class="text-muted small">Live count of checked-in visitors and contractors</div>
        </div>
        <a href="<?= page_url('gate', 'list') ?>" class="btn btn-sm btn-outline-slate">Open Gate Screen</a>
      </div>
      <div class="table-responsive">
        <table class="table-clean table">
          <thead>
            <tr>
              <th>Pass No.</th>
              <th>Visitor</th>
              <th>Host</th>
              <th>In At</th>
              <th>Duration</th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($ONSITE as $o): ?>
            <tr>
              <td><span class="ref-pill"><?= e($o['pass']) ?></span></td>
              <td>
                <div class="fw-semibold text-dark"><?= e($o['name']) ?></div>
                <span class="table-subtext"><?= e($o['company']) ?> · <?= e($o['type']) ?></span>
              </td>
              <td><span class="text-dark small"><?= e($o['host']) ?></span></td>
              <td><span class="text-muted small"><?= e($o['in']) ?></span></td>
              <td>
                <span class="badge bg-light text-dark border">
                  <i class="bi bi-clock me-1 text-muted"></i><?= e($o['duration']) ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-xl-5">
    <div class="panel mb-4">
      <div class="panel-header">
        <h2 class="panel-title">Pending Approvals Queue</h2>
        <a href="<?= page_url('approvals', 'list') ?>" class="btn btn-sm btn-outline-slate">View All (<?= count($APPROVALS) ?>)</a>
      </div>
      <div class="list-group list-group-flush">
        <?php foreach (array_slice($APPROVALS, 0, 4) as $a): ?>
          <div class="list-group-item px-3 py-3 d-flex align-items-center justify-content-between gap-2 border-bottom">
            <div>
              <div class="d-flex align-items-center gap-2 mb-1">
                <span class="ref-pill small"><?= e($a['id']) ?></span>
                <?= badge($a['due'] === 'escalated' ? 'Escalated' : $a['type']) ?>
              </div>
              <div class="text-dark fw-medium small"><?= e($a['subject']) ?></div>
              <div class="text-muted small">Requested by <?= e($a['requester']) ?> · <?= e($a['site']) ?></div>
            </div>
            <div class="text-end">
              <span class="badge bg-light text-muted border small"><?= e($a['age']) ?> ago</span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Security & Attention Alerts -->
    <div class="panel">
      <div class="panel-header">
        <h2 class="panel-title">Security &amp; Policy Alerts</h2>
      </div>
      <div class="list-group list-group-flush">
        <div class="list-group-item d-flex align-items-start gap-3 p-3">
          <i class="bi bi-shield-exclamation text-danger fs-5 mt-1"></i>
          <div>
            <div class="fw-semibold text-dark small">Watchlist Match Detected</div>
            <div class="text-muted small">Anil Pawar is listed on the watchlist and scheduled for visit on 28 Oct.</div>
          </div>
        </div>
        <div class="list-group-item d-flex align-items-start gap-3 p-3">
          <i class="bi bi-file-earmark-x text-warning fs-5 mt-1"></i>
          <div>
            <div class="fw-semibold text-dark small">Contractor Safety Induction Expired</div>
            <div class="text-muted small">Apex Scaffolding work order expired on 12 Oct. Re-verification required.</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
$weekLabels = json_encode(array_keys($VISIT_TREND));
$weekVals = json_encode(array_values($VISIT_TREND));

require_once __DIR__ . '/../../includes/footer.php';

$extraScripts = <<<JS
<script>
document.addEventListener('DOMContentLoaded', () => {
  if (window.Chart) {
    new Chart(document.getElementById('weekChart'), {
      type: 'bar',
      data: {
        labels: $weekLabels,
        datasets: [{
          label: 'Total Check-ins',
          data: $weekVals,
          backgroundColor: '#367f70',
          borderRadius: 6,
          maxBarThickness: 36
        }]
      },
      options: {
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
          x: { grid: { display: false } }
        }
      }
    });

    new Chart(document.getElementById('mixChart'), {
      type: 'doughnut',
      data: {
        labels: ['Visitors', 'Contractors', 'Deliveries'],
        datasets: [{
          data: [14, 11, 2],
          backgroundColor: ['#367f70', '#8bb6aa', '#dfbd7e'],
          borderWidth: 2,
          borderColor: '#ffffff'
        }]
      },
      options: {
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: { legend: { display: false } }
      }
    });
  }
});
</script>
JS;

require_once __DIR__ . '/../../includes/footerscript.php';
?>

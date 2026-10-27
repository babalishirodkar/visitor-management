<?php
/* header.php – <body> opening, minimalist modern sidebar, and clean topbar. */
require_once __DIR__ . '/headerscript.php';

$activeNav = $activeNav ?? 'dashboard';
$groups = [];
foreach ($NAV as $key => $item) { 
    if (can_see($key)) {
        $groups[$item[3]][$key] = $item; 
    }
}
$pendingApprovalsCount = count($APPROVALS ?? []);
$currentUser = get_current_user_info();
?>
<body data-module="<?= e($activeNav) ?>">
<a class="skip-link" href="#main">Skip to main content</a>

<div class="app-layout">
  <!-- Minimalist Modern Sidebar -->
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-label="Main navigation">
    <div class="sidebar-brand">
      <div class="brand-icon">
        <i class="bi bi-shield-fill-check"></i>
      </div>
      <div class="brand-text">
        <span class="brand-title">Gate<span>Desk</span></span>
        <span class="brand-badge">Visitor management</span>
      </div>
      <button class="btn-close d-lg-none ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Close navigation"></button>
    </div>

    <div class="sidebar-nav-container">
      <nav class="sidebar-nav">
        <?php foreach ($groups as $groupLabel => $items): ?>
          <div class="nav-section-label"><?= e($groupLabel) ?></div>
          <?php foreach ($items as $key => $item): 
            $isActive = ($key === $activeNav);
            $href = nav_url($item[1]);
          ?>
            <a class="nav-item-link <?= $isActive ? 'active' : '' ?>" href="<?= e($href) ?>" <?= $isActive ? 'aria-current="page"' : '' ?>>
              <i class="bi bi-<?= e($item[2]) ?> nav-item-icon"></i>
              <span class="nav-item-text"><?= e($item[0]) ?></span>
              <?php if ($key === 'approvals' && $pendingApprovalsCount > 0): ?>
                <span class="badge-pill bg-warning-subtle text-warning-emphasis ms-auto"><?= $pendingApprovalsCount ?></span>
              <?php endif; ?>
            </a>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </nav>
    </div>

    <div class="sidebar-footer">
      <div class="user-card-mini">
        <div class="user-avatar-mini">
          <?= strtoupper(substr($currentUser['name'] ?? 'V', 0, 1)) ?>
        </div>
        <div class="user-info-mini">
          <div class="user-name-mini"><?= e($currentUser['name'] ?? 'Ved Karmalkar') ?></div>
          <div class="user-role-mini"><?= e($currentUser['email'] ?? 'vedkarmalkar@gmail.com') ?></div>
        </div>
        <a href="<?= url('logout.php') ?>" class="btn-logout-mini" title="Sign out" aria-label="Sign out" data-bs-toggle="tooltip" data-bs-placement="top">
          <i class="bi bi-box-arrow-right"></i>
        </a>
      </div>
    </div>
  </aside>

  <!-- Content Wrap -->
  <div class="main-wrapper">
    <!-- Topbar -->
    <header class="topbar">
      <div class="d-flex align-items-center gap-3">
        <button class="btn btn-icon d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Toggle navigation">
          <i class="bi bi-list"></i>
        </button>

        <!-- Site Switcher -->
        <div class="dropdown">
          <button class="site-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-geo-alt text-primary"></i>
            <span class="site-name"><?= e($currentSite) ?></span>
          </button>
          <ul class="dropdown-menu shadow-sm border-0 site-menu">
            <li class="dropdown-header text-uppercase small fw-semibold text-muted px-3 py-1">Locations</li>
            <?php foreach ($SITES as $s): ?>
              <li>
                <a class="dropdown-item py-2 px-3 <?= $s === $currentSite ? 'active' : '' ?>" href="?site=<?= urlencode($s) ?>">
                  <i class="bi bi-building me-2 <?= $s === $currentSite ? 'text-primary' : 'text-muted' ?>"></i><?= e($s) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>

      <!-- Center Search -->
      <button type="button" class="workspace-search d-none d-md-flex" data-bs-toggle="modal" data-bs-target="#workspaceSearch" aria-label="Search workspaces">
        <i class="bi bi-search" aria-hidden="true"></i><span>Jump to a workspace...</span><kbd>Ctrl K</kbd>
      </button>

      <!-- Right Actions -->
      <div class="topbar-actions ms-auto d-flex align-items-center gap-2">
        <button type="button" class="btn btn-icon d-md-none" data-bs-toggle="modal" data-bs-target="#workspaceSearch" aria-label="Search workspaces"><i class="bi bi-search" aria-hidden="true"></i></button>
        <div class="system-live d-none d-xl-flex" title="Preview workspace with sample data">
          <span class="live-pulse"></span><span>Demo workspace</span>
        </div>
        <!-- Notifications -->
        <a href="<?= page_url('notifications', 'list') ?>" class="btn btn-icon position-relative" aria-label="Notifications" title="Notifications">
          <i class="bi bi-bell"></i>
          <span class="unread-dot"></span>
        </a>

        <!-- Role Preview & Profile Dropdown -->
        <div class="dropdown">
          <button class="profile-dropdown-btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" aria-label="Profile and role menu">
            <div class="avatar-badge">
              <?= strtoupper(substr($currentUser['name'] ?? 'V', 0, 1)) ?>
            </div>
            <div class="d-none d-md-block text-start lh-1">
              <span class="profile-name"><?= e($currentUser['name'] ?? 'Ved Karmalkar') ?></span>
              <span class="profile-role"><?= e($ROLES[$currentRole] ?? 'Admin') ?></span>
            </div>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 profile-menu py-2">
            <li class="px-3 py-2 border-bottom">
              <div class="fw-semibold text-dark"><?= e($currentUser['name'] ?? 'Ved Karmalkar') ?></div>
              <div class="text-muted small"><?= e($currentUser['email'] ?? 'vedkarmalkar@gmail.com') ?></div>
            </li>
            <li class="dropdown-header text-uppercase small fw-semibold text-muted px-3 pt-2">Role simulation</li>
            <?php foreach ($ROLES as $roleKey => $roleName): ?>
              <li>
                <a class="dropdown-item py-1 px-3 small <?= $roleKey === $currentRole ? 'active' : '' ?>" href="?role=<?= e($roleKey) ?>">
                  <?= e($roleName) ?>
                </a>
              </li>
            <?php endforeach; ?>
            <li class="dropdown-divider"></li>
            <li>
              <a class="dropdown-item py-2 px-3 text-danger d-flex align-items-center gap-2" href="<?= url('logout.php') ?>">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign out</span>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </header>

    <?php
      $blocked = isset($NAV[$activeNav]) && !can_see($activeNav);
    ?>
    <main id="main" class="main-content" tabindex="-1">
      <?php if (is_readonly()): ?>
        <div class="alert alert-info d-flex align-items-center gap-2 border-0 shadow-sm mb-4" role="alert">
          <i class="bi bi-info-circle-fill fs-5"></i>
          <div>Viewing as <strong>Auditor / Management</strong>. All records are displayed in read-only mode.</div>
        </div>
      <?php endif; ?>

      <?php if ($blocked): ?>
        <div class="empty-state-card text-center py-5">
          <div class="empty-icon-wrap mx-auto mb-3">
            <i class="bi bi-shield-lock text-muted fs-1"></i>
          </div>
          <h2 class="h4 fw-bold mb-2">Access Restricted</h2>
          <p class="text-muted max-w-md mx-auto mb-4">
            The role <strong><?= e($ROLES[$currentRole] ?? $currentRole) ?></strong> is not authorized to view the <?= e($NAV[$activeNav][0] ?? 'this page') ?> module.
          </p>
          <a href="<?= page_url('dashboard', 'list') ?>" class="btn btn-primary rounded-pill px-4">
            Return to Dashboard
          </a>
        </div>
      <?php
        require __DIR__ . '/footer.php';
        require __DIR__ . '/footerscript.php';
        exit;
      endif; ?>

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/data.php';

/* ---- Static credentials ---- */
define('STATIC_USER_EMAIL', 'vedkarmalkar@gmail.com');
define('STATIC_USER_PASS', 'Admin@123');
define('STATIC_USER_NAME', 'Ved Karmalkar');

/* ---- Auth helpers ---- */
function is_authenticated(): bool {
    return !empty($_SESSION['auth_user']);
}

function get_current_user_info(): array {
    if (is_authenticated()) {
        return $_SESSION['auth_user'];
    }
    return [
        'name'  => STATIC_USER_NAME,
        'email' => STATIC_USER_EMAIL,
        'role'  => $_SESSION['role'] ?? 'super_admin',
    ];
}

function require_auth(): void {
    if (!is_authenticated()) {
        $loginUrl = url('login.php');
        header("Location: {$loginUrl}");
        exit;
    }
}

function login_user(string $email, string $password): bool {
    if (trim($email) === STATIC_USER_EMAIL && $password === STATIC_USER_PASS) {
        $_SESSION['auth_user'] = [
            'name'  => STATIC_USER_NAME,
            'email' => STATIC_USER_EMAIL,
            'role'  => 'super_admin',
        ];
        $_SESSION['role'] = 'super_admin';
        return true;
    }
    return false;
}

function logout_user(): void {
    $_SESSION['auth_user'] = null;
    unset($_SESSION['auth_user']);
    session_destroy();
}

/* ---- Path & URL helpers using SITE_URL from config.php ---- */
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    return SITE_URL . $path;
}

function asset_url(string $path): string {
    $path = ltrim($path, '/');
    return SITE_URL . $path;
}

function versioned_asset(string $path): string {
    $path = ltrim($path, '/');
    $file = BASE_PATH . '/' . $path;
    $modified = filemtime($file);
    // Apache's assets/.htaccess declares the encoding and original MIME type.
    // Other servers and stale/missing compressed artifacts use the source file.
    $apache = stripos($_SERVER['SERVER_SOFTWARE'] ?? '', 'Apache') !== false;
    $acceptsGzip = false;
    foreach (explode(',', $_SERVER['HTTP_ACCEPT_ENCODING'] ?? '') as $encoding) {
        $parts = explode(';', strtolower(trim($encoding)), 2);
        if ($parts[0] === 'gzip' && (!isset($parts[1]) || !preg_match('/q\s*=\s*0(?:\.0*)?\s*$/', $parts[1]))) {
            $acceptsGzip = true;
        }
    }
    if ($apache && $acceptsGzip && is_file($file . '.gz') && filemtime($file . '.gz') >= $modified) {
        $path .= '.gz';
    }
    return asset_url($path) . '?v=' . $modified;
}

function page_url(string $module, string $action = 'list', array $params = []): string {
    if (str_contains($module, '.php')) {
        $clean = ltrim(str_replace(['../', 'pages/'], '', $module), '/');
        $target = "pages/{$clean}";
    } else {
        $target = "pages/{$module}/{$module}-{$action}.php";
    }
    $targetUrl = SITE_URL . ltrim($target, '/');
    if (!empty($params)) {
        $targetUrl .= '?' . http_build_query($params);
    }
    return $targetUrl;
}

/* ---- Role switcher (demo support) ---- */
if (isset($_GET['role']) && isset($ROLES[$_GET['role']])) { 
    $_SESSION['role'] = $_GET['role']; 
}
if (isset($_GET['site']) && in_array($_GET['site'], $SITES, true)) { 
    $_SESSION['site'] = $_GET['site']; 
}
$currentRole = $_SESSION['role'] ?? 'super_admin';
$currentSite = $_SESSION['site'] ?? $SITES[0];

/* ---- Navigation: key => [label, target_page, icon, group, roles ('*' = everyone)] ---- */
$ALL = '*';
$NAV = [
    'dashboard'     => ['Dashboard', 'pages/dashboard/dashboard-list.php', 'grid-1x2', 'Overview', $ALL],
    'gate'          => ['Gate operations', 'pages/gate/gate-list.php', 'shield-check', 'Gate & visitors', ['super_admin','org_admin','security','receptionist','auditor']],
    'appointments'  => ['Appointments', 'pages/appointments/appointments-list.php', 'calendar2-check', 'Gate & visitors', ['super_admin','org_admin','receptionist','host','approver','security','auditor']],
    'walkin'        => ['Walk-in registration', 'pages/walkin/walkin-list.php', 'person-plus', 'Gate & visitors', ['super_admin','org_admin','receptionist','security']],
    'approvals'     => ['Approvals', 'pages/approvals/approvals-list.php', 'check2-circle', 'Gate & visitors', ['super_admin','org_admin','host','approver','stores','contractor_mgr','auditor']],
    'passes'        => ['Gate passes & badges', 'pages/passes/passes-list.php', 'qr-code-scan', 'Gate & visitors', ['super_admin','org_admin','receptionist','security','host','auditor']],
    'visitors'      => ['Visitor master', 'pages/visitors/visitors-list.php', 'people', 'Gate & visitors', ['super_admin','org_admin','receptionist','security','auditor']],
    'watchlist'     => ['Watchlist', 'pages/watchlist/watchlist-list.php', 'shield-exclamation', 'Gate & visitors', ['super_admin','org_admin','security','auditor']],
    'inward'        => ['Material inward', 'pages/material-inward/material-inward-list.php', 'box-arrow-in-down', 'Materials', ['super_admin','org_admin','stores','security','approver','auditor']],
    'outward'       => ['Material outward', 'pages/material-outward/material-outward-list.php', 'box-arrow-up', 'Materials', ['super_admin','org_admin','stores','security','approver','host','auditor']],
    'returns'       => ['Returnable items', 'pages/returns/returns-list.php', 'arrow-return-left', 'Materials', ['super_admin','org_admin','stores','approver','auditor']],
    'contractors'   => ['Contractors', 'pages/contractors/contractors-list.php', 'cone-striped', 'Workforce & safety', ['super_admin','org_admin','contractor_mgr','security','ehs','auditor']],
    'safety'        => ['Safety & restricted', 'pages/safety/safety-list.php', 'shield-lock', 'Workforce & safety', ['super_admin','org_admin','ehs','security','auditor']],
    'vehicles'      => ['Vehicles & parking', 'pages/vehicles/vehicles-list.php', 'truck', 'Workforce & safety', ['super_admin','org_admin','security','auditor']],
    'reports'       => ['Reports', 'pages/reports/reports-list.php', 'bar-chart-line', 'Insights', ['super_admin','org_admin','auditor','approver','stores','ehs']],
    'notifications' => ['Notifications', 'pages/notifications/notifications-list.php', 'bell', 'Insights', ['super_admin','org_admin','receptionist','host','approver','security','stores','contractor_mgr','ehs']],
    'audit'         => ['Audit log', 'pages/audit/audit-list.php', 'journal-text', 'Insights', ['super_admin','org_admin','auditor']],
    'organization'  => ['Organization & sites', 'pages/organization/organization-list.php', 'buildings', 'Administration', ['super_admin','org_admin']],
    'users'         => ['Users & permissions', 'pages/users/users-list.php', 'person-gear', 'Administration', ['super_admin','org_admin']],
];

function nav_url(string $target): string {
    return url($target);
}

function can_see(string $key): bool {
    global $NAV, $currentRole;
    if (!isset($NAV[$key])) return true;
    $r = $NAV[$key][4];
    return $r === '*' || in_array($currentRole, $r, true);
}

function is_readonly(): bool { 
    global $currentRole; 
    return $currentRole === 'auditor'; 
}

/* ---- Session Appointments CRUD ---- */
function init_appointments(): void {
    global $APPOINTMENTS;
    if (!isset($_SESSION['appointments'])) {
        $_SESSION['appointments'] = $APPOINTMENTS;
    }
}
init_appointments();

function get_all_appointments(): array {
    return $_SESSION['appointments'] ?? [];
}

function get_appointment_by_ref(string $ref): ?array {
    $list = get_all_appointments();
    foreach ($list as $item) {
        if (strcasecmp($item['ref'], $ref) === 0) {
            return $item;
        }
    }
    return null;
}

function save_new_appointment(array $data): string {
    if (!isset($_SESSION['appointments'])) {
        init_appointments();
    }
    $newRef = 'APT-26-' . sprintf('%04d', rand(1000, 9999));
    $record = array_merge([
        'ref'         => $newRef,
        'visitor'     => $data['visitor'] ?? 'Guest',
        'company'     => $data['company'] ?? 'Independent',
        'category'    => $data['category'] ?? 'Visitor',
        'email'       => $data['email'] ?? '',
        'phone'       => $data['phone'] ?? '',
        'host'        => $data['host'] ?? 'Sneha Kapoor',
        'dept'        => $data['dept'] ?? 'Administration',
        'site'        => $data['site'] ?? 'Pune Plant',
        'when'        => $data['when'] ?? date('d M, H:i'),
        'purpose'     => $data['purpose'] ?? 'General meeting',
        'status'      => 'Pending approval',
        'group'       => (int)($data['group'] ?? 1),
        'vehicle'     => $data['vehicle'] ?? '',
        'vehicle_type'=> $data['vehicle_type'] ?? '',
        'notes'       => $data['notes'] ?? '',
        'created_at'  => date('d M Y, H:i'),
    ], $data);
    $record['ref'] = $newRef;

    array_unshift($_SESSION['appointments'], $record);
    return $newRef;
}

function update_appointment_record(string $ref, array $data): bool {
    if (!isset($_SESSION['appointments'])) return false;
    foreach ($_SESSION['appointments'] as $idx => $item) {
        if (strcasecmp($item['ref'], $ref) === 0) {
            $_SESSION['appointments'][$idx] = array_merge($item, $data);
            return true;
        }
    }
    return false;
}

/* ---- UI helpers ---- */
function e($s) { 
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); 
}

function badge(string $status): string {
    $map = [
        'approved'           => ['cls' => 'status-ok',     'icon' => 'check-circle-fill'],
        'released'           => ['cls' => 'status-ok',     'icon' => 'check-circle-fill'],
        'received'           => ['cls' => 'status-ok',     'icon' => 'check-circle-fill'],
        'valid'              => ['cls' => 'status-ok',     'icon' => 'shield-check'],
        'complete'           => ['cls' => 'status-ok',     'icon' => 'check-circle-fill'],
        'closed'             => ['cls' => 'status-ok',     'icon' => 'check2'],
        'inside'             => ['cls' => 'status-ok',     'icon' => 'door-open-fill'],
        'checked in'         => ['cls' => 'status-ok',     'icon' => 'check2'],
        'active'             => ['cls' => 'status-ok',     'icon' => 'record-fill'],
        'on time'            => ['cls' => 'status-neutral','icon' => 'clock'],
        'allowed'            => ['cls' => 'status-ok',     'icon' => 'check-circle'],
        
        'pending approval'   => ['cls' => 'status-warn',   'icon' => 'hourglass-split'],
        'pending'            => ['cls' => 'status-warn',   'icon' => 'hourglass-split'],
        'open'               => ['cls' => 'status-warn',   'icon' => 'circle'],
        'partial'            => ['cls' => 'status-warn',   'icon' => 'pie-chart-fill'],
        'rescheduled'        => ['cls' => 'status-info',   'icon' => 'calendar-range'],
        'reminder sent'      => ['cls' => 'status-warn',   'icon' => 'bell-fill'],
        'info requested'     => ['cls' => 'status-info',   'icon' => 'info-circle-fill'],
        'warn only'          => ['cls' => 'status-warn',   'icon' => 'exclamation-circle-fill'],
        'outside'            => ['cls' => 'status-neutral','icon' => 'geo-alt'],
        
        'overdue'            => ['cls' => 'status-bad',    'icon' => 'exclamation-octagon-fill'],
        'expired'            => ['cls' => 'status-bad',    'icon' => 'x-circle-fill'],
        'blocked'            => ['cls' => 'status-bad',    'icon' => 'slash-circle-fill'],
        'no-show'            => ['cls' => 'status-bad',    'icon' => 'person-x-fill'],
        'escalated'          => ['cls' => 'status-bad',    'icon' => 'arrow-up-right-circle-fill'],
        'deny entry'         => ['cls' => 'status-bad',    'icon' => 'dash-circle-fill'],
        'received – shortage'=> ['cls' => 'status-bad',    'icon' => 'dash-square-fill'],
        'received – damage'  => ['cls' => 'status-bad',    'icon' => 'exclamation-triangle-fill'],
        'revoked'            => ['cls' => 'status-bad',    'icon' => 'shield-x'],
        
        'expiring in 20 days'=> ['cls' => 'status-warn',   'icon' => 'clock-history'],
        'used'               => ['cls' => 'status-neutral','icon' => 'check2-all'],
        'cancelled'          => ['cls' => 'status-neutral','icon' => 'x-circle'],
    ];
    $k = strtolower($status);
    $item = $map[$k] ?? ['cls' => (str_starts_with($k, 'pending') ? 'status-warn' : 'status-neutral'), 'icon' => 'record-fill'];
    return '<span class="status-badge ' . $item['cls'] . '"><i class="bi bi-' . $item['icon'] . '"></i>' . e($status) . '</span>';
}

function page_header(string $title, string $subtitle = '', string $actions = '', array $breadcrumbs = []): void {
    global $NAV, $activeNav;
    $module = $NAV[$activeNav ?? 'dashboard'] ?? null;
    echo '<div class="page-head-container">';
    echo '<div class="page-eyebrow"><span class="page-module-icon" aria-hidden="true"><i class="bi bi-' . e($module[2] ?? 'grid') . '"></i></span>' . e($module[3] ?? 'Workspace') . '<span class="eyebrow-divider"></span>GateDesk workspace</div>';
    if (!empty($breadcrumbs)) {
        echo '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-2">';
        foreach ($breadcrumbs as $crumb) {
            if (!empty($crumb['url'])) {
                echo '<li class="breadcrumb-item"><a href="' . e($crumb['url']) . '">' . e($crumb['label']) . '</a></li>';
            } else {
                echo '<li class="breadcrumb-item active" aria-current="page">' . e($crumb['label']) . '</li>';
            }
        }
        echo '</ol></nav>';
    }
    echo '<div class="page-head">';
    echo '<div><h1 class="page-title">' . e($title) . '</h1>';
    if ($subtitle) echo '<p class="page-subtitle">' . e($subtitle) . '</p>';
    echo '</div><div class="page-actions">' . $actions . '</div></div></div>';
}

function action_btn(string $label, string $icon, string $target = '', string $cls = 'btn-primary'): string {
    if (is_readonly()) return '';
    $t = $target ? ' data-bs-toggle="modal" data-bs-target="#' . e($target) . '"' : '';
    return '<button class="btn ' . $cls . '"' . $t . '><i class="bi bi-' . $icon . ' me-1"></i>' . e($label) . '</button>';
}

function stat_tile(string $label, $value, string $icon, string $note = '', string $tone = 'primary'): void {
    $tone = ['warn' => 'warning', 'alert' => 'danger', 'good' => 'success'][$tone] ?? $tone;
    $toneClass = 'stat-' . $tone;
    echo '<div class="col-6 col-xl-3"><div class="stat-card ' . e($toneClass) . '">';
    echo '<div class="stat-icon-wrap" aria-hidden="true"><i class="bi bi-' . e($icon) . '"></i></div>';
    echo '<div class="stat-info">';
    echo '<div class="stat-value">' . e($value) . '</div>';
    echo '<div class="stat-label">' . e($label) . '</div>';
    if ($note) echo '<div class="stat-note">' . e($note) . '</div>';
    echo '</div></div></div>';
}

function export_btns(): string {
    return '<div class="btn-group">'
         . '<button class="btn btn-outline-slate dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-download me-1"></i>Export</button>'
         . '<ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">'
         . '<li><a class="dropdown-item py-2" href="#" data-toast="Exporting Excel (.xlsx)..."><i class="bi bi-file-earmark-excel text-success me-2"></i>Excel (.xlsx)</a></li>'
         . '<li><a class="dropdown-item py-2" href="#" data-toast="Exporting CSV..."><i class="bi bi-file-earmark-text text-primary me-2"></i>CSV</a></li>'
         . '<li><a class="dropdown-item py-2" href="#" data-toast="Generating PDF report..."><i class="bi bi-file-earmark-pdf text-danger me-2"></i>PDF Document</a></li>'
         . '</ul></div>';
}

function table_tools(string $tableId, string $placeholder = 'Search this list'): void {
    echo '<div class="table-filter-bar"><div class="table-filter-search"><i class="bi bi-search" aria-hidden="true"></i><input type="search" class="form-control" aria-label="' . e($placeholder) . '" placeholder="' . e($placeholder) . '" data-filter-table="' . e($tableId) . '"></div></div>';
}

function modal_open(string $id, string $title, string $size = 'modal-lg', string $submit = 'Save', string $toast = 'Saved'): void {
    echo '<div class="modal fade" id="' . e($id) . '" tabindex="-1" aria-hidden="true" aria-labelledby="' . e($id) . '-title">';
    echo '<div class="modal-dialog ' . e($size) . ' modal-dialog-scrollable">';
    echo '<form class="modal-content needs-validation" novalidate data-toast="' . e($toast) . '">';
    echo '<div class="modal-header"><h2 id="' . e($id) . '-title" class="modal-title h5 mb-0 fw-semibold">' . e($title) . '</h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body">';
}

function modal_close(string $submit = 'Save'): void {
    echo '</div><div class="modal-footer"><button type="button" class="btn btn-outline-slate" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">' . e($submit) . '</button></div></form></div></div>';
}

function table_actions(string $module, string $id, array $options = []): string {
    $canEdit = !is_readonly();
    $viewUrl = page_url($module, 'add', ['id' => $id, 'mode' => 'view']);
    $editUrl = page_url($module, 'add', ['id' => $id, 'mode' => 'edit']);
    
    $html = '<div class="d-inline-flex gap-1 align-items-center justify-content-end">';
    
    // View icon (Blue)
    $html .= '<a href="' . e($viewUrl) . '" class="btn-action-icon view" title="View details" data-bs-toggle="tooltip"><i class="bi bi-eye"></i></a>';
    
    // Edit icon (Green)
    if ($canEdit) {
        $html .= '<a href="' . e($editUrl) . '" class="btn-action-icon edit" title="Edit record" data-bs-toggle="tooltip"><i class="bi bi-pencil"></i></a>';
    }
    
    // Pass / QR (Purple, optional)
    if (!empty($options['qr'])) {
        $qrUrl = is_string($options['qr']) ? $options['qr'] : page_url('passes', 'list', ['ref' => $id]);
        $html .= '<a href="' . e($qrUrl) . '" class="btn-action-icon qr" title="Gate pass / QR" data-bs-toggle="tooltip"><i class="bi bi-qr-code"></i></a>';
    }
    
    // Delete icon (Red)
    if ($canEdit) {
        $name = e($options['name'] ?? $id);
        $html .= '<button type="button" class="btn-action-icon delete" title="Delete" aria-label="Delete ' . $name . '" data-delete-name="' . $name . '" data-bs-toggle="tooltip"><i class="bi bi-trash"></i></button>';
    }
    
    $html .= '</div>';
    return $html;
}

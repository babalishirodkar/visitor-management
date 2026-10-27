<?php
session_start();
$isPublicPage = true;
require_once __DIR__ . '/includes/helpers.php';

// If already logged in, redirect to dashboard or appointments
if (is_authenticated()) {
    header('Location: ' . page_url('dashboard', 'list'));
    exit;
}

$errorMsg = '';
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $emailValue = $email;

    if (login_user($email, $password)) {
        header('Location: ' . page_url('appointments', 'list'));
        exit;
    } else {
        $errorMsg = 'Invalid email or password. Please check your credentials.';
    }
}

$loggedOut = isset($_GET['logged_out']);
$pageTitle = 'Sign in';
require __DIR__ . '/includes/headerscript.php';
?>
<body class="login-page">

<main class="login-shell">
  <section class="login-showcase" aria-label="GateDesk introduction">
    <div class="showcase-art" aria-hidden="true"><?php include __DIR__ . '/includes/site-illustration.php'; ?></div>
    <div class="showcase-brand"><div class="login-brand-icon m-0"><i class="bi bi-shield-fill-check"></i></div><div>GateDesk<small>Secure operations</small></div></div>
    <div class="showcase-copy">
      <h2>Every arrival.<br><em>A better welcome.</em></h2>
      <p>Bring people, places, and processes together. Your workspace for welcoming visitors and keeping site operations in view.</p>
    </div>
    <div class="trust-row"><span><i class="bi bi-shield-check me-1"></i> Enterprise security</span><span><i class="bi bi-lightning-charge me-1"></i> Real-time operations</span><span><i class="bi bi-patch-check me-1"></i> Audit ready</span></div>
  </section>
  <section class="login-panel">
<div class="login-container">
  <div class="login-card">
    <div class="login-heading">
      <span class="page-eyebrow">YOUR WORKDAY STARTS HERE</span>
      <h1>Welcome back</h1>
      <p class="text-muted small mb-0">Sign in to your visitor management workspace.</p>
    </div>

    <?php if ($loggedOut): ?>
      <div class="alert alert-success d-flex align-items-center gap-2 py-2 px-3 small border-0 mb-3" role="alert">
        <i class="bi bi-check-circle-fill text-success fs-6"></i>
        <div>You have signed out successfully.</div>
      </div>
    <?php endif; ?>

    <?php if (!empty($errorMsg)): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 py-2 px-3 small border-0 mb-3" role="alert">
        <i class="bi bi-exclamation-triangle-fill text-danger fs-6"></i>
        <div><?= e($errorMsg) ?></div>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="needs-validation" novalidate id="loginForm">
      <div class="mb-3">
        <label for="email" class="form-label">Email address</label>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
          <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" autocomplete="username" 
                 value="<?= e($emailValue ?: 'vedkarmalkar@gmail.com') ?>" placeholder="name@company.com" required autofocus>
        </div>
      </div>

      <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-1">
          <label for="password" class="form-label mb-0">Password</label>
          <a href="#demoAccess" class="small">Demo access</a>
        </div>
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-lock"></i></span>
          <input type="password" class="form-control border-start-0 border-end-0 ps-0" id="password" name="password" autocomplete="current-password" 
                 value="Admin@123" placeholder="Enter password" required>
          <button class="btn btn-outline-slate border-start-0" type="button" id="togglePasswordBtn" aria-label="Show password" aria-pressed="false" aria-controls="password">
            <i class="bi bi-eye text-muted" id="togglePasswordIcon"></i>
          </button>
        </div>
      </div>

      <div class="d-flex align-items-center justify-content-between mb-4">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" id="rememberMe" checked>
          <label class="form-check-label small text-muted" for="rememberMe">Remember me</label>
        </div>
      </div>

      <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
        Sign in to Portal <i class="bi bi-arrow-right ms-2" aria-hidden="true"></i>
      </button>
    </form>
    <div id="demoAccess" class="login-demo-note"><i class="bi bi-info-circle" aria-hidden="true"></i><span>Exploring GateDesk? Demo credentials are filled in for you. Sign in to preview the workspace and its roles.</span></div>
  </div>

  <div class="login-footer">
    &copy; <?= date('Y') ?> GateDesk Enterprise · Visitor Management System
  </div>
</div>

</section>
</main>

<?php require __DIR__ . '/includes/footerscript.php'; ?>

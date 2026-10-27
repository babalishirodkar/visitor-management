<?php /* footer.php – closes main-content, wrapper, toast container. */ ?>
    </main>
    <footer class="app-footer">
      <div class="footer-left">
        <span class="footer-brand">GateDesk</span>
        <span class="footer-dot">·</span>
        <span class="footer-desc">Enterprise Visitor Management &amp; Gate Pass</span>
      </div>
      <div class="footer-right">
        <span>Site: <strong class="text-dark"><?= e($currentSite) ?></strong></span>
        <span class="footer-dot">·</span>
        <span><?= date('D, d M Y') ?></span>
      </div>
    </footer>
  </div>
</div>

<div class="modal fade workspace-modal" id="workspaceSearch" tabindex="-1" aria-labelledby="workspaceSearchTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <div><span class="page-eyebrow">YOUR WORKSPACE</span><h2 class="modal-title h5" id="workspaceSearchTitle">Where would you like to go?</h2></div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close search"></button>
      </div>
      <div class="workspace-search-field"><i class="bi bi-search" aria-hidden="true"></i><input type="search" id="workspaceQuery" class="form-control" placeholder="Try visitors, materials, reports..." aria-label="Find a workspace" aria-controls="workspaceResults" autocomplete="off"></div>
      <div id="workspaceResults" class="workspace-results">
        <?php foreach ($NAV as $key => $item): if (!can_see($key)) continue; ?>
        <a class="workspace-result" href="<?= e(nav_url($item[1])) ?>"><span class="workspace-result-icon"><i class="bi bi-<?= e($item[2]) ?>" aria-hidden="true"></i></span><span><strong><?= e($item[0]) ?></strong><small><?= e($item[3]) ?></small></span><i class="bi bi-arrow-up-right ms-auto" aria-hidden="true"></i></a>
        <?php endforeach; ?>
      </div>
      <p class="workspace-empty text-center p-4" hidden>No workspaces found. Try a different name.</p>
      <div class="workspace-search-footer"><span id="workspaceCount" role="status" aria-live="polite">Search the workspaces available to your role</span><span><kbd>Esc</kbd> to close</span></div>
    </div>
  </div>
</div>

<!-- Modern Minimalist Global Toast -->
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
  <div id="appToast" class="toast custom-toast border-0 shadow-lg" role="alert" aria-live="polite" aria-atomic="true">
    <div class="d-flex align-items-center p-3">
      <div class="toast-icon-wrap me-3">
        <i class="bi bi-check-circle-fill text-success fs-5"></i>
      </div>
      <div class="toast-body p-0 flex-grow-1">
        <div class="toast-title fw-semibold text-dark mb-0" id="appToastTitle">Notification</div>
        <div class="toast-msg small text-muted" id="appToastText">Action completed successfully</div>
      </div>
      <button type="button" class="btn-close ms-2" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

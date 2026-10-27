/* GateDesk shared interactions. No runtime dependencies beyond bundled Bootstrap. */
(() => {
  'use strict';
  const $ = (selector, root = document) => root.querySelector(selector);
  const $$ = (selector, root = document) => Array.from(root.querySelectorAll(selector));
  const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
  const normalize = value => value.replace(/\s+/g, ' ').trim().toLowerCase();

  // Observe only independent surfaces; never hide a large table or nest reveals.
  if (!motion.matches && 'IntersectionObserver' in window) {
    const observer = new IntersectionObserver(entries => {
      entries.forEach(({ target, isIntersecting }) => {
        if (!isIntersecting) return;
        target.classList.add('reveal-once');
        observer.unobserve(target);
      });
    }, { threshold: 0, rootMargin: '0px 0px -16px' });
    $$('.command-hero, .stat-card, .main-content > .panel, .main-content > .detail-card').forEach(el => observer.observe(el));
    window.addEventListener('pagehide', () => observer.disconnect(), { once: true });
  }

  // Storage may be unavailable in private browsing. Navigation must still work.
  const sidebar = $('.sidebar-nav-container');
  if (sidebar) {
    try { sidebar.scrollTop = Number(sessionStorage.getItem('gatedesk.sidebar.scrollTop')) || 0; } catch (_) { /* optional */ }
    const active = $('.nav-item-link.active', sidebar);
    if (active) {
      const item = active.getBoundingClientRect(), view = sidebar.getBoundingClientRect();
      if (item.top < view.top || item.bottom > view.bottom) sidebar.scrollTop += item.top - view.top - view.height / 2;
    }
    window.addEventListener('pagehide', () => {
      try { sessionStorage.setItem('gatedesk.sidebar.scrollTop', String(sidebar.scrollTop)); } catch (_) { /* optional */ }
    });
  }

  window.toast = (message, title = 'Notification') => {
    const el = $('#appToast');
    if (!el) return;
    $('#appToastTitle').textContent = title;
    $('#appToastText').textContent = message;
    bootstrap.Toast.getOrCreateInstance(el, { delay: 4500 }).show();
  };

  // All controls for a table are evaluated together. Row text is cached until it changes.
  const tableGroups = new Map();
  $$('[data-filter-table]').forEach(control => {
    const id = control.dataset.filterTable;
    if (!tableGroups.has(id)) tableGroups.set(id, []);
    tableGroups.get(id).push(control);
  });
  tableGroups.forEach((controls, id) => {
    const table = document.getElementById(id);
    const tbody = table?.tBodies[0];
    if (!tbody) return;
    const empty = document.createElement('tr');
    empty.className = 'filter-empty';
    empty.hidden = true;
    const cell = empty.insertCell();
    cell.colSpan = table.tHead?.rows[0]?.cells.length || 1;
    cell.innerHTML = '<span class="empty-state-icon" aria-hidden="true"><i class="bi bi-search"></i></span><strong>No matching records</strong><p>Try a different search or clear your filters.</p><button type="button" class="btn btn-sm btn-outline-slate">Clear filters</button>';
    tbody.appendChild(empty);
    const feedback = document.createElement('div');
    feedback.className = 'table-feedback';
    const count = document.createElement('span');
    count.id = id + '-count';
    count.setAttribute('role', 'status');
    count.setAttribute('aria-live', 'polite');
    const reset = document.createElement('button');
    reset.type = 'button';
    reset.className = 'btn btn-sm btn-link p-0';
    reset.textContent = 'Clear filters';
    reset.hidden = true;
    feedback.append(count, reset);
    (table.closest('.table-responsive') || table).after(feedback);
    let rows = [], frame = 0;
    const rebuild = () => {
      rows = Array.from(tbody.rows).filter(row => row !== empty && row.cells.length > 1).map(row => ({
        row, text: normalize(row.textContent), cells: Array.from(row.cells, c => normalize(c.textContent))
      }));
    };
    const apply = () => {
      const filters = controls.map(c => ({ value: normalize(c.value), col: c.dataset.filterCol, exact: c.dataset.filterMatch === 'exact' })).filter(f => f.value);
      let visible = 0;
      rows.forEach(({ row, text, cells }) => {
        const matches = filters.every(f => {
          const textValue = f.col === undefined ? text : (cells[Number(f.col)] || '');
          return f.exact ? textValue === f.value : textValue.includes(f.value);
        });
        row.hidden = !matches;
        if (matches) visible++;
      });
      // Keep an existing server-rendered empty state when the dataset itself is empty.
      empty.hidden = visible > 0 || (!rows.length && Array.from(tbody.rows).some(r => r !== empty));
      count.textContent = `${visible} of ${rows.length} records`;
      reset.hidden = !filters.length;
    };
    const schedule = () => { cancelAnimationFrame(frame); frame = requestAnimationFrame(apply); };
    const clear = () => { controls.forEach(c => { c.value = ''; }); apply(); controls[0]?.focus(); };
    reset.addEventListener('click', clear);
    $('button', empty).addEventListener('click', clear);
    controls.forEach(c => {
      c.setAttribute('aria-controls', id);
      c.setAttribute('aria-describedby', count.id);
      c.addEventListener(c.tagName === 'SELECT' ? 'change' : 'input', schedule);
    });
    rebuild(); apply();
    const observer = new MutationObserver(() => { rebuild(); schedule(); });
    observer.observe(tbody, { childList: true, subtree: true, characterData: true });
  });

  // Repair label associations in the existing compact PHP form templates.
  // Keep visible text as the accessible name, including repeated material items.
  let nextId = 0;
  window.enhanceControls = (root = document) => {
    $$('input:not([type="hidden"]), select, textarea', root).forEach(control => {
      if (control.labels?.length || control.hasAttribute('aria-label') || control.hasAttribute('aria-labelledby')) return;
      const container = control.closest('.input-group')?.parentElement || control.parentElement;
      const label = Array.from(container.children).find(el => el.matches('label'));
      if (label && !label.htmlFor) {
        if (!control.id) control.id = `field-${++nextId}`;
        label.htmlFor = control.id;
      } else {
        const td = control.closest('td');
        const heading = td?.closest('table')?.tHead?.rows[0]?.cells[td.cellIndex]?.textContent.trim();
        const rowName = td?.parentElement.cells[0]?.textContent.trim();
        control.setAttribute('aria-label', heading ? `${heading}${rowName ? ' for ' + rowName : ''}` : (control.placeholder || control.name || (control.type === 'file' ? 'Upload file' : 'Enable option')));
      }
    });
    $$('button[title], a[title]', root).forEach(el => {
      if (!el.textContent.trim() && !el.hasAttribute('aria-label')) el.setAttribute('aria-label', el.title);
    });
  };
  enhanceControls();
  const tableRegions = $$('.table-responsive');
  const overflowHints = new Map();
  tableRegions.forEach(region => {
    region.tabIndex = 0;
    region.setAttribute('role', 'region');
    region.setAttribute('aria-label', `${$('h2', region.closest('.panel') || region)?.textContent || 'Records'} table, scroll for more columns`);
    const hint = document.createElement('span');
    hint.className = 'table-scroll-hint';
    hint.textContent = 'Scroll horizontally to view all columns →';
    hint.hidden = true;
    region.before(hint);
    overflowHints.set(region, hint);
  });
  if ('ResizeObserver' in window) {
    const tableObserver = new ResizeObserver(entries => entries.forEach(({ target }) => {
      overflowHints.get(target).hidden = target.scrollWidth <= target.clientWidth + 1;
    }));
    tableRegions.forEach(region => tableObserver.observe(region));
  }
  const sidebarElement = $('#sidebar');
  sidebarElement?.addEventListener('show.bs.offcanvas', () => {
    document.body.dataset.sidebarOpen = 'true';
  });
  sidebarElement?.addEventListener('hidden.bs.offcanvas', () => {
    delete document.body.dataset.sidebarOpen;
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && sidebarElement?.classList.contains('show')) {
      bootstrap.Offcanvas.getInstance(sidebarElement)?.hide();
    }
  });
  $$('[data-bs-toggle="tab"]').forEach((tab, index) => {
    const panel = $(tab.dataset.bsTarget);
    if (!panel) return;
    if (!tab.id) tab.id = `tab-${index}`;
    tab.setAttribute('role', 'tab');
    tab.setAttribute('aria-controls', panel.id);
    tab.setAttribute('aria-selected', String(tab.classList.contains('active')));
    tab.tabIndex = tab.classList.contains('active') ? 0 : -1;
    tab.closest('.nav')?.setAttribute('role', 'tablist');
    tab.closest('.nav-item')?.setAttribute('role', 'presentation');
    panel.setAttribute('role', 'tabpanel');
    panel.setAttribute('aria-labelledby', tab.id);
  });

  // Improve validation while preserving every explicit POST action and submitter value.
  document.addEventListener('submit', event => {
    const form = event.target;
    if (!form.matches('form.needs-validation')) return;
    if (!form.checkValidity()) {
      event.preventDefault();
      form.classList.add('was-validated');
      let summary = $('.form-feedback', form);
      if (!summary) {
        summary = document.createElement('div');
        summary.className = 'form-feedback';
        summary.setAttribute('role', 'alert');
        ($('.modal-body, .panel-body', form) || form).prepend(summary);
      }
      const invalid = $$(':invalid', form).filter(el => el.matches('input, select, textarea'));
      invalid.forEach(el => el.setAttribute('aria-invalid', 'true'));
      summary.textContent = `Please check ${invalid.length} ${invalid.length === 1 ? 'field' : 'fields'}. ${invalid[0]?.validationMessage || ''}`;
      invalid[0]?.focus();
      return;
    }
    $('.form-feedback', form)?.remove();
    const modal = form.closest('.modal');
    if (modal || (!form.hasAttribute('action') && form.dataset.toast)) {
      event.preventDefault();
      if (modal) bootstrap.Modal.getInstance(modal)?.hide();
      toast(form.dataset.toast || 'Saved successfully');
      form.classList.remove('was-validated');
      $$('[aria-invalid]', form).forEach(el => el.removeAttribute('aria-invalid'));
      // Keep inline settings visible; reset dialog fields for the next record.
      if (modal) form.reset();
    } else if (!event.defaultPrevented) {
      if (form.dataset.submitting) { event.preventDefault(); return; }
      form.dataset.submitting = 'true';
      form.setAttribute('aria-busy', 'true');
      const button = event.submitter;
      if (button) {
        button.setAttribute('aria-disabled', 'true');
        const spinner = document.createElement('span');
        spinner.className = 'submit-spinner';
        spinner.setAttribute('aria-hidden', 'true');
        button.prepend(spinner);
      }
    }
  });
  document.addEventListener('input', event => {
    const el = event.target;
    if (el.hasAttribute('aria-invalid')) el.setAttribute('aria-invalid', String(!el.validity.valid));
  });
  document.addEventListener('reset', event => {
    event.target.classList.remove('was-validated');
    $('.form-feedback', event.target)?.remove();
    $$('[aria-invalid]', event.target).forEach(el => el.removeAttribute('aria-invalid'));
  });
  window.addEventListener('pageshow', () => {
    $$('form[data-submitting]').forEach(form => {
      delete form.dataset.submitting;
      form.removeAttribute('aria-busy');
      $$('[aria-disabled]', form).forEach(el => el.removeAttribute('aria-disabled'));
      $$('.submit-spinner', form).forEach(el => el.remove());
    });
  });

  document.addEventListener('click', event => {
    const trigger = event.target.closest('[data-toast]:not(form)');
    if (trigger) { event.preventDefault(); toast(trigger.dataset.toast); }
    const remove = event.target.closest('[data-delete-name]');
    if (remove) window.confirmDelete(remove.dataset.deleteName, remove);
  });
  window.confirmDelete = (name, button) => {
    if (!confirm(`Are you sure you want to delete "${name}"?`)) return;
    const row = button.closest('tr');
    const finish = () => {
      $$('[data-bs-toggle="tooltip"]', row || button).forEach(el => bootstrap.Tooltip.getInstance(el)?.dispose());
      row?.remove();
      toast(`${name} removed from this preview`, 'Deleted');
    };
    if (row && !motion.matches && row.animate) {
      row.animate([{ opacity: 1 }, { opacity: 0 }], { duration: 160 }).finished.then(finish, finish);
    } else finish();
  };

  // Instantiate tooltips on demand rather than for every action in every row.
  const showTooltip = event => {
    const el = event.target.closest('[data-bs-toggle="tooltip"]');
    if (el && !bootstrap.Tooltip.getInstance(el)) bootstrap.Tooltip.getOrCreateInstance(el).show();
  };
  document.addEventListener('pointerover', showTooltip);
  document.addEventListener('focusin', showTooltip);

  const searchModal = $('#workspaceSearch');
  const query = $('#workspaceQuery');
  if (searchModal && query) {
    const links = $$('.workspace-result', searchModal).map(link => ({ link, text: normalize(link.textContent) }));
    query.addEventListener('input', () => {
      const terms = normalize(query.value).split(' ').filter(Boolean);
      let count = 0;
      links.forEach(({ link, text }) => { link.hidden = !terms.every(term => text.includes(term)); if (!link.hidden) count++; });
      $('.workspace-empty', searchModal).hidden = count > 0;
      $('#workspaceCount').textContent = `${count} ${count === 1 ? 'workspace' : 'workspaces'} available`;
    });
    searchModal.addEventListener('shown.bs.modal', () => { query.focus(); query.select(); });
    searchModal.addEventListener('keydown', event => {
      const visible = links.filter(({ link }) => !link.hidden).map(({ link }) => link);
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        const current = visible.indexOf(document.activeElement);
        const next = event.key === 'ArrowDown' ? (current + 1) % visible.length : (current <= 0 ? visible.length - 1 : current - 1);
        visible[next]?.focus();
      }
      if (event.key === 'Enter' && document.activeElement === query) { event.preventDefault(); visible[0]?.click(); }
    });
    document.addEventListener('keydown', event => {
      if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 'k') {
        if ($('.modal.show:not(#workspaceSearch), .offcanvas.show')) return;
        event.preventDefault();
        bootstrap.Modal.getOrCreateInstance(searchModal).show();
      }
    });
  }

  const toggle = $('#togglePasswordBtn');
  toggle?.addEventListener('click', () => {
    const input = $('#password'), show = input.type === 'password';
    input.type = show ? 'text' : 'password';
    toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    toggle.setAttribute('aria-pressed', String(show));
    $('#togglePasswordIcon').className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
  });

  if (window.Chart) {
    Chart.defaults.font.family = "'IBM Plex Sans', system-ui, sans-serif";
    Chart.defaults.color = '#5c707a';
    Chart.defaults.borderColor = '#e5ece8';
    Chart.defaults.animation = motion.matches ? false : { duration: 450 };
    Chart.defaults.plugins.legend.labels.usePointStyle = true;
    Chart.defaults.plugins.legend.labels.boxWidth = 8;
    motion.addEventListener('change', () => {
      Chart.defaults.animation = motion.matches ? false : { duration: 450 };
      Object.values(Chart.instances).forEach(chart => { chart.options.animation = Chart.defaults.animation; chart.update('none'); });
    });
  }
  window.renderQR = (selector, text) => {
    const element = $(selector);
    if (!element || !window.qrcode) return;
    try {
      const qr = qrcode(0, 'M');
      qr.addData(text); qr.make();
      element.innerHTML = qr.createSvgTag({ cellSize: 4, margin: 4, scalable: true });
    } catch (error) { console.warn('QR generation failed', error); }
  };
})();

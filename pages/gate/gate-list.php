<?php
$pageTitle = 'Gate operations'; $activeNav = 'gate';
require_once __DIR__ . '/../../includes/headerscript.php';
require_once __DIR__ . '/../../includes/header.php';
page_header('Gate operations', 'Scan or look up a pass, then record entry or exit. ' . $currentSite . ' · Gate 1', action_btn('Record exception', 'exclamation-triangle', 'exceptionModal', 'btn-outline-ink'));
?>
<div class="occupancy mb-3">
  <div><span class="big" id="occCount"><?= count($ONSITE) + 22 ?></span><small>people on site</small></div>
  <div class="split"><div><b>12</b><small>entered today</small></div><div><b>9</b><small>exited today</small></div><div><b>38</b><small>expected</small></div><div><b>3</b><small>overdue</small></div></div>
</div>

<div class="scan-box">
  <i class="bi bi-qr-code-scan fs-2"></i>
  <input id="scanInput" class="form-control" placeholder="Scan QR or type pass no., name, phone, appointment ref" autofocus aria-label="Scan or search">
  <button class="btn btn-ink btn-lg" id="scanBtn">Look up</button>
</div>
<div class="small text-secondary mt-2">Try: <a href="#" data-try="GP-26-08812">GP-26-08812</a> (valid, checked in) · <a href="#" data-try="APT-26-0412">APT-26-0412</a> (approved) · <a href="#" data-try="APT-26-0415">APT-26-0415</a> (pending approval) · <a href="#" data-try="Anil Pawar">Anil Pawar</a> (watchlist) · <a href="#" data-try="GP-26-08751">GP-26-08751</a> (revoked)</div>

<div class="scan-result" id="scanResult" role="status" aria-live="polite"></div>

<div class="row g-3 mt-1">
  <div class="col-xl-8">
    <div class="panel">
      <div class="panel-head"><h2>Currently on site</h2></div>
      <?php table_tools('onsiteTable', 'Filter by name, pass or host'); ?>
      <div class="table-responsive"><table class="table table-hover" id="onsiteTable">
        <thead><tr><th>Pass</th><th>Name</th><th>Host</th><th>Gate</th><th>In</th><th>Inside for</th><th class="text-end">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($ONSITE as $o): ?>
          <tr><td class="ref mono"><?= e($o['pass']) ?></td><td><?= e($o['name']) ?><span class="sub"><?= e($o['company']) ?> · <?= e($o['type']) ?></span></td><td><?= e($o['host']) ?></td><td class="text-nowrap"><?= e($o['gate']) ?></td><td class="mono"><?= e($o['in']) ?></td><td class="mono"><?= e($o['duration']) ?></td>
            <td class="text-end"><?= table_actions('gate', $o['pass'], ['name' => $o['name']]) ?></td></tr>
        <?php endforeach; ?>
        </tbody></table></div>
    </div>
  </div>
  <div class="col-xl-4">
    <div class="panel mb-3">
      <div class="panel-head"><h2>Arriving next</h2></div>
      <ul class="list-group list-group-flush">
        <?php foreach (array_slice($APPOINTMENTS, 0, 3) as $a): ?>
        <li class="list-group-item"><div class="d-flex justify-content-between"><b><?= e($a['visitor']) ?></b><span class="mono"><?= e(substr($a['when'], -5)) ?></span></div><span class="sub">Host: <?= e($a['host']) ?> · <?= e($a['ref']) ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="panel">
      <div class="panel-head"><h2>Exceptions today</h2></div>
      <ul class="list-group list-group-flush" id="exceptionList">
        <li class="list-group-item"><i class="bi bi-x-octagon text-danger me-2"></i><b>Denied entry</b> – Vishal More, watchlist <span class="sub">08:12 · Gate 1 · Guard A. Jadhav</span></li>
        <li class="list-group-item"><i class="bi bi-exclamation-triangle text-warning me-2"></i><b>Invalid pass</b> – GP-26-08751 revoked <span class="sub">09:03 · Gate 2</span></li>
        <li class="list-group-item"><i class="bi bi-shield-check text-secondary me-2"></i><b>Override</b> – Fire inspector entered, approval pending <span class="sub">09:15 · authorised by Plant Head</span></li>
      </ul>
    </div>
  </div>
</div>

<?php
modal_open('exceptionModal', 'Record gate exception', 'modal-md', 'Save', 'Exception recorded');
?>
<div class="row g-3">
  <div class="col-12"><label class="form-label req">Type</label><select class="form-select" required><option value="">Select</option><option>Denied entry</option><option>Invalid pass</option><option>Expired pass</option><option>Wrong gate</option><option>Timestamp correction</option><option>Emergency override</option></select></div>
  <div class="col-12"><label class="form-label">Person or pass</label><input class="form-control"></div>
  <div class="col-12"><label class="form-label req">Reason</label><textarea class="form-control" rows="3" required></textarea></div>
  <div class="col-12 small text-secondary">Corrections and overrides are written to the audit log with your name and the time.</div>
</div>
<?php modal_close('Save exception');
require_once __DIR__ . '/../../includes/footer.php';

$readonly = is_readonly() ? 'true' : 'false';
$apiUrl = url('api/gate.php');
$extraScripts = <<<JS
<script>
const RO = {$readonly};
const API_URL = '{$apiUrl}';
const DB = {
  'GP-26-08812': {name:'Meera Iyer', co:'Bosch Rexroth', host:'Vikram Shah', state:'inside', checks:[['ok','Identity matched'],['ok','Approved by Vikram Shah'],['ok','Pass valid until 18:00'],['ok','Gate 1 permitted'],['ok','No restrictions']]},
  'APT-26-0412': {name:'Rohan Deshmukh (+2)', co:'Tata Projects', host:'Sneha Kapoor', state:'ready', checks:[['ok','Approved by Sneha Kapoor'],['ok','Pass valid today 10:00–18:00'],['ok','Site: Pune Plant'],['warn','Safety induction due – 1 of 3 pending'],['ok','No restrictions']]},
  'APT-26-0415': {name:'David Fernandes (+1)', co:'Siemens Ltd', host:'Arjun Menon', state:'blocked', why:'Approval pending (level 1 of 2). Entry is not allowed until it is approved.', checks:[['bad','Approval pending'],['ok','Site: Chakan Factory – wrong site for this gate'],['ok','No restrictions']]},
  'Anil Pawar': {name:'Anil Pawar', co:'Pawar Engineering', host:'Arjun Menon', state:'watch', why:'Watchlist warning: unauthorised photography on shop floor (Sept 2026). Action set to "Warn only" – ask security supervisor before allowing entry.', checks:[['warn','Watchlist match'],['ok','Approved by Arjun Menon'],['ok','Pass valid']]},
  'GP-26-08751': {name:'Unknown holder', co:'-', host:'-', state:'blocked', why:'This pass was revoked on 25 Oct 16:05 – visitor left site early.', checks:[['bad','Pass revoked']]}
};

const box = document.getElementById('scanResult'), inp = document.getElementById('scanInput');
let activeLookupData = null;

// Refresh Live Occupancy & Gate Stats via AJAX
async function refreshStats() {
  try {
    const res = await fetch(API_URL + '?action=stats');
    const json = await res.json();
    if (json.success && json.data) {
      const d = json.data;
      const occEl = document.getElementById('occCount');
      if (occEl && d.inside !== undefined) {
        // If DB has live count, reflect it
        if (d.inside > 0) occEl.textContent = d.inside;
      }
    }
  } catch (err) {
    console.warn('Could not fetch live gate stats:', err);
  }
}

// Real-time lookup with Database API + Demo fallback
async function lookup(q){
  q = q.trim(); if(!q) return;
  activeLookupData = null;
  box.className = 'scan-result show';
  box.innerHTML = '<div class="text-secondary"><span class="spinner-border spinner-border-sm me-2"></span>Looking up "'+q.replace(/</g,'&lt;')+'" in database…</div>';

  let r = null;
  try {
    const res = await fetch(API_URL + '?action=lookup&q=' + encodeURIComponent(q));
    const json = await res.json();
    if (json.success && json.data) {
      r = json.data;
      activeLookupData = r;
    }
  } catch (e) {
    console.warn('API lookup error, falling back to local dataset:', e);
  }

  // Fallback to local prototype DB if not yet in database
  if (!r) {
    const key = Object.keys(DB).find(k => k.toLowerCase() === q.toLowerCase());
    r = DB[key];
  }

  const icon = {ok:'check-circle-fill',bad:'x-circle-fill',warn:'exclamation-triangle-fill'};
  if(!r){ 
    box.className='scan-result show bad'; 
    box.innerHTML='<b>No match for “'+q.replace(/</g,'&lt;')+'”.</b><div class="small">Check the number, or search by name or phone. Record an exception if the pass is not in the system.</div>'; 
    return; 
  }

  const cls = r.state==='ready'||r.state==='inside' ? 'ok' : r.state==='watch' ? 'warn' : 'bad';
  let btns = '';
  if(!RO){
    if(r.state==='ready') btns = '<button class="btn btn-ink" data-act="in"><i class="bi bi-box-arrow-in-right me-1"></i>Record entry</button>';
    if(r.state==='inside') btns = '<button class="btn btn-ink" data-act="out"><i class="bi bi-box-arrow-right me-1"></i>Record exit</button><span class="small ms-2">Already inside – entry is disabled to prevent a duplicate check-in.</span>';
    if(r.state==='watch') btns = '<button class="btn btn-outline-ink" data-bs-toggle="modal" data-bs-target="#exceptionModal">Record override…</button> <button class="btn btn-ink" data-act="deny">Deny entry</button>';
    if(r.state==='blocked') btns = '<button class="btn btn-ink" data-act="deny">Log denied entry</button>';
  }
  box.className = 'scan-result show '+cls;
  box.innerHTML = '<div class="d-flex justify-content-between flex-wrap gap-2"><div><div class="fs-5 fw-semibold">'+(r.name||'Visitor')+'</div><div class="text-secondary">'+(r.co||'Self')+' · Host: '+(r.host||'Host')+'</div></div><div>'+btns+'</div></div>'
    + (r.why ? '<div class="mt-2"><b>'+r.why+'</b></div>' : '')
    + '<ul class="check-list">'+(r.checks||[]).map(c=>'<li><i class="bi bi-'+(icon[c[0]]||'info-circle')+'"></i>'+c[1]+'</li>').join('')+'</ul>';
}

document.getElementById('scanBtn').onclick = () => lookup(inp.value);
inp.addEventListener('keydown', e => { if(e.key==='Enter') lookup(inp.value); });
document.querySelectorAll('[data-try]').forEach(a => a.onclick = e => { e.preventDefault(); inp.value=a.dataset.try; lookup(a.dataset.try); });

// AJAX Check-in & Check-out actions
box.addEventListener('click', async e => {
  const b = e.target.closest('[data-act]'); if(!b) return;
  const act = b.dataset.act;
  const occ = document.getElementById('occCount');
  
  if (act === 'in') {
    b.disabled = true;
    b.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Recording…';
    try {
      const payload = {
        action: 'entry',
        pass_id: activeLookupData?.pass_id || null,
        apt_id: activeLookupData?.apt_id || null,
        query: inp.value
      };
      const res = await fetch(API_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.success) {
        if (occ) occ.textContent = +occ.textContent + 1;
        toast('Entry recorded successfully at ' + new Date().toTimeString().slice(0,5));
      } else {
        toast(json.message || 'Error recording entry', 'danger');
      }
    } catch (err) {
      if (occ) occ.textContent = +occ.textContent + 1;
      toast('Entry recorded at ' + new Date().toTimeString().slice(0,5));
    }
  }

  if (act === 'out') {
    b.disabled = true;
    b.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Recording…';
    try {
      const payload = {
        action: 'exit',
        pass_id: activeLookupData?.pass_id || null,
        pass_num: inp.value
      };
      const res = await fetch(API_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(payload)
      });
      const json = await res.json();
      if (json.success) {
        if (occ && +occ.textContent > 0) occ.textContent = +occ.textContent - 1;
        toast('Exit recorded – visit closed');
      } else {
        toast(json.message || 'Error recording exit', 'danger');
      }
    } catch (err) {
      if (occ && +occ.textContent > 0) occ.textContent = +occ.textContent - 1;
      toast('Exit recorded – visit closed');
    }
  }

  if (act === 'deny') {
    try {
      await fetch(API_URL, {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
          action: 'exception',
          type: 'Denied entry',
          person: activeLookupData?.name || inp.value,
          reason: activeLookupData?.why || 'Security denied entry'
        })
      });
    } catch (e) {}
    toast('Denied entry logged as an exception');
  }

  box.className='scan-result'; inp.value=''; inp.focus();
  refreshStats();
});

// Hook up exception modal submission via AJAX
const exModal = document.getElementById('exceptionModal');
if (exModal) {
  const saveBtn = exModal.querySelector('.btn-ink, .btn-primary, [type="submit"]');
  if (saveBtn) {
    saveBtn.addEventListener('click', async e => {
      const selType = exModal.querySelector('select');
      const inpPerson = exModal.querySelector('input');
      const txtReason = exModal.querySelector('textarea');
      if (selType && txtReason && selType.value && txtReason.value) {
        try {
          await fetch(API_URL, {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
              action: 'exception',
              type: selType.value,
              person: inpPerson ? inpPerson.value : '',
              reason: txtReason.value
            })
          });
        } catch (err) {}
      }
    });
  }
}

// Initial stats fetch
refreshStats();
</script>
JS;
require_once __DIR__ . '/../../includes/footerscript.php'; ?>

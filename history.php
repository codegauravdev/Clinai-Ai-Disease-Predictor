<?php
require_once __DIR__ . '/php/config.php';
requireLogin();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ClinAI — Assessment History</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css"/>
  <style>
    /* ── History page premium medical theme ──────────────────────── */

    /* Full-screen dark medical background */
    body::before {
      content: '';
      position: fixed; inset: 0; z-index: -2;
      background: linear-gradient(160deg, #020c22 0%, #051428 25%, #071e3d 50%, #082040 75%, #030e20 100%);
      pointer-events: none;
    }
    body::after {
      content: '';
      position: fixed; inset: 0; z-index: -1;
      background:
        radial-gradient(ellipse 80% 60% at 20% 10%, rgba(26,111,196,0.20) 0%, transparent 55%),
        radial-gradient(ellipse 60% 50% at 85% 80%, rgba(6,182,212,0.16) 0%, transparent 50%),
        linear-gradient(rgba(56,189,248,.02) 1px, transparent 1px) 0 0 / 50px 50px,
        linear-gradient(90deg, rgba(56,189,248,.02) 1px, transparent 1px) 0 0 / 50px 50px;
      pointer-events: none;
    }
    [data-theme="light"] body::before {
      background: linear-gradient(160deg, #e8f2ff 0%, #eef6ff 30%, #e4effd 60%, #ecf4ff 100%);
    }
    [data-theme="light"] body::after {
      background:
        radial-gradient(ellipse 80% 60% at 20% 10%, rgba(26,111,196,0.08) 0%, transparent 55%),
        radial-gradient(ellipse 60% 50% at 85% 80%, rgba(6,182,212,0.06) 0%, transparent 50%);
    }

    .dash-header {
      background: linear-gradient(135deg, #020f28 0%, #061c45 30%, #0c3070 60%, #1048a0 85%, #1a6fc4 100%);
      padding: 0;
      position: sticky; top: 0; z-index: 200;
      box-shadow: 0 4px 40px rgba(0,0,0,.38);
      border-bottom: 1px solid rgba(56,189,248,0.14);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }
    .dash-nav {
      max-width:1200px; margin:0 auto;
      padding:14px 28px;
      display:flex; align-items:center; gap:16px;
    }
    .nav-logo {
      font-size:1.1rem; font-weight:800; color:#fff;
      display:flex; align-items:center; gap:10px;
      text-decoration:none;
    }
    .nav-logo span {
      background: linear-gradient(135deg,#38bdf8,#06b6d4);
      -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;
    }
    .nav-links { display:flex; gap:4px; margin-left:20px; }
    .nav-link {
      padding:7px 14px; border-radius:9px;
      font-size:.83rem; font-weight:600; color:rgba(180,220,255,.75);
      text-decoration:none; transition:all .2s;
      display:flex; align-items:center; gap:5px;
    }
    .nav-link:hover, .nav-link.active {
      background:rgba(255,255,255,.12); color:#fff;
    }
    .nav-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
    .nav-user {
      display:flex; align-items:center; gap:10px;
      padding:6px 14px 6px 8px;
      background:rgba(255,255,255,.08);
      border:1px solid rgba(120,200,255,.20);
      border-radius:40px;
      color:#fff; font-size:.83rem; font-weight:600;
      backdrop-filter:blur(8px);
    }
    .nav-avatar {
      width:28px; height:28px; border-radius:50%;
      display:flex; align-items:center; justify-content:center;
      font-size:.78rem; font-weight:800; color:#fff;
    }
    .btn-logout {
      padding:7px 14px; border-radius:9px;
      font-size:.83rem; font-weight:600; color:rgba(252,165,165,.80);
      background:transparent; border:none; cursor:pointer; font-family:var(--font);
      transition:all .2s; display:flex; align-items:center; gap:5px;
    }
    .btn-logout:hover { background:rgba(220,38,38,.18); color:#fca5a5; }
    .progress-track { height:3px; background:rgba(56,189,248,.08); }
    .progress-fill  { height:100%; background:linear-gradient(90deg,#1a6fc4,#38bdf8,#06b6d4); width:100%; }

    /* Stats bar */
    .stats-bar {
      max-width:1200px; margin:32px auto 0;
      padding:0 28px;
      display:grid; grid-template-columns:repeat(4,1fr); gap:16px;
    }
    .stat-card {
      background: rgba(10,25,55,0.70);
      border: 1px solid rgba(120,180,255,0.20);
      border-radius: var(--radius-lg);
      padding: 20px 22px;
      display:flex; align-items:center; gap:14px;
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
      box-shadow: 0 20px 60px rgba(0,0,0,0.30);
      transition: var(--transition);
    }
    [data-theme="light"] .stat-card {
      background: rgba(255,255,255,0.82);
      border-color: rgba(56,139,253,0.16);
      box-shadow: 0 4px 28px rgba(26,111,196,0.10);
    }
    .stat-card:hover { transform:translateY(-2px); box-shadow:var(--shadow-md); }
    .stat-icon {
      width:44px; height:44px; border-radius:12px;
      display:flex; align-items:center; justify-content:center;
      font-size:20px; flex-shrink:0;
    }
    .stat-content {}
    .stat-number { font-size:1.6rem; font-weight:900; letter-spacing:-1px; line-height:1; }
    .stat-desc { font-size:.75rem; color:var(--text-muted); font-weight:500; margin-top:2px; }

    /* History page body */
    .hist-body {
      max-width:1200px; margin:24px auto 60px;
      padding:0 28px;
    }
    .hist-toolbar {
      display:flex; align-items:center; gap:12px;
      margin-bottom:20px; flex-wrap:wrap;
    }
    .hist-title { font-size:1.3rem; font-weight:800; letter-spacing:-.4px; }
    .triage-filter-select {
      padding:9px 14px; border-radius:var(--radius-sm);
      background:rgba(10,25,55,.60); border:1.5px solid rgba(120,180,255,.20);
      font-family:var(--font); font-size:.85rem; color:var(--text); outline:none;
      cursor:pointer; transition:var(--transition); backdrop-filter:blur(12px);
    }
    .triage-filter-select:focus { border-color:#38bdf8; box-shadow:0 0 0 3px rgba(56,189,248,.16); }
    .btn-export {
      padding:9px 18px; border-radius:var(--radius-sm);
      background:rgba(5,150,105,.14); border:1px solid rgba(5,150,105,.28);
      color:#34d399; font-size:.85rem; font-weight:700;
      cursor:pointer; font-family:var(--font); text-decoration:none;
      display:inline-flex; align-items:center; gap:6px; transition:all .2s;
    }
    .btn-export:hover { background:rgba(5,150,105,.25); }
    .search-field {
      flex:1; min-width:220px;
      position:relative;
    }
    .search-field input {
      width:100%; padding:10px 16px 10px 40px;
      background: rgba(10,25,55,0.60);
      border:1.5px solid rgba(120,180,255,0.20);
      border-radius:var(--radius-sm);
      font-family:var(--font); font-size:.88rem; color:var(--text);
      outline:none; transition:var(--transition);
      backdrop-filter:blur(12px);
    }
    [data-theme="light"] .search-field input {
      background: rgba(255,255,255,0.82);
      border-color: rgba(56,139,253,0.18);
      color: var(--text);
    }
    .search-field input:focus {
      border-color:#38bdf8;
      box-shadow:0 0 0 3px rgba(56,189,248,0.16);
    }
    .search-icon-inside {
      position:absolute; left:14px; top:50%; transform:translateY(-50%);
      font-size:.9rem; color:var(--text-muted); pointer-events:none;
    }

    /* Table */
    .table-wrap {
      background: rgba(10,25,55,0.70);
      border: 1px solid rgba(120,180,255,0.20);
      border-radius: var(--radius-xl);
      overflow:hidden;
      box-shadow: 0 20px 60px rgba(0,0,0,0.35);
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }
    [data-theme="light"] .table-wrap {
      background: rgba(255,255,255,0.88);
      border-color: rgba(56,139,253,0.16);
      box-shadow: 0 4px 28px rgba(26,111,196,0.10);
    }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:rgba(26,111,196,0.10); }
    th {
      padding:13px 16px; text-align:left;
      font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.7px;
      color:var(--text-muted); border-bottom:1px solid var(--border);
      white-space:nowrap;
    }
    td {
      padding:14px 16px; font-size:.85rem;
      border-bottom:1px solid var(--border);
      color:var(--text);
    }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:rgba(26,111,196,0.06); }
    tbody tr { transition:background .15s; }

    .triage-pill{
      display:inline-flex;align-items:center;gap:5px;
      padding:3px 10px;border-radius:20px;
      font-size:.7rem;font-weight:800;letter-spacing:.4px;text-transform:uppercase;
    }
    .triage-critical{background:rgba(220,38,38,.15);color:#ef4444;border:1px solid rgba(220,38,38,.3);}
    .triage-high{background:rgba(234,88,12,.12);color:#ea580c;border:1px solid rgba(234,88,12,.3);}
    .triage-moderate{background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.3);}
    .triage-low{background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.3);}
    .triage-inconclusive{background:rgba(107,114,128,.12);color:#6b7280;border:1px solid rgba(107,114,128,.3);}

    .conf-badge{
      display:inline-block;
      padding:2px 9px;border-radius:6px;
      font-size:.75rem;font-weight:700;
      background:var(--primary-lt);color:var(--primary);
    }

    .btn-view{
      padding:5px 12px;border-radius:6px;
      background:var(--primary-lt);color:var(--primary);
      border:1px solid rgba(26,111,196,.25);
      font-size:.75rem;font-weight:700;cursor:pointer;
      font-family:var(--font);transition:var(--transition);
      text-decoration:none;display:inline-block;
    }
    .btn-view:hover{background:var(--primary);color:#fff;}
    .btn-del{
      padding:5px 10px;border-radius:6px;
      background:rgba(220,38,38,.10);color:#ef4444;
      border:1px solid rgba(220,38,38,.25);
      font-size:.72rem;font-weight:700;cursor:pointer;
      font-family:var(--font);transition:var(--transition);
      margin-left:5px;
    }
    .btn-del:hover{background:rgba(220,38,38,.22);}

    .empty-state{
      text-align:center;padding:60px 20px;
      color:var(--text-muted);
    }
    .empty-icon{font-size:3rem;margin-bottom:12px;}
    .empty-title{font-size:1rem;font-weight:700;color:var(--text);margin-bottom:6px;}

    .pagination{
      display:flex;align-items:center;justify-content:space-between;
      padding:14px 18px;
      border-top:1px solid var(--border);
      font-size:.8rem;color:var(--text-muted);
    }
    .page-btns{display:flex;gap:6px;}
    .page-btn{
      padding:5px 12px;border-radius:6px;
      border:1px solid var(--border);background:var(--surface);
      font-size:.78rem;font-weight:600;cursor:pointer;
      font-family:var(--font);color:var(--text);transition:var(--transition);
    }
    .page-btn:hover:not(:disabled){border-color:var(--primary);color:var(--primary);}
    .page-btn:disabled{opacity:.4;cursor:not-allowed;}

    .loading-row td { text-align:center; padding:40px; color:var(--text-muted); }

    /* Footer */
    .hist-footer {
      text-align:center;
      padding:22px 20px;
      font-size:.74rem;
      color:var(--text-light);
      border-top:1px solid var(--border);
      margin-top:40px;
      line-height:1.8;
    }
    .hist-footer .footer-author {
      color:var(--text-muted);
      font-weight:700;
    }

    /* Responsive */
    @media (max-width:900px) {
      .stats-bar { grid-template-columns:repeat(2,1fr); }
    }
    @media (max-width:600px) {
      .stats-bar { grid-template-columns:1fr 1fr; }
      .hist-body { padding:0 14px; }
      .dash-nav  { padding:12px 14px; }
      .nav-user span:last-child { display:none; }
    }
  </style>
</head>
<body>

<!-- Header -->
<div class="dash-header">
  <div class="dash-nav">
    <a href="index.php" class="nav-logo">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
        <rect x="9" y="2" width="6" height="20" rx="2" fill="white"/>
        <rect x="2" y="9" width="20" height="6" rx="2" fill="white"/>
      </svg>
      Clin<span>AI</span>
    </a>
    <div class="nav-links">
      <a href="index.php" class="nav-link">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12l7-7 7 7"/></svg>
        New Assessment
      </a>
      <a href="history.php" class="nav-link active">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        History
      </a>
    </div>
    <div class="nav-right">
      <div class="nav-user">
        <div class="nav-avatar" style="background:<?= htmlspecialchars($user['color']) ?>"><?= strtoupper(substr($user['name'],0,1)) ?></div>
        <span><?= htmlspecialchars($user['name']) ?></span>
        <span style="opacity:.5;font-weight:400"><?= htmlspecialchars($user['role']) ?></span>
      </div>
      <button class="btn-logout" onclick="logout()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </button>
    </div>
  </div>
  <div class="progress-track"><div class="progress-fill"></div></div>
</div>

<!-- Stats bar -->
<div class="stats-bar" id="stats-bar">
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(26,111,196,0.14);border:1px solid rgba(26,111,196,0.22)">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
    </div>
    <div class="stat-content"><div class="stat-number" id="st-total">—</div><div class="stat-desc">Total Assessments</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(5,150,105,0.14);border:1px solid rgba(5,150,105,0.22)">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
    </div>
    <div class="stat-content"><div class="stat-number" id="st-patients">—</div><div class="stat-desc">Patients Assessed</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(220,38,38,0.14);border:1px solid rgba(220,38,38,0.22)">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    </div>
    <div class="stat-content"><div class="stat-number" id="st-critical">—</div><div class="stat-desc">Critical / High</div></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon" style="background:rgba(217,119,6,0.14);border:1px solid rgba(217,119,6,0.22)">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
    </div>
    <div class="stat-content"><div class="stat-number" id="st-top">—</div><div class="stat-desc">Top Condition</div></div>
  </div>
</div>

<!-- Main body -->
<div class="hist-body">
  <div class="hist-toolbar">
    <div class="hist-title">Assessment History</div>
    <div class="search-field">
      <span class="search-icon-inside">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </span>
      <input type="text" id="search-input" placeholder="Search patient name, diagnosis…" oninput="debounceSearch()"/>
    </div>
    <a href="index.php" class="btn btn-primary" style="padding:10px 20px;font-size:.85rem;border-radius:var(--radius-sm);background:linear-gradient(135deg,#1a6fc4,#06b6d4);color:#fff;font-weight:700;text-decoration:none;display:inline-flex;align-items:center;gap:7px;box-shadow:0 4px 16px rgba(26,111,196,0.40);">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      New Assessment
    </a>
  </div>

  <div class="table-wrap">
    <table id="history-table">
      <thead>
        <tr>
          <th>#</th>
          <th>Patient</th>
          <th>Age/Sex</th>
          <th>Ward</th>
          <th>Triage</th>
          <th>Top Diagnosis</th>
          <th>Confidence</th>
          <th>Symptoms</th>
          <th>Date</th>
          <th>Assessed By</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id="history-body">
        <tr class="loading-row"><td colspan="11">Loading…</td></tr>
      </tbody>
    </table>
    <div class="pagination" id="pagination">
      <span id="page-info">—</span>
      <div class="page-btns">
        <button class="page-btn" id="btn-prev" onclick="changePage(-1)" disabled>← Prev</button>
        <button class="page-btn" id="btn-next" onclick="changePage(1)"  disabled>Next →</button>
      </div>
    </div>
  </div>
</div>

<script>
let currentOffset = 0;
const LIMIT = 15;
let searchTimer;

function debounceSearch() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(() => { currentOffset = 0; loadHistory(); }, 350);
}

function changePage(dir) {
  currentOffset = Math.max(0, currentOffset + dir * LIMIT);
  loadHistory();
}

async function loadHistory() {
  const q   = document.getElementById('search-input').value.trim();
  const url = `php/api.php?action=history&limit=${LIMIT}&offset=${currentOffset}&q=${encodeURIComponent(q)}`;
  const body = document.getElementById('history-body');
  body.innerHTML = '<tr class="loading-row"><td colspan="11">Loading…</td></tr>';

  try {
    const res  = await fetch(url);
    const data = await res.json();
    if (!data.success) { body.innerHTML = `<tr class="loading-row"><td colspan="11">Error: ${data.error}</td></tr>`; return; }
    renderTable(data.data);
    updatePagination(data.total, data.offset);
  } catch (e) {
    body.innerHTML = '<tr class="loading-row"><td colspan="11">Network error — are you running on XAMPP?</td></tr>';
  }
}

function renderTable(rows) {
  const body = document.getElementById('history-body');
  if (!rows.length) {
    body.innerHTML = '<tr><td colspan="11"><div class="empty-state"><div class="empty-icon">📭</div><div class="empty-title">No assessments found</div><p>Run a new assessment to see results here.</p></div></td></tr>';
    return;
  }
  body.innerHTML = rows.map((r,i) => `
    <tr>
      <td style="color:var(--text-muted);font-weight:600">${currentOffset+i+1}</td>
      <td><strong>${esc(r.patient_name)}</strong>${r.mrn ? `<br><span style="font-size:.72rem;color:var(--text-muted)">MRN: ${esc(r.mrn)}</span>` : ''}</td>
      <td>${r.age} / ${capitalize(r.gender)}</td>
      <td>${r.ward ? esc(r.ward) : '<span style="color:var(--text-muted)">—</span>'}</td>
      <td><span class="triage-pill triage-${r.triage_level}">${r.triage_level}</span></td>
      <td>${r.top_diagnosis ? esc(r.top_diagnosis) : '<span style="color:var(--text-muted)">—</span>'}</td>
      <td>${r.top_confidence ? `<span class="conf-badge">${r.top_confidence}%</span>` : '—'}</td>
      <td>${r.symptom_count}</td>
      <td style="white-space:nowrap;font-size:.78rem">${formatDate(r.assessed_at)}</td>
      <td style="font-size:.78rem">${r.assessed_by ? esc(r.assessed_by) : '—'}</td>
      <td><a href="index.php?view=${r.assessment_id}" class="btn-view">View</a></td>
    </tr>`).join('');
}

function updatePagination(total, offset) {
  const start = offset + 1;
  const end   = Math.min(offset + LIMIT, total);
  document.getElementById('page-info').textContent = `Showing ${start}–${end} of ${total}`;
  document.getElementById('btn-prev').disabled = offset <= 0;
  document.getElementById('btn-next').disabled = offset + LIMIT >= total;
}

async function loadStats() {
  try {
    const res  = await fetch('php/api.php?action=stats');
    const data = await res.json();
    if (!data.success) return;
    const s = data.data;
    document.getElementById('st-total').textContent    = s.total_assessments;
    document.getElementById('st-patients').textContent = s.total_patients;
    const critHigh = (s.by_triage.find(t=>t.triage_level==='critical')?.cnt||0) + (s.by_triage.find(t=>t.triage_level==='high')?.cnt||0);
    document.getElementById('st-critical').textContent = critHigh;
    const top = s.top_conditions[0];
    document.getElementById('st-top').textContent = top ? top.top_diagnosis.split(' ')[0] : '—';
  } catch(e) { console.warn('Stats unavailable'); }
}

async function logout() {
  await fetch('php/auth.php?action=logout');
  window.location.href = 'login.php';
}

function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function capitalize(s) { return s ? s[0].toUpperCase()+s.slice(1) : ''; }
function formatDate(d) {
  return new Date(d).toLocaleString('en-US', { month:'short', day:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' });
}

// Init
loadStats();
loadHistory();

// Footer
document.querySelector('.hist-body')?.insertAdjacentHTML('afterend', `
<footer class="hist-footer">
  ClinAI v2.0 &nbsp;·&nbsp; AI-Powered Clinical Decision Support &nbsp;·&nbsp;
  For licensed healthcare professionals only &nbsp;·&nbsp;
  Not a substitute for clinical diagnosis &nbsp;·&nbsp;
  By <span class="footer-author">Gaurav Tamshete</span>
</footer>`);

// Theme toggle — default dark for premium look
const saved = localStorage.getItem('clinai-theme');
if (saved === 'light') {
  document.documentElement.setAttribute('data-theme','light');
} else {
  document.documentElement.setAttribute('data-theme','dark');
}
</script>
</body>
</html>

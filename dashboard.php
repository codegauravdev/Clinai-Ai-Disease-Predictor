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
  <title>ClinAI — Dashboard</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css?v=50"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    body::before {
      content:''; position:fixed; inset:0; z-index:-2;
      background:linear-gradient(160deg,#020c22 0%,#051428 25%,#071e3d 50%,#082040 75%,#030e20 100%);
    }
    body::after {
      content:''; position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(ellipse 80% 60% at 20% 10%,rgba(26,111,196,.20) 0%,transparent 55%),
        radial-gradient(ellipse 60% 50% at 85% 80%,rgba(6,182,212,.16) 0%,transparent 50%),
        linear-gradient(rgba(56,189,248,.025) 1px,transparent 1px) 0 0/50px 50px,
        linear-gradient(90deg,rgba(56,189,248,.025) 1px,transparent 1px) 0 0/50px 50px;
    }
    .dash-header {
      background:linear-gradient(135deg,#020f28 0%,#061c45 30%,#0c3070 60%,#1048a0 85%,#1a6fc4 100%);
      position:sticky; top:0; z-index:200;
      box-shadow:0 4px 40px rgba(0,0,0,.38);
      border-bottom:1px solid rgba(56,189,248,0.14);
      backdrop-filter:blur(20px);
    }
    .dash-nav {
      max-width:1300px; margin:0 auto; padding:14px 28px;
      display:flex; align-items:center; gap:16px;
    }
    .nav-logo {
      font-size:1.1rem; font-weight:800; color:#fff;
      display:flex; align-items:center; gap:10px; text-decoration:none;
    }
    .nav-logo span { background:linear-gradient(135deg,#38bdf8,#06b6d4); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .nav-links { display:flex; gap:4px; margin-left:16px; }
    .nav-link { padding:7px 14px; border-radius:9px; font-size:.83rem; font-weight:600; color:rgba(180,220,255,.75); text-decoration:none; transition:all .2s; display:flex; align-items:center; gap:5px; }
    .nav-link:hover,.nav-link.active { background:rgba(255,255,255,.12); color:#fff; }
    .nav-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
    .nav-user { display:flex; align-items:center; gap:8px; padding:6px 14px 6px 8px; background:rgba(255,255,255,.08); border:1px solid rgba(120,200,255,.20); border-radius:40px; color:#fff; font-size:.83rem; font-weight:600; }
    .nav-avatar { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:800; color:#fff; }
    .btn-logout { padding:7px 14px; border-radius:9px; font-size:.83rem; font-weight:600; color:rgba(252,165,165,.80); background:transparent; border:none; cursor:pointer; font-family:var(--font); transition:all .2s; display:flex; align-items:center; gap:5px; }
    .btn-logout:hover { background:rgba(220,38,38,.18); color:#fca5a5; }
    .progress-track { height:3px; background:rgba(56,189,248,.08); }
    .progress-fill { height:100%; background:linear-gradient(90deg,#1a6fc4,#38bdf8,#06b6d4); width:100%; }

    /* Page body */
    .dash-body { max-width:1300px; margin:0 auto; padding:32px 28px 80px; }
    .page-title { font-size:1.5rem; font-weight:900; letter-spacing:-.5px; margin-bottom:6px; }
    .page-subtitle { font-size:.84rem; color:var(--text-muted); margin-bottom:28px; }

    /* Stat grid */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
    .stat-card {
      background:rgba(10,25,55,0.70); border:1px solid rgba(120,180,255,0.20);
      border-radius:var(--radius-lg); padding:22px 24px;
      display:flex; align-items:center; gap:16px;
      backdrop-filter:blur(18px); box-shadow:0 20px 60px rgba(0,0,0,.30);
      transition:var(--transition); position:relative; overflow:hidden;
    }
    .stat-card:hover { transform:translateY(-2px); box-shadow:0 24px 64px rgba(0,0,0,.40); }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
    .stat-card.blue::before { background:linear-gradient(90deg,#1a6fc4,#38bdf8); }
    .stat-card.green::before { background:linear-gradient(90deg,#059669,#06b6d4); }
    .stat-card.red::before { background:linear-gradient(90deg,#dc2626,#ea580c); }
    .stat-card.amber::before { background:linear-gradient(90deg,#d97706,#f59e0b); }
    .stat-icon { width:48px; height:48px; border-radius:13px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-number { font-size:1.8rem; font-weight:900; letter-spacing:-1px; line-height:1; }
    .stat-desc { font-size:.74rem; color:var(--text-muted); font-weight:500; margin-top:3px; }
    .stat-trend { font-size:.72rem; font-weight:700; margin-top:6px; }
    .stat-trend.up { color:#34d399; }

    /* Chart grid */
    .chart-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; margin-bottom:28px; }
    .chart-card {
      background:rgba(10,25,55,0.70); border:1px solid rgba(120,180,255,0.20);
      border-radius:var(--radius-xl); padding:28px;
      backdrop-filter:blur(18px); box-shadow:0 20px 60px rgba(0,0,0,.30);
    }
    .chart-card.full { grid-column:1/-1; }
    .chart-title { font-size:.88rem; font-weight:800; letter-spacing:-.2px; margin-bottom:5px; }
    .chart-subtitle { font-size:.73rem; color:var(--text-muted); margin-bottom:18px; }
    .chart-wrap { position:relative; height:260px; }
    .chart-wrap.tall { height:300px; }

    /* Top conditions list */
    .cond-list { display:flex; flex-direction:column; gap:8px; margin-top:8px; }
    .cond-item { display:flex; align-items:center; gap:12px; }
    .cond-bar-bg { flex:1; height:8px; background:rgba(255,255,255,.08); border-radius:4px; overflow:hidden; }
    .cond-bar-fill { height:100%; border-radius:4px; background:linear-gradient(90deg,#1a6fc4,#38bdf8); transition:width 1s ease; }
    .cond-name { font-size:.79rem; font-weight:600; min-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .cond-count { font-size:.79rem; font-weight:800; color:var(--primary); min-width:28px; text-align:right; }

    /* Recent assessments quick table */
    .section-head { font-size:1rem; font-weight:800; letter-spacing:-.3px; margin-bottom:14px; display:flex; align-items:center; justify-content:space-between; }
    .recent-table-wrap { background:rgba(10,25,55,0.70); border:1px solid rgba(120,180,255,0.20); border-radius:var(--radius-xl); overflow:hidden; backdrop-filter:blur(18px); box-shadow:0 20px 60px rgba(0,0,0,.30); }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:rgba(26,111,196,0.10); }
    th { padding:11px 16px; text-align:left; font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:var(--text-muted); border-bottom:1px solid var(--border); white-space:nowrap; }
    td { padding:13px 16px; font-size:.84rem; border-bottom:1px solid var(--border); color:var(--text); }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:rgba(26,111,196,.06); }
    .triage-pill { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.68rem; font-weight:800; letter-spacing:.4px; text-transform:uppercase; }
    .triage-critical{background:rgba(220,38,38,.15);color:#ef4444;border:1px solid rgba(220,38,38,.3);}
    .triage-high{background:rgba(234,88,12,.12);color:#ea580c;border:1px solid rgba(234,88,12,.3);}
    .triage-moderate{background:rgba(245,158,11,.12);color:#f59e0b;border:1px solid rgba(245,158,11,.3);}
    .triage-low{background:rgba(16,185,129,.12);color:#10b981;border:1px solid rgba(16,185,129,.3);}
    .triage-inconclusive{background:rgba(107,114,128,.12);color:#6b7280;border:1px solid rgba(107,114,128,.3);}
    .btn-sm { padding:4px 11px; border-radius:6px; background:var(--primary-lt); color:var(--primary); border:1px solid rgba(26,111,196,.25); font-size:.73rem; font-weight:700; cursor:pointer; font-family:var(--font); transition:var(--transition); text-decoration:none; display:inline-block; }
    .btn-sm:hover { background:var(--primary); color:#fff; }

    .hist-footer { text-align:center; padding:22px 20px; font-size:.74rem; color:var(--text-light); border-top:1px solid var(--border); margin-top:40px; line-height:1.8; }
    .hist-footer .footer-author { color:var(--text-muted); font-weight:700; }

    @media (max-width:960px) { .stat-grid { grid-template-columns:1fr 1fr; } .chart-grid { grid-template-columns:1fr; } }
    @media (max-width:600px) { .stat-grid { grid-template-columns:1fr 1fr; } .dash-body { padding:20px 14px; } .dash-nav { padding:12px 14px; } }
  </style>
</head>
<body>

<div class="dash-header">
  <div class="dash-nav">
    <a href="index.php" class="nav-logo">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="9" y="2" width="6" height="20" rx="2" fill="white"/><rect x="2" y="9" width="20" height="6" rx="2" fill="white"/></svg>
      Clin<span>AI</span>
    </a>
    <div class="nav-links">
      <a href="index.php" class="nav-link">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12l7-7 7 7M5 12v7a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-7"/></svg>
        New Assessment
      </a>
      <a href="dashboard.php" class="nav-link active">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="history.php" class="nav-link">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
        History
      </a>
      <?php if ($user['role'] === 'admin'): ?>
      <a href="admin.php" class="nav-link">
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        Admin
      </a>
      <?php endif; ?>
    </div>
    <div class="nav-right">
      <div class="nav-user">
        <div class="nav-avatar" style="background:<?= htmlspecialchars($user['color']) ?>"><?= strtoupper(substr($user['name'],0,1)) ?></div>
        <span><?= htmlspecialchars($user['name']) ?></span>
      </div>
      <button class="btn-logout" onclick="logout()">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </button>
    </div>
  </div>
  <div class="progress-track"><div class="progress-fill"></div></div>
</div>

<div class="dash-body">
  <div class="page-title">Analytics Dashboard</div>
  <div class="page-subtitle">Live overview of all AI assessments, triage distribution, and clinical trends.</div>

  <!-- Stat cards -->
  <div class="stat-grid" id="stat-grid">
    <div class="stat-card blue">
      <div class="stat-icon" style="background:rgba(26,111,196,.14);border:1px solid rgba(26,111,196,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div><div class="stat-number" id="st-assessments">—</div><div class="stat-desc">Total Assessments</div></div>
    </div>
    <div class="stat-card green">
      <div class="stat-icon" style="background:rgba(5,150,105,.14);border:1px solid rgba(5,150,105,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div><div class="stat-number" id="st-patients">—</div><div class="stat-desc">Patients Assessed</div></div>
    </div>
    <div class="stat-card red">
      <div class="stat-icon" style="background:rgba(220,38,38,.14);border:1px solid rgba(220,38,38,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
      </div>
      <div><div class="stat-number" id="st-critical">—</div><div class="stat-desc">Critical / High Triage</div></div>
    </div>
    <div class="stat-card amber">
      <div class="stat-icon" style="background:rgba(217,119,6,.14);border:1px solid rgba(217,119,6,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fbbf24" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div><div class="stat-number" id="st-users">—</div><div class="stat-desc">Clinical Users</div></div>
    </div>
  </div>

  <!-- Charts row 1 -->
  <div class="chart-grid">
    <div class="chart-card">
      <div class="chart-title">Triage Level Distribution</div>
      <div class="chart-subtitle">Breakdown of all assessments by urgency level</div>
      <div class="chart-wrap"><canvas id="chart-triage"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title">Top 8 Most Predicted Conditions</div>
      <div class="chart-subtitle">Highest frequency AI diagnoses across all assessments</div>
      <div id="cond-list" class="cond-list" style="margin-top:16px"></div>
    </div>
    <div class="chart-card full">
      <div class="chart-title">Assessment Activity — Last 14 Days</div>
      <div class="chart-subtitle">Daily assessment volume trend</div>
      <div class="chart-wrap tall"><canvas id="chart-weekly"></canvas></div>
    </div>
  </div>

  <!-- Recent assessments -->
  <div class="section-head">
    <span>Recent Assessments</span>
    <a href="history.php" class="btn-sm">View All →</a>
  </div>
  <div class="recent-table-wrap">
    <table>
      <thead>
        <tr><th>#</th><th>Patient</th><th>Age/Sex</th><th>Triage</th><th>Top Diagnosis</th><th>Confidence</th><th>Date</th><th>By</th><th></th></tr>
      </thead>
      <tbody id="recent-body">
        <tr><td colspan="9" style="text-align:center;padding:30px;color:var(--text-muted)">Loading…</td></tr>
      </tbody>
    </table>
  </div>

  <footer class="hist-footer">
    ClinAI v2.0 &nbsp;·&nbsp; AI-Powered Clinical Decision Support &nbsp;·&nbsp;
    For licensed healthcare professionals only &nbsp;·&nbsp;
    Not a substitute for clinical diagnosis &nbsp;·&nbsp;
    By <span class="footer-author">Gaurav Tamshete</span>
  </footer>
</div>

<script>
// Always dark
document.documentElement.setAttribute('data-theme','dark');

async function logout() {
  await fetch('php/auth.php?action=logout');
  window.location.href = 'login.php';
}

function esc(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function fmtDate(d) { return new Date(d).toLocaleString('en-US',{month:'short',day:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}); }
function cap(s) { return s ? s[0].toUpperCase()+s.slice(1) : ''; }

const CHART_COLORS = {
  critical:'#ef4444', high:'#ea580c', moderate:'#f59e0b', low:'#10b981', inconclusive:'#6b7280'
};

Chart.defaults.color = 'rgba(180,220,255,0.70)';
Chart.defaults.font.family = "'Inter', system-ui, sans-serif";

async function loadAll() {
  const [statsRes, weeklyRes, histRes] = await Promise.all([
    fetch('php/api.php?action=stats').then(r=>r.json()),
    fetch('php/api.php?action=weekly').then(r=>r.json()),
    fetch('php/api.php?action=history&limit=8&offset=0').then(r=>r.json())
  ]);

  // Stat cards
  if (statsRes.success) {
    const s = statsRes.data;
    document.getElementById('st-assessments').textContent = s.total_assessments;
    document.getElementById('st-patients').textContent = s.total_patients;
    document.getElementById('st-users').textContent = s.total_users;
    const critHigh = (s.by_triage.find(t=>t.triage_level==='critical')?.cnt||0)
                   + (s.by_triage.find(t=>t.triage_level==='high')?.cnt||0);
    document.getElementById('st-critical').textContent = critHigh;

    // Triage donut chart
    const triageOrder = ['critical','high','moderate','low','inconclusive'];
    const triageData = triageOrder.map(k => ({
      label: cap(k), value: parseInt(s.by_triage.find(t=>t.triage_level===k)?.cnt||0), color: CHART_COLORS[k]
    })).filter(d => d.value > 0);

    new Chart(document.getElementById('chart-triage'), {
      type: 'doughnut',
      data: {
        labels: triageData.map(d=>d.label),
        datasets: [{ data: triageData.map(d=>d.value), backgroundColor: triageData.map(d=>d.color), borderWidth:0, hoverOffset:8 }]
      },
      options: {
        responsive:true, maintainAspectRatio:false, cutout:'68%',
        plugins: { legend:{ position:'right', labels:{ padding:16, usePointStyle:true, pointStyle:'circle' } }, tooltip:{ callbacks:{ label: ctx => ` ${ctx.label}: ${ctx.parsed} assessments` } } }
      }
    });

    // Top conditions horizontal bars
    const maxCnt = Math.max(...s.top_conditions.map(c=>parseInt(c.cnt)));
    const condList = document.getElementById('cond-list');
    if (s.top_conditions.length === 0) {
      condList.innerHTML = '<div style="color:var(--text-muted);font-size:.82rem;padding:12px 0">No data yet — run assessments to populate.</div>';
    } else {
      condList.innerHTML = s.top_conditions.map(c => `
        <div class="cond-item">
          <div class="cond-name" title="${esc(c.top_diagnosis)}">${esc(c.top_diagnosis)}</div>
          <div class="cond-bar-bg"><div class="cond-bar-fill" style="width:${Math.round((c.cnt/maxCnt)*100)}%"></div></div>
          <div class="cond-count">${c.cnt}</div>
        </div>`).join('');
    }
  }

  // Weekly line chart
  if (weeklyRes.success) {
    // Build last 14 days scaffold
    const days = [];
    for (let i = 13; i >= 0; i--) {
      const d = new Date(); d.setDate(d.getDate()-i);
      days.push(d.toISOString().slice(0,10));
    }
    const map = {};
    weeklyRes.data.forEach(r => { map[r.day] = parseInt(r.cnt); });
    const values = days.map(d => map[d] || 0);
    const labels = days.map(d => { const dt = new Date(d+'T12:00:00'); return dt.toLocaleDateString('en-US',{month:'short',day:'numeric'}); });

    new Chart(document.getElementById('chart-weekly'), {
      type: 'line',
      data: {
        labels,
        datasets: [{
          label: 'Assessments', data: values,
          borderColor: '#38bdf8', backgroundColor: 'rgba(56,189,248,0.10)',
          borderWidth: 2.5, pointRadius: 4, pointHoverRadius: 7,
          pointBackgroundColor: '#38bdf8', fill: true, tension: 0.35
        }]
      },
      options: {
        responsive:true, maintainAspectRatio:false,
        scales: {
          x: { grid:{ color:'rgba(120,180,255,.08)' }, ticks:{ font:{ size:11 } } },
          y: { grid:{ color:'rgba(120,180,255,.08)' }, beginAtZero:true, ticks:{ stepSize:1, font:{ size:11 } } }
        },
        plugins: { legend:{ display:false }, tooltip:{ callbacks:{ label: ctx => ` ${ctx.parsed.y} assessment${ctx.parsed.y!==1?'s':''}` } } }
      }
    });
  }

  // Recent assessments table
  if (histRes.success) {
    const body = document.getElementById('recent-body');
    if (!histRes.data.length) {
      body.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--text-muted)">No assessments yet — <a href="index.php" style="color:var(--primary)">run your first assessment</a></td></tr>';
    } else {
      body.innerHTML = histRes.data.map((r,i) => `
        <tr>
          <td style="color:var(--text-muted);font-weight:600">${i+1}</td>
          <td><strong>${esc(r.patient_name)}</strong>${r.mrn?`<br><span style="font-size:.7rem;color:var(--text-muted)">MRN:${esc(r.mrn)}</span>`:''}</td>
          <td>${r.age} / ${cap(r.gender)}</td>
          <td><span class="triage-pill triage-${r.triage_level}">${r.triage_level}</span></td>
          <td style="font-size:.82rem">${r.top_diagnosis ? esc(r.top_diagnosis) : '<span style="color:var(--text-muted)">—</span>'}</td>
          <td>${r.top_confidence ? `<span style="font-weight:700;color:var(--primary)">${r.top_confidence}%</span>` : '—'}</td>
          <td style="font-size:.76rem;white-space:nowrap">${fmtDate(r.created_at)}</td>
          <td style="font-size:.76rem">${r.assessed_by ? esc(r.assessed_by) : '—'}</td>
          <td><a href="assessment.php?id=${r.id}" class="btn-sm">View</a></td>
        </tr>`).join('');
    }
  }
}

loadAll().catch(e => console.error('Dashboard error:', e));
</script>
</body>
</html>

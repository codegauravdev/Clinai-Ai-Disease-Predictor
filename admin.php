<?php
require_once __DIR__ . '/php/config.php';
requireLogin();
$user = currentUser();
if ($user['role'] !== 'admin') { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ClinAI — Admin Panel</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css?v=50"/>
  <style>
    body::before { content:''; position:fixed; inset:0; z-index:-2; background:linear-gradient(160deg,#020c22 0%,#051428 25%,#071e3d 50%,#082040 75%,#030e20 100%); }
    body::after { content:''; position:fixed; inset:0; z-index:-1; background: radial-gradient(ellipse 80% 60% at 20% 10%,rgba(26,111,196,.20) 0%,transparent 55%), radial-gradient(ellipse 60% 50% at 85% 80%,rgba(6,182,212,.16) 0%,transparent 50%); }
    .dash-header { background:linear-gradient(135deg,#020f28 0%,#061c45 30%,#0c3070 60%,#1048a0 85%,#1a6fc4 100%); position:sticky; top:0; z-index:200; box-shadow:0 4px 40px rgba(0,0,0,.38); border-bottom:1px solid rgba(56,189,248,.14); backdrop-filter:blur(20px); }
    .dash-nav { max-width:1200px; margin:0 auto; padding:14px 28px; display:flex; align-items:center; gap:16px; }
    .nav-logo { font-size:1.1rem; font-weight:800; color:#fff; display:flex; align-items:center; gap:10px; text-decoration:none; }
    .nav-logo span { background:linear-gradient(135deg,#38bdf8,#06b6d4); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .nav-links { display:flex; gap:4px; margin-left:16px; }
    .nav-link { padding:7px 14px; border-radius:9px; font-size:.83rem; font-weight:600; color:rgba(180,220,255,.75); text-decoration:none; transition:all .2s; display:flex; align-items:center; gap:5px; }
    .nav-link:hover,.nav-link.active { background:rgba(255,255,255,.12); color:#fff; }
    .nav-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
    .nav-user { display:flex; align-items:center; gap:8px; padding:6px 14px 6px 8px; background:rgba(255,255,255,.08); border:1px solid rgba(120,200,255,.20); border-radius:40px; color:#fff; font-size:.83rem; font-weight:600; }
    .nav-avatar { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:800; color:#fff; }
    .btn-logout { padding:7px 14px; border-radius:9px; font-size:.83rem; font-weight:600; color:rgba(252,165,165,.80); background:transparent; border:none; cursor:pointer; font-family:var(--font); transition:all .2s; }
    .btn-logout:hover { background:rgba(220,38,38,.18); color:#fca5a5; }
    .progress-track { height:3px; background:rgba(56,189,248,.08); }
    .progress-fill { height:100%; background:linear-gradient(90deg,#1a6fc4,#38bdf8,#06b6d4); width:100%; }

    .page-body { max-width:1200px; margin:0 auto; padding:32px 28px 80px; }
    .page-title { font-size:1.4rem; font-weight:900; letter-spacing:-.4px; margin-bottom:5px; display:flex; align-items:center; gap:12px; }
    .admin-badge { background:linear-gradient(135deg,#dc2626,#ea580c); color:#fff; font-size:.62rem; font-weight:800; padding:3px 10px; border-radius:20px; letter-spacing:.5px; text-transform:uppercase; }
    .page-subtitle { font-size:.83rem; color:var(--text-muted); margin-bottom:28px; }

    /* Stat cards */
    .stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:16px; margin-bottom:28px; }
    .stat-card { background:rgba(10,25,55,.70); border:1px solid rgba(120,180,255,.20); border-radius:var(--radius-lg); padding:20px 22px; display:flex; align-items:center; gap:14px; backdrop-filter:blur(18px); box-shadow:0 20px 60px rgba(0,0,0,.30); }
    .stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-number { font-size:1.7rem; font-weight:900; letter-spacing:-1px; line-height:1; }
    .stat-desc { font-size:.73rem; color:var(--text-muted); font-weight:500; margin-top:2px; }

    /* User table */
    .glass-card { background:rgba(10,25,55,.70); border:1px solid rgba(120,180,255,.20); border-radius:var(--radius-xl); overflow:hidden; backdrop-filter:blur(18px); box-shadow:0 20px 60px rgba(0,0,0,.30); margin-bottom:24px; }
    .table-header { padding:20px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); }
    .table-title { font-size:1rem; font-weight:800; letter-spacing:-.2px; }
    table { width:100%; border-collapse:collapse; }
    thead tr { background:rgba(26,111,196,.10); }
    th { padding:11px 16px; text-align:left; font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.7px; color:var(--text-muted); border-bottom:1px solid var(--border); white-space:nowrap; }
    td { padding:13px 16px; font-size:.84rem; border-bottom:1px solid var(--border); color:var(--text); }
    tbody tr:last-child td { border-bottom:none; }
    tbody tr:hover td { background:rgba(26,111,196,.06); }
    .role-pill { display:inline-flex; padding:2px 10px; border-radius:20px; font-size:.68rem; font-weight:800; letter-spacing:.4px; text-transform:uppercase; }
    .role-admin { background:rgba(220,38,38,.15); color:#ef4444; border:1px solid rgba(220,38,38,.3); }
    .role-doctor { background:rgba(26,111,196,.15); color:#38bdf8; border:1px solid rgba(26,111,196,.3); }
    .role-nurse { background:rgba(5,150,105,.15); color:#34d399; border:1px solid rgba(5,150,105,.3); }
    .status-badge { display:inline-flex; padding:2px 10px; border-radius:20px; font-size:.68rem; font-weight:800; letter-spacing:.3px; }
    .status-active { background:rgba(5,150,105,.15); color:#34d399; border:1px solid rgba(5,150,105,.3); }
    .status-inactive { background:rgba(107,114,128,.15); color:#9ca3af; border:1px solid rgba(107,114,128,.3); }
    .btn-toggle { padding:5px 13px; border-radius:7px; font-size:.72rem; font-weight:700; cursor:pointer; font-family:var(--font); transition:all .2s; border:none; }
    .btn-activate { background:rgba(5,150,105,.15); color:#34d399; border:1px solid rgba(5,150,105,.3); }
    .btn-activate:hover { background:#059669; color:#fff; }
    .btn-deactivate { background:rgba(220,38,38,.12); color:#ef4444; border:1px solid rgba(220,38,38,.25); }
    .btn-deactivate:hover { background:#dc2626; color:#fff; }
    .current-badge { font-size:.66rem; background:rgba(56,189,248,.15); color:#38bdf8; border:1px solid rgba(56,189,248,.25); padding:2px 8px; border-radius:20px; font-weight:700; }

    .hist-footer { text-align:center; padding:22px 20px; font-size:.74rem; color:var(--text-light); border-top:1px solid var(--border); margin-top:40px; line-height:1.8; }
    .hist-footer .footer-author { color:var(--text-muted); font-weight:700; }
    @media (max-width:800px) { .stat-row { grid-template-columns:1fr 1fr; } .page-body { padding:20px 14px; } }
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
      <a href="index.php" class="nav-link">New Assessment</a>
      <a href="dashboard.php" class="nav-link">Dashboard</a>
      <a href="history.php" class="nav-link">History</a>
      <a href="admin.php" class="nav-link active">Admin</a>
    </div>
    <div class="nav-right">
      <div class="nav-user">
        <div class="nav-avatar" style="background:<?= htmlspecialchars($user['color']) ?>"><?= strtoupper(substr($user['name'],0,1)) ?></div>
        <span><?= htmlspecialchars($user['name']) ?></span>
      </div>
      <button class="btn-logout" onclick="logout()">Sign Out</button>
    </div>
  </div>
  <div class="progress-track"><div class="progress-fill"></div></div>
</div>

<div class="page-body">
  <div class="page-title">
    Admin Panel <span class="admin-badge">Admin Only</span>
  </div>
  <div class="page-subtitle">User management, system overview, and account controls. Only visible to administrators.</div>

  <div class="stat-row" id="stat-row">
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(26,111,196,.14);border:1px solid rgba(26,111,196,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#38bdf8" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      </div>
      <div><div class="stat-number" id="st-users">—</div><div class="stat-desc">Total Users</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(5,150,105,.14);border:1px solid rgba(5,150,105,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#34d399" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      </div>
      <div><div class="stat-number" id="st-assessments">—</div><div class="stat-desc">Total Assessments</div></div>
    </div>
    <div class="stat-card">
      <div class="stat-icon" style="background:rgba(220,38,38,.14);border:1px solid rgba(220,38,38,.22)">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f87171" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </div>
      <div><div class="stat-number" id="st-patients">—</div><div class="stat-desc">Patients on Record</div></div>
    </div>
  </div>

  <div class="glass-card">
    <div class="table-header">
      <div class="table-title">Registered Users</div>
      <div style="font-size:.78rem;color:var(--text-muted)">Click Activate/Deactivate to change access</div>
    </div>
    <table id="users-table">
      <thead>
        <tr><th>#</th><th>Name</th><th>Email</th><th>Role</th><th>Department</th><th>Status</th><th>Last Login</th><th>Registered</th><th>Action</th></tr>
      </thead>
      <tbody id="users-body">
        <tr><td colspan="9" style="text-align:center;padding:30px;color:var(--text-muted)">Loading…</td></tr>
      </tbody>
    </table>
  </div>

  <footer class="hist-footer">
    ClinAI v2.0 &nbsp;·&nbsp; Admin Panel &nbsp;·&nbsp; AI-Powered Clinical Decision Support &nbsp;·&nbsp;
    By <span class="footer-author">Gaurav Tamshete</span>
  </footer>
</div>

<script>
document.documentElement.setAttribute('data-theme','dark');

const CURRENT_USER_ID = <?= (int)$user['id'] ?>;

async function logout() { await fetch('php/auth.php?action=logout'); window.location.href = 'login.php'; }
function esc(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function fmtDate(d) { if(!d) return '—'; return new Date(d).toLocaleString('en-US',{month:'short',day:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'}); }
function cap(s) { return s ? s[0].toUpperCase()+s.slice(1) : ''; }

async function loadAll() {
  const [statsRes, usersRes] = await Promise.all([
    fetch('php/api.php?action=stats').then(r=>r.json()),
    fetch('php/api.php?action=users').then(r=>r.json())
  ]);

  if (statsRes.success) {
    const s = statsRes.data;
    document.getElementById('st-users').textContent = s.total_users;
    document.getElementById('st-assessments').textContent = s.total_assessments;
    document.getElementById('st-patients').textContent = s.total_patients;
  }

  if (usersRes.success) {
    renderUsers(usersRes.data);
  }
}

function renderUsers(users) {
  const body = document.getElementById('users-body');
  if (!users.length) { body.innerHTML = '<tr><td colspan="9" style="text-align:center;padding:30px;color:var(--text-muted)">No users found</td></tr>'; return; }
  body.innerHTML = users.map((u,i) => `
    <tr>
      <td style="color:var(--text-muted);font-weight:600">${i+1}</td>
      <td>
        <strong>${esc(u.full_name)}</strong>
        ${u.id == CURRENT_USER_ID ? ' <span class="current-badge">You</span>' : ''}
      </td>
      <td style="font-size:.8rem;color:var(--text-muted)">${esc(u.email)}</td>
      <td><span class="role-pill role-${u.role}">${u.role}</span></td>
      <td style="font-size:.8rem">${u.department ? esc(u.department) : '<span style="color:var(--text-muted)">—</span>'}</td>
      <td><span class="status-badge ${u.is_active ? 'status-active' : 'status-inactive'}">${u.is_active ? 'Active' : 'Inactive'}</span></td>
      <td style="font-size:.76rem">${fmtDate(u.last_login)}</td>
      <td style="font-size:.76rem">${fmtDate(u.created_at)}</td>
      <td>
        ${u.id != CURRENT_USER_ID ? `
          <button class="btn-toggle ${u.is_active ? 'btn-deactivate' : 'btn-activate'}"
                  onclick="toggleUser(${u.id}, ${u.is_active ? 0 : 1}, this)">
            ${u.is_active ? 'Deactivate' : 'Activate'}
          </button>` : '<span style="color:var(--text-muted);font-size:.76rem">N/A</span>'}
      </td>
    </tr>`).join('');
}

async function toggleUser(uid, newActive, btn) {
  const label = newActive ? 'activate' : 'deactivate';
  if (!confirm(`${cap(label)} this user account?`)) return;
  btn.disabled = true; btn.textContent = 'Updating…';
  try {
    const res  = await fetch(`php/api.php?action=user_toggle&id=${uid}&active=${newActive}`);
    const data = await res.json();
    if (data.success) { loadAll(); }
    else { alert('Failed: ' + data.error); btn.disabled = false; }
  } catch(e) { alert('Network error'); btn.disabled = false; }
}

loadAll();
</script>
</body>
</html>

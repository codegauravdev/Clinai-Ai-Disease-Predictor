<?php
require_once __DIR__ . '/php/config.php';
requireLogin();
$user = currentUser();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: history.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ClinAI — Assessment Detail</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css?v=50"/>
  <style>
    body::before { content:''; position:fixed; inset:0; z-index:-2; background:linear-gradient(160deg,#020c22 0%,#051428 25%,#071e3d 50%,#082040 75%,#030e20 100%); }
    body::after { content:''; position:fixed; inset:0; z-index:-1; background: radial-gradient(ellipse 80% 60% at 20% 10%,rgba(26,111,196,.20) 0%,transparent 55%), radial-gradient(ellipse 60% 50% at 85% 80%,rgba(6,182,212,.16) 0%,transparent 50%), linear-gradient(rgba(56,189,248,.025) 1px,transparent 1px) 0 0/50px 50px, linear-gradient(90deg,rgba(56,189,248,.025) 1px,transparent 1px) 0 0/50px 50px; }

    /* Header */
    .dash-header { background:linear-gradient(135deg,#020f28 0%,#061c45 30%,#0c3070 60%,#1048a0 85%,#1a6fc4 100%); position:sticky; top:0; z-index:200; box-shadow:0 4px 40px rgba(0,0,0,.38); border-bottom:1px solid rgba(56,189,248,0.14); backdrop-filter:blur(20px); }
    .dash-nav { max-width:1100px; margin:0 auto; padding:14px 28px; display:flex; align-items:center; gap:16px; }
    .nav-logo { font-size:1.1rem; font-weight:800; color:#fff; display:flex; align-items:center; gap:10px; text-decoration:none; }
    .nav-logo span { background:linear-gradient(135deg,#38bdf8,#06b6d4); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }
    .nav-links { display:flex; gap:4px; margin-left:16px; }
    .nav-link { padding:7px 14px; border-radius:9px; font-size:.83rem; font-weight:600; color:rgba(180,220,255,.75); text-decoration:none; transition:all .2s; display:flex; align-items:center; gap:5px; }
    .nav-link:hover { background:rgba(255,255,255,.12); color:#fff; }
    .nav-right { margin-left:auto; display:flex; align-items:center; gap:10px; }
    .nav-user { display:flex; align-items:center; gap:8px; padding:6px 14px 6px 8px; background:rgba(255,255,255,.08); border:1px solid rgba(120,200,255,.20); border-radius:40px; color:#fff; font-size:.83rem; font-weight:600; }
    .nav-avatar { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:800; color:#fff; }
    .btn-logout { padding:7px 14px; border-radius:9px; font-size:.83rem; font-weight:600; color:rgba(252,165,165,.80); background:transparent; border:none; cursor:pointer; font-family:var(--font); transition:all .2s; display:flex; align-items:center; gap:5px; }
    .btn-logout:hover { background:rgba(220,38,38,.18); color:#fca5a5; }
    .progress-track { height:3px; background:rgba(56,189,248,.08); }
    .progress-fill { height:100%; background:linear-gradient(90deg,#1a6fc4,#38bdf8,#06b6d4); width:100%; }

    /* Page */
    .page-body { max-width:1100px; margin:0 auto; padding:32px 28px 80px; }
    .action-toolbar { display:flex; align-items:center; gap:10px; margin-bottom:24px; flex-wrap:wrap; }
    .btn-back { padding:9px 18px; border-radius:9px; background:rgba(10,25,55,.70); border:1px solid rgba(120,180,255,.20); color:rgba(180,220,255,.80); font-size:.84rem; font-weight:600; cursor:pointer; font-family:var(--font); text-decoration:none; display:inline-flex; align-items:center; gap:7px; transition:all .2s; }
    .btn-back:hover { background:rgba(26,111,196,.18); border-color:rgba(56,189,248,.30); color:#fff; }
    .btn-print { padding:9px 18px; border-radius:9px; background:linear-gradient(135deg,#1a6fc4,#06b6d4); color:#fff; font-size:.84rem; font-weight:700; cursor:pointer; font-family:var(--font); border:none; display:inline-flex; align-items:center; gap:7px; transition:all .2s; text-decoration:none; }
    .btn-print:hover { transform:translateY(-1px); box-shadow:0 8px 24px rgba(26,111,196,.40); }
    .btn-delete { padding:9px 18px; border-radius:9px; background:rgba(220,38,38,.12); border:1px solid rgba(220,38,38,.25); color:#ef4444; font-size:.84rem; font-weight:600; cursor:pointer; font-family:var(--font); display:inline-flex; align-items:center; gap:7px; transition:all .2s; }
    .btn-delete:hover { background:rgba(220,38,38,.22); }

    /* Glass card */
    .glass-card { background:rgba(10,25,55,0.70); border:1px solid rgba(120,180,255,0.20); border-radius:var(--radius-xl); padding:28px; backdrop-filter:blur(18px); box-shadow:0 20px 60px rgba(0,0,0,.30); margin-bottom:20px; }

    /* Patient header */
    .patient-header { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .patient-avatar { width:56px; height:56px; border-radius:14px; background:linear-gradient(135deg,#1a6fc4,#38bdf8); display:flex; align-items:center; justify-content:center; font-size:24px; flex-shrink:0; }
    .patient-info { flex:1; }
    .patient-name { font-size:1.4rem; font-weight:900; letter-spacing:-.5px; margin-bottom:5px; }
    .patient-meta { font-size:.83rem; color:var(--text-muted); display:flex; flex-wrap:wrap; gap:12px; }
    .patient-meta span { display:flex; align-items:center; gap:4px; }
    .triage-badge { padding:10px 20px; border-radius:10px; color:#fff; font-size:.84rem; font-weight:800; letter-spacing:.2px; display:flex; align-items:center; gap:8px; white-space:nowrap; flex-shrink:0; }

    /* Triage colors */
    .tc-critical { background:linear-gradient(135deg,#b91c1c,#dc2626); }
    .tc-high { background:linear-gradient(135deg,#c2410c,#ea580c); }
    .tc-moderate { background:linear-gradient(135deg,#b45309,#d97706); }
    .tc-low { background:linear-gradient(135deg,#047857,#059669); }
    .tc-inconclusive { background:linear-gradient(135deg,#4b5563,#6b7280); }

    /* Section label */
    .sec-label { font-size:.67rem; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:var(--text-muted); margin-bottom:12px; display:flex; align-items:center; gap:8px; }
    .sec-label::before { content:''; display:inline-block; width:3px; height:12px; background:linear-gradient(135deg,#1a6fc4,#38bdf8); border-radius:2px; }

    /* Vitals grid */
    .vitals-row { display:flex; flex-wrap:wrap; gap:10px; }
    .vital-box { padding:12px 16px; border-radius:10px; border:1.5px solid; min-width:90px; text-align:center; }
    .vital-box.normal { border-color:rgba(5,150,105,.40); background:rgba(5,150,105,.10); }
    .vital-box.warn { border-color:rgba(217,119,6,.40); background:rgba(217,119,6,.10); }
    .vital-box.danger { border-color:rgba(220,38,38,.40); background:rgba(220,38,38,.10); }
    .vital-box.none { border-color:var(--border); background:var(--surface2); }
    .vb-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:var(--text-muted); }
    .vb-val { font-size:1.15rem; font-weight:900; margin-top:3px; }
    .vb-unit { font-size:.62rem; font-weight:400; }
    .normal .vb-val { color:#34d399; } .warn .vb-val { color:#fbbf24; } .danger .vb-val { color:#f87171; } .none .vb-val { color:var(--text-muted); }

    /* Symptom chips */
    .chip-row { display:flex; flex-wrap:wrap; gap:7px; }
    .chip { padding:5px 13px; border:1.5px solid var(--border); border-radius:20px; background:var(--surface2); font-size:.78rem; font-weight:500; color:var(--text-muted); }

    /* Disease result card */
    .disease-result-card { background:rgba(5,15,40,.50); border:1px solid rgba(120,180,255,.18); border-radius:var(--radius-lg); padding:20px 24px; margin-bottom:12px; position:relative; overflow:hidden; }
    .disease-result-card.primary-result { border-color:rgba(56,189,248,.35); box-shadow:0 0 0 3px rgba(26,111,196,.15); }
    .disease-result-card.primary-result::after { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#1a6fc4,#38bdf8); }
    .drc-header { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:10px; }
    .primary-label { display:inline-block; background:linear-gradient(135deg,#1a6fc4,#38bdf8); color:#fff; font-size:.62rem; font-weight:800; padding:2px 9px; border-radius:4px; margin-bottom:6px; letter-spacing:.5px; text-transform:uppercase; }
    .drc-name { font-size:1.05rem; font-weight:800; margin-bottom:2px; }
    .drc-meta { font-size:.72rem; color:var(--text-muted); }
    .conf-ring { position:relative; width:58px; height:58px; flex-shrink:0; }
    .conf-ring svg { width:100%; height:100%; transform:rotate(-90deg); }
    .conf-ring .cr-bg { fill:none; stroke:rgba(255,255,255,.10); stroke-width:3.5; }
    .conf-ring .cr-fill { fill:none; stroke-width:3.5; stroke-linecap:round; }
    .conf-ring .cr-text { position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); font-size:.78rem; font-weight:900; }
    .drc-desc { font-size:.82rem; color:var(--text-muted); margin-bottom:10px; line-height:1.6; }
    .urgency-tag { display:inline-flex; align-items:center; gap:5px; border:1.5px solid; border-radius:6px; padding:2px 10px; font-size:.67rem; font-weight:800; letter-spacing:.5px; text-transform:uppercase; margin-bottom:10px; }
    .match-chips { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:10px; }
    .match-chip { background:rgba(26,111,196,.12); color:var(--primary); border:1px solid rgba(26,111,196,.22); padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:600; }

    /* Rec list */
    .rec-list { list-style:none; display:flex; flex-direction:column; gap:5px; }
    .rec-list li { padding:7px 12px 7px 28px; background:rgba(255,255,255,.04); border:1px solid var(--border); border-radius:7px; font-size:.8rem; position:relative; }
    .rec-list li::before { content:"→"; position:absolute; left:10px; color:var(--primary); font-weight:700; }

    /* Notes box */
    .notes-box { background:rgba(26,111,196,.08); border:1px solid rgba(26,111,196,.20); border-radius:10px; padding:14px 18px; font-size:.84rem; color:var(--text-muted); line-height:1.65; }

    /* Disclaimer */
    .disclaimer { background:rgba(217,119,6,.07); border:1px solid rgba(217,119,6,.22); border-left:4px solid #d97706; border-radius:0 10px 10px 0; padding:12px 16px; font-size:.78rem; color:#fcd34d; line-height:1.65; margin-top:4px; }

    .hist-footer { text-align:center; padding:22px 20px; font-size:.74rem; color:var(--text-light); border-top:1px solid var(--border); margin-top:40px; line-height:1.8; }
    .hist-footer .footer-author { color:var(--text-muted); font-weight:700; }

    @media (max-width:600px) { .page-body { padding:20px 14px; } .action-toolbar { flex-direction:column; align-items:stretch; } .patient-header { flex-direction:column; } }
    @media print { .dash-header,.action-toolbar { display:none !important; } body { background:#fff !important; } body::before,body::after { display:none !important; } .glass-card { background:#fff; border:1px solid #ddd; box-shadow:none; } }
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

<div class="page-body" id="page-body">
  <div class="action-toolbar">
    <a href="history.php" class="btn-back">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      Back to History
    </a>
    <a href="report.php?id=<?= $id ?>" class="btn-print" target="_blank">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 6 2 18 2 18 9"/><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
      Print / PDF Report
    </button>
    </a>
    <button class="btn-delete" id="delete-btn" onclick="deleteAssessment()">
      <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
      Delete
    </button>
  </div>
  <div id="assessment-container">
    <div style="text-align:center;padding:60px;color:var(--text-muted)">Loading assessment…</div>
  </div>
  <footer class="hist-footer">
    ClinAI v2.0 &nbsp;·&nbsp; AI-Powered Clinical Decision Support &nbsp;·&nbsp;
    For licensed healthcare professionals only &nbsp;·&nbsp;
    Not a substitute for clinical diagnosis &nbsp;·&nbsp;
    By <span class="footer-author">Gaurav Tamshete</span>
  </footer>
</div>

<script>
document.documentElement.setAttribute('data-theme','dark');

const assessmentId = <?= $id ?>;
let assessmentData = null;

async function logout() { await fetch('php/auth.php?action=logout'); window.location.href = 'login.php'; }
function esc(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function cap(s) { return s ? s[0].toUpperCase()+s.slice(1) : ''; }

const URGENCY_COLOR = { critical:'#dc2626', high:'#ea580c', moderate:'#d97706', low:'#16a34a' };

async function loadAssessment() {
  try {
    const res  = await fetch(`php/api.php?action=get&id=${assessmentId}`);
    const data = await res.json();
    if (!data.success) { document.getElementById('assessment-container').innerHTML = `<div class="glass-card" style="text-align:center;padding:40px;color:var(--danger)">Error: ${esc(data.error)}</div>`; return; }
    assessmentData = data.data;
    renderAssessment(data.data);
  } catch(e) {
    document.getElementById('assessment-container').innerHTML = '<div class="glass-card" style="text-align:center;padding:40px;color:var(--danger)">Network error — is XAMPP running?</div>';
  }
}

function getVitalStatus(label, value) {
  const ranges = {
    'Temp': { normal:[36.1,37.2], warn:[37.3,38.4] },
    'HR':   { normal:[60,100], warn:[50,59] },
    'SpO2': { normal:[95,100], warn:[92,94] },
    'RR':   { normal:[12,20], warn:[21,24] },
    'SBP':  { normal:[90,119], warn:[120,139] },
    'DBP':  { normal:[60,79], warn:[80,89] },
    'Glucose': { normal:[70,99], warn:[100,125] }
  };
  if (value == null) return 'none';
  const r = ranges[label];
  if (!r) return 'normal';
  if (value >= r.normal[0] && value <= r.normal[1]) return 'normal';
  if (r.warn && value >= r.warn[0] && value <= r.warn[1]) return 'warn';
  return 'danger';
}

function renderAssessment(a) {
  const gIcon = a.gender === 'female' ? '👩' : a.gender === 'male' ? '👨' : '🧑';
  const tClass = `tc-${a.triage_level || 'inconclusive'}`;
  const triageLabel = a.triage_level ? cap(a.triage_level) : 'Inconclusive';
  const triageIcon = a.triage_level === 'critical' ? '🚨' : a.triage_level === 'high' ? '⚠️' : a.triage_level === 'moderate' ? '⚡' : '✅';
  const fmtDate = d => new Date(d).toLocaleString('en-US',{weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit'});
  const bmi = (a.weight_kg && a.height_cm) ? (a.weight_kg / Math.pow(a.height_cm/100,2)).toFixed(1) : null;

  const vitalsData = [
    { lbl:'Temp', val:a.v_temperature, unit:'°C' },
    { lbl:'SBP', val:a.v_systolic_bp, unit:'mmHg' },
    { lbl:'DBP', val:a.v_diastolic_bp, unit:'mmHg' },
    { lbl:'HR', val:a.v_heart_rate, unit:'bpm' },
    { lbl:'SpO2', val:a.v_spo2, unit:'%' },
    { lbl:'RR', val:a.v_resp_rate, unit:'/min' },
    { lbl:'Glucose', val:a.v_blood_glucose, unit:'mg/dL' },
  ];

  const predictions = a.predictions_json || [];
  const symptoms    = a.symptoms_json || [];

  let html = `
    <div class="glass-card">
      <div class="patient-header">
        <div style="display:flex;align-items:flex-start;gap:16px">
          <div class="patient-avatar">${gIcon}</div>
          <div class="patient-info">
            <div class="patient-name">${esc(a.patient_name)}</div>
            <div class="patient-meta">
              <span>🎂 Age ${a.age}</span>
              <span>⚕️ ${cap(a.gender)}</span>
              ${a.mrn ? `<span>🆔 MRN: ${esc(a.mrn)}</span>` : ''}
              ${a.ward ? `<span>🏥 ${esc(a.ward)}</span>` : ''}
              ${bmi ? `<span>⚖️ BMI: ${bmi}</span>` : ''}
              ${a.weight_kg ? `<span>Weight: ${a.weight_kg}kg</span>` : ''}
              ${a.height_cm ? `<span>Height: ${a.height_cm}cm</span>` : ''}
            </div>
            <div style="font-size:.73rem;color:var(--text-muted);margin-top:8px">
              🕐 ${fmtDate(a.created_at)}
              ${a.assessed_by_name ? ` &nbsp;·&nbsp; 👨‍⚕️ ${esc(a.assessed_by_name)} (${cap(a.assessor_role||'')})` : ''}
            </div>
          </div>
        </div>
        <div class="triage-badge ${tClass}">${triageIcon} ${triageLabel} Triage</div>
      </div>
    </div>

    <div class="glass-card">
      <div class="sec-label">Vital Signs</div>
      <div class="vitals-row">
        ${vitalsData.map(v => {
          const st = getVitalStatus(v.lbl, v.val);
          return `<div class="vital-box ${st}"><div class="vb-label">${v.lbl}</div><div class="vb-val">${v.val ?? '—'}<span class="vb-unit">${v.val != null ? v.unit : ''}</span></div></div>`;
        }).join('')}
      </div>
    </div>

    <div class="glass-card">
      <div class="sec-label">Reported Symptoms (${symptoms.length})</div>
      <div class="chip-row">${symptoms.map(s => `<span class="chip">${esc(s.replace(/_/g,' '))}</span>`).join('') || '<span style="color:var(--text-muted);font-size:.83rem">No symptoms recorded</span>'}</div>
    </div>`;

  // Risk factors
  const rfMap = {
    rf_smoker:'🚬 Current Smoker', rf_diabetes:'🩸 Known Diabetes', rf_hypertension:'💉 Hypertension',
    rf_immunocompromised:'🛡️ Immunocompromised', rf_pregnant:'🤰 Pregnant', rf_family_history:'👨‍👩‍👦 Family History CVD/DM',
    rf_travel:'✈️ Recent Tropical Travel', rf_tb_contact:'🫁 TB Contact', rf_hiv:'🧬 HIV Positive',
    rf_stress:'🧠 High Stress', rf_catheter:'🏥 Urinary Catheter'
  };
  const activeRFs = Object.entries(rfMap).filter(([k]) => a[k] == 1).map(([,v]) => v);
  if (activeRFs.length) {
    html += `<div class="glass-card"><div class="sec-label">Risk Factors</div><div class="chip-row">${activeRFs.map(r=>`<span class="chip" style="border-color:rgba(217,119,6,.30);color:#fcd34d;background:rgba(217,119,6,.08)">${r}</span>`).join('')}</div></div>`;
  }

  // Clinical notes
  if (a.notes) {
    html += `<div class="glass-card"><div class="sec-label">Clinical Notes</div><div class="notes-box">${esc(a.notes)}</div></div>`;
  }

  // AI Predictions
  if (predictions.length) {
    html += `<div class="sec-label" style="margin-bottom:14px">AI Prediction Results (${predictions.length} conditions identified)</div>`;
    html += predictions.map((pred, i) => {
      const d = pred.disease || {};
      const conf = pred.confidence || 0;
      const uc = URGENCY_COLOR[pred.urgency] || '#6b7280';
      const cc = conf>=70?'#ef4444':conf>=45?'#ea580c':conf>=25?'#d97706':'#16a34a';
      const circ = Math.round(conf * 100) / 100;
      return `
        <div class="disease-result-card ${i===0?'primary-result':''}">
          <div class="drc-header">
            <div>
              ${i===0?'<div class="primary-label">Primary Prediction</div>':''}
              <div class="drc-name">${esc(d.name||'')}</div>
              <div class="drc-meta">ICD-10: ${esc(d.icd10||'')} &nbsp;·&nbsp; ${esc(d.specialty||'')}</div>
            </div>
            <div class="conf-ring">
              <svg viewBox="0 0 36 36">
                <path class="cr-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                <path class="cr-fill" style="stroke:${cc};stroke-dasharray:${circ},100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
              </svg>
              <div class="cr-text" style="color:${cc}">${conf}%</div>
            </div>
          </div>
          <div class="urgency-tag" style="border-color:${uc};color:${uc}">Urgency: ${(pred.urgency||'').toUpperCase()}</div>
          ${pred.matchedSymptoms && pred.matchedSymptoms.length ? `
            <div style="font-size:.67rem;font-weight:800;text-transform:uppercase;letter-spacing:.7px;color:var(--text-muted);margin-bottom:7px">Matched Symptoms (${pred.matchedSymptoms.length})</div>
            <div class="match-chips">${pred.matchedSymptoms.map(s=>`<span class="match-chip">${esc(s.replace(/_/g,' '))}</span>`).join('')}</div>
          ` : ''}
        </div>`;
    }).join('');
  }

  html += `<div class="disclaimer"><strong>⚕️ Medical Disclaimer:</strong> This AI-assisted analysis is for clinical decision-support only and does not constitute a definitive diagnosis. All findings must be interpreted by a qualified healthcare professional. In emergencies, call emergency services immediately.</div>`;

  document.getElementById('assessment-container').innerHTML = html;
}

async function deleteAssessment() {
  if (!confirm('Delete this assessment? This cannot be undone.')) return;
  const btn = document.getElementById('delete-btn');
  btn.disabled = true; btn.textContent = 'Deleting…';
  try {
    const res  = await fetch(`php/api.php?action=delete&id=${assessmentId}`);
    const data = await res.json();
    if (data.success) { window.location.href = 'history.php'; }
    else { alert('Delete failed: ' + data.error); btn.disabled = false; btn.innerHTML = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg> Delete'; }
  } catch(e) { alert('Network error'); btn.disabled = false; }
}

loadAssessment();
</script>
</body>
</html>

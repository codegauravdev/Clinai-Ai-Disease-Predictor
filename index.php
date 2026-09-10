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
  <title>ClinAI — AI Disease Predictor | Hospital Edition</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="css/style.css?v=50"/>
  <style>
    /* ══════════════════════════════════════════════════════════════════════
       PREMIUM MEDICAL BACKGROUND + HOSPITAL HUD SYSTEM
       ══════════════════════════════════════════════════════════════════════ */

    /* ── Full-screen medical background ─────────────────────────────────── */
    .hospital-bg {
      position: fixed; inset: 0; z-index: -2;
      pointer-events: none;
      overflow: hidden;
    }

    /* Deep navy-to-blue base gradient — clinical atmosphere */
    .hospital-bg-base {
      position: absolute; inset: 0;
      background: linear-gradient(160deg,
        #020c22 0%,
        #051428 25%,
        #071e3d 45%,
        #082040 65%,
        #051428 85%,
        #030e20 100%);
    }

    /* Subtle radial depth lighting — simulates clinical monitor glow */
    .hospital-bg-light {
      position: absolute; inset: 0;
      background:
        radial-gradient(ellipse 90% 70% at 15% 20%, rgba(26,111,196,0.22) 0%, transparent 55%),
        radial-gradient(ellipse 60% 60% at 85% 80%, rgba(6,182,212,0.18) 0%, transparent 50%),
        radial-gradient(ellipse 40% 50% at 50% 50%, rgba(14,80,160,0.12) 0%, transparent 60%),
        radial-gradient(ellipse 30% 40% at 70% 15%, rgba(56,189,248,0.10) 0%, transparent 45%);
    }

    /* Fine grid overlay — clinical workstation feel */
    .hospital-bg-grid {
      position: absolute; inset: 0;
      background-image:
        linear-gradient(rgba(56,189,248,0.025) 1px, transparent 1px),
        linear-gradient(90deg, rgba(56,189,248,0.025) 1px, transparent 1px);
      background-size: 50px 50px;
    }

    /* Medical cross tiled pattern — very subtle */
    .hospital-bg-crosses {
      position: absolute; inset: 0; opacity: 0.04;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect x='44' y='24' width='12' height='52' rx='2.5' fill='%2338bdf8'/%3E%3Crect x='24' y='44' width='52' height='12' rx='2.5' fill='%2338bdf8'/%3E%3C/svg%3E");
      background-size: 100px 100px;
    }

    /* Light mode override */
    [data-theme="light"] .hospital-bg-base {
      background: linear-gradient(160deg, #e8f2ff 0%, #eef6ff 30%, #e4effd 60%, #ecf4ff 100%);
    }
    [data-theme="light"] .hospital-bg-light {
      background:
        radial-gradient(ellipse 90% 70% at 15% 20%, rgba(26,111,196,0.10) 0%, transparent 55%),
        radial-gradient(ellipse 60% 60% at 85% 80%, rgba(6,182,212,0.08) 0%, transparent 50%),
        radial-gradient(ellipse 40% 50% at 50% 50%, rgba(14,80,160,0.05) 0%, transparent 60%);
    }
    [data-theme="light"] .hospital-bg-crosses { opacity: 0.045; }

    /* ── Medical HUD Decorative Elements ─────────────────────────────────── */
    .med-hud {
      position: fixed; inset: 0; z-index: -1;
      pointer-events: none;
      overflow: hidden;
    }

    /* ECG waveform — top-right corner */
    .hud-ecg {
      position: absolute;
      top: 88px; right: 24px;
      width: 340px; height: 54px;
      opacity: 0.08;
    }
    [data-theme="light"] .hud-ecg { opacity: 0.06; }
    .hud-ecg-line {
      stroke: #38bdf8; stroke-width: 2; fill: none;
      stroke-linecap: round; stroke-linejoin: round;
      animation: ecg-draw 3.2s ease-in-out infinite;
      stroke-dasharray: 600; stroke-dashoffset: 0;
    }
    @keyframes ecg-draw {
      0%   { stroke-dashoffset: 600; opacity: 0; }
      10%  { opacity: 1; }
      80%  { stroke-dashoffset: 0; opacity: 1; }
      100% { stroke-dashoffset: 0; opacity: 0; }
    }

    /* Scanning ring — bottom left */
    .hud-ring {
      position: absolute;
      bottom: 60px; left: 40px;
      width: 120px; height: 120px;
      opacity: 0.07;
      animation: ring-pulse 4s ease-in-out infinite;
    }
    [data-theme="light"] .hud-ring { opacity: 0.05; }
    @keyframes ring-pulse {
      0%, 100% { transform: scale(1); opacity: 0.07; }
      50%       { transform: scale(1.08); opacity: 0.12; }
    }

    /* Medical cross — top left */
    .hud-cross {
      position: absolute;
      top: 120px; left: 32px;
      width: 48px; height: 48px;
      opacity: 0.09;
      animation: cross-fade 5s ease-in-out infinite;
    }
    [data-theme="light"] .hud-cross { opacity: 0.07; }
    @keyframes cross-fade {
      0%, 100% { opacity: 0.09; }
      50%       { opacity: 0.15; }
    }

    /* AI dots — scattered subtle dots */
    .hud-dots {
      position: absolute; inset: 0;
      opacity: 0.06;
      background-image:
        radial-gradient(circle 2px at 8% 30%, #38bdf8 100%, transparent 100%),
        radial-gradient(circle 2px at 92% 20%, #38bdf8 100%, transparent 100%),
        radial-gradient(circle 1.5px at 5% 70%, #06b6d4 100%, transparent 100%),
        radial-gradient(circle 2px at 95% 65%, #38bdf8 100%, transparent 100%),
        radial-gradient(circle 1.5px at 15% 85%, #06b6d4 100%, transparent 100%),
        radial-gradient(circle 2px at 88% 90%, #38bdf8 100%, transparent 100%);
    }
    [data-theme="light"] .hud-dots { opacity: 0.04; }

    /* Corner hex decorations */
    .hud-hex-br {
      position: absolute;
      bottom: 30px; right: 30px;
      width: 100px; height: 100px;
      opacity: 0.07;
      animation: hex-spin 20s linear infinite;
    }
    [data-theme="light"] .hud-hex-br { opacity: 0.05; }
    @keyframes hex-spin {
      from { transform: rotate(0deg); }
      to   { transform: rotate(360deg); }
    }

    /* Hospital silhouette — bottom strip */
    .hud-hospital {
      position: absolute;
      bottom: 0; left: 0; right: 0;
      height: 140px;
      opacity: 0.05;
    }
    [data-theme="light"] .hud-hospital { opacity: 0.04; }

    /* ── Premium HEADER overrides ─────────────────────────────────────────── */
    .app-header {
      background: linear-gradient(135deg, #020f28 0%, #061c45 30%, #0c3070 60%, #1048a0 85%, #1a6fc4 100%);
      border-bottom: 1px solid rgba(56,189,248,0.15);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
    }
    .header-logo-wrap svg { display: block; }

    /* Subtle ECG pulse on header logo */
    .header-logo { position: relative; }
    .header-logo::after {
      content: '';
      position: absolute; inset: -2px;
      border-radius: 14px;
      border: 1px solid rgba(56,189,248,0.30);
      animation: logo-border-pulse 2.5s ease-in-out infinite;
    }
    @keyframes logo-border-pulse {
      0%, 100% { opacity: 0.3; transform: scale(1); }
      50%       { opacity: 0.7; transform: scale(1.04); }
    }

    /* Progress bar — cyan medical */
    .progress-track { background: rgba(56,189,248,0.08); }
    .progress-fill  { background: linear-gradient(90deg, #1a6fc4, #38bdf8, #06b6d4); }

    /* ── Save banner ──────────────────────────────────────────────────────── */
    .save-banner {
      position: fixed; bottom: 24px; right: 24px; z-index: 999;
      background: linear-gradient(135deg, #059669, #06b6d4);
      color: #fff; border-radius: 14px;
      padding: 14px 22px;
      font-size: .88rem; font-weight: 700;
      box-shadow: 0 8px 32px rgba(5,150,105,0.45);
      animation: slideUp .35s cubic-bezier(.4,0,.2,1);
      display: flex; align-items: center; gap: 8px;
    }
    @keyframes slideUp {
      from { opacity:0; transform:translateY(16px); }
      to   { opacity:1; transform:translateY(0); }
    }
  </style>
</head>
<body>

<!-- ═══ MEDICAL BACKGROUND ══════════════════════════════════════════════════ -->
<div class="hospital-bg">
  <div class="hospital-bg-base"></div>
  <div class="hospital-bg-light"></div>
  <div class="hospital-bg-grid"></div>
  <div class="hospital-bg-crosses"></div>
</div>

<!-- ═══ MEDICAL HUD ══════════════════════════════════════════════════════════ -->
<div class="med-hud">
  <!-- ECG waveform top-right -->
  <svg class="hud-ecg" viewBox="0 0 340 54" xmlns="http://www.w3.org/2000/svg">
    <polyline class="hud-ecg-line"
      points="0,27 30,27 50,27 65,5 78,50 92,27 110,27 130,27 148,14 162,40 176,27 200,27 220,27 238,20 252,34 265,27 290,27 310,27 325,18 335,36 340,27"/>
  </svg>

  <!-- Scanning ring bottom-left -->
  <svg class="hud-ring" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
    <circle cx="60" cy="60" r="50" stroke="#38bdf8" stroke-width="1" fill="none" stroke-dasharray="8 6"/>
    <circle cx="60" cy="60" r="38" stroke="#06b6d4" stroke-width="0.8" fill="none" stroke-dasharray="5 8"/>
    <circle cx="60" cy="60" r="24" stroke="#38bdf8" stroke-width="0.6" fill="none"/>
    <circle cx="60" cy="60" r="4" fill="#38bdf8"/>
    <line x1="60" y1="10" x2="60" y2="20" stroke="#38bdf8" stroke-width="1"/>
    <line x1="60" y1="100" x2="60" y2="110" stroke="#38bdf8" stroke-width="1"/>
    <line x1="10" y1="60" x2="20" y2="60" stroke="#38bdf8" stroke-width="1"/>
    <line x1="100" y1="60" x2="110" y2="60" stroke="#38bdf8" stroke-width="1"/>
  </svg>

  <!-- Medical cross top-left -->
  <svg class="hud-cross" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
    <rect x="18" y="4" width="12" height="40" rx="3" fill="#38bdf8"/>
    <rect x="4" y="18" width="40" height="12" rx="3" fill="#38bdf8"/>
  </svg>

  <!-- AI dots -->
  <div class="hud-dots"></div>

  <!-- Hexagonal ring bottom-right -->
  <svg class="hud-hex-br" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
    <polygon points="50,5 90,27.5 90,72.5 50,95 10,72.5 10,27.5"
      stroke="#38bdf8" stroke-width="1" fill="none"/>
    <polygon points="50,18 78,33 78,67 50,82 22,67 22,33"
      stroke="#06b6d4" stroke-width="0.7" fill="none"/>
    <circle cx="50" cy="50" r="6" stroke="#38bdf8" stroke-width="0.8" fill="none"/>
  </svg>

  <!-- Hospital building silhouette -->
  <svg class="hud-hospital" viewBox="0 0 1440 140" preserveAspectRatio="xMidYMax slice" xmlns="http://www.w3.org/2000/svg">
    <!-- Main hospital -->
    <rect x="80" y="30" width="200" height="110" fill="#38bdf8"/>
    <rect x="150" y="5" width="60" height="32" fill="#38bdf8"/>
    <rect x="166" y="14" width="10" height="18" fill="#020f28"/>
    <rect x="182" y="14" width="10" height="18" fill="#020f28"/>
    <rect x="163" y="1" width="5" height="20" fill="#38bdf8"/>
    <rect x="158" y="6" width="15" height="5" fill="#38bdf8"/>
    <rect x="98"  y="50" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="134" y="50" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="170" y="50" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="206" y="50" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="242" y="50" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="98"  y="80" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="134" y="80" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="170" y="80" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="206" y="80" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="242" y="80" width="22" height="18" rx="2" fill="#020f28"/>
    <rect x="152" y="108" width="56" height="32" rx="3" fill="#020f28"/>
    <!-- Second building -->
    <rect x="340" y="50" width="160" height="90" fill="#38bdf8"/>
    <rect x="390" y="25" width="60" height="30" fill="#38bdf8"/>
    <rect x="354" y="70" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="386" y="70" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="418" y="70" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="450" y="70" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="354" y="98" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="386" y="98" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="418" y="98" width="20" height="16" rx="2" fill="#020f28"/>
    <rect x="450" y="98" width="20" height="16" rx="2" fill="#020f28"/>
    <!-- Ambulance -->
    <rect x="560" y="100" width="110" height="38" rx="6" fill="#38bdf8"/>
    <rect x="624" y="88"  width="46" height="22" rx="3" fill="#38bdf8"/>
    <circle cx="580" cy="140" r="10" fill="#38bdf8"/>
    <circle cx="644" cy="140" r="10" fill="#38bdf8"/>
    <rect x="596" y="107" width="5" height="16" fill="#020f28"/>
    <rect x="589" y="114" width="19" height="5" fill="#020f28"/>
    <!-- Trees -->
    <ellipse cx="520" cy="110" rx="20" ry="28" fill="#38bdf8"/>
    <rect x="516" y="120" width="8" height="20" fill="#38bdf8"/>
    <ellipse cx="700" cy="108" rx="18" ry="24" fill="#38bdf8"/>
    <rect x="696" y="118" width="8" height="22" fill="#38bdf8"/>
    <!-- Third building right -->
    <rect x="760" y="40" width="140" height="100" fill="#38bdf8"/>
    <rect x="800" y="20" width="60" height="25" fill="#38bdf8"/>
    <rect x="772" y="58" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="802" y="58" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="832" y="58" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="862" y="58" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="772" y="84" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="802" y="84" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="832" y="84" width="18" height="14" rx="2" fill="#020f28"/>
    <rect x="862" y="84" width="18" height="14" rx="2" fill="#020f28"/>
    <!-- Ground line -->
    <rect x="0" y="138" width="1440" height="2" fill="#38bdf8"/>
  </svg>
</div>

<!-- ═══ HEADER ════════════════════════════════════════════════════════════════ -->
<header class="app-header">
  <div class="header-inner">

    <!-- Logo with medical cross SVG -->
    <div class="header-logo-wrap">
      <div class="header-logo">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect x="9" y="2" width="6" height="20" rx="2" fill="white"/>
          <rect x="2" y="9" width="20" height="6" rx="2" fill="white"/>
        </svg>
      </div>
    </div>

    <!-- Title -->
    <div class="header-text">
      <h1>ClinAI — AI Disease Predictor</h1>
      <p>Clinical Decision Support &nbsp;·&nbsp; Hospital Edition v2.0</p>
    </div>

    <!-- Right section -->
    <div class="header-right">
      <div class="header-badges">
        <span class="header-badge">100+ Conditions</span>
        <span class="header-badge">AI&#8209;Powered</span>
        <span class="header-badge">ICD&#8209;10</span>
      </div>
      <a href="dashboard.php" class="btn-nav-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
        Dashboard
      </a>
      <a href="history.php" class="btn-nav-link">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        History
      </a>
      <div class="nav-user-pill">
        <div class="nav-avatar" style="background:<?= htmlspecialchars($user['color']) ?>">
          <?= strtoupper(substr($user['name'],0,1)) ?>
        </div>
        <span class="nav-user-name-wrap">
          <span class="nav-user-name"><?= htmlspecialchars($user['name']) ?></span>
          <span class="nav-user-role"><?= htmlspecialchars(ucfirst($user['role'])) ?></span>
        </span>
      </div>
      <button class="theme-toggle" id="theme-toggle" title="Toggle dark/light">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" id="theme-icon-moon"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
      </button>
      <button class="btn-nav-link btn-logout-nav" onclick="logout()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Sign Out
      </button>
    </div>
  </div>
  <div class="progress-track">
    <div class="progress-fill" id="progress-bar"></div>
  </div>
</header>

<!-- ═══ MAIN ══════════════════════════════════════════════════════════════════ -->
<main class="app-main">

  <!-- Step Navigator -->
  <nav class="step-nav" aria-label="Assessment Steps">
    <div class="step-indicator active" id="step-ind-1">
      <div class="step-circle"><span>1</span></div>
      <div class="step-label">Patient Info</div>
    </div>
    <div class="step-connector" id="conn-1-2"></div>
    <div class="step-indicator" id="step-ind-2">
      <div class="step-circle"><span>2</span></div>
      <div class="step-label">Symptoms</div>
    </div>
    <div class="step-connector" id="conn-2-3"></div>
    <div class="step-indicator" id="step-ind-3">
      <div class="step-circle"><span>3</span></div>
      <div class="step-label">Vitals</div>
    </div>
    <div class="step-connector" id="conn-3-4"></div>
    <div class="step-indicator" id="step-ind-4">
      <div class="step-circle"><span>4</span></div>
      <div class="step-label">Results</div>
    </div>
  </nav>

  <!-- ══ STEP 1 ═══════════════════════════════════════════════════════════════ -->
  <section class="step-panel active" id="step-1">
    <div class="card">
      <div class="section-header">
        <div class="section-icon blue">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#1a6fc4" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="section-info">
          <div class="card-title">Patient Information</div>
          <div class="card-subtitle">Enter the patient's demographics. Fields marked * are required to continue.</div>
        </div>
      </div>
      <div class="form-grid">
        <div class="form-group full">
          <label for="patient-name">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Full Name *
          </label>
          <input type="text" id="patient-name" placeholder="e.g., John Doe" autocomplete="off"/>
        </div>
        <div class="form-group">
          <label for="patient-age">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            Age (years) *
          </label>
          <input type="number" id="patient-age" placeholder="e.g., 45" min="0" max="120"/>
        </div>
        <div class="form-group">
          <label for="patient-gender">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><path d="M12 2a5 5 0 1 0 0 10 5 5 0 0 0 0-10z"/><path d="M12 12v10M8 16h8"/></svg>
            Biological Sex *
          </label>
          <select id="patient-gender">
            <option value="">Select…</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other / Not specified</option>
          </select>
        </div>
        <div class="form-group">
          <label for="patient-id">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="9" x2="17" y2="9"/><line x1="7" y1="13" x2="14" y2="13"/></svg>
            Patient ID / MRN
          </label>
          <input type="text" id="patient-id" placeholder="Hospital ID (optional)"/>
        </div>
        <div class="form-group">
          <label for="ward">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Ward / Department
          </label>
          <input type="text" id="ward" placeholder="e.g., Emergency, OPD, ICU"/>
        </div>
      </div>

      <hr class="section-divider"/>

      <div class="section-header" style="margin-bottom:16px">
        <div class="section-icon teal">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </div>
        <div class="section-info">
          <div class="card-title">BMI Calculator <span style="font-size:.75rem;font-weight:400;color:var(--text-muted)">(optional)</span></div>
          <div class="card-subtitle">Auto-calculates and flags obesity as a risk factor</div>
        </div>
      </div>
      <div class="bmi-row">
        <div class="form-group">
          <label for="weight">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><circle cx="12" cy="5" r="3"/><path d="M6.5 8a2 2 0 0 0-1.905 1.46L2.1 18.5A2 2 0 0 0 4 21h16a2 2 0 0 0 1.925-2.54L19.4 9.5A2 2 0 0 0 17.48 8z"/></svg>
            Weight (kg)
          </label>
          <input type="number" id="weight" placeholder="e.g., 72" min="1" max="300" step="0.1"/>
        </div>
        <div class="form-group">
          <label for="height">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><line x1="12" y1="2" x2="12" y2="22"/><polyline points="8 6 12 2 16 6"/><polyline points="8 18 12 22 16 18"/></svg>
            Height (cm)
          </label>
          <input type="number" id="height" placeholder="e.g., 175" min="50" max="250" step="0.5"/>
        </div>
        <div id="bmi-display">—</div>
      </div>

      <hr class="section-divider"/>

      <div class="section-header" style="margin-bottom:16px">
        <div class="section-icon violet">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#7c5cf6" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="section-info">
          <div class="card-title">Medical History &amp; Risk Factors</div>
          <div class="card-subtitle">Select all that apply — significantly refine AI predictions</div>
        </div>
      </div>
      <div class="rf-grid">
        <label class="rf-item"><input type="checkbox" id="rf-smoker"/><span class="rf-label">🚬 Current Smoker</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-diabetes"/><span class="rf-label">🩸 Known Diabetes</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-hypertension"/><span class="rf-label">💉 Hypertension</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-immunocompromised"/><span class="rf-label">🛡️ Immunocompromised</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-pregnant"/><span class="rf-label">🤰 Pregnant</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-family-history"/><span class="rf-label">👨‍👩‍👦 Family History CVD/DM</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-travel"/><span class="rf-label">✈️ Recent Tropical Travel</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-tb-contact"/><span class="rf-label">🫁 TB Contact</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-hiv"/><span class="rf-label">🧬 HIV Positive</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-stress"/><span class="rf-label">🧠 High Stress / Anxiety</span></label>
        <label class="rf-item"><input type="checkbox" id="rf-catheter"/><span class="rf-label">🏥 Urinary Catheter</span></label>
      </div>
      <div class="form-group full" style="margin-top:6px">
        <label for="clinical-notes">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;vertical-align:-1px"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          Clinical Notes
        </label>
        <textarea id="clinical-notes" rows="2" placeholder="Additional clinical observations, presenting complaint, relevant history…" style="resize:vertical;min-height:60px"></textarea>
      </div>
      <div class="action-bar">
        <span style="font-size:.78rem;color:var(--text-muted);font-weight:500">Step 1 of 4</span>
        <button class="btn btn-primary" data-next>Continue to Symptoms &nbsp;→</button>
      </div>
    </div>
  </section>

  <!-- ══ STEP 2 ═══════════════════════════════════════════════════════════════ -->
  <section class="step-panel" id="step-2">
    <div class="card">
      <div class="section-header">
        <div class="section-icon green">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div class="section-info">
          <div class="card-title">Symptom Selection</div>
          <div class="card-subtitle">Tap to select all symptoms the patient is currently experiencing. Use search to filter quickly.</div>
        </div>
      </div>
      <div class="symptom-search-bar">
        <span class="search-icon">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </span>
        <input type="text" id="symptom-search" placeholder="Search symptoms (e.g., fever, chest pain, dizziness)…"/>
      </div>
      <div class="symptom-count-bar">
        <span id="symptom-count">No symptoms selected yet</span>
        <button class="clear-symptoms" onclick="App.clearAllSymptoms()">✕ Clear All</button>
      </div>
      <div class="global-error" id="symptom-step-error"></div>
      <div class="symptom-grid-wrapper">
        <div id="symptom-grid"></div>
      </div>
      <div class="action-bar">
        <button class="btn btn-secondary" data-prev>← Back</button>
        <button class="btn btn-primary" data-next>Continue to Vitals &nbsp;→</button>
      </div>
    </div>
  </section>

  <!-- ══ STEP 3 ═══════════════════════════════════════════════════════════════ -->
  <section class="step-panel" id="step-3">
    <div class="card">
      <div class="section-header">
        <div class="section-icon amber">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
        </div>
        <div class="section-info">
          <div class="card-title">Vital Signs</div>
          <div class="card-subtitle">Enter measured vitals. Abnormal values apply clinical threshold boosts. All optional.</div>
        </div>
      </div>
      <div class="vitals-note">
        ℹ️ Abnormal vitals significantly increase prediction accuracy. Leave blank if unavailable.
      </div>
      <div class="form-grid">
        <div class="form-group vital-input-group">
          <label for="v-temperature">Body Temperature</label>
          <input type="number" id="v-temperature" placeholder="e.g., 38.5" min="34" max="42" step="0.1"/>
          <span class="vital-input-icon">°C</span>
          <span class="input-hint">Normal: 36.1 – 37.2 °C</span>
        </div>
        <div class="form-group vital-input-group">
          <label for="v-heart-rate">Heart Rate</label>
          <input type="number" id="v-heart-rate" placeholder="e.g., 88" min="20" max="250"/>
          <span class="vital-input-icon">bpm</span>
          <span class="input-hint">Normal: 60 – 100 bpm</span>
        </div>
        <div class="form-group vital-input-group">
          <label for="v-systolic">Systolic Blood Pressure</label>
          <input type="number" id="v-systolic" placeholder="e.g., 140" min="50" max="260"/>
          <span class="vital-input-icon">mmHg</span>
          <span class="input-hint">Normal: &lt; 120 mmHg</span>
        </div>
        <div class="form-group vital-input-group">
          <label for="v-diastolic">Diastolic Blood Pressure</label>
          <input type="number" id="v-diastolic" placeholder="e.g., 90" min="30" max="160"/>
          <span class="vital-input-icon">mmHg</span>
          <span class="input-hint">Normal: &lt; 80 mmHg</span>
        </div>
        <div class="form-group vital-input-group">
          <label for="v-spo2">Oxygen Saturation (SpO₂)</label>
          <input type="number" id="v-spo2" placeholder="e.g., 97" min="50" max="100"/>
          <span class="vital-input-icon">%</span>
          <span class="input-hint">Normal: 95 – 100%</span>
        </div>
        <div class="form-group vital-input-group">
          <label for="v-resp-rate">Respiratory Rate</label>
          <input type="number" id="v-resp-rate" placeholder="e.g., 18" min="4" max="60"/>
          <span class="vital-input-icon">/min</span>
          <span class="input-hint">Normal: 12 – 20 /min</span>
        </div>
        <div class="form-group vital-input-group full">
          <label for="v-glucose">Blood Glucose (Fasting)</label>
          <input type="number" id="v-glucose" placeholder="e.g., 110" min="30" max="700" step="0.1"/>
          <span class="vital-input-icon">mg/dL</span>
          <span class="input-hint">Normal fasting: 70 – 99 &nbsp;·&nbsp; Diabetes: ≥ 126 mg/dL</span>
        </div>
      </div>
      <div class="action-bar">
        <button class="btn btn-secondary" data-prev>← Back</button>
        <button class="btn btn-predict" id="predict-btn">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="3"/><path d="M20.188 10.934c.326.299.326.833 0 1.132l-7.254 6.628a1 1 0 0 1-1.334 0L4.346 12.066c-.326-.299-.326-.833 0-1.132L11.6 4.306a1 1 0 0 1 1.334 0z"/></svg>
          &nbsp;Run AI Prediction
        </button>
      </div>
    </div>
  </section>

  <!-- ══ STEP 4 ═══════════════════════════════════════════════════════════════ -->
  <section class="step-panel" id="step-4">
    <div class="results-action-bar">
      <button class="btn btn-secondary" id="reset-btn">🔄 New Assessment</button>
      <button class="btn btn-secondary" id="save-btn">💾 Save to Database</button>
      <button class="btn btn-secondary" id="copy-report-btn">📋 Copy Report</button>
      <button class="btn btn-secondary" id="print-btn">🖨️ Print / PDF</button>
      <a href="history.php" class="btn btn-secondary">📂 View History</a>
    </div>
    <div id="results-container"></div>
  </section>

</main>

<!-- ═══ FOOTER ════════════════════════════════════════════════════════════════ -->
<footer class="app-footer">
  ClinAI v2.0 &nbsp;·&nbsp; AI-Powered Clinical Decision Support &nbsp;·&nbsp;
  For licensed healthcare professionals only &nbsp;·&nbsp;
  Not a substitute for clinical diagnosis &nbsp;·&nbsp;
  By <span class="footer-author">Gaurav Tamshete</span>
</footer>

<!-- Save success banner -->
<div class="save-banner" id="save-banner" style="display:none">
  ✅ Assessment saved to database!
</div>

<!-- Scripts -->
<script src="js/diseases.js"></script>
<script src="js/engine.js"></script>
<script src="js/app.js"></script>

<script>
// ── Dark/Light mode ────────────────────────────────────────────────────────
const themeBtn  = document.getElementById('theme-toggle');
const htmlRoot  = document.documentElement;

function applyTheme(dark) {
  htmlRoot.setAttribute('data-theme', dark ? 'dark' : 'light');
  const icon = document.getElementById('theme-icon-moon');
  if (icon) {
    icon.setAttribute('d', dark
      ? 'M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 1 0 0 14A7 7 0 0 0 12 5z'
      : 'M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z');
  }
  localStorage.setItem('clinai-theme', dark ? 'dark' : 'light');
}

// Always force premium dark medical theme — clear any old light preference
localStorage.removeItem('clinai-theme');
applyTheme(true);
themeBtn.addEventListener('click', () => applyTheme(htmlRoot.getAttribute('data-theme') !== 'dark'));

// ── Logout ─────────────────────────────────────────────────────────────────
async function logout() {
  await fetch('php/auth.php?action=logout');
  window.location.href = 'login.php';
}

// ── Save to DB ─────────────────────────────────────────────────────────────
document.getElementById('save-btn')?.addEventListener('click', async () => {
  const payload = window.__lastAssessmentPayload;
  if (!payload) { alert('Run a prediction first.'); return; }

  // Attach clinical notes before saving
  payload.notes = document.getElementById('clinical-notes')?.value?.trim() || null;

  const btn = document.getElementById('save-btn');
  btn.disabled = true; btn.textContent = '💾 Saving…';

  try {
    const res  = await fetch('php/api.php?action=save', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });
    const data = await res.json();
    if (data.success) {
      showSaveBanner('✅ Assessment saved! ID #' + data.assessment_id);
      btn.textContent = '✓ Saved';
    } else {
      alert('Save failed: ' + (data.error || 'Unknown error'));
      btn.disabled = false; btn.textContent = '💾 Save to Database';
    }
  } catch(e) {
    alert('Network error. Is XAMPP running?');
    btn.disabled = false; btn.textContent = '💾 Save to Database';
  }
});

function showSaveBanner(msg) {
  const b = document.getElementById('save-banner');
  b.textContent = msg;
  b.style.display = 'flex';
  setTimeout(() => { b.style.display = 'none'; }, 4000);
}
</script>
</body>
</html>

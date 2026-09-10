<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ClinAI — Login | Hospital AI Disease Predictor</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet"/>
  <style>
    /* ── Variables — Deep Navy Medical Blue ───────────────────────── */
    :root {
      --font: 'Inter', system-ui, sans-serif;
      --grad-hero: linear-gradient(135deg, #020c22 0%, #051428 25%, #071e3d 50%, #082244 75%, #0a2a55 100%);
      --glass-bg:  rgba(255,255,255,0.07);
      --glass-border: rgba(120,200,255,0.18);
      --glass-bg2: rgba(255,255,255,0.11);
      --primary:   #1a6fc4;
      --primary2:  #0e5aad;
      --accent:    #06b6d4;
      --success:   #059669;
      --danger:    #dc2626;
      --text:      #ffffff;
      --text-dim:  rgba(200,230,255,0.70);
      --text-dimmer: rgba(160,200,255,0.45);
      --input-bg:  rgba(255,255,255,0.08);
      --input-border: rgba(120,200,255,0.22);
      --radius:    14px;
      --radius-lg: 22px;
    }

    /* ── Reset ──────────────────────────────────────────────────────── */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    html{font-size:16px;-webkit-font-smoothing:antialiased}
    body{
      font-family:var(--font);
      min-height:100vh;
      display:flex;
      align-items:stretch;
      overflow:hidden;
    }

    /* ── Hospital Background ────────────────────────────────────────── */
    .bg-scene {
      position:fixed;inset:0;z-index:0;
      background: var(--grad-hero);
      overflow:hidden;
    }

    /* Grid overlay */
    .bg-scene::before {
      content:'';
      position:absolute;inset:0;
      background-image:
        linear-gradient(rgba(56,189,248,.025) 1px,transparent 1px),
        linear-gradient(90deg,rgba(56,189,248,.025) 1px,transparent 1px);
      background-size:50px 50px;
      z-index:1;
    }

    /* Radial depth lighting — clinical monitor glow */
    .bg-scene::after {
      content:'';
      position:absolute;inset:0;z-index:2;
      background:
        radial-gradient(ellipse 70% 80% at -10% 50%, rgba(26,111,196,0.30) 0%, transparent 60%),
        radial-gradient(ellipse 50% 60% at 110% 50%, rgba(6,182,212,0.22) 0%, transparent 55%),
        radial-gradient(ellipse 40% 40% at 50% 100%, rgba(14,80,160,0.25) 0%, transparent 60%),
        radial-gradient(ellipse 25% 30% at 80% 10%, rgba(56,189,248,0.14) 0%, transparent 50%);
    }

    /* Floating orbs — blue/cyan only */
    .orb {
      position:absolute;border-radius:50%;filter:blur(80px);z-index:2;
      animation:floatOrb 8s ease-in-out infinite;
    }
    .orb-1{width:420px;height:420px;top:-120px;left:-120px;background:rgba(26,111,196,0.28);animation-delay:0s}
    .orb-2{width:320px;height:320px;bottom:-90px;right:-90px;background:rgba(6,182,212,0.22);animation-delay:-3s}
    .orb-3{width:220px;height:220px;top:45%;left:60%;background:rgba(14,80,160,0.18);animation-delay:-5s}
    @keyframes floatOrb{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-28px) scale(1.05)}}

    /* Medical cross pattern */
    .med-pattern {
      position:absolute;inset:0;z-index:2;opacity:0.04;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100'%3E%3Crect x='44' y='24' width='12' height='52' rx='2.5' fill='%2338bdf8'/%3E%3Crect x='24' y='44' width='52' height='12' rx='2.5' fill='%2338bdf8'/%3E%3C/svg%3E");
      background-size:100px 100px;
    }

    /* Hospital art container */
    .hospital-art {
      position:absolute;bottom:0;left:0;right:0;z-index:3;
      height:200px;
      display:flex;align-items:flex-end;justify-content:flex-start;
      padding-left:60px;
      opacity:0.12;
    }

    /* ── Page layout ────────────────────────────────────────────────── */
    .page {
      position:relative;z-index:10;
      display:flex;width:100%;min-height:100vh;
    }

    /* Left panel — branding */
    .brand-panel {
      flex:1;
      display:flex;flex-direction:column;justify-content:center;
      padding:60px 72px;
      max-width:540px;
    }
    .brand-logo {
      width:64px;height:64px;
      background:rgba(255,255,255,0.10);
      border:1px solid rgba(120,200,255,0.28);
      border-radius:18px;
      display:flex;align-items:center;justify-content:center;
      margin-bottom:32px;
      backdrop-filter:blur(12px);
      box-shadow:0 8px 32px rgba(0,0,0,0.25);
      animation:pulse-glow 3s ease-in-out infinite;
    }
    @keyframes pulse-glow{
      0%,100%{box-shadow:0 8px 32px rgba(0,0,0,.25),0 0 0 0 rgba(56,189,248,.35)}
      50%{box-shadow:0 8px 32px rgba(0,0,0,.25),0 0 0 14px rgba(56,189,248,0)}
    }
    .brand-title {
      font-size:3rem;font-weight:900;color:#fff;line-height:1.1;
      letter-spacing:-1.5px;margin-bottom:16px;
    }
    .brand-title span{
      background:linear-gradient(135deg,#38bdf8,#06b6d4);
      -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
    }
    .brand-desc {
      font-size:1.05rem;color:var(--text-dim);line-height:1.7;margin-bottom:40px;
      font-weight:300;
    }
    .brand-features {
      display:flex;flex-direction:column;gap:14px;
    }
    .feature-item {
      display:flex;align-items:center;gap:12px;
      font-size:.9rem;color:var(--text-dim);font-weight:400;
    }
    .feature-dot {
      width:8px;height:8px;border-radius:50%;flex-shrink:0;
    }
    .feat-blue{background:linear-gradient(135deg,#1a6fc4,#38bdf8)}
    .feat-teal{background:linear-gradient(135deg,#06b6d4,#22d3ee)}
    .feat-green{background:linear-gradient(135deg,#059669,#34d399)}
    .feat-cyan{background:linear-gradient(135deg,#0284c7,#38bdf8)}

    /* Stats row */
    .brand-stats {
      display:flex;gap:32px;margin-top:48px;
    }
    .stat-item{text-align:left;}
    .stat-num{font-size:2rem;font-weight:900;color:#fff;letter-spacing:-1px;line-height:1;}
    .stat-label{font-size:.72rem;color:var(--text-dimmer);font-weight:500;text-transform:uppercase;letter-spacing:.6px;margin-top:4px;}

    /* Right panel — auth form */
    .auth-panel {
      width:480px;flex-shrink:0;
      display:flex;align-items:center;justify-content:center;
      padding:40px 48px;
    }

    .auth-card {
      width:100%;
      background:rgba(255,255,255,0.08);
      border:1px solid rgba(255,255,255,0.16);
      border-radius:28px;
      padding:40px 36px;
      backdrop-filter:blur(32px);
      -webkit-backdrop-filter:blur(32px);
      box-shadow:0 32px 80px rgba(0,0,0,0.35),inset 0 1px 0 rgba(255,255,255,0.15);
    }

    .auth-tabs {
      display:flex;gap:0;margin-bottom:32px;
      background:rgba(255,255,255,0.06);
      border-radius:12px;
      padding:4px;
      border:1px solid rgba(255,255,255,0.10);
    }
    .tab-btn {
      flex:1;padding:9px 0;
      background:transparent;border:none;
      font-family:var(--font);font-size:.88rem;font-weight:600;
      color:var(--text-dimmer);cursor:pointer;
      border-radius:9px;transition:all .25s;
    }
    .tab-btn.active {
      background:rgba(255,255,255,0.14);color:#fff;
      box-shadow:0 2px 8px rgba(0,0,0,0.15);
    }

    .auth-form { display:none; }
    .auth-form.active { display:block; }

    .form-title {
      font-size:1.5rem;font-weight:800;color:#fff;letter-spacing:-.5px;
      margin-bottom:4px;
    }
    .form-sub {
      font-size:.82rem;color:var(--text-dim);margin-bottom:24px;
    }

    /* Floating label fields */
    .field-wrap {
      position:relative;margin-bottom:16px;
    }
    .field-wrap label {
      position:absolute;top:14px;left:16px;
      font-size:.82rem;font-weight:500;color:var(--text-dimmer);
      transition:all .22s;pointer-events:none;z-index:2;
    }
    .field-wrap input,
    .field-wrap select {
      width:100%;padding:20px 16px 8px;
      background:var(--input-bg);
      border:1.5px solid var(--input-border);
      border-radius:12px;
      font-family:var(--font);font-size:.95rem;font-weight:500;
      color:#fff;outline:none;
      transition:all .22s;
      -webkit-autofill-selected:none;
    }
    .field-wrap select option { background:#1e1b4b;color:#fff; }
    .field-wrap input::placeholder { color:transparent; }
    .field-wrap input:focus,
    .field-wrap select:focus {
      border-color:rgba(56,189,248,0.80);
      background:rgba(255,255,255,0.12);
      box-shadow:0 0 0 3px rgba(56,189,248,0.20);
    }
    .field-wrap input:focus ~ label,
    .field-wrap input:not(:placeholder-shown) ~ label,
    .field-wrap select:focus ~ label,
    .field-wrap select:valid ~ label {
      top:6px;font-size:.68rem;color:rgba(125,210,255,0.90);font-weight:600;letter-spacing:.3px;
    }
    .field-icon {
      position:absolute;right:14px;top:50%;transform:translateY(-50%);
      font-size:1rem;color:var(--text-dimmer);cursor:pointer;
      transition:color .2s;z-index:3;
    }
    .field-icon:hover{color:var(--text-dim);}

    .form-row { display:grid;grid-template-columns:1fr 1fr;gap:10px; }

    .btn-auth {
      width:100%;padding:14px;
      background:linear-gradient(135deg,#1a6fc4,#06b6d4);
      border:none;border-radius:12px;
      font-family:var(--font);font-size:.95rem;font-weight:700;
      color:#fff;cursor:pointer;
      transition:all .25s;
      box-shadow:0 4px 22px rgba(26,111,196,0.48);
      letter-spacing:.2px;
      position:relative;overflow:hidden;
      margin-top:8px;
    }
    .btn-auth::after {
      content:'';position:absolute;inset:0;
      background:linear-gradient(90deg,transparent,rgba(255,255,255,0.16),transparent);
      transform:translateX(-100%);transition:transform .5s;
    }
    .btn-auth:hover::after{transform:translateX(100%);}
    .btn-auth:hover{transform:translateY(-2px);box-shadow:0 8px 32px rgba(26,111,196,0.58);}
    .btn-auth:disabled{opacity:.6;cursor:not-allowed;transform:none;}

    .auth-divider {
      text-align:center;margin:16px 0;
      font-size:.76rem;color:var(--text-dimmer);
      position:relative;
    }
    .auth-divider::before,.auth-divider::after {
      content:'';position:absolute;top:50%;width:42%;height:1px;
      background:rgba(255,255,255,0.12);
    }
    .auth-divider::before{left:0;}.auth-divider::after{right:0;}

    .alert {
      padding:11px 16px;border-radius:10px;
      font-size:.82rem;font-weight:500;
      margin-bottom:14px;display:none;
    }
    .alert-error{background:rgba(239,68,68,0.18);border:1px solid rgba(239,68,68,0.4);color:#fca5a5;}
    .alert-success{background:rgba(16,185,129,0.18);border:1px solid rgba(16,185,129,0.4);color:#6ee7b7;}

    .pass-toggle{cursor:pointer;user-select:none;}

    .demo-hint {
      margin-top:18px;padding:12px 14px;
      background:rgba(255,255,255,0.06);
      border:1px solid rgba(255,255,255,0.12);
      border-radius:10px;
      font-size:.76rem;color:var(--text-dimmer);
      line-height:1.6;text-align:center;
    }
    .demo-hint strong{color:rgba(125,210,255,0.90);}

    /* Spinner */
    .spinner {
      width:18px;height:18px;
      border:2px solid rgba(255,255,255,0.3);
      border-top-color:#fff;
      border-radius:50%;
      animation:spin .7s linear infinite;
      display:inline-block;vertical-align:middle;margin-right:8px;
    }
    @keyframes spin{to{transform:rotate(360deg)}}

    /* ── Responsive ───────────────────────────────────────────────── */
    @media(max-width:900px){
      .brand-panel{display:none;}
      .auth-panel{width:100%;padding:24px 20px;}
    }
  </style>
</head>
<body>

<!-- Background scene -->
<div class="bg-scene">
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
  <div class="med-pattern"></div>
  <!-- Hospital SVG art -->
  <svg class="hospital-art" viewBox="0 0 1200 200" fill="none" xmlns="http://www.w3.org/2000/svg" style="position:absolute;bottom:0;left:0;width:100%;height:220px;opacity:.07;z-index:3">
    <!-- Hospital building silhouette -->
    <rect x="80" y="60" width="180" height="140" fill="white"/>
    <rect x="140" y="20" width="60" height="50" fill="white"/>
    <rect x="155" y="32" width="12" height="26" fill="#1e1b4b"/>
    <rect x="173" y="32" width="12" height="26" fill="#1e1b4b"/>
    <!-- Cross on tower -->
    <rect x="162" y="5" width="8" height="24" fill="white"/>
    <rect x="155" y="12" width="22" height="8" fill="white"/>
    <!-- Windows -->
    <rect x="100" y="80" width="28" height="24" rx="3" fill="#1e1b4b"/>
    <rect x="144" y="80" width="28" height="24" rx="3" fill="#1e1b4b"/>
    <rect x="188" y="80" width="28" height="24" rx="3" fill="#1e1b4b"/>
    <rect x="100" y="120" width="28" height="24" rx="3" fill="#1e1b4b"/>
    <rect x="144" y="120" width="28" height="24" rx="3" fill="#1e1b4b"/>
    <rect x="188" y="120" width="28" height="24" rx="3" fill="#1e1b4b"/>
    <!-- Entrance -->
    <rect x="148" y="160" width="44" height="40" rx="4" fill="#1e1b4b"/>
    <!-- Ambulance -->
    <rect x="320" y="165" width="100" height="35" rx="6" fill="white"/>
    <rect x="380" y="155" width="40" height="20" rx="3" fill="white"/>
    <circle cx="340" cy="200" r="12" fill="white"/>
    <circle cx="395" cy="200" r="12" fill="white"/>
    <rect x="352" y="170" width="6" height="18" fill="#1e1b4b"/>
    <rect x="345" y="177" width="20" height="6" fill="#1e1b4b"/>
    <!-- Second building -->
    <rect x="500" y="80" width="140" height="120" fill="white"/>
    <rect x="540" y="50" width="60" height="40" fill="white"/>
    <rect x="510" y="100" width="24" height="20" rx="2" fill="#1e1b4b"/>
    <rect x="548" y="100" width="24" height="20" rx="2" fill="#1e1b4b"/>
    <rect x="586" y="100" width="24" height="20" rx="2" fill="#1e1b4b"/>
    <rect x="510" y="135" width="24" height="20" rx="2" fill="#1e1b4b"/>
    <rect x="548" y="135" width="24" height="20" rx="2" fill="#1e1b4b"/>
    <rect x="586" y="135" width="24" height="20" rx="2" fill="#1e1b4b"/>
    <!-- Trees -->
    <ellipse cx="460" cy="170" rx="25" ry="30" fill="white"/>
    <rect x="456" y="180" width="8" height="20" fill="white"/>
    <ellipse cx="690" cy="165" rx="20" ry="25" fill="white"/>
    <rect x="686" y="175" width="8" height="25" fill="white"/>
    <!-- Ground line -->
    <rect x="0" y="199" width="1200" height="2" fill="white"/>
    <!-- Stars/dots -->
    <circle cx="750" cy="40" r="3" fill="white"/>
    <circle cx="820" cy="20" r="2" fill="white"/>
    <circle cx="900" cy="55" r="2.5" fill="white"/>
    <circle cx="960" cy="15" r="2" fill="white"/>
    <circle cx="1050" cy="45" r="3" fill="white"/>
    <circle cx="1100" cy="30" r="1.5" fill="white"/>
    <!-- Heartbeat line (ECG) -->
    <polyline points="800,100 830,100 845,60 860,140 875,100 905,100 920,80 935,120 950,100 980,100" stroke="white" stroke-width="3" fill="none" opacity="0.5"/>
  </svg>
</div>

<div class="page">
  <!-- Brand Panel -->
  <div class="brand-panel">
    <div class="brand-logo">
      <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect x="9" y="2" width="6" height="20" rx="2" fill="white"/>
        <rect x="2" y="9" width="20" height="6" rx="2" fill="white"/>
      </svg>
    </div>
    <div class="brand-title">Clin<span>AI</span></div>
    <p class="brand-desc">
      An AI-powered clinical decision support system for hospitals. Predict diseases from patient symptoms, vitals, and risk factors with ICD-10 coded recommendations.
    </p>
    <div class="brand-features">
      <div class="feature-item">
        <div class="feature-dot feat-blue"></div>
        15 conditions with weighted symptom AI engine
      </div>
      <div class="feature-item">
        <div class="feature-dot feat-teal"></div>
        Real-time vital sign threshold analysis
      </div>
      <div class="feature-item">
        <div class="feature-dot feat-green"></div>
        Full patient history &amp; assessment records
      </div>
      <div class="feature-item">
        <div class="feature-dot feat-cyan"></div>
        ICD-10 coded clinical recommendations
      </div>
    </div>
    <div class="brand-stats">
      <div class="stat-item">
        <div class="stat-num">15+</div>
        <div class="stat-label">Conditions</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">80+</div>
        <div class="stat-label">Symptoms</div>
      </div>
      <div class="stat-item">
        <div class="stat-num">AI</div>
        <div class="stat-label">Powered</div>
      </div>
    </div>
  </div>

  <!-- Auth Panel -->
  <div class="auth-panel">
    <div class="auth-card">

      <!-- Tabs -->
      <div class="auth-tabs">
        <button class="tab-btn active" onclick="switchTab('login')">Sign In</button>
        <button class="tab-btn" onclick="switchTab('register')">Register</button>
      </div>

      <!-- LOGIN FORM -->
      <div class="auth-form active" id="form-login">
        <div class="form-title">Welcome back</div>
        <div class="form-sub">Sign in to your hospital account</div>

        <div class="alert alert-error" id="login-error"></div>
        <div class="alert alert-success" id="login-success"></div>

        <form id="loginForm" onsubmit="handleLogin(event)">
          <div class="field-wrap">
            <input type="email" id="login-email" placeholder="Email address" required autocomplete="email"/>
            <label for="login-email">Email address</label>
            <span class="field-icon">✉️</span>
          </div>
          <div class="field-wrap">
            <input type="password" id="login-pass" placeholder="Password" required autocomplete="current-password"/>
            <label for="login-pass">Password</label>
            <span class="field-icon pass-toggle" onclick="togglePass('login-pass',this)">👁️</span>
          </div>
          <button type="submit" class="btn-auth" id="login-btn">Sign In to ClinAI</button>
        </form>

        <div class="demo-hint">
          <strong>Demo Account:</strong><br/>
          Email: <strong>admin@clinai.local</strong> &nbsp;·&nbsp; Password: <strong>Admin@123</strong>
        </div>
      </div>

      <!-- REGISTER FORM -->
      <div class="auth-form" id="form-register">
        <div class="form-title">Create account</div>
        <div class="form-sub">Register as hospital staff</div>

        <div class="alert alert-error" id="reg-error"></div>
        <div class="alert alert-success" id="reg-success"></div>

        <form id="regForm" onsubmit="handleRegister(event)">
          <div class="field-wrap">
            <input type="text" id="reg-name" placeholder="Full name" required/>
            <label for="reg-name">Full name</label>
            <span class="field-icon">👤</span>
          </div>
          <div class="field-wrap">
            <input type="email" id="reg-email" placeholder="Email address" required/>
            <label for="reg-email">Email address</label>
            <span class="field-icon">✉️</span>
          </div>
          <div class="form-row">
            <div class="field-wrap">
              <select id="reg-role" required>
                <option value="" disabled selected></option>
                <option value="doctor">Doctor</option>
                <option value="nurse">Nurse</option>
                <option value="admin">Admin</option>
              </select>
              <label for="reg-role">Role</label>
            </div>
            <div class="field-wrap">
              <input type="text" id="reg-dept" placeholder="Department"/>
              <label for="reg-dept">Department</label>
            </div>
          </div>
          <div class="field-wrap">
            <input type="password" id="reg-pass" placeholder="Password (min 8 chars)" required minlength="8"/>
            <label for="reg-pass">Password</label>
            <span class="field-icon pass-toggle" onclick="togglePass('reg-pass',this)">👁️</span>
          </div>
          <button type="submit" class="btn-auth" id="reg-btn">Create Account</button>
        </form>
      </div>

    </div>
  </div>
</div>

<script>
// ── Tab switching ──────────────────────────────────────────────────────────
function switchTab(tab) {
  document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('form-' + tab).classList.add('active');
  event.target.classList.add('active');
  hideAlerts();
}

function hideAlerts() {
  document.querySelectorAll('.alert').forEach(a => { a.style.display='none'; a.textContent=''; });
}

function showAlert(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg; el.style.display = 'block';
}

function togglePass(id, icon) {
  const inp = document.getElementById(id);
  inp.type = inp.type === 'password' ? 'text' : 'password';
  icon.textContent = inp.type === 'password' ? '👁️' : '🙈';
}

// ── LOGIN ──────────────────────────────────────────────────────────────────
async function handleLogin(e) {
  e.preventDefault();
  hideAlerts();
  const btn = document.getElementById('login-btn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>Signing in…';

  const body = new FormData();
  body.append('action', 'login');
  body.append('email', document.getElementById('login-email').value.trim());
  body.append('password', document.getElementById('login-pass').value);

  try {
    const res  = await fetch('php/auth.php?action=login', { method:'POST', body });
    const data = await res.json();
    if (data.success) {
      showAlert('login-success', '✅ Signed in successfully! Redirecting…');
      setTimeout(() => window.location.href = data.redirect || 'index.php', 900);
    } else {
      showAlert('login-error', '⚠️ ' + (data.error || 'Login failed.'));
      btn.disabled = false;
      btn.textContent = 'Sign In to ClinAI';
    }
  } catch {
    showAlert('login-error', '⚠️ Network error. Please try again.');
    btn.disabled = false;
    btn.textContent = 'Sign In to ClinAI';
  }
}

// ── REGISTER ───────────────────────────────────────────────────────────────
async function handleRegister(e) {
  e.preventDefault();
  hideAlerts();
  const btn = document.getElementById('reg-btn');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner"></span>Creating account…';

  const body = new FormData();
  body.append('action', 'register');
  body.append('full_name',   document.getElementById('reg-name').value.trim());
  body.append('email',       document.getElementById('reg-email').value.trim());
  body.append('password',    document.getElementById('reg-pass').value);
  body.append('role',        document.getElementById('reg-role').value);
  body.append('department',  document.getElementById('reg-dept').value.trim());

  try {
    const res  = await fetch('php/auth.php?action=register', { method:'POST', body });
    const data = await res.json();
    if (data.success) {
      showAlert('reg-success', '✅ Account created! Switch to Sign In to login.');
      document.getElementById('regForm').reset();
    } else {
      showAlert('reg-error', '⚠️ ' + (data.error || 'Registration failed.'));
    }
  } catch {
    showAlert('reg-error', '⚠️ Network error. Please try again.');
  }

  btn.disabled = false;
  btn.textContent = 'Create Account';
}
</script>
</body>
</html>

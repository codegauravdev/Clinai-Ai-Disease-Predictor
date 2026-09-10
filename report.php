<?php
require_once __DIR__ . '/php/config.php';
requireLogin();
$user = currentUser();
$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: history.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>ClinAI — Clinical Assessment Report</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
  <style>
    * { box-sizing:border-box; margin:0; padding:0; }
    body { font-family:'Inter',system-ui,sans-serif; background:#f8fafc; color:#0f172a; font-size:14px; line-height:1.6; }

    /* Print control bar */
    .print-bar { background:#1a3a6e; padding:12px 32px; display:flex; align-items:center; justify-content:space-between; position:fixed; top:0; left:0; right:0; z-index:100; }
    .print-bar h3 { color:#fff; font-size:.9rem; font-weight:700; }
    .print-actions { display:flex; gap:10px; }
    .btn-print-now { padding:8px 20px; background:linear-gradient(135deg,#38bdf8,#06b6d4); color:#fff; border:none; border-radius:8px; font-size:.82rem; font-weight:700; cursor:pointer; font-family:inherit; }
    .btn-back-link { padding:8px 18px; background:rgba(255,255,255,.12); color:rgba(255,255,255,.85); border:1px solid rgba(255,255,255,.2); border-radius:8px; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }

    /* Report container */
    .report { max-width:820px; margin:74px auto 40px; background:#fff; box-shadow:0 4px 40px rgba(0,0,0,.12); }

    /* Report header */
    .report-header { background:linear-gradient(135deg,#071e3d 0%,#0c3070 50%,#1a6fc4 100%); padding:28px 36px; color:#fff; }
    .report-header-top { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:16px; }
    .report-logo { display:flex; align-items:center; gap:10px; }
    .report-logo-icon { width:38px; height:38px; background:rgba(255,255,255,.15); border-radius:9px; display:flex; align-items:center; justify-content:center; }
    .report-logo-text { font-size:1.3rem; font-weight:900; letter-spacing:-.5px; }
    .report-logo-sub { font-size:.7rem; color:rgba(180,220,255,.75); font-weight:500; }
    .report-id { text-align:right; font-size:.73rem; color:rgba(180,220,255,.70); line-height:1.8; }
    .report-title { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:rgba(180,220,255,.70); margin-bottom:4px; }
    .report-doc-title { font-size:1.1rem; font-weight:800; letter-spacing:-.2px; }

    /* Sections */
    .section { padding:24px 36px; border-bottom:1px solid #e2e8f0; }
    .section:last-child { border-bottom:none; }
    .sec-title { font-size:.68rem; font-weight:800; text-transform:uppercase; letter-spacing:.8px; color:#64748b; margin-bottom:14px; display:flex; align-items:center; gap:8px; }
    .sec-title::before { content:''; display:inline-block; width:3px; height:12px; background:linear-gradient(135deg,#1a6fc4,#38bdf8); border-radius:2px; }

    /* Patient info grid */
    .info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px; }
    .info-item { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; }
    .info-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; color:#94a3b8; margin-bottom:3px; }
    .info-value { font-size:.9rem; font-weight:700; color:#0f172a; }

    /* Triage banner */
    .triage-banner { padding:14px 20px; border-radius:10px; display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
    .triage-banner.critical { background:#fef2f2; border:1.5px solid #fca5a5; }
    .triage-banner.high { background:#fff7ed; border:1.5px solid #fed7aa; }
    .triage-banner.moderate { background:#fffbeb; border:1.5px solid #fde68a; }
    .triage-banner.low { background:#f0fdf4; border:1.5px solid #bbf7d0; }
    .triage-banner.inconclusive { background:#f8fafc; border:1.5px solid #cbd5e1; }
    .triage-label { font-size:.95rem; font-weight:800; }
    .triage-banner.critical .triage-label { color:#dc2626; }
    .triage-banner.high .triage-label { color:#ea580c; }
    .triage-banner.moderate .triage-label { color:#d97706; }
    .triage-banner.low .triage-label { color:#16a34a; }
    .triage-banner.inconclusive .triage-label { color:#64748b; }
    .triage-time { font-size:.73rem; color:#64748b; }

    /* Vitals table */
    .vitals-table { width:100%; border-collapse:collapse; }
    .vitals-table th { background:#f1f5f9; padding:8px 12px; font-size:.64rem; font-weight:800; text-transform:uppercase; letter-spacing:.6px; color:#64748b; text-align:left; border:1px solid #e2e8f0; }
    .vitals-table td { padding:9px 12px; font-size:.84rem; border:1px solid #e2e8f0; }
    .vt-normal { color:#16a34a; font-weight:700; }
    .vt-warn { color:#d97706; font-weight:700; }
    .vt-danger { color:#dc2626; font-weight:700; }

    /* Symptom chips */
    .chip-wrap { display:flex; flex-wrap:wrap; gap:6px; }
    .chip { display:inline-block; padding:4px 12px; border:1.5px solid #cbd5e1; border-radius:20px; background:#f8fafc; font-size:.77rem; font-weight:500; color:#475569; }

    /* Prediction card */
    .pred-card { border:1.5px solid #e2e8f0; border-radius:10px; padding:16px 20px; margin-bottom:12px; position:relative; }
    .pred-card.primary { border-color:#2563eb; background:#eff6ff; }
    .pred-card.primary::before { content:'PRIMARY PREDICTION'; position:absolute; top:-10px; left:16px; background:#2563eb; color:#fff; font-size:.6rem; font-weight:800; padding:2px 10px; border-radius:4px; letter-spacing:.5px; }
    .pred-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:8px; }
    .pred-name { font-size:1rem; font-weight:800; color:#0f172a; }
    .pred-meta { font-size:.72rem; color:#64748b; margin-top:2px; }
    .conf-pill { background:#dbeafe; color:#1d4ed8; border:1px solid #bfdbfe; padding:4px 12px; border-radius:20px; font-size:.78rem; font-weight:800; }
    .urgency-tag { display:inline-flex; padding:3px 10px; border-radius:6px; font-size:.67rem; font-weight:800; letter-spacing:.5px; text-transform:uppercase; margin-top:8px; margin-bottom:8px; border:1.5px solid; }
    .ut-critical { color:#dc2626; border-color:#fca5a5; }
    .ut-high { color:#ea580c; border-color:#fed7aa; }
    .ut-moderate { color:#d97706; border-color:#fde68a; }
    .ut-low { color:#16a34a; border-color:#bbf7d0; }
    .match-chips-report { display:flex; flex-wrap:wrap; gap:5px; margin-bottom:8px; }
    .mc { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; padding:2px 9px; border-radius:20px; font-size:.7rem; font-weight:600; }

    /* Rec list */
    .rec-list { list-style:none; }
    .rec-list li { padding:5px 0 5px 18px; position:relative; font-size:.8rem; color:#334155; border-bottom:1px solid #f1f5f9; }
    .rec-list li::before { content:"→"; position:absolute; left:0; color:#2563eb; font-weight:700; }
    .rec-list li:last-child { border-bottom:none; }

    /* Notes */
    .notes-box { background:#fffbeb; border:1.5px solid #fde68a; border-radius:8px; padding:12px 16px; font-size:.83rem; color:#78350f; line-height:1.65; }

    /* Footer */
    .report-footer { background:#f1f5f9; padding:18px 36px; }
    .disclaimer { font-size:.72rem; color:#64748b; line-height:1.7; }
    .footer-sig { display:flex; justify-content:space-between; align-items:flex-end; margin-top:24px; padding-top:16px; border-top:1px solid #e2e8f0; }
    .sig-line { width:180px; border-top:1.5px solid #94a3b8; padding-top:6px; font-size:.72rem; color:#64748b; text-align:center; }

    @media print {
      .print-bar { display:none !important; }
      .report { margin:0; box-shadow:none; max-width:100%; }
      body { background:#fff; }
      @page { margin:1cm 1.5cm; }
    }
  </style>
</head>
<body>

<div class="print-bar">
  <h3>🖨️ ClinAI Clinical Assessment Report</h3>
  <div class="print-actions">
    <a href="assessment.php?id=<?= $id ?>" class="btn-back-link">
      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
      Back
    </a>
    <button class="btn-print-now" onclick="window.print()">🖨️ Print / Save as PDF</button>
  </div>
</div>

<div class="report" id="report">
  <div style="text-align:center;padding:60px;color:#64748b" id="loading-msg">Loading report…</div>
</div>

<script>
function esc(s) { return String(s??'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function cap(s) { return s ? s[0].toUpperCase()+s.slice(1) : ''; }
function fmtDate(d) { return new Date(d).toLocaleString('en-US',{weekday:'short',year:'numeric',month:'short',day:'numeric',hour:'2-digit',minute:'2-digit'}); }

function getVitalStatus(label, value) {
  const r = { Temp:{n:[36.1,37.2],w:[37.3,38.4]}, HR:{n:[60,100],w:[50,59]}, SpO2:{n:[95,100],w:[92,94]}, RR:{n:[12,20],w:[21,24]}, SBP:{n:[90,119],w:[120,139]}, DBP:{n:[60,79],w:[80,89]}, Glucose:{n:[70,99],w:[100,125]} };
  if (value==null) return 'none';
  const ranges = r[label]; if(!ranges) return 'normal';
  if(value>=ranges.n[0]&&value<=ranges.n[1]) return 'normal';
  if(ranges.w&&value>=ranges.w[0]&&value<=ranges.w[1]) return 'warn';
  return 'danger';
}

async function loadReport() {
  try {
    const res  = await fetch('php/api.php?action=get&id=<?= $id ?>');
    const data = await res.json();
    if (!data.success) { document.getElementById('report').innerHTML = `<div style="padding:40px;text-align:center;color:#dc2626">Error: ${esc(data.error)}</div>`; return; }
    renderReport(data.data);
  } catch(e) {
    document.getElementById('report').innerHTML = '<div style="padding:40px;text-align:center;color:#dc2626">Network error.</div>';
  }
}

function renderReport(a) {
  const bmi = (a.weight_kg&&a.height_cm) ? (a.weight_kg/Math.pow(a.height_cm/100,2)).toFixed(1) : null;
  const preds = a.predictions_json || [];
  const syms  = a.symptoms_json || [];
  const triage = a.triage_level || 'inconclusive';
  const triageLabel = {critical:'🚨 CRITICAL — Immediate Action Required',high:'⚠️ HIGH — Urgent Medical Attention',moderate:'⚡ MODERATE — See a Doctor Soon',low:'✅ LOW — Non-urgent Care Recommended',inconclusive:'⚪ INCONCLUSIVE'}[triage] || 'Inconclusive';

  const rfMap = {rf_smoker:'Current Smoker',rf_diabetes:'Known Diabetes',rf_hypertension:'Hypertension',rf_immunocompromised:'Immunocompromised',rf_pregnant:'Pregnant',rf_family_history:'Family History CVD/DM',rf_travel:'Recent Tropical Travel',rf_tb_contact:'TB Contact',rf_hiv:'HIV Positive',rf_stress:'High Stress/Anxiety',rf_catheter:'Urinary Catheter'};
  const activeRFs = Object.entries(rfMap).filter(([k])=>a[k]==1).map(([,v])=>v);

  const vitals = [
    {lbl:'Temperature',key:'v_temperature',unit:'°C',stat:'Temp'},
    {lbl:'Blood Pressure',val:a.v_systolic_bp&&a.v_diastolic_bp?`${a.v_systolic_bp}/${a.v_diastolic_bp}`:null,unit:'mmHg',stat:'SBP'},
    {lbl:'Heart Rate',key:'v_heart_rate',unit:'bpm',stat:'HR'},
    {lbl:'SpO₂',key:'v_spo2',unit:'%',stat:'SpO2'},
    {lbl:'Respiratory Rate',key:'v_resp_rate',unit:'/min',stat:'RR'},
    {lbl:'Blood Glucose',key:'v_blood_glucose',unit:'mg/dL',stat:'Glucose'},
  ].map(v => ({...v, val:v.val??a[v.key]}));

  const html = `
    <div class="report-header">
      <div class="report-header-top">
        <div class="report-logo">
          <div class="report-logo-icon">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><rect x="9" y="2" width="6" height="20" rx="2" fill="white"/><rect x="2" y="9" width="20" height="6" rx="2" fill="white"/></svg>
          </div>
          <div>
            <div class="report-logo-text">ClinAI</div>
            <div class="report-logo-sub">Clinical Decision Support · Hospital Edition v2.0</div>
          </div>
        </div>
        <div class="report-id">
          <div>Report ID: #${a.id}</div>
          <div>Generated: ${fmtDate(new Date())}</div>
          <div>Assessed: ${fmtDate(a.created_at)}</div>
          ${a.assessed_by_name ? `<div>By: ${esc(a.assessed_by_name)} (${cap(a.assessor_role||'')})</div>` : ''}
        </div>
      </div>
      <div class="report-title">AI Clinical Assessment Report</div>
      <div class="report-doc-title">Differential Diagnosis & Clinical Recommendations</div>
    </div>

    <div class="section">
      <div class="sec-title">Patient Demographics</div>
      <div class="triage-banner ${triage}">
        <div class="triage-label">${triageLabel}</div>
        <div class="triage-time">Assessment #${a.id} · ${fmtDate(a.created_at)}</div>
      </div>
      <div class="info-grid">
        <div class="info-item"><div class="info-label">Full Name</div><div class="info-value">${esc(a.patient_name)}</div></div>
        <div class="info-item"><div class="info-label">Age</div><div class="info-value">${a.age} years</div></div>
        <div class="info-item"><div class="info-label">Biological Sex</div><div class="info-value">${cap(a.gender)}</div></div>
        ${a.mrn ? `<div class="info-item"><div class="info-label">MRN / Patient ID</div><div class="info-value">${esc(a.mrn)}</div></div>` : ''}
        ${a.ward ? `<div class="info-item"><div class="info-label">Ward / Department</div><div class="info-value">${esc(a.ward)}</div></div>` : ''}
        ${a.weight_kg ? `<div class="info-item"><div class="info-label">Weight</div><div class="info-value">${a.weight_kg} kg</div></div>` : ''}
        ${a.height_cm ? `<div class="info-item"><div class="info-label">Height</div><div class="info-value">${a.height_cm} cm</div></div>` : ''}
        ${bmi ? `<div class="info-item"><div class="info-label">BMI</div><div class="info-value">${bmi}</div></div>` : ''}
      </div>
    </div>

    <div class="section">
      <div class="sec-title">Vital Signs</div>
      <table class="vitals-table">
        <thead><tr><th>Parameter</th><th>Value</th><th>Unit</th><th>Status</th></tr></thead>
        <tbody>${vitals.map(v => {
          const st = getVitalStatus(v.stat, typeof v.val === 'number' ? v.val : null);
          const stLabel = {normal:'✓ Normal',warn:'⚡ Borderline',danger:'⚠ Abnormal',none:'—'}[st];
          const stClass = {normal:'vt-normal',warn:'vt-warn',danger:'vt-danger',none:''}[st];
          return `<tr><td>${v.lbl}</td><td style="font-weight:700">${v.val ?? '—'}</td><td>${v.val!=null?v.unit:'—'}</td><td class="${stClass}">${stLabel}</td></tr>`;
        }).join('')}</tbody>
      </table>
    </div>

    <div class="section">
      <div class="sec-title">Reported Symptoms (${syms.length})</div>
      <div class="chip-wrap">${syms.map(s=>`<span class="chip">${esc(s.replace(/_/g,' '))}</span>`).join('') || '<em style="color:#64748b">None recorded</em>'}</div>
    </div>

    ${activeRFs.length ? `
    <div class="section">
      <div class="sec-title">Risk Factors</div>
      <div class="chip-wrap">${activeRFs.map(r=>`<span class="chip" style="border-color:#fde68a;background:#fffbeb;color:#78350f">${r}</span>`).join('')}</div>
    </div>` : ''}

    ${a.notes ? `
    <div class="section">
      <div class="sec-title">Clinical Notes</div>
      <div class="notes-box">${esc(a.notes)}</div>
    </div>` : ''}

    <div class="section">
      <div class="sec-title">AI Differential Diagnosis (${preds.length} conditions)</div>
      ${preds.map((pred,i) => {
        const d = pred.disease||{};
        const uc = `ut-${pred.urgency||'low'}`;
        return `
          <div class="pred-card ${i===0?'primary':''}">
            <div class="pred-header">
              <div>
                <div class="pred-name">${i+1}. ${esc(d.name||'')}</div>
                <div class="pred-meta">ICD-10: ${esc(d.icd10||'')} &nbsp;·&nbsp; ${esc(d.specialty||'')}</div>
              </div>
              <span class="conf-pill">AI Confidence: ${pred.confidence}%</span>
            </div>
            <span class="urgency-tag ${uc}">Urgency: ${(pred.urgency||'').toUpperCase()}</span>
            ${pred.matchedSymptoms && pred.matchedSymptoms.length ? `
              <div style="font-size:.67rem;font-weight:800;text-transform:uppercase;letter-spacing:.6px;color:#64748b;margin-bottom:6px">Matched Symptoms (${pred.matchedSymptoms.length})</div>
              <div class="match-chips-report">${pred.matchedSymptoms.map(s=>`<span class="mc">${esc(s.replace(/_/g,' '))}</span>`).join('')}</div>` : ''}
          </div>`;
      }).join('')}
    </div>

    <div class="report-footer">
      <div class="disclaimer">
        <strong>⚕️ Medical Disclaimer:</strong> This report was generated by ClinAI — an AI-powered clinical decision-support system. It is intended to assist qualified healthcare professionals and does not constitute a definitive medical diagnosis. All findings must be interpreted in the context of the patient's complete clinical presentation by a licensed healthcare provider. In case of medical emergency, contact emergency services immediately. This report should not be used as a substitute for professional clinical judgement.
      </div>
      <div class="footer-sig">
        <div>
          <div style="font-size:.75rem;color:#64748b;margin-bottom:4px">ClinAI v2.0 · AI Disease Predictor · Hospital Edition</div>
          <div style="font-size:.73rem;color:#94a3b8">For licensed healthcare professionals only · ICD-10 mapped predictions</div>
        </div>
        <div>
          <div class="sig-line">Clinician Signature</div>
        </div>
      </div>
      <div style="text-align:center;margin-top:16px;font-size:.7rem;color:#94a3b8">
        By <strong>Gaurav Tamshete</strong> &nbsp;·&nbsp; ClinAI — AI Disease Predictor &nbsp;·&nbsp; BSc Computer Science Final Year Project
      </div>
    </div>`;

  document.getElementById('report').innerHTML = html;
}

loadReport();
</script>
</body>
</html>

/**
 * App Controller — handles all UI logic, step navigation, form collection,
 * and results rendering for the AI Disease Predictor
 */

const App = (() => {

  // ─── State ──────────────────────────────────────────────────────────────────
  let currentStep = 1;
  const TOTAL_STEPS = 4;
  let selectedSymptoms = new Set();
  let patientData = {};
  let vitalsData = {};
  let predictions = [];

  // ─── Init ────────────────────────────────────────────────────────────────────
  function init() {
    buildSymptomGrid();
    attachEventListeners();
    updateStepUI();
  }

  // ─── Build the dynamic symptom grid from the registry ────────────────────────
  function buildSymptomGrid() {
    const container = document.getElementById("symptom-grid");
    if (!container) return;

    // Group by category
    const categories = {};
    for (const [id, info] of Object.entries(SYMPTOM_REGISTRY)) {
      if (!categories[info.category]) categories[info.category] = [];
      if (!categories[info.category].find(s => s.id === id)) {
        categories[info.category].push({ id, label: info.label });
      }
    }

    let html = "";
    for (const [cat, symptoms] of Object.entries(categories)) {
      html += `<div class="symptom-category">
        <h4 class="symptom-cat-title">${cat}</h4>
        <div class="symptom-chips">`;
      for (const s of symptoms) {
        html += `<button type="button" class="symptom-chip" data-symptom="${s.id}" onclick="App.toggleSymptom('${s.id}', this)">${s.label}</button>`;
      }
      html += `</div></div>`;
    }
    container.innerHTML = html;
  }

  // ─── Toggle symptom chip ─────────────────────────────────────────────────────
  function toggleSymptom(id, el) {
    if (selectedSymptoms.has(id)) {
      selectedSymptoms.delete(id);
      el.classList.remove("active");
    } else {
      selectedSymptoms.add(id);
      el.classList.add("active");
    }
    updateSymptomCount();
  }

  function updateSymptomCount() {
    const count = selectedSymptoms.size;
    const el = document.getElementById("symptom-count");
    if (el) el.textContent = count === 0 ? "No symptoms selected" : `${count} symptom${count !== 1 ? "s" : ""} selected`;
  }

  // ─── Step navigation ─────────────────────────────────────────────────────────
  function attachEventListeners() {
    document.querySelectorAll("[data-next]").forEach(btn => {
      btn.addEventListener("click", () => nextStep());
    });
    document.querySelectorAll("[data-prev]").forEach(btn => {
      btn.addEventListener("click", () => prevStep());
    });

    // BMI auto-calculator
    ["weight", "height"].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.addEventListener("input", updateBMI);
    });

    // Symptom search filter
    const searchEl = document.getElementById("symptom-search");
    if (searchEl) searchEl.addEventListener("input", filterSymptoms);

    // Predict button
    const predictBtn = document.getElementById("predict-btn");
    if (predictBtn) predictBtn.addEventListener("click", runPrediction);

    // Print button
    const printBtn = document.getElementById("print-btn");
    if (printBtn) printBtn.addEventListener("click", () => window.print());

    // Reset button
    const resetBtn = document.getElementById("reset-btn");
    if (resetBtn) resetBtn.addEventListener("click", resetApp);

    // Copy report button
    const copyBtn = document.getElementById("copy-report-btn");
    if (copyBtn) copyBtn.addEventListener("click", copyReport);
  }

  function nextStep() {
    if (!validateStep(currentStep)) return;
    if (currentStep === 3) {
      collectData();
    }
    if (currentStep < TOTAL_STEPS) {
      currentStep++;
      updateStepUI();
    }
  }

  function prevStep() {
    if (currentStep > 1) {
      currentStep--;
      updateStepUI();
    }
  }

  function updateStepUI() {
    document.querySelectorAll(".step-panel").forEach((panel, i) => {
      panel.classList.toggle("active", i + 1 === currentStep);
    });
    document.querySelectorAll(".step-indicator").forEach((ind, i) => {
      ind.classList.remove("active", "completed");
      if (i + 1 === currentStep) ind.classList.add("active");
      if (i + 1 < currentStep) ind.classList.add("completed");
    });
    // Update step connectors
    const connectors = [
      document.getElementById("conn-1-2"),
      document.getElementById("conn-2-3"),
      document.getElementById("conn-3-4"),
    ];
    connectors.forEach((c, i) => {
      if (c) c.classList.toggle("filled", currentStep > i + 1);
    });
    // Update progress bar
    const progress = ((currentStep - 1) / (TOTAL_STEPS - 1)) * 100;
    const bar = document.getElementById("progress-bar");
    if (bar) bar.style.width = progress + "%";
  }

  // ─── Validation ──────────────────────────────────────────────────────────────
  function validateStep(step) {
    clearErrors();
    let valid = true;

    if (step === 1) {
      const required = ["patient-name", "patient-age", "patient-gender"];
      for (const id of required) {
        const el = document.getElementById(id);
        if (!el || !el.value.trim()) {
          showError(id, "This field is required.");
          valid = false;
        }
      }
      const age = parseInt(document.getElementById("patient-age")?.value);
      if (isNaN(age) || age < 0 || age > 120) {
        showError("patient-age", "Please enter a valid age (0–120).");
        valid = false;
      }
    }

    if (step === 2) {
      if (selectedSymptoms.size === 0) {
        showGlobalError("symptom-step-error", "Please select at least one symptom before continuing.");
        valid = false;
      }
    }

    return valid;
  }

  function showError(fieldId, msg) {
    const el = document.getElementById(fieldId);
    if (el) {
      el.classList.add("input-error");
      const errEl = document.createElement("span");
      errEl.className = "field-error";
      errEl.textContent = msg;
      el.parentNode.appendChild(errEl);
    }
  }

  function showGlobalError(id, msg) {
    const el = document.getElementById(id);
    if (el) { el.textContent = msg; el.style.display = "block"; }
  }

  function clearErrors() {
    document.querySelectorAll(".input-error").forEach(el => el.classList.remove("input-error"));
    document.querySelectorAll(".field-error").forEach(el => el.remove());
    document.querySelectorAll(".global-error").forEach(el => { el.textContent = ""; el.style.display = "none"; });
  }

  // ─── Data collection ─────────────────────────────────────────────────────────
  function collectData() {
    patientData = {
      name:             document.getElementById("patient-name")?.value?.trim(),
      age:              parseInt(document.getElementById("patient-age")?.value),
      gender:           document.getElementById("patient-gender")?.value,
      bmi:              parseFloat(document.getElementById("bmi-display")?.dataset.value) || null,
      smoker:           document.getElementById("rf-smoker")?.checked,
      hasDiabetes:      document.getElementById("rf-diabetes")?.checked,
      hasHypertension:  document.getElementById("rf-hypertension")?.checked,
      immunocompromised:document.getElementById("rf-immunocompromised")?.checked,
      pregnant:         document.getElementById("rf-pregnant")?.checked,
      familyHistory:    document.getElementById("rf-family-history")?.checked,
      recentTravel:     document.getElementById("rf-travel")?.checked,
      tbContact:        document.getElementById("rf-tb-contact")?.checked,
      hivPositive:      document.getElementById("rf-hiv")?.checked,
      highStress:       document.getElementById("rf-stress")?.checked,
      catheter:         document.getElementById("rf-catheter")?.checked,
    };

    vitalsData = {
      temperature:      parseFloat(document.getElementById("v-temperature")?.value) || null,
      systolicBP:       parseInt(document.getElementById("v-systolic")?.value) || null,
      diastolicBP:      parseInt(document.getElementById("v-diastolic")?.value) || null,
      heartRate:        parseInt(document.getElementById("v-heart-rate")?.value) || null,
      respiratoryRate:  parseInt(document.getElementById("v-resp-rate")?.value) || null,
      spo2:             parseInt(document.getElementById("v-spo2")?.value) || null,
      bloodGlucose:     parseFloat(document.getElementById("v-glucose")?.value) || null,
    };
  }

  // ─── BMI calculator ──────────────────────────────────────────────────────────
  function updateBMI() {
    const weight = parseFloat(document.getElementById("weight")?.value);
    const height = parseFloat(document.getElementById("height")?.value) / 100; // cm to m
    const display = document.getElementById("bmi-display");
    if (!display) return;

    if (weight > 0 && height > 0) {
      const bmi = (weight / (height * height)).toFixed(1);
      let category = "";
      let color = "#16a34a";
      if (bmi < 18.5) { category = "Underweight"; color = "#d97706"; }
      else if (bmi < 25) { category = "Normal"; color = "#16a34a"; }
      else if (bmi < 30) { category = "Overweight"; color = "#d97706"; }
      else { category = "Obese"; color = "#dc2626"; }

      display.textContent = `BMI: ${bmi} (${category})`;
      display.style.color = color;
      display.dataset.value = bmi;
    } else {
      display.textContent = "";
      display.dataset.value = "";
    }
  }

  // ─── Symptom filter ──────────────────────────────────────────────────────────
  function filterSymptoms() {
    const query = document.getElementById("symptom-search")?.value?.toLowerCase() || "";
    document.querySelectorAll(".symptom-chip").forEach(chip => {
      const label = chip.textContent.toLowerCase();
      chip.style.display = label.includes(query) ? "" : "none";
    });
    document.querySelectorAll(".symptom-category").forEach(cat => {
      const visible = [...cat.querySelectorAll(".symptom-chip")].some(c => c.style.display !== "none");
      cat.style.display = visible ? "" : "none";
    });
  }

  // ─── Run AI prediction ───────────────────────────────────────────────────────
  function runPrediction() {
    collectData();
    const symptomsArray = Array.from(selectedSymptoms);
    predictions = PredictionEngine.predict(symptomsArray, vitalsData, patientData);

    // Expose payload globally so PHP save can pick it up
    window.__lastAssessmentPayload = {
      patient:     {
        name:   patientData.name,
        age:    patientData.age,
        gender: patientData.gender,
        id:     document.getElementById("patient-id")?.value?.trim() || null,
        ward:   document.getElementById("ward")?.value?.trim() || null,
        weight: document.getElementById("weight")?.value || null,
        height: document.getElementById("height")?.value || null,
      },
      vitals:      vitalsData,
      riskFactors: patientData,
      symptoms:    symptomsArray,
      predictions: predictions.map(r => ({
        disease:    { name: r.disease.name, icd10: r.disease.icd10, specialty: r.disease.specialty },
        confidence: r.confidence,
        urgency:    r.disease.urgency,
        matchedSymptoms: r.matchedSymptoms,
      })),
    };

    renderResults(predictions);
    currentStep = 4;
    updateStepUI();

    // Re-enable save button on new prediction
    const saveBtn = document.getElementById("save-btn");
    if (saveBtn) { saveBtn.disabled = false; saveBtn.textContent = "💾 Save to Database"; }
  }

  // ─── Render results ──────────────────────────────────────────────────────────
  function renderResults(predictions) {
    const container = document.getElementById("results-container");
    if (!container) return;

    const triage = PredictionEngine.getTriageLevel(predictions);
    const now = new Date().toLocaleString("en-US", { dateStyle: "long", timeStyle: "short" });

    const genderIcon = patientData.gender === "female" ? "👩" : patientData.gender === "male" ? "👨" : "🧑";
    let html = `
      <div class="result-header">
        <div class="result-patient-info">
          <div class="result-patient-avatar">${genderIcon}</div>
          <div>
            <div class="result-patient-name">${patientData.name}</div>
            <div class="result-meta">
              ${patientData.age} yrs · ${capitalize(patientData.gender)}
              ${patientData.bmi ? ` · BMI ${parseFloat(patientData.bmi).toFixed(1)}` : ""}
              ${document.getElementById("ward")?.value ? ` · ${document.getElementById("ward").value}` : ""}
              · ${now}
            </div>
          </div>
        </div>
        <div class="triage-badge" style="background:${triage.color}">
          <span>${triage.badge === "red" ? "🚨" : triage.badge === "orange" ? "⚠️" : triage.badge === "yellow" ? "⚡" : "✅"}</span>
          <span>${triage.level}</span>
        </div>
      </div>

      <div class="vitals-summary">
        ${renderVitalsSummary()}
      </div>
    `;

    if (predictions.length === 0) {
      html += `<div class="no-results">
        <div class="no-results-icon">🔍</div>
        <h3>Insufficient Data for Prediction</h3>
        <p>The selected symptoms do not match any condition in the knowledge base with sufficient confidence. Please add more symptoms or consult a physician directly.</p>
      </div>`;
    } else {
      html += `<div class="results-subtitle">
        <strong>${predictions.length} potential condition${predictions.length > 1 ? "s" : ""} identified</strong> — 
        Results are sorted by AI confidence score. This tool supports clinical decision-making and does not replace professional diagnosis.
      </div>`;

      html += `<div class="disease-cards">`;
      predictions.forEach((result, index) => {
        html += renderDiseaseCard(result, index);
      });
      html += `</div>`;
    }

    // Symptom summary
    html += renderSymptomSummaryPanel();

    // Disclaimer
    html += `<div class="disclaimer">
      <strong>⚕️ Medical Disclaimer:</strong> This AI-assisted analysis is intended as a clinical decision-support tool only. 
      It does not constitute a definitive medical diagnosis. All findings must be interpreted by a qualified healthcare professional 
      in the context of the patient's complete clinical presentation. In case of emergency, call emergency services immediately.
    </div>`;

    container.innerHTML = html;
  }

  function renderVitalsSummary() {
    const vitals = [
      { label: "Temp", value: vitalsData.temperature, unit: "°C", normal: [36.1, 37.2], warn: [37.3, 38.4], danger: [38.5, 99] },
      { label: "BP", value: vitalsData.systolicBP && vitalsData.diastolicBP ? `${vitalsData.systolicBP}/${vitalsData.diastolicBP}` : null, unit: "mmHg" },
      { label: "HR", value: vitalsData.heartRate, unit: "bpm", normal: [60, 100], warn: [50, 59], danger: [0, 49] },
      { label: "SpO₂", value: vitalsData.spo2, unit: "%", normal: [95, 100], warn: [92, 94], danger: [0, 91] },
      { label: "RR", value: vitalsData.respiratoryRate, unit: "/min", normal: [12, 20], warn: [21, 24], danger: [25, 99] },
      { label: "Glucose", value: vitalsData.bloodGlucose, unit: "mg/dL", normal: [70, 99], warn: [100, 125], danger: [126, 999] },
    ];

    let html = `<div class="vitals-title">Vital Signs</div><div class="vitals-grid">`;
    for (const v of vitals) {
      if (!v.value) continue;
      let statusClass = "vital-normal";
      if (v.normal && typeof v.value === "number") {
        if (v.value < v.normal[0] || v.value > v.normal[1]) statusClass = "vital-warn";
        if (v.danger && (v.value <= v.danger[0] || v.value >= v.danger[1])) {
          // Check if in danger zone
          if (v.danger[0] < v.normal[0] && v.value <= v.danger[0]) statusClass = "vital-danger";
          if (v.danger[1] > v.normal[1] && v.value >= v.danger[1]) statusClass = "vital-danger";
        }
      }
      html += `<div class="vital-card ${statusClass}">
        <div class="vital-label">${v.label}</div>
        <div class="vital-value">${v.value}<span class="vital-unit">${v.unit}</span></div>
      </div>`;
    }
    html += `</div>`;
    return html;
  }

  function renderDiseaseCard(result, index) {
    const d = result.disease;
    const conf = result.confidence;
    const urgencyColors = { critical: "#dc2626", high: "#ea580c", moderate: "#d97706", low: "#16a34a" };
    const urgencyColor = urgencyColors[d.urgency] || "#6b7280";
    const confColor = conf >= 70 ? "#dc2626" : conf >= 45 ? "#ea580c" : conf >= 25 ? "#d97706" : "#16a34a";

    const matchedLabels = result.matchedSymptoms
      .map(s => SYMPTOM_REGISTRY[s]?.label || s)
      .join(", ");

    const recItems = d.recommendations.map(r => `<li>${r}</li>`).join("");

    return `
      <div class="disease-card ${index === 0 ? "primary-card" : ""}">
        <div class="card-header">
          <div class="card-title-section">
            ${index === 0 ? `<span class="primary-badge">Primary Prediction</span>` : ""}
            <h3 class="disease-name">${d.name}</h3>
            <div class="disease-meta">ICD-10: ${d.icd10} · ${d.specialty}</div>
          </div>
          <div class="confidence-section">
            <div class="confidence-ring" style="--conf-color:${confColor}">
              <svg viewBox="0 0 36 36">
                <path class="conf-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path class="conf-fill" style="stroke:${confColor};stroke-dasharray:${conf},100" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
              </svg>
              <div class="conf-text" style="color:${confColor}">${conf}%</div>
            </div>
            <div class="conf-label">AI Confidence</div>
          </div>
        </div>

        <div class="conf-bar-row">
          <div class="conf-bar-bg">
            <div class="conf-bar-fill" style="width:${conf}%;background:${confColor}"></div>
          </div>
        </div>

        <p class="disease-description">${d.description}</p>

        <div class="urgency-tag" style="border-color:${urgencyColor};color:${urgencyColor}">
          Urgency: ${d.urgency.toUpperCase()}
        </div>

        <div class="matched-section">
          <div class="section-label">Matching Symptoms (${result.matchedSymptoms.length})</div>
          <div class="matched-chips">
            ${result.matchedSymptoms.map(s => `<span class="matched-chip">${SYMPTOM_REGISTRY[s]?.label || s}</span>`).join("")}
          </div>
        </div>

        <div class="rec-section">
          <div class="section-label">Clinical Recommendations</div>
          <ul class="rec-list">${recItems}</ul>
        </div>
      </div>
    `;
  }

  function renderSymptomSummaryPanel() {
    const labels = Array.from(selectedSymptoms).map(s => SYMPTOM_REGISTRY[s]?.label || s);
    if (!labels.length) return "";
    return `
      <div class="symptom-summary-panel">
        <div class="section-label">All Reported Symptoms (${labels.length})</div>
        <div class="matched-chips">
          ${labels.map(l => `<span class="matched-chip reported">${l}</span>`).join("")}
        </div>
      </div>
    `;
  }

  // ─── Copy report ─────────────────────────────────────────────────────────────
  function copyReport() {
    if (!predictions.length) return;
    const lines = [
      `CLINICAL AI ASSESSMENT REPORT`,
      `Generated: ${new Date().toLocaleString()}`,
      `Patient: ${patientData.name}, ${patientData.age}y, ${capitalize(patientData.gender)}`,
      ``,
      `SYMPTOMS REPORTED: ${Array.from(selectedSymptoms).map(s => SYMPTOM_REGISTRY[s]?.label || s).join(", ")}`,
      ``,
      `AI PREDICTIONS:`,
      ...predictions.map((r, i) => [
        `${i + 1}. ${r.disease.name} (ICD-10: ${r.disease.icd10})`,
        `   Confidence: ${r.confidence}% | Urgency: ${r.urgency.toUpperCase()}`,
        `   Specialty: ${r.disease.specialty}`,
        `   Recommendations: ${r.disease.recommendations.join("; ")}`,
      ].join("\n")),
      ``,
      `DISCLAIMER: This AI assessment is for clinical decision support only. Confirm with qualified physician.`
    ];
    navigator.clipboard?.writeText(lines.join("\n")).then(() => {
      const btn = document.getElementById("copy-report-btn");
      if (btn) { btn.textContent = "✓ Copied!"; setTimeout(() => btn.textContent = "📋 Copy Report", 2000); }
    });
  }

  // ─── Reset ───────────────────────────────────────────────────────────────────
  function resetApp() {
    selectedSymptoms.clear();
    patientData = {};
    vitalsData = {};
    predictions = [];
    currentStep = 1;
    document.querySelectorAll(".symptom-chip").forEach(c => c.classList.remove("active"));
    document.querySelectorAll("input, select").forEach(el => { if (el.type === "checkbox") el.checked = false; else el.value = ""; });
    document.getElementById("bmi-display") && (document.getElementById("bmi-display").textContent = "");
    updateSymptomCount();
    updateStepUI();
  }

  // ─── Helpers ─────────────────────────────────────────────────────────────────
  function capitalize(str) {
    return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
  }

  function clearAllSymptoms() {
    selectedSymptoms.clear();
    document.querySelectorAll(".symptom-chip.active").forEach(c => c.classList.remove("active"));
    updateSymptomCount();
  }

  return { init, toggleSymptom, clearAllSymptoms };
})();

document.addEventListener("DOMContentLoaded", () => App.init());

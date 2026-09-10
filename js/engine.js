/**
 * AI Disease Prediction Engine
 * Uses weighted symptom scoring + vital sign triggers + risk factor modifiers
 */

const PredictionEngine = (() => {

  /**
   * Normalise a raw score to a 0–100 confidence percentage
   * using a sigmoid-like transformation for a realistic output curve
   */
  function normaliseScore(rawScore, maxPossible) {
    if (maxPossible === 0) return 0;
    const ratio = rawScore / maxPossible;
    // Sigmoid squash: maps 0-1 ratio to 0-100 with natural roll-off
    const sigmoid = 1 / (1 + Math.exp(-12 * (ratio - 0.4)));
    return Math.min(100, Math.round(sigmoid * 100));
  }

  /**
   * Apply vital sign boost: if the patient's vitals cross a disease threshold,
   * add a significant bonus multiplier to that disease's score.
   */
  function computeVitalsBoost(disease, vitals) {
    const v = disease.vitals || {};
    let boost = 1.0;

    if (v.temp_min && vitals.temperature >= v.temp_min) boost += 0.35;
    if (v.temp_max && vitals.temperature > v.temp_max) boost -= 0.1;
    if (v.spo2_max && vitals.spo2 && vitals.spo2 <= v.spo2_max) boost += 0.45;
    if (v.sbp_min && vitals.systolicBP >= v.sbp_min) boost += 0.4;
    if (v.dbp_min && vitals.diastolicBP >= v.dbp_min) boost += 0.3;
    if (v.rr_max && vitals.respiratoryRate >= v.rr_max) boost += 0.3;
    if (v.glucose_min && vitals.bloodGlucose >= v.glucose_min) boost += 0.55;

    return boost;
  }

  /**
   * Apply risk factor bonus: matches patient profile with disease risk factors
   */
  function computeRiskBoost(disease, patient) {
    const rf = disease.riskFactors || {};
    let bonus = 0;

    if (rf.elderly && patient.age >= 65) bonus += rf.elderly * 0.15;
    if (rf.obese && patient.bmi >= 30) bonus += rf.obese * 0.12;
    if (rf.smoker && patient.smoker) bonus += rf.smoker * 0.12;
    if (rf.diabetes && patient.hasDiabetes) bonus += rf.diabetes * 0.1;
    if (rf.hypertension && patient.hasHypertension) bonus += rf.hypertension * 0.1;
    if (rf.immunocompromised && patient.immunocompromised) bonus += rf.immunocompromised * 0.15;
    if (rf.pregnant && patient.pregnant) bonus += rf.pregnant * 0.1;
    if (rf.female && patient.gender === "female") bonus += rf.female * 0.1;
    if (rf.male && patient.gender === "male") bonus += rf.male * 0.05;
    if (rf["family_history"] && patient.familyHistory) bonus += rf["family_history"] * 0.1;
    if (rf["tropical_travel"] && patient.recentTravel) bonus += rf["tropical_travel"] * 0.2;
    if (rf["tb_contact"] && patient.tbContact) bonus += rf["tb_contact"] * 0.25;
    if (rf["hiv_positive"] && patient.hivPositive) bonus += rf["hiv_positive"] * 0.2;
    if (rf.stress && patient.highStress) bonus += rf.stress * 0.08;
    if (rf.catheter && patient.catheter) bonus += rf.catheter * 0.2;

    return bonus;
  }

  /**
   * Core prediction function
   * @param {string[]} selectedSymptoms - list of symptom IDs
   * @param {object}   vitals           - { temperature, systolicBP, diastolicBP, spo2, heartRate, bloodGlucose, respiratoryRate }
   * @param {object}   patient          - demographics and risk factors
   * @returns {Array}  sorted array of { disease, confidence, matchedSymptoms, urgency }
   */
  function predict(selectedSymptoms, vitals, patient) {
    const symptomSet = new Set(selectedSymptoms);
    const results = [];

    for (const disease of DISEASE_KB) {
      const diseaseSymptoms = disease.symptoms;
      let rawScore = 0;
      let maxScore = 0;
      const matchedSymptoms = [];

      // 1. Symptom matching score
      for (const [symptom, weight] of Object.entries(diseaseSymptoms)) {
        maxScore += weight;
        if (symptomSet.has(symptom)) {
          rawScore += weight;
          matchedSymptoms.push(symptom);
        }
      }

      if (matchedSymptoms.length === 0) continue;

      // Require at least 2 matched symptoms (or 1 if it's a high-weight symptom)
      const topWeight = matchedSymptoms.reduce((acc, s) => acc + diseaseSymptoms[s], 0);
      if (matchedSymptoms.length < 2 && topWeight < 4) continue;

      // 2. Apply vital sign boost
      const vitalsBoost = computeVitalsBoost(disease, vitals);
      rawScore = rawScore * vitalsBoost;

      // 3. Apply risk factor bonus (additive on top of score)
      const riskBonus = computeRiskBoost(disease, patient);
      rawScore = rawScore * (1 + riskBonus);

      // 4. Compute normalised confidence
      const confidence = normaliseScore(rawScore, maxScore * 1.2); // scale allows boosted scores

      if (confidence >= 15) { // minimum threshold
        results.push({
          disease,
          confidence,
          matchedSymptoms,
          urgency: disease.urgency
        });
      }
    }

    // Sort by confidence descending, then critical ones first
    results.sort((a, b) => {
      const urgencyOrder = { critical: 4, high: 3, moderate: 2, low: 1 };
      if (b.confidence !== a.confidence) return b.confidence - a.confidence;
      return (urgencyOrder[b.urgency] || 0) - (urgencyOrder[a.urgency] || 0);
    });

    return results.slice(0, 6); // top 6 predictions
  }

  /**
   * Returns a triage level label based on the top result urgency
   */
  function getTriageLevel(predictions) {
    if (!predictions.length) return { level: "Inconclusive", color: "#6b7280", badge: "grey" };
    const topUrgency = predictions[0].urgency;
    const map = {
      critical: { level: "CRITICAL — Immediate Action Required", color: "#dc2626", badge: "red" },
      high:     { level: "HIGH — Urgent Medical Attention",       color: "#ea580c", badge: "orange" },
      moderate: { level: "MODERATE — See a Doctor Soon",          color: "#d97706", badge: "yellow" },
      low:      { level: "LOW — Non-urgent Care Recommended",     color: "#16a34a", badge: "green" }
    };
    return map[topUrgency] || map.low;
  }

  return { predict, getTriageLevel };
})();

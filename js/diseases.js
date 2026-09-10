/**
 * ClinAI Disease Knowledge Base — v3.0
 * 100+ diseases with weighted symptoms, vitals triggers, risk factors, recommendations
 */
const DISEASE_KB = [
  // ── RESPIRATORY ─────────────────────────────────────────────────────────────
  {
    id: "influenza", name: "Influenza (Flu)", icd10: "J11", urgency: "moderate", specialty: "General Medicine",
    description: "A contagious respiratory illness caused by influenza viruses affecting the nose, throat, and lungs.",
    symptoms: { fever:3, chills:3, headache:2, body_aches:3, fatigue:3, sore_throat:2, runny_nose:2, cough:2, loss_of_appetite:1, nasal_congestion:1, sneezing:1 },
    vitals: { temp_min:38.0, temp_max:40.5 },
    riskFactors: { elderly:1, immunocompromised:2, pregnant:1 },
    recommendations: ["Rest and stay hydrated","Antiviral medications (oseltamivir) if within 48 hours of onset","Fever management with paracetamol/ibuprofen","Isolate to prevent spread","Annual flu vaccination recommended"]
  },
  {
    id: "covid19", name: "COVID-19", icd10: "U07.1", urgency: "high", specialty: "Infectious Disease",
    description: "Respiratory illness caused by SARS-CoV-2 coronavirus with wide clinical spectrum.",
    symptoms: { fever:3, cough:3, shortness_of_breath:3, fatigue:3, loss_of_smell:4, loss_of_taste:4, headache:2, sore_throat:2, body_aches:2, chills:2, chest_pain:2, diarrhea:1, runny_nose:1 },
    vitals: { temp_min:37.5, spo2_max:94 },
    riskFactors: { elderly:2, immunocompromised:2, diabetes:1, hypertension:1, obese:1 },
    recommendations: ["PCR/Antigen test for confirmation","Isolation for at least 5 days","Monitor SpO2 closely","Seek emergency care if SpO2 < 92%","Antivirals (nirmatrelvir/ritonavir) for high-risk patients","COVID-19 vaccination recommended"]
  },
  {
    id: "pneumonia", name: "Pneumonia", icd10: "J18", urgency: "high", specialty: "Pulmonology",
    description: "Infection causing inflammation of air sacs in one or both lungs, which may fill with fluid.",
    symptoms: { fever:3, cough:3, shortness_of_breath:3, chest_pain:3, fatigue:2, chills:3, productive_cough:3, sweating:2, loss_of_appetite:1, rapid_breathing:3, confusion:2 },
    vitals: { temp_min:38.5, spo2_max:92, rr_max:25 },
    riskFactors: { elderly:2, immunocompromised:2, smoker:2 },
    recommendations: ["Chest X-ray for confirmation","Antibiotic therapy (amoxicillin/azithromycin)","Hospitalization if PSI score high","Supplemental oxygen if SpO2 < 94%","Follow-up CXR in 6–8 weeks","Pneumococcal vaccination recommended"]
  },
  {
    id: "common_cold", name: "Common Cold", icd10: "J00", urgency: "low", specialty: "General Medicine",
    description: "Viral upper respiratory infection primarily affecting the nose and throat.",
    symptoms: { runny_nose:4, nasal_congestion:4, sneezing:3, sore_throat:3, cough:2, headache:1, fatigue:1, mild_fever:2, watery_eyes:2 },
    vitals: { temp_min:37.0, temp_max:38.0 },
    riskFactors: { immunocompromised:1 },
    recommendations: ["Rest and increased fluid intake","Saline nasal rinse","Decongestants and antihistamines for symptom relief","Zinc lozenges may reduce duration","Antibiotics are NOT effective (viral cause)","Isolate to prevent spread"]
  },
  {
    id: "bronchitis", name: "Acute Bronchitis", icd10: "J20", urgency: "low", specialty: "Pulmonology",
    description: "Inflammation of the bronchial tubes lining, usually following a viral infection.",
    symptoms: { cough:4, productive_cough:3, chest_tightness:3, fatigue:2, sore_throat:2, mild_fever:2, wheezing:2, shortness_of_breath:2, runny_nose:1 },
    vitals: { temp_min:37.5 },
    riskFactors: { smoker:3, elderly:1 },
    recommendations: ["Rest and increased fluids","Cough suppressants or expectorants","Avoid smoking and irritants","Bronchodilators if wheezing","Antibiotics only if bacterial co-infection suspected","Chest physiotherapy if needed"]
  },
  {
    id: "copd", name: "COPD (Chronic Obstructive Pulmonary Disease)", icd10: "J44", urgency: "high", specialty: "Pulmonology",
    description: "Chronic inflammatory lung disease causing obstructed airflow, including emphysema and chronic bronchitis.",
    symptoms: { shortness_of_breath:4, chronic_cough:4, productive_cough:3, wheezing:3, chest_tightness:3, exercise_intolerance:3, fatigue:2, cyanosis:2, weight_loss:1 },
    vitals: { spo2_max:93, rr_max:24 },
    riskFactors: { smoker:4, elderly:2, occupational_exposure:2 },
    recommendations: ["Spirometry (FEV1/FVC < 0.70 confirms diagnosis)","Smoking cessation — most critical intervention","Bronchodilators (LABA/LAMA)","Inhaled corticosteroids for frequent exacerbations","Pulmonary rehabilitation","Annual flu and pneumococcal vaccines","Oxygen therapy if SpO2 persistently < 88%"]
  },
  {
    id: "asthma", name: "Bronchial Asthma", icd10: "J45", urgency: "moderate", specialty: "Pulmonology",
    description: "Chronic inflammatory airway disease causing recurrent episodes of wheezing, breathlessness, and coughing.",
    symptoms: { shortness_of_breath:4, wheezing:4, cough:3, chest_tightness:4, nocturnal_symptoms:3, exercise_intolerance:2, shortness_of_breath_at_rest:3 },
    vitals: { spo2_max:94 },
    riskFactors: { family_history:2, smoker:2 },
    recommendations: ["Spirometry and peak flow measurement","SABA (salbutamol) for acute relief","Inhaled corticosteroids as controller therapy","Identify and avoid triggers","Written asthma action plan","Annual influenza vaccination"]
  },
  {
    id: "pulmonary_embolism", name: "Pulmonary Embolism", icd10: "I26", urgency: "critical", specialty: "Cardiology / Pulmonology",
    description: "EMERGENCY: Blood clot blocking pulmonary arteries, reducing blood flow to the lungs.",
    symptoms: { shortness_of_breath:5, chest_pain:4, rapid_breathing:4, cough:3, bloody_cough:4, palpitations:3, dizziness:3, sweating:3, leg_swelling:3, calf_swelling_redness:3 },
    vitals: { spo2_max:93, rr_max:24 },
    riskFactors: { immobility:3, pregnant:2, family_history:2, obese:2 },
    recommendations: ["🚨 EMERGENCY — immediate anticoagulation","CT pulmonary angiography (CTPA) for confirmation","D-dimer blood test (screening)","IV heparin infusion","Thrombolytics if haemodynamically unstable","Monitor SpO2 and vital signs continuously"]
  },
  {
    id: "sinusitis", name: "Acute Sinusitis", icd10: "J32", urgency: "low", specialty: "ENT / General Medicine",
    description: "Inflammation of the sinuses, usually following a viral upper respiratory tract infection.",
    symptoms: { nasal_congestion:4, facial_pain:4, headache:3, runny_nose:3, loss_of_smell:2, fever:2, sore_throat:2, cough:1, bad_breath:2 },
    vitals: { temp_min:37.5 },
    riskFactors: { allergies:2 },
    recommendations: ["Saline nasal irrigation","Nasal corticosteroid sprays","Decongestants for symptom relief","Antibiotics only if bacterial (>10 days or worsening)","Pain relief with NSAIDs","ENT referral if chronic (>12 weeks)"]
  },
  // ── CARDIAC ─────────────────────────────────────────────────────────────────
  {
    id: "myocardial_infarction", name: "Myocardial Infarction (Heart Attack)", icd10: "I21", urgency: "critical", specialty: "Cardiology / Emergency",
    description: "EMERGENCY: Blockage of coronary artery causing heart muscle necrosis.",
    symptoms: { chest_pain:5, left_arm_pain:5, jaw_pain:4, sweating:4, shortness_of_breath:4, nausea:3, back_pain:3, fatigue:2, palpitations:3, dizziness:3, anxiety:2 },
    vitals: { sbp_min:90 },
    riskFactors: { elderly:2, smoker:3, diabetes:2, hypertension:2, family_history:3, obese:2, male:1 },
    recommendations: ["🚨 CALL EMERGENCY SERVICES IMMEDIATELY","12-lead ECG immediately","Aspirin 325mg (if not contraindicated)","Nitroglycerin sublingual","IV access and continuous monitoring","Primary PCI within 90 minutes","Troponin, CK-MB levels"]
  },
  {
    id: "heart_failure", name: "Congestive Heart Failure", icd10: "I50", urgency: "high", specialty: "Cardiology",
    description: "The heart cannot pump blood efficiently, causing fluid accumulation in lungs and body.",
    symptoms: { shortness_of_breath:4, leg_swelling:4, orthopnea:4, paroxysmal_nocturnal_dyspnea:4, fatigue:3, rapid_breathing:3, cough:2, weight_gain:2, exercise_intolerance:3, palpitations:2 },
    vitals: { spo2_max:93, rr_max:24 },
    riskFactors: { elderly:3, hypertension:3, diabetes:2, obese:2, family_history:2 },
    recommendations: ["Echocardiogram and BNP levels","ACE inhibitors/ARBs/ARNI (sacubitril-valsartan)","Beta-blockers (carvedilol/bisoprolol)","Diuretics (furosemide) for fluid overload","Daily weight monitoring","Fluid and sodium restriction","Cardiology referral"]
  },
  {
    id: "angina", name: "Angina Pectoris", icd10: "I20", urgency: "high", specialty: "Cardiology",
    description: "Chest pain from reduced blood flow to heart muscle, typically triggered by exertion.",
    symptoms: { chest_pain:4, left_arm_pain:3, jaw_pain:2, shortness_of_breath:3, sweating:2, dizziness:2, nausea:2, fatigue:2 },
    vitals: {},
    riskFactors: { elderly:2, smoker:3, diabetes:2, hypertension:2, family_history:3, obese:1, male:1 },
    recommendations: ["ECG during pain episode","Exercise stress test or myocardial perfusion imaging","Sublingual nitroglycerin for acute attacks","Aspirin and statins","Beta-blockers or calcium channel blockers","Coronary angiography if refractory","Lifestyle modification"]
  },
  {
    id: "hypertension", name: "Hypertension", icd10: "I10", urgency: "moderate", specialty: "Cardiology",
    description: "Chronically elevated blood pressure — a major risk factor for heart disease, stroke and kidney disease.",
    symptoms: { headache:2, dizziness:2, blurred_vision:2, chest_pain:2, shortness_of_breath:1, nosebleed:2, fatigue:1, palpitations:2 },
    vitals: { sbp_min:140, dbp_min:90 },
    riskFactors: { elderly:1, obese:2, smoker:1, family_history:2, diabetes:1 },
    recommendations: ["24-hour ambulatory BP monitoring","DASH diet, reduce sodium, exercise","Antihypertensives (ACE inhibitors, ARBs, CCBs)","Regular home BP monitoring","Renal function and electrolytes","Cardiovascular risk assessment"]
  },
  {
    id: "arrhythmia", name: "Cardiac Arrhythmia", icd10: "I49", urgency: "high", specialty: "Cardiology",
    description: "Irregular heart rhythm — may be too fast, too slow, or erratic, affecting cardiac output.",
    symptoms: { palpitations:5, dizziness:3, shortness_of_breath:3, chest_pain:3, fatigue:3, syncope:3, irregular_heartbeat:5, sweating:2, anxiety:2 },
    vitals: {},
    riskFactors: { elderly:2, hypertension:2, diabetes:1, family_history:2, smoker:1 },
    recommendations: ["12-lead ECG and 24-hour Holter monitor","Echocardiogram","Electrolyte panel (K, Mg, Ca)","Rate/rhythm control medications","Anticoagulation if atrial fibrillation","Cardiology referral","Ablation or cardioversion if indicated"]
  },
  {
    id: "dvt", name: "Deep Vein Thrombosis (DVT)", icd10: "I82", urgency: "high", specialty: "Vascular Medicine",
    description: "Blood clot forming in a deep vein, usually in the leg, with risk of pulmonary embolism.",
    symptoms: { leg_swelling:4, calf_swelling_redness:4, leg_pain_walking:3, warmth_redness_leg:4, fatigue:1 },
    vitals: {},
    riskFactors: { immobility:3, pregnant:2, obese:2, family_history:2 },
    recommendations: ["Compression ultrasound of leg veins","D-dimer blood test","Anticoagulation (LMWH, rivaroxaban, apixaban)","Compression stockings","Avoid prolonged immobility","Monitor for signs of pulmonary embolism","Haematology review if recurrent"]
  },
  // ── GASTROINTESTINAL ─────────────────────────────────────────────────────────
  {
    id: "gastroenteritis", name: "Acute Gastroenteritis", icd10: "A09", urgency: "low", specialty: "Gastroenterology",
    description: "Inflammation of the stomach and intestines, usually from viral or bacterial infection.",
    symptoms: { nausea:3, vomiting:3, diarrhea:3, abdominal_pain:3, fever:2, loss_of_appetite:2, fatigue:1, stomach_cramps:3 },
    vitals: { temp_min:37.5 },
    riskFactors: { elderly:1, immunocompromised:1 },
    recommendations: ["Oral rehydration therapy (ORS)","BRAT diet","Antiemetics if vomiting severe","Seek care if signs of dehydration","Stool culture if > 3 days or blood in stool","Hand hygiene and food safety"]
  },
  {
    id: "gerd", name: "GERD (Gastroesophageal Reflux Disease)", icd10: "K21", urgency: "low", specialty: "Gastroenterology",
    description: "Chronic acid reflux causing heartburn and potential esophageal damage.",
    symptoms: { heartburn:4, acid_regurgitation:4, chest_pain:2, sore_throat:2, chronic_cough:2, nausea:2, bad_breath:2, difficulty_swallowing:2 },
    vitals: {},
    riskFactors: { obese:3, pregnant:2, smoker:2 },
    recommendations: ["Proton pump inhibitors (omeprazole)","H2 blockers (ranitidine)","Avoid trigger foods (fatty, spicy, caffeine)","Elevate head of bed","Weight loss if overweight","Upper GI endoscopy if alarm symptoms","Avoid eating 2–3 hours before bedtime"]
  },
  {
    id: "peptic_ulcer", name: "Peptic Ulcer Disease", icd10: "K27", urgency: "moderate", specialty: "Gastroenterology",
    description: "Open sores on the stomach lining or duodenum, often caused by H. pylori or NSAIDs.",
    symptoms: { epigastric_pain:4, nausea:3, vomiting:2, black_tarry_stool:4, heartburn:3, loss_of_appetite:2, abdominal_pain:3, bloody_vomit:3 },
    vitals: {},
    riskFactors: { smoker:2 },
    recommendations: ["H. pylori urea breath test or stool antigen","Triple therapy for H. pylori (PPI + 2 antibiotics)","PPIs for 4–8 weeks","Avoid NSAIDs and aspirin","Endoscopy for bleeding or persistent symptoms","Dietary modification"]
  },
  {
    id: "appendicitis", name: "Acute Appendicitis", icd10: "K35", urgency: "critical", specialty: "General Surgery",
    description: "EMERGENCY: Inflammation of the appendix requiring urgent surgical evaluation.",
    symptoms: { abdominal_pain:5, right_lower_pain:5, nausea:3, vomiting:3, fever:3, loss_of_appetite:3, rebound_tenderness:4, abdominal_rigidity:4, fatigue:1 },
    vitals: { temp_min:37.8 },
    riskFactors: {},
    recommendations: ["🚨 URGENT SURGICAL EVALUATION","CBC with differential (elevated WBC)","CT scan abdomen/pelvis","Ultrasound (children/pregnant women)","NPO immediately","IV antibiotics (ceftriaxone + metronidazole)","Laparoscopic appendectomy"]
  },
  {
    id: "ibs", name: "Irritable Bowel Syndrome (IBS)", icd10: "K58", urgency: "low", specialty: "Gastroenterology",
    description: "Functional bowel disorder causing abdominal pain, bloating and altered bowel habits without structural abnormality.",
    symptoms: { abdominal_pain:4, bloating:4, constipation:3, diarrhea:3, stomach_cramps:4, mucus_in_stool:3, abdominal_pain_upper:2, fatigue:2 },
    vitals: {},
    riskFactors: { stress:3, female:2, family_history:1 },
    recommendations: ["Rome IV criteria for diagnosis","High-fibre diet or low-FODMAP diet","Antispasmodics for cramping","Probiotics may help","Stress reduction and CBT","Exclude inflammatory bowel disease","Colonoscopy if alarm features"]
  },
  {
    id: "crohns", name: "Crohn's Disease", icd10: "K50", urgency: "moderate", specialty: "Gastroenterology",
    description: "Chronic transmural inflammatory bowel disease affecting any part of the GI tract.",
    symptoms: { diarrhea:4, abdominal_pain:4, weight_loss:3, fatigue:3, fever:2, rectal_bleeding:3, mucus_in_stool:3, oral_ulcers_gi:3, joint_pain:2 },
    vitals: { temp_min:37.5 },
    riskFactors: { family_history:3, smoker:2, stress:2 },
    recommendations: ["Colonoscopy with biopsy for diagnosis","MRI enterography for small bowel involvement","Corticosteroids for flare (prednisolone)","Immunomodulators (azathioprine, methotrexate)","Biologics (infliximab, adalimumab)","Nutritional support","Gastroenterology referral"]
  },
  {
    id: "hepatitis_b", name: "Hepatitis B", icd10: "B16", urgency: "high", specialty: "Infectious Disease / Hepatology",
    description: "Viral liver infection transmitted via blood/body fluids; can become chronic and cause cirrhosis.",
    symptoms: { jaundice:4, fatigue:3, abdominal_pain:3, dark_urine_gi:4, clay_stool:3, nausea:3, vomiting:2, fever:2, joint_pain:2, loss_of_appetite:2 },
    vitals: { temp_min:37.5 },
    riskFactors: { hiv_positive:2 },
    recommendations: ["HBsAg, anti-HBc, HBeAg serology","Liver function tests and viral load","Antiviral therapy (tenofovir/entecavir) if chronic","Hepatitis B vaccination for contacts","Monitor for cirrhosis and hepatocellular carcinoma","Avoid alcohol","Hepatology referral"]
  },
  {
    id: "pancreatitis", name: "Acute Pancreatitis", icd10: "K85", urgency: "high", specialty: "Gastroenterology / Surgery",
    description: "Sudden inflammation of the pancreas, commonly from gallstones or alcohol.",
    symptoms: { epigastric_pain:5, abdominal_pain:4, nausea:4, vomiting:4, fever:3, loss_of_appetite:3, back_pain:3, bloating:2 },
    vitals: { temp_min:37.8 },
    riskFactors: {},
    recommendations: ["Serum lipase/amylase (3x upper normal = diagnostic)","CT abdomen for severity (Balthazar score)","IV fluid resuscitation (aggressive)","NPO initially — early enteral feeding when tolerated","Analgesia (IV morphine/pethidine)","Treat underlying cause (ERCP for gallstones)","ITU monitoring if severe"]
  },
  {
    id: "gallstones", name: "Cholelithiasis / Gallstones", icd10: "K80", urgency: "moderate", specialty: "General Surgery",
    description: "Hardened bile deposits in the gallbladder causing biliary colic or cholecystitis.",
    symptoms: { right_upper_pain:4, fatty_food_pain:4, nausea:3, vomiting:3, jaundice:2, fever:2, abdominal_pain:3 },
    vitals: { temp_min:37.5 },
    riskFactors: { female:2, obese:3, elderly:1 },
    recommendations: ["Abdominal ultrasound (first-line)","LFTs, bilirubin, GGT","Laparoscopic cholecystectomy (definitive treatment)","Low-fat diet pre-operatively","ERCP if CBD stones present","Analgesia (diclofenac/morphine)"]
  },
  // ── ENDOCRINE ────────────────────────────────────────────────────────────────
  {
    id: "diabetes_t2", name: "Type 2 Diabetes Mellitus", icd10: "E11", urgency: "moderate", specialty: "Endocrinology",
    description: "Metabolic disorder with high blood glucose due to insulin resistance and relative insulin deficiency.",
    symptoms: { frequent_urination:4, excessive_thirst:4, blurred_vision:3, fatigue:3, slow_healing:3, frequent_infections:2, weight_loss:3, tingling_hands_feet:3, increased_hunger:3, dry_mouth:2 },
    vitals: { glucose_min:126 },
    riskFactors: { obese:3, elderly:2, family_history:3, hypertension:1 },
    recommendations: ["Fasting glucose and HbA1c testing","OGTT if borderline","Metformin as first-line therapy","Dietary consultation","Regular exercise (150 min/week)","Annual eye, foot, kidney exams","BP and cholesterol management"]
  },
  {
    id: "hypothyroidism", name: "Hypothyroidism", icd10: "E03", urgency: "low", specialty: "Endocrinology",
    description: "Underactive thyroid gland failing to produce sufficient thyroid hormones.",
    symptoms: { fatigue:4, weight_gain:4, cold_intolerance:4, constipation:3, dry_skin:3, hair_loss:3, slow_heart_rate:3, depression:2, puffy_face:3, muscle_weakness:3 },
    vitals: {},
    riskFactors: { female:3, elderly:2, family_history:2 },
    recommendations: ["TSH (elevated) and free T4 (low) blood test","Levothyroxine replacement therapy","Regular TSH monitoring (6–12 monthly)","Adjust dose based on symptoms and TSH","Avoid high-iodine supplements","Endocrinology referral if complex"]
  },
  {
    id: "hyperthyroidism", name: "Hyperthyroidism", icd10: "E05", urgency: "moderate", specialty: "Endocrinology",
    description: "Overactive thyroid producing excess thyroid hormones, commonly from Graves' disease.",
    symptoms: { weight_loss:4, heat_intolerance:4, palpitations:4, trembling:3, anxiety:3, sweating:3, diarrhea:2, fatigue:2, hair_loss:2, goiter:3, irregular_heartbeat:3 },
    vitals: {},
    riskFactors: { female:3, family_history:2, stress:2 },
    recommendations: ["TSH (suppressed) and free T4/T3 (elevated)","Thyroid antibodies (TSH-R Ab for Graves')","Antithyroid drugs (carbimazole, propylthiouracil)","Beta-blockers for symptom control","Radioactive iodine therapy","Thyroidectomy if indicated","Endocrinology referral"]
  },
  {
    id: "hypoglycemia", name: "Hypoglycaemia", icd10: "E16.0", urgency: "high", specialty: "Endocrinology / Emergency",
    description: "Abnormally low blood glucose (< 70 mg/dL), causing neurological and autonomic symptoms.",
    symptoms: { shakiness_hunger:5, sweating_hunger:4, dizziness:4, confusion:4, palpitations:3, anxiety:3, blurred_vision:3, headache:2, fatigue:3, trembling:3 },
    vitals: { glucose_min:0 },
    riskFactors: { diabetes:4 },
    recommendations: ["⚠️ Give 15–20g fast-acting glucose (glucose tablets/juice)","Recheck glucose in 15 minutes","If unconscious: IV dextrose or IM glucagon","Identify and treat underlying cause","Review insulin/medication doses","Dietary counselling","Continuous glucose monitoring consideration"]
  },
  // ── INFECTIOUS DISEASE ───────────────────────────────────────────────────────
  {
    id: "tuberculosis", name: "Pulmonary Tuberculosis", icd10: "A15", urgency: "high", specialty: "Infectious Disease / Pulmonology",
    description: "Bacterial infection (Mycobacterium tuberculosis) primarily affecting the lungs.",
    symptoms: { cough:4, bloody_cough:5, fever:3, night_sweats:4, weight_loss:4, fatigue:3, chest_pain:2, shortness_of_breath:2, loss_of_appetite:3 },
    vitals: { temp_min:37.5 },
    riskFactors: { immunocompromised:3, hiv_positive:3, tb_contact:4, smoker:1 },
    recommendations: ["🔴 RESPIRATORY ISOLATION immediately","Mantoux (TST) or IGRA blood test","Chest X-ray and sputum AFB smear/culture","GeneXpert MTB/RIF for rapid diagnosis","RIPE therapy: Rifampicin, Isoniazid, Pyrazinamide, Ethambutol","DOTS compliance","Contact tracing and screening"]
  },
  {
    id: "dengue", name: "Dengue Fever", icd10: "A90", urgency: "high", specialty: "Infectious Disease",
    description: "Mosquito-borne viral illness causing high fever and severe flu-like symptoms.",
    symptoms: { fever:4, severe_headache:4, eye_pain:3, joint_pain:4, muscle_pain:4, rash:3, nausea:2, vomiting:2, fatigue:3, bleeding_gums:4, abdominal_pain:3 },
    vitals: { temp_min:39.0 },
    riskFactors: { tropical_travel:3 },
    recommendations: ["NS1 antigen and dengue serology (IgM/IgG)","CBC — monitor platelet count","IV fluid resuscitation if severe","Paracetamol (AVOID aspirin/ibuprofen)","Hospitalize if platelet < 100,000","Mosquito avoidance"]
  },
  {
    id: "malaria", name: "Malaria", icd10: "B54", urgency: "high", specialty: "Infectious Disease",
    description: "Parasitic infection transmitted by Anopheles mosquitoes causing cyclical fevers.",
    symptoms: { fever:5, chills:4, sweating:4, headache:3, muscle_pain:3, nausea:3, vomiting:2, fatigue:3, joint_pain:2, abdominal_pain:2, jaundice:2 },
    vitals: { temp_min:38.5 },
    riskFactors: { tropical_travel:5 },
    recommendations: ["Thick and thin blood smear (gold standard)","Rapid malaria antigen test (RDT)","Artemisinin-based combination therapy (ACT)","IV artesunate for severe malaria","Admit if P. falciparum","Supportive: IV fluids, antipyretics","Post-travel screening"]
  },
  {
    id: "typhoid", name: "Typhoid Fever", icd10: "A01", urgency: "high", specialty: "Infectious Disease",
    description: "Systemic infection caused by Salmonella typhi via contaminated food or water.",
    symptoms: { fever:4, headache:3, abdominal_pain:4, constipation:3, diarrhea:2, fatigue:3, loss_of_appetite:3, nausea:2, rose_spots:3, slow_heart_rate:2 },
    vitals: { temp_min:39.0 },
    riskFactors: { tropical_travel:4 },
    recommendations: ["Blood culture (weeks 1–2), urine/stool culture","Widal test (supportive)","Azithromycin or fluoroquinolones","IV ceftriaxone for severe cases","Adequate hydration","Contact precautions","Typhoid vaccine for travellers"]
  },
  {
    id: "sepsis", name: "Sepsis", icd10: "A41", urgency: "critical", specialty: "Intensive Care / Emergency",
    description: "EMERGENCY: Life-threatening organ dysfunction from dysregulated response to infection.",
    symptoms: { fever:3, rapid_breathing:4, confusion:4, fatigue:4, sweating:3, dizziness:3, shortness_of_breath:4, palpitations:3, cold_hands_feet:3 },
    vitals: { temp_min:38.3, spo2_max:93, rr_max:22, sbp_min:90 },
    riskFactors: { immunocompromised:4, elderly:3, diabetes:2 },
    recommendations: ["🚨 SEPSIS SIX within 1 hour: oxygen, blood cultures, IV antibiotics, fluids, lactate, urine output","Broad-spectrum antibiotics (piperacillin-tazobactam)","Fluid resuscitation (30 mL/kg crystalloid)","Vasopressors (norepinephrine) if refractory hypotension","ITU admission for organ support","Source control"]
  },
  {
    id: "chickenpox", name: "Chickenpox (Varicella)", icd10: "B01", urgency: "low", specialty: "Infectious Disease",
    description: "Highly contagious viral infection causing characteristic itchy vesicular rash.",
    symptoms: { rash:5, fever:3, itching:5, vesicles:5, fatigue:3, loss_of_appetite:2, headache:2, sore_throat:1 },
    vitals: { temp_min:37.8 },
    riskFactors: { immunocompromised:3, pregnant:3 },
    recommendations: ["Clinical diagnosis (characteristic rash)","Isolate until all lesions crusted","Calamine lotion and antihistamines for itching","Aciclovir for immunocompromised or adults","Avoid aspirin (Reye's syndrome risk)","Varicella vaccination for susceptible contacts","Monitor for secondary bacterial infection"]
  },
  {
    id: "cellulitis", name: "Cellulitis", icd10: "L03", urgency: "moderate", specialty: "Dermatology / General Medicine",
    description: "Bacterial skin infection causing redness, swelling, and warmth, typically of the leg.",
    symptoms: { rash:3, purulent_discharge:2, fever:3, warmth_redness_leg:4, leg_swelling:3, pain_on_touch:4, fatigue:2 },
    vitals: { temp_min:37.8 },
    riskFactors: { diabetes:2, immunocompromised:2, obese:2 },
    recommendations: ["Clinical diagnosis","Antibiotics (flucloxacillin/cefalexin)","IV antibiotics if severe or facial","Mark the borders of redness to monitor spread","Elevate affected limb","Blood culture if systemically unwell","Exclude DVT if leg involvement"]
  },
  // ── NEUROLOGICAL ─────────────────────────────────────────────────────────────
  {
    id: "migraine", name: "Migraine", icd10: "G43", urgency: "low", specialty: "Neurology",
    description: "Recurrent moderate-to-severe headache, often with nausea and sensory hypersensitivity.",
    symptoms: { headache:4, nausea:3, vomiting:2, light_sensitivity:4, sound_sensitivity:3, visual_aura:3, dizziness:2, fatigue:2, neck_stiffness:1, unilateral_headache:4 },
    vitals: {},
    riskFactors: { female:2, family_history:2, stress:2 },
    recommendations: ["Triptans (sumatriptan) for acute attacks","NSAIDs and antiemetics","Rest in dark quiet room","Identify and avoid triggers","Prophylactic therapy if > 4 attacks/month","Neurology referral if refractory"]
  },
  {
    id: "stroke", name: "Ischaemic Stroke", icd10: "I63", urgency: "critical", specialty: "Neurology / Emergency",
    description: "EMERGENCY: Sudden blockage of brain blood supply causing neurological deficit. FAST: Face, Arms, Speech, Time.",
    symptoms: { weakness_one_side:5, facial_droop:5, slurred_speech:5, sudden_blindness:4, severe_headache:4, dizziness:4, confusion:4, loss_of_balance:4 },
    vitals: { sbp_min:160 },
    riskFactors: { elderly:3, hypertension:4, diabetes:2, smoker:3, family_history:2, atrial_fibrillation:3 },
    recommendations: ["🚨 EMERGENCY — call 999/112 immediately","FAST assessment: Face/Arms/Speech/Time","CT/MRI brain without contrast (exclude haemorrhage)","IV thrombolysis (alteplase) within 4.5 hours if ischaemic","Stroke unit admission","Aspirin 300mg after haemorrhage excluded","Anticoagulation for AF-related stroke","Rehabilitation: physio, OT, SALT"]
  },
  {
    id: "meningitis", name: "Bacterial Meningitis", icd10: "G03", urgency: "critical", specialty: "Infectious Disease / Neurology",
    description: "EMERGENCY: Bacterial infection of the meninges — brain and spinal cord coverings.",
    symptoms: { severe_headache:5, neck_rigidity:5, fever:5, photophobia_gi:4, rash:4, confusion:4, vomiting:3, kernig_sign:4, fatigue:3 },
    vitals: { temp_min:38.5 },
    riskFactors: { immunocompromised:3, tb_contact:2 },
    recommendations: ["🚨 EMERGENCY — do NOT delay antibiotics for LP","IV ceftriaxone immediately (+ dexamethasone)","Lumbar puncture after CT if safe","Blood cultures before antibiotics","Non-blanching rash = meningococcal sepsis","Isolate until 24 hours of antibiotics","Contact prophylaxis (rifampicin/ciprofloxacin)"]
  },
  {
    id: "epilepsy", name: "Epilepsy / Seizure Disorder", icd10: "G40", urgency: "high", specialty: "Neurology",
    description: "Neurological disorder with recurrent unprovoked seizures due to abnormal brain electrical activity.",
    symptoms: { seizures:5, confusion:4, loss_of_consciousness:4, muscle_jerks:4, staring_spell:3, fatigue:3, headache:2, incontinence:3 },
    vitals: {},
    riskFactors: { family_history:2, stress:2 },
    recommendations: ["EEG for seizure characterisation","MRI brain for structural causes","Anti-epileptic drugs (sodium valproate, levetiracetam, lamotrigine)","Seizure diary and trigger identification","Driving restrictions (DVLA regulations)","Neurology referral","Safety advice (bathing, heights, machinery)"]
  },
  {
    id: "parkinsons", name: "Parkinson's Disease", icd10: "G20", urgency: "moderate", specialty: "Neurology",
    description: "Progressive neurodegenerative disorder affecting movement, with dopamine deficiency in basal ganglia.",
    symptoms: { tremor_resting:5, muscle_rigidity:4, slowness_of_movement:5, balance_problems:4, facial_weakness:3, memory_loss:2, sleep_problems:3, constipation:2 },
    vitals: {},
    riskFactors: { elderly:4, male:1, family_history:2 },
    recommendations: ["Clinical diagnosis (bradykinesia + rest tremor/rigidity)","Levodopa + carbidopa (gold standard)","Dopamine agonists (ropinirole, pramipexole)","MRI brain to exclude other causes","Physiotherapy, speech therapy, OT","Deep brain stimulation for advanced disease","Neurology referral"]
  },
  {
    id: "multiple_sclerosis", name: "Multiple Sclerosis", icd10: "G35", urgency: "moderate", specialty: "Neurology",
    description: "Autoimmune demyelinating disease of the central nervous system causing relapsing/remitting neurological symptoms.",
    symptoms: { fatigue:4, weakness_one_side:4, blurred_vision:4, double_vision:3, tingling_hands_feet:4, balance_problems:3, bladder_problems:3, muscle_stiffness:3, cognitive_decline:3 },
    vitals: {},
    riskFactors: { female:3, family_history:2 },
    recommendations: ["MRI brain and spinal cord (demyelinating plaques)","Lumbar puncture for oligoclonal bands","Evoked potentials","Disease-modifying therapy (interferon-beta, natalizumab, alemtuzumab)","IV methylprednisolone for acute relapse","Physiotherapy and occupational therapy","MS nurse and neurology MDT"]
  },
  // ── MUSCULOSKELETAL ──────────────────────────────────────────────────────────
  {
    id: "rheumatoid_arthritis", name: "Rheumatoid Arthritis", icd10: "M06", urgency: "moderate", specialty: "Rheumatology",
    description: "Systemic autoimmune disease causing symmetric inflammatory polyarthritis and joint destruction.",
    symptoms: { joint_pain:4, joint_swelling:4, morning_stiffness:5, joint_redness:3, fatigue:3, fever:2, loss_of_appetite:2 },
    vitals: {},
    riskFactors: { female:3, family_history:3, smoker:2, stress:2 },
    recommendations: ["Rheumatoid factor (RF) and anti-CCP antibodies","ESR, CRP, full blood count","X-rays of hands and feet","DMARDs (methotrexate first-line)","Hydroxychloroquine and sulfasalazine","Biologics (TNF inhibitors) if DMARDs fail","Physiotherapy and occupational therapy","Rheumatology referral"]
  },
  {
    id: "gout", name: "Gout", icd10: "M10", urgency: "moderate", specialty: "Rheumatology",
    description: "Crystal arthropathy from uric acid deposition in joints, causing acute inflammatory attacks.",
    symptoms: { joint_pain:5, joint_swelling:5, joint_redness:5, tophi:4, fever:2, fatigue:1 },
    vitals: {},
    riskFactors: { male:3, elderly:2, obese:2 },
    recommendations: ["Serum uric acid levels (may be normal during attack)","NSAIDs (indomethacin) for acute attack","Colchicine as alternative","Allopurinol for urate-lowering therapy","Avoid trigger foods (red meat, shellfish, alcohol, beer)","Increase fluid intake","Rheumatology referral if recurrent"]
  },
  {
    id: "lupus", name: "Systemic Lupus Erythematosus (SLE)", icd10: "M32", urgency: "moderate", specialty: "Rheumatology",
    description: "Systemic autoimmune disease affecting multiple organ systems with characteristic butterfly rash.",
    symptoms: { butterfly_rash:5, joint_pain:4, fatigue:4, fever:3, photosensitivity:4, hair_loss:3, chest_pain:2, kidney_symptoms:3 },
    vitals: { temp_min:37.5 },
    riskFactors: { female:4, family_history:3, stress:2 },
    recommendations: ["ANA, anti-dsDNA, complement levels (C3/C4)","Urinalysis for proteinuria (lupus nephritis)","Hydroxychloroquine (all patients)","Corticosteroids for flares","Immunosuppressants (azathioprine, mycophenolate)","Sun protection","Rheumatology and nephrology referral"]
  },
  {
    id: "fibromyalgia", name: "Fibromyalgia", icd10: "M79.7", urgency: "low", specialty: "Rheumatology",
    description: "Chronic widespread musculoskeletal pain with fatigue, sleep problems, and cognitive disturbance.",
    symptoms: { body_aches:4, fatigue:4, sleep_problems:4, muscle_pain:4, headache:3, joint_pain:3, difficulty_concentrating:3, anxiety:3, morning_stiffness:3 },
    vitals: {},
    riskFactors: { female:4, stress:3, family_history:2 },
    recommendations: ["Clinical diagnosis (widespread pain > 3 months, fibromyalgia criteria)","Exclude inflammatory arthritis and thyroid disease","Aerobic exercise (graded)","CBT for pain management","Low-dose amitriptyline or duloxetine","Sleep hygiene","Pain clinic referral"]
  },
  // ── RENAL ─────────────────────────────────────────────────────────────────────
  {
    id: "uti", name: "Urinary Tract Infection (UTI)", icd10: "N39.0", urgency: "low", specialty: "Urology / General Medicine",
    description: "Bacterial infection affecting any part of the urinary system.",
    symptoms: { painful_urination:4, frequent_urination:3, urgency_urination:3, cloudy_urine:3, blood_in_urine:3, lower_back_pain:2, pelvic_pain:2, fever:2, foul_smelling_urine:3 },
    vitals: { temp_min:37.5 },
    riskFactors: { female:2, elderly:1, diabetes:1, catheter:3, pregnant:2 },
    recommendations: ["Urinalysis and urine culture","Antibiotics (trimethoprim/nitrofurantoin)","Increase fluid intake","Complete full antibiotic course","If recurrent: imaging and urology referral"]
  },
  {
    id: "kidney_stones", name: "Nephrolithiasis (Kidney Stones)", icd10: "N20", urgency: "moderate", specialty: "Urology",
    description: "Hard mineral deposits forming in the kidney, causing severe colicky flank pain when passing.",
    symptoms: { flank_pain:5, blood_in_urine:4, painful_urination:3, nausea:3, vomiting:2, frequent_urination:3, lower_back_pain:4 },
    vitals: {},
    riskFactors: { male:2, obese:1, family_history:2 },
    recommendations: ["Urine dipstick and midstream culture","Non-contrast CT KUB (gold standard)","Renal ultrasound","Analgesia (diclofenac IV + opiates)","High fluid intake (2–3L/day)","Urology referral for stones > 6mm or obstruction","Stone analysis to guide prevention"]
  },
  {
    id: "chronic_kidney_disease", name: "Chronic Kidney Disease (CKD)", icd10: "N18", urgency: "moderate", specialty: "Nephrology",
    description: "Progressive loss of kidney function over months to years, often from diabetes or hypertension.",
    symptoms: { fatigue:4, leg_swelling:3, decreased_urine:3, foamy_urine:4, periorbital_edema:3, nausea:3, shortness_of_breath:2, confusion:2 },
    vitals: { sbp_min:130 },
    riskFactors: { diabetes:4, hypertension:4, elderly:3, family_history:2 },
    recommendations: ["eGFR and urine ACR (albumin:creatinine ratio)","Blood pressure control (< 130/80)","RAAS blockade (ACE inhibitor/ARB) if proteinuria","Tight glycaemic control in diabetes","Avoid nephrotoxic drugs (NSAIDs, contrast)","Dietary modification (low protein, low potassium)","Nephrology referral for CKD stage 4–5"]
  },
  // ── SKIN ─────────────────────────────────────────────────────────────────────
  {
    id: "eczema", name: "Atopic Dermatitis (Eczema)", icd10: "L20", urgency: "low", specialty: "Dermatology",
    description: "Chronic inflammatory skin condition with recurring dry, itchy, inflamed patches.",
    symptoms: { itching:5, rash:4, dry_skin:4, skin_thickening:3, redness:3, vesicles:3 },
    vitals: {},
    riskFactors: { family_history:3 },
    recommendations: ["Clinical diagnosis","Regular emollients (moisturisers)","Topical corticosteroids for flares","Avoid triggers (soaps, synthetic fabrics, stress)","Antihistamines for itch","Tacrolimus/pimecrolimus for sensitive areas","Dermatology referral if severe"]
  },
  {
    id: "psoriasis", name: "Psoriasis", icd10: "L40", urgency: "low", specialty: "Dermatology",
    description: "Chronic autoimmune skin disease causing rapid skin cell buildup, forming scales and plaques.",
    symptoms: { scaly_patches:5, itching:3, joint_pain:2, rash:4, skin_thickening:3, nail_changes:3 },
    vitals: {},
    riskFactors: { family_history:4, stress:3, smoker:2, obese:2 },
    recommendations: ["Clinical diagnosis (silvery plaques on elbows, knees, scalp)","Topical corticosteroids and vitamin D analogues","Phototherapy (UVB)","Methotrexate or ciclosporin for moderate-severe","Biologics (TNF inhibitors, IL-17 inhibitors)","Dermatology referral","Psoriatic arthritis screening"]
  },
  {
    id: "shingles", name: "Herpes Zoster (Shingles)", icd10: "B02", urgency: "moderate", specialty: "Dermatology / Infectious Disease",
    description: "Reactivation of varicella-zoster virus causing painful dermatomal rash with vesicles.",
    symptoms: { rash:5, vesicles:5, burning_pain:5, itching:4, fever:3, fatigue:3, headache:2, light_sensitivity:2 },
    vitals: { temp_min:37.5 },
    riskFactors: { elderly:4, immunocompromised:4, stress:3 },
    recommendations: ["Clinical diagnosis (unilateral dermatomal vesicular rash)","Aciclovir/valaciclovir within 72 hours of rash onset","Analgesia (NSAIDs, opiates)","Postherpetic neuralgia: amitriptyline, gabapentin","Avoid contact with immunocompromised/pregnant/neonates","Shingles vaccine (Zostavax/Shingrix) for prevention"]
  },
  // ── MENTAL HEALTH ─────────────────────────────────────────────────────────────
  {
    id: "anxiety_disorder", name: "Generalised Anxiety Disorder (GAD)", icd10: "F41.1", urgency: "low", specialty: "Psychiatry",
    description: "Persistent excessive worry and anxiety affecting daily functioning.",
    symptoms: { anxiety:4, palpitations:3, shortness_of_breath:2, sweating:2, trembling:3, fatigue:2, sleep_problems:3, difficulty_concentrating:3, headache:2, dizziness:2, muscle_tension:3, irritability:3 },
    vitals: {},
    riskFactors: { stress:3, family_history:2, female:1 },
    recommendations: ["GAD-7 and PHQ-9 screening","CBT — first-line treatment","SSRIs/SNRIs (sertraline, escitalopram)","Mindfulness and relaxation techniques","Regular exercise","Psychiatry/psychology referral","Rule out thyroid and cardiac causes"]
  },
  {
    id: "depression", name: "Major Depressive Disorder", icd10: "F32", urgency: "moderate", specialty: "Psychiatry",
    description: "Persistent low mood, anhedonia and neurovegetative symptoms lasting ≥ 2 weeks.",
    symptoms: { depressed_mood:5, anhedonia:5, fatigue:4, sleep_problems:4, weight_loss:3, difficulty_concentrating:4, irritability:3, suicidal_ideation:4, anxiety:2 },
    vitals: {},
    riskFactors: { stress:4, family_history:3, female:2 },
    recommendations: ["PHQ-9 for severity assessment","SSRIs (sertraline, fluoxetine) as first-line","CBT or IPT psychotherapy","Safety planning if suicidal ideation","Regular follow-up every 2–4 weeks","Refer to psychiatry if severe or treatment-resistant","Exclude hypothyroidism and anaemia"]
  },
  {
    id: "panic_disorder", name: "Panic Disorder", icd10: "F41.0", urgency: "low", specialty: "Psychiatry",
    description: "Recurrent unexpected panic attacks with persistent concern about future attacks.",
    symptoms: { sudden_intense_fear:5, palpitations:5, shortness_of_breath:4, chest_pain:4, sweating:4, trembling:4, dizziness:4, fear_of_dying:4, nausea:3, numbness:3 },
    vitals: {},
    riskFactors: { stress:3, family_history:2, female:2 },
    recommendations: ["Rule out cardiac causes (ECG, troponin)","CBT with exposure therapy (gold standard)","SSRIs/SNRIs for pharmacotherapy","Short-term benzodiazepines (avoid dependence)","Breathing retraining techniques","Psychiatry or psychology referral"]
  },
  // ── ANAEMIA / HAEMATOLOGY ─────────────────────────────────────────────────────
  {
    id: "anemia", name: "Iron Deficiency Anaemia", icd10: "D50", urgency: "moderate", specialty: "Haematology",
    description: "Insufficient red blood cells/haemoglobin due to iron deficiency, reducing oxygen delivery.",
    symptoms: { fatigue:4, pale_skin:4, shortness_of_breath:3, dizziness:3, cold_hands_feet:3, headache:2, palpitations:3, brittle_nails:3, hair_loss:2, chest_pain:1, cravings_non_food:2 },
    vitals: {},
    riskFactors: { female:2, pregnant:3 },
    recommendations: ["FBC, iron studies (ferritin, TIBC, serum iron)","Oral iron (ferrous sulfate 200mg TID)","Dietary iron: red meat, legumes, leafy greens","Vitamin C to enhance absorption","Identify underlying cause (GI bleed, diet)","Follow-up FBC in 4–6 weeks","IV iron if oral not tolerated"]
  },
  // ── OPHTHALMOLOGY ─────────────────────────────────────────────────────────────
  {
    id: "glaucoma", name: "Glaucoma", icd10: "H40", urgency: "moderate", specialty: "Ophthalmology",
    description: "Progressive optic neuropathy with elevated intraocular pressure causing visual field loss.",
    symptoms: { eye_pain:4, blurred_vision:4, halos_around_lights:4, headache:3, nausea:2, sudden_blindness:3 },
    vitals: {},
    riskFactors: { elderly:3, family_history:3, diabetes:2 },
    recommendations: ["Intraocular pressure measurement (tonometry)","Optic disc examination","Visual field testing","Topical beta-blockers (timolol) or prostaglandin analogues","Laser trabeculoplasty","Surgery (trabeculectomy) if refractory","Regular ophthalmology monitoring"]
  },
  // ── ENT ──────────────────────────────────────────────────────────────────────
  {
    id: "vertigo", name: "Benign Paroxysmal Positional Vertigo (BPPV)", icd10: "H81", urgency: "low", specialty: "ENT / Neurology",
    description: "Sudden brief episodes of vertigo triggered by head position changes, due to displaced otoliths.",
    symptoms: { dizziness:5, loss_of_balance:4, nausea:3, vomiting:2, ear_ringing:2, hearing_loss:1 },
    vitals: {},
    riskFactors: { elderly:3, female:2 },
    recommendations: ["Dix-Hallpike test to confirm BPPV","Epley manoeuvre (highly effective)","Vestibular rehabilitation exercises","Betahistine for vestibular disorders","ENT or neurology referral if persists","MRI if central cause suspected","Avoid sudden head movements acutely"]
  },
  {
    id: "allergic_rhinitis", name: "Allergic Rhinitis", icd10: "J30", urgency: "low", specialty: "ENT / Allergy",
    description: "IgE-mediated nasal inflammation in response to allergens such as pollen, dust, or pet dander.",
    symptoms: { sneezing:4, runny_nose:4, nasal_congestion:4, itching:4, watery_eyes:4, loss_of_smell:2, headache:2, fatigue:2, sore_throat:1 },
    vitals: {},
    riskFactors: { family_history:3 },
    recommendations: ["Clinical diagnosis and allergen history","Intranasal corticosteroids (first-line)","Oral antihistamines (cetirizine, loratadine)","Allergen avoidance","RAST/skin prick testing for specific allergens","Immunotherapy (desensitisation) for severe","Avoid allergen triggers"]
  },
  // ── EMERGENCY / OTHER ─────────────────────────────────────────────────────────
  {
    id: "heat_stroke", name: "Heat Stroke", icd10: "T67", urgency: "critical", specialty: "Emergency Medicine",
    description: "EMERGENCY: Life-threatening heat illness with core temperature > 40°C and CNS dysfunction.",
    symptoms: { hyperthermia_heat:5, confusion:5, hot_dry_skin:5, rapid_breathing:4, rapid_heartbeat:4, headache:4, nausea:3, vomiting:3, syncope:4 },
    vitals: { temp_min:40.0 },
    riskFactors: { elderly:3 },
    recommendations: ["🚨 EMERGENCY — active cooling immediately","Remove from heat, cool environment","Ice packs to neck, axilla, groin","Cold IV fluids","Continuous temperature monitoring","ABG and electrolytes","ICU monitoring","Avoid antipyretics (ineffective in heat stroke)"]
  },
  {
    id: "dehydration", name: "Dehydration", icd10: "E86", urgency: "moderate", specialty: "General Medicine",
    description: "Insufficient body fluid causing physiological dysfunction, from inadequate intake or excessive loss.",
    symptoms: { excessive_thirst:4, decreased_urine:4, dry_mucous_membranes:4, sunken_eyes:3, fatigue:3, dizziness:3, confusion:3, dry_mouth:3, decreased_skin_turgor:4 },
    vitals: {},
    riskFactors: { elderly:3 },
    recommendations: ["Oral rehydration solutions (mild-moderate)","IV fluids (0.9% NaCl or Hartmann's) for severe","Identify and treat underlying cause","Electrolyte panel (Na, K, Cl)","Urine output monitoring","Weight monitoring","Treat trigger: diarrhoea, vomiting, fever"]
  },
  {
    id: "anaphylaxis", name: "Anaphylaxis", icd10: "T78.2", urgency: "critical", specialty: "Emergency Medicine / Allergy",
    description: "EMERGENCY: Severe life-threatening systemic allergic reaction requiring immediate treatment.",
    symptoms: { hives:5, throat_swelling:5, shortness_of_breath:5, chest_pain:4, dizziness:4, vomiting:3, rash:4, rapid_heartbeat:4, anxiety:3 },
    vitals: { spo2_max:92, sbp_min:90 },
    riskFactors: { allergies:4 },
    recommendations: ["🚨 EMERGENCY — Adrenaline (epinephrine) 0.5mg IM immediately","Call emergency services","Lay flat with legs raised (unless breathing difficulty)","Repeat adrenaline after 5 minutes if no improvement","IV chlorphenamine and hydrocortisone","High-flow oxygen","Referral to allergy clinic; prescribe auto-injector (EpiPen)"]
  }
];


// ── SYMPTOM REGISTRY ─────────────────────────────────────────────────────────
const SYMPTOM_REGISTRY = {
  // General
  fever:                    { label: "Fever",                          category: "General" },
  chills:                   { label: "Chills / Rigors",                category: "General" },
  fatigue:                  { label: "Fatigue / Weakness",             category: "General" },
  weight_loss:              { label: "Unexplained Weight Loss",        category: "General" },
  weight_gain:              { label: "Unexplained Weight Gain",        category: "General" },
  night_sweats:             { label: "Night Sweats",                   category: "General" },
  sweating:                 { label: "Excessive Sweating",             category: "General" },
  loss_of_appetite:         { label: "Loss of Appetite",               category: "General" },
  mild_fever:               { label: "Low-grade Fever",                category: "General" },
  // Head & Neuro
  headache:                 { label: "Headache",                       category: "Head & Neuro" },
  severe_headache:          { label: "Severe Headache",                category: "Head & Neuro" },
  unilateral_headache:      { label: "One-sided Headache",             category: "Head & Neuro" },
  dizziness:                { label: "Dizziness / Lightheadedness",    category: "Head & Neuro" },
  confusion:                { label: "Confusion / Disorientation",     category: "Head & Neuro" },
  visual_aura:              { label: "Visual Aura / Flashes",          category: "Head & Neuro" },
  blurred_vision:           { label: "Blurred Vision",                 category: "Head & Neuro" },
  double_vision:            { label: "Double Vision",                  category: "Head & Neuro" },
  eye_pain:                 { label: "Eye Pain",                       category: "Head & Neuro" },
  light_sensitivity:        { label: "Sensitivity to Light",           category: "Head & Neuro" },
  sound_sensitivity:        { label: "Sensitivity to Sound",           category: "Head & Neuro" },
  weakness_one_side:        { label: "Weakness on One Side of Body",   category: "Head & Neuro" },
  facial_droop:             { label: "Facial Droop / Asymmetry",       category: "Head & Neuro" },
  slurred_speech:           { label: "Slurred / Difficult Speech",     category: "Head & Neuro" },
  sudden_blindness:         { label: "Sudden Vision Loss",             category: "Head & Neuro" },
  seizures:                 { label: "Seizures / Convulsions",         category: "Head & Neuro" },
  tremor_resting:           { label: "Resting Tremor",                 category: "Head & Neuro" },
  memory_loss:              { label: "Memory Loss",                    category: "Head & Neuro" },
  loss_of_balance:          { label: "Loss of Balance / Coordination", category: "Head & Neuro" },
  loss_of_consciousness:    { label: "Loss of Consciousness",          category: "Head & Neuro" },
  muscle_jerks:             { label: "Muscle Jerks / Twitches",        category: "Head & Neuro" },
  staring_spell:            { label: "Staring Spells",                 category: "Head & Neuro" },
  ear_ringing:              { label: "Ear Ringing (Tinnitus)",         category: "Head & Neuro" },
  hearing_loss:             { label: "Hearing Loss",                   category: "Head & Neuro" },
  facial_weakness:          { label: "Facial Weakness",                category: "Head & Neuro" },
  balance_problems:         { label: "Balance Problems",               category: "Head & Neuro" },
  cognitive_decline:        { label: "Cognitive Decline",              category: "Head & Neuro" },
  slowness_of_movement:     { label: "Slowness of Movement",           category: "Head & Neuro" },
  muscle_rigidity:          { label: "Muscle Rigidity",                category: "Head & Neuro" },
  neck_stiffness:           { label: "Neck Stiffness",                 category: "Head & Neuro" },
  neck_rigidity:            { label: "Neck Rigidity (Meningismus)",    category: "Head & Neuro" },
  kernig_sign:              { label: "Kernig's / Brudzinski's Sign",   category: "Head & Neuro" },
  photophobia_gi:           { label: "Photophobia",                    category: "Head & Neuro" },
  halos_around_lights:      { label: "Halos Around Lights",            category: "Head & Neuro" },
  syncope:                  { label: "Fainting / Syncope",             category: "Head & Neuro" },
  incontinence:             { label: "Bladder / Bowel Incontinence",   category: "Head & Neuro" },
  numbness:                 { label: "Numbness / Pins & Needles",      category: "Head & Neuro" },
  bladder_problems:         { label: "Bladder Problems",               category: "Head & Neuro" },
  muscle_stiffness:         { label: "Muscle Stiffness / Spasticity",  category: "Head & Neuro" },
  // Respiratory
  cough:                    { label: "Cough (Dry)",                    category: "Respiratory" },
  productive_cough:         { label: "Productive Cough (Phlegm)",      category: "Respiratory" },
  bloody_cough:             { label: "Coughing Blood (Haemoptysis)",   category: "Respiratory" },
  chronic_cough:            { label: "Chronic Cough (> 8 weeks)",      category: "Respiratory" },
  shortness_of_breath:      { label: "Shortness of Breath",            category: "Respiratory" },
  rapid_breathing:          { label: "Rapid Breathing (Tachypnoea)",   category: "Respiratory" },
  wheezing:                 { label: "Wheezing",                       category: "Respiratory" },
  chest_tightness:          { label: "Chest Tightness",                category: "Respiratory" },
  sore_throat:              { label: "Sore Throat",                    category: "Respiratory" },
  runny_nose:               { label: "Runny Nose",                     category: "Respiratory" },
  nasal_congestion:         { label: "Nasal Congestion",               category: "Respiratory" },
  sneezing:                 { label: "Sneezing",                       category: "Respiratory" },
  loss_of_smell:            { label: "Loss of Smell (Anosmia)",        category: "Respiratory" },
  loss_of_taste:            { label: "Loss of Taste (Ageusia)",        category: "Respiratory" },
  nocturnal_symptoms:       { label: "Symptoms Worse at Night",        category: "Respiratory" },
  exercise_intolerance:     { label: "Exercise Intolerance",           category: "Respiratory" },
  shortness_of_breath_at_rest: { label: "Breathlessness at Rest",      category: "Respiratory" },
  cyanosis:                 { label: "Cyanosis (Blue Lips/Fingertips)", category: "Respiratory" },
  facial_pain:              { label: "Facial Pain / Pressure",         category: "Respiratory" },
  bad_breath:               { label: "Bad Breath / Halitosis",         category: "Respiratory" },
  watery_eyes:              { label: "Watery / Itchy Eyes",            category: "Respiratory" },
  // Cardiovascular
  chest_pain:               { label: "Chest Pain / Pressure",         category: "Cardiovascular" },
  palpitations:             { label: "Heart Palpitations",             category: "Cardiovascular" },
  left_arm_pain:            { label: "Left Arm / Shoulder Pain",       category: "Cardiovascular" },
  jaw_pain:                 { label: "Jaw / Neck Pain",                category: "Cardiovascular" },
  nosebleed:                { label: "Nosebleed",                      category: "Cardiovascular" },
  leg_swelling:             { label: "Leg Swelling / Oedema",          category: "Cardiovascular" },
  orthopnea:                { label: "Breathless Lying Flat (Orthopnoea)", category: "Cardiovascular" },
  paroxysmal_nocturnal_dyspnea: { label: "Waking Breathless at Night (PND)", category: "Cardiovascular" },
  irregular_heartbeat:      { label: "Irregular Heartbeat",            category: "Cardiovascular" },
  leg_pain_walking:         { label: "Leg Pain on Walking (Claudication)", category: "Cardiovascular" },
  calf_swelling_redness:    { label: "Calf Swelling / Redness (DVT)", category: "Cardiovascular" },
  warmth_redness_leg:       { label: "Warmth / Redness of Leg",        category: "Cardiovascular" },
  rapid_heartbeat:          { label: "Rapid Heart Rate",               category: "Cardiovascular" },
  slow_heart_rate:          { label: "Slow Heart Rate (Bradycardia)",  category: "Cardiovascular" },
  // Gastrointestinal
  nausea:                   { label: "Nausea",                         category: "Gastrointestinal" },
  vomiting:                 { label: "Vomiting",                       category: "Gastrointestinal" },
  diarrhea:                 { label: "Diarrhoea",                      category: "Gastrointestinal" },
  constipation:             { label: "Constipation",                   category: "Gastrointestinal" },
  abdominal_pain:           { label: "Abdominal Pain (General)",       category: "Gastrointestinal" },
  right_lower_pain:         { label: "Right Lower Abdominal Pain",     category: "Gastrointestinal" },
  stomach_cramps:           { label: "Stomach Cramps",                 category: "Gastrointestinal" },
  rebound_tenderness:       { label: "Rebound Tenderness",             category: "Gastrointestinal" },
  abdominal_rigidity:       { label: "Abdominal Rigidity / Guarding",  category: "Gastrointestinal" },
  abdominal_pain_upper:     { label: "Upper Abdominal Pain",           category: "Gastrointestinal" },
  epigastric_pain:          { label: "Epigastric Pain (Stomach Area)", category: "Gastrointestinal" },
  bleeding_gums:            { label: "Bleeding Gums",                  category: "Gastrointestinal" },
  heartburn:                { label: "Heartburn / Acid Indigestion",   category: "Gastrointestinal" },
  acid_regurgitation:       { label: "Acid Regurgitation",             category: "Gastrointestinal" },
  black_tarry_stool:        { label: "Black / Tarry Stool (Melaena)",  category: "Gastrointestinal" },
  bloody_vomit:             { label: "Vomiting Blood (Haematemesis)",  category: "Gastrointestinal" },
  rectal_bleeding:          { label: "Rectal Bleeding / Blood in Stool", category: "Gastrointestinal" },
  bloating:                 { label: "Abdominal Bloating / Distension", category: "Gastrointestinal" },
  mucus_in_stool:           { label: "Mucus in Stool",                 category: "Gastrointestinal" },
  jaundice:                 { label: "Jaundice (Yellow Skin/Eyes)",    category: "Gastrointestinal" },
  dark_urine_gi:            { label: "Dark Urine (Tea-coloured)",      category: "Gastrointestinal" },
  clay_stool:               { label: "Pale / Clay-coloured Stool",     category: "Gastrointestinal" },
  fatty_food_pain:          { label: "Pain After Fatty Food",          category: "Gastrointestinal" },
  right_upper_pain:         { label: "Right Upper Abdominal Pain",     category: "Gastrointestinal" },
  oral_ulcers_gi:           { label: "Mouth Ulcers",                   category: "Gastrointestinal" },
  difficulty_swallowing:    { label: "Difficulty Swallowing (Dysphagia)", category: "Gastrointestinal" },
  rose_spots:               { label: "Rose Spots (Typhoid Rash)",      category: "Gastrointestinal" },
  // Urinary
  frequent_urination:       { label: "Frequent Urination",             category: "Urinary" },
  painful_urination:        { label: "Painful / Burning Urination",    category: "Urinary" },
  urgency_urination:        { label: "Urinary Urgency",                category: "Urinary" },
  cloudy_urine:             { label: "Cloudy Urine",                   category: "Urinary" },
  blood_in_urine:           { label: "Blood in Urine (Haematuria)",    category: "Urinary" },
  foul_smelling_urine:      { label: "Foul-smelling Urine",            category: "Urinary" },
  flank_pain:               { label: "Flank / Loin Pain",              category: "Urinary" },
  decreased_urine:          { label: "Decreased Urine Output (Oliguria)", category: "Urinary" },
  foamy_urine:              { label: "Foamy / Frothy Urine",           category: "Urinary" },
  periorbital_edema:        { label: "Periorbital / Eye Area Swelling", category: "Urinary" },
  kidney_symptoms:          { label: "Kidney / Flank Discomfort",      category: "Urinary" },
  // Musculoskeletal
  body_aches:               { label: "Body Aches / Myalgia",          category: "Musculoskeletal" },
  joint_pain:               { label: "Joint Pain (Arthralgia)",        category: "Musculoskeletal" },
  muscle_pain:              { label: "Muscle Pain / Myalgia",          category: "Musculoskeletal" },
  muscle_tension:           { label: "Muscle Tension",                 category: "Musculoskeletal" },
  back_pain:                { label: "Back Pain",                      category: "Musculoskeletal" },
  lower_back_pain:          { label: "Lower Back Pain",                category: "Musculoskeletal" },
  pelvic_pain:              { label: "Pelvic Pain",                    category: "Musculoskeletal" },
  joint_swelling:           { label: "Joint Swelling",                 category: "Musculoskeletal" },
  joint_redness:            { label: "Joint Redness / Warmth",         category: "Musculoskeletal" },
  tophi:                    { label: "Tophi (Urate Crystal Deposits)", category: "Musculoskeletal" },
  morning_stiffness:        { label: "Morning Stiffness (> 30 min)",   category: "Musculoskeletal" },
  muscle_weakness:          { label: "Muscle Weakness",                category: "Musculoskeletal" },
  // Skin
  rash:                     { label: "Skin Rash",                      category: "Skin" },
  pale_skin:                { label: "Pale Skin (Pallor)",             category: "Skin" },
  brittle_nails:            { label: "Brittle Nails",                  category: "Skin" },
  hair_loss:                { label: "Hair Loss (Alopecia)",           category: "Skin" },
  slow_healing:             { label: "Slow Wound Healing",             category: "Skin" },
  frequent_infections:      { label: "Frequent Skin Infections",       category: "Skin" },
  dry_skin:                 { label: "Dry / Flaky Skin",               category: "Skin" },
  itching:                  { label: "Itching (Pruritus)",             category: "Skin" },
  vesicles:                 { label: "Blisters / Vesicles",            category: "Skin" },
  scaly_patches:            { label: "Scaly Patches (Plaques)",        category: "Skin" },
  hives:                    { label: "Hives / Urticaria",              category: "Skin" },
  skin_thickening:          { label: "Skin Thickening / Lichenification", category: "Skin" },
  purulent_discharge:       { label: "Pus / Purulent Discharge",       category: "Skin" },
  butterfly_rash:           { label: "Butterfly Rash (Malar Rash)",    category: "Skin" },
  photosensitivity:         { label: "Skin Photosensitivity",          category: "Skin" },
  redness:                  { label: "Skin Redness / Erythema",        category: "Skin" },
  pain_on_touch:            { label: "Skin Tenderness / Pain on Touch", category: "Skin" },
  burning_pain:             { label: "Burning / Shooting Pain",        category: "Skin" },
  nail_changes:             { label: "Nail Changes / Pitting",         category: "Skin" },
  // Metabolic / Endocrine
  excessive_thirst:         { label: "Excessive Thirst (Polydipsia)",  category: "Metabolic" },
  dry_mouth:                { label: "Dry Mouth (Xerostomia)",         category: "Metabolic" },
  increased_hunger:         { label: "Increased Hunger (Polyphagia)",  category: "Metabolic" },
  cravings_non_food:        { label: "Craving Non-food Items (Pica)",  category: "Metabolic" },
  tingling_hands_feet:      { label: "Tingling in Hands/Feet",         category: "Metabolic" },
  cold_hands_feet:          { label: "Cold Hands / Feet",              category: "Metabolic" },
  heat_intolerance:         { label: "Heat Intolerance",               category: "Metabolic" },
  cold_intolerance:         { label: "Cold Intolerance",               category: "Metabolic" },
  goiter:                   { label: "Goitre / Neck Swelling",         category: "Metabolic" },
  puffy_face:               { label: "Puffy / Bloated Face",           category: "Metabolic" },
  shakiness_hunger:         { label: "Shakiness / Tremor with Hunger", category: "Metabolic" },
  sweating_hunger:          { label: "Sweating Associated with Hunger", category: "Metabolic" },
  // Psychological
  anxiety:                  { label: "Anxiety / Panic",                category: "Psychological" },
  sleep_problems:           { label: "Sleep Problems / Insomnia",      category: "Psychological" },
  difficulty_concentrating: { label: "Difficulty Concentrating",       category: "Psychological" },
  irritability:             { label: "Irritability / Mood Changes",    category: "Psychological" },
  trembling:                { label: "Trembling / Shakiness",          category: "Psychological" },
  depression:               { label: "Low Mood / Depressed",           category: "Psychological" },
  depressed_mood:           { label: "Persistent Depressed Mood",      category: "Psychological" },
  anhedonia:                { label: "Loss of Interest (Anhedonia)",   category: "Psychological" },
  suicidal_ideation:        { label: "Suicidal Thoughts",              category: "Psychological" },
  manic_episodes:           { label: "Manic / Elevated Mood Episodes", category: "Psychological" },
  hallucinations:           { label: "Hallucinations",                 category: "Psychological" },
  delusions:                { label: "Delusions / Paranoia",           category: "Psychological" },
  flashbacks:               { label: "Flashbacks / Nightmares (PTSD)", category: "Psychological" },
  hypervigilance:           { label: "Hypervigilance / Startle Reflex", category: "Psychological" },
  compulsions:              { label: "Compulsive Behaviours (OCD)",    category: "Psychological" },
  obsessions:               { label: "Intrusive Thoughts (OCD)",       category: "Psychological" },
  sudden_intense_fear:      { label: "Sudden Intense Fear / Panic",    category: "Psychological" },
  fear_of_dying:            { label: "Fear of Dying / Losing Control", category: "Psychological" },
  // Other / Emergency
  dry_mucous_membranes:     { label: "Dry Mouth / Mucous Membranes",   category: "Other" },
  sunken_eyes:              { label: "Sunken Eyes / Orbits",           category: "Other" },
  decreased_skin_turgor:    { label: "Decreased Skin Turgor",          category: "Other" },
  hyperthermia_heat:        { label: "Extremely High Body Temperature", category: "Other" },
  hot_dry_skin:             { label: "Hot Dry Skin (Heat Stroke)",     category: "Other" },
  throat_swelling:          { label: "Throat Swelling (Angio-oedema)", category: "Other" },
  atrial_fibrillation:      { label: "Atrial Fibrillation History",    category: "Other" },
  occupational_exposure:    { label: "Occupational Dust/Fume Exposure", category: "Other" },
  immobility:               { label: "Prolonged Immobility / Bedrest", category: "Other" },
  allergies:                { label: "Known Allergies",                category: "Other" },
};

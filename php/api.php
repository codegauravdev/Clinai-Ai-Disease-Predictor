<?php
/**
 * ClinAI — Assessment Save & History API  |  v2.0
 * Endpoints: save, history, get, stats, delete, export, weekly
 */

require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

startSecureSession();

if (!isLoggedIn()) jsonResponse(['success' => false, 'error' => 'Unauthorised'], 401);

$action = $_GET['action'] ?? '';
$user   = currentUser();
$db     = getDB();

switch ($action) {

    // ── SAVE ASSESSMENT ─────────────────────────────────────────────────────
    case 'save':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'error' => 'POST required'], 405);

        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);
        if (!$data) jsonResponse(['success' => false, 'error' => 'Invalid JSON payload'], 400);

        $p = $data['patient']     ?? [];
        $v = $data['vitals']      ?? [];
        $r = $data['riskFactors'] ?? [];
        $s = $data['symptoms']    ?? [];
        $pred  = $data['predictions'] ?? [];
        $notes = $data['notes']   ?? null;

        if (empty($p['name']) || empty($p['age'])) jsonResponse(['success' => false, 'error' => 'Patient name and age required'], 400);

        // Upsert patient
        $patStmt = $db->prepare(
            'INSERT INTO patients (mrn, full_name, age, gender, weight_kg, height_cm, ward, created_by)
             VALUES (?,?,?,?,?,?,?,?)'
        );
        $patStmt->execute([
            $p['id'] ?? null,
            $p['name'],
            (int)$p['age'],
            $p['gender'] ?? 'other',
            !empty($p['weight']) ? (float)$p['weight'] : null,
            !empty($p['height']) ? (float)$p['height'] : null,
            $p['ward'] ?? null,
            $user['id'],
        ]);
        $patientId = $db->lastInsertId();

        // Top prediction
        $top      = $pred[0] ?? null;
        $triage   = strtolower($top['urgency'] ?? 'inconclusive');
        $validT   = ['critical','high','moderate','low','inconclusive'];
        if (!in_array($triage, $validT)) $triage = 'inconclusive';

        $assStmt = $db->prepare(
            'INSERT INTO assessments
             (patient_id, assessed_by,
              rf_smoker, rf_diabetes, rf_hypertension, rf_immunocompromised,
              rf_pregnant, rf_family_history, rf_travel, rf_tb_contact,
              rf_hiv, rf_stress, rf_catheter,
              v_temperature, v_heart_rate, v_systolic_bp, v_diastolic_bp,
              v_spo2, v_resp_rate, v_blood_glucose,
              symptoms_json, predictions_json,
              triage_level, top_diagnosis, top_confidence, notes)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)'
        );
        $assStmt->execute([
            $patientId,
            $user['id'],
            (int)($r['smoker']            ?? 0),
            (int)($r['hasDiabetes']       ?? 0),
            (int)($r['hasHypertension']   ?? 0),
            (int)($r['immunocompromised'] ?? 0),
            (int)($r['pregnant']          ?? 0),
            (int)($r['familyHistory']     ?? 0),
            (int)($r['recentTravel']      ?? 0),
            (int)($r['tbContact']         ?? 0),
            (int)($r['hivPositive']       ?? 0),
            (int)($r['highStress']        ?? 0),
            (int)($r['catheter']          ?? 0),
            !empty($v['temperature'])     ? (float)$v['temperature']    : null,
            !empty($v['heartRate'])       ? (int)$v['heartRate']        : null,
            !empty($v['systolicBP'])      ? (int)$v['systolicBP']       : null,
            !empty($v['diastolicBP'])     ? (int)$v['diastolicBP']      : null,
            !empty($v['spo2'])            ? (int)$v['spo2']             : null,
            !empty($v['respiratoryRate']) ? (int)$v['respiratoryRate']  : null,
            !empty($v['bloodGlucose'])    ? (float)$v['bloodGlucose']   : null,
            json_encode($s),
            json_encode($pred),
            $triage,
            $top['disease']['name'] ?? null,
            $top['confidence'] ?? null,
            $notes ?: null,
        ]);

        jsonResponse([
            'success'       => true,
            'assessment_id' => (int)$db->lastInsertId(),
            'patient_id'    => (int)$patientId,
            'message'       => 'Assessment saved successfully.',
        ]);
        break;

    // ── HISTORY LIST ────────────────────────────────────────────────────────
    case 'history':
        $limit    = min((int)($_GET['limit'] ?? 20), 100);
        $offset   = (int)($_GET['offset'] ?? 0);
        $search   = trim($_GET['q'] ?? '');
        $tfilter  = trim($_GET['triage'] ?? '');

        $where  = $user['role'] === 'admin' ? '1=1' : 'u.id = :uid';
        $params = $user['role'] === 'admin' ? [] : [':uid' => $user['id']];

        if ($search) {
            $where .= " AND (p.full_name LIKE :q OR p.mrn LIKE :q OR a.top_diagnosis LIKE :q)";
            $params[':q'] = "%$search%";
        }
        if ($tfilter && in_array($tfilter, ['critical','high','moderate','low','inconclusive'])) {
            $where .= " AND a.triage_level = :triage";
            $params[':triage'] = $tfilter;
        }

        $sql = "SELECT a.id, p.full_name AS patient_name, p.age, p.gender, p.mrn, p.ward,
                       a.triage_level, a.top_diagnosis, a.top_confidence,
                       a.created_at, u.full_name AS assessed_by,
                       JSON_LENGTH(a.symptoms_json) AS symptom_count,
                       a.notes
                FROM assessments a
                JOIN patients p ON p.id = a.patient_id
                LEFT JOIN users u ON u.id = a.assessed_by
                WHERE $where
                ORDER BY a.created_at DESC
                LIMIT :lim OFFSET :off";

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $val) $stmt->bindValue($k, $val);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        // Total count
        $cntSql = "SELECT COUNT(*) FROM assessments a JOIN patients p ON p.id = a.patient_id LEFT JOIN users u ON u.id = a.assessed_by WHERE $where";
        $cntStmt = $db->prepare($cntSql);
        foreach ($params as $k => $val) $cntStmt->bindValue($k, $val);
        $cntStmt->execute();
        $total = (int)$cntStmt->fetchColumn();

        jsonResponse(['success' => true, 'data' => $rows, 'total' => $total, 'limit' => $limit, 'offset' => $offset]);
        break;

    // ── GET SINGLE ASSESSMENT ───────────────────────────────────────────────
    case 'get':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'error' => 'ID required'], 400);

        $stmt = $db->prepare(
            'SELECT a.*, p.full_name AS patient_name, p.age, p.gender, p.mrn, p.ward,
                    p.weight_kg, p.height_cm, u.full_name AS assessed_by_name, u.role AS assessor_role
             FROM assessments a
             JOIN patients p ON p.id = a.patient_id
             LEFT JOIN users u ON u.id = a.assessed_by
             WHERE a.id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if (!$row) jsonResponse(['success' => false, 'error' => 'Not found'], 404);

        $row['symptoms_json']    = json_decode($row['symptoms_json'], true);
        $row['predictions_json'] = json_decode($row['predictions_json'], true);
        jsonResponse(['success' => true, 'data' => $row]);
        break;

    // ── DELETE ASSESSMENT ───────────────────────────────────────────────────
    case 'delete':
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) jsonResponse(['success' => false, 'error' => 'ID required'], 400);

        // Only admin or the assessor can delete
        if ($user['role'] !== 'admin') {
            $checkStmt = $db->prepare('SELECT assessed_by FROM assessments WHERE id = ?');
            $checkStmt->execute([$id]);
            $row = $checkStmt->fetch();
            if (!$row) jsonResponse(['success' => false, 'error' => 'Not found'], 404);
            if ($row['assessed_by'] != $user['id']) jsonResponse(['success' => false, 'error' => 'Not authorised to delete this assessment'], 403);
        }

        $del = $db->prepare('DELETE FROM assessments WHERE id = ?');
        $del->execute([$id]);
        if ($del->rowCount() === 0) jsonResponse(['success' => false, 'error' => 'Assessment not found'], 404);

        jsonResponse(['success' => true, 'message' => 'Assessment deleted.']);
        break;

    // ── STATS ────────────────────────────────────────────────────────────────
    case 'stats':
        $stats = [];
        $stats['total_assessments'] = (int)$db->query('SELECT COUNT(*) FROM assessments')->fetchColumn();
        $stats['total_patients']    = (int)$db->query('SELECT COUNT(*) FROM patients')->fetchColumn();
        $stats['total_users']       = (int)$db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $stmt = $db->query("SELECT triage_level, COUNT(*) as cnt FROM assessments GROUP BY triage_level");
        $stats['by_triage'] = $stmt->fetchAll();
        $stmt = $db->query("SELECT top_diagnosis, COUNT(*) as cnt FROM assessments WHERE top_diagnosis IS NOT NULL GROUP BY top_diagnosis ORDER BY cnt DESC LIMIT 8");
        $stats['top_conditions'] = $stmt->fetchAll();
        jsonResponse(['success' => true, 'data' => $stats]);
        break;

    // ── WEEKLY STATS (for dashboard chart) ──────────────────────────────────
    case 'weekly':
        $rows = $db->query(
            "SELECT DATE(created_at) AS day, COUNT(*) AS cnt
             FROM assessments
             WHERE created_at >= DATE_SUB(NOW(), INTERVAL 14 DAY)
             GROUP BY DATE(created_at)
             ORDER BY day ASC"
        )->fetchAll();
        jsonResponse(['success' => true, 'data' => $rows]);
        break;

    // ── EXPORT CSV ──────────────────────────────────────────────────────────
    case 'export':
        // Remove JSON header and set CSV headers
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="clinai_assessments_' . date('Ymd_His') . '.csv"');

        $where  = $user['role'] === 'admin' ? '1=1' : 'u.id = ?';
        $params = $user['role'] === 'admin' ? [] : [$user['id']];

        $stmt = $db->prepare(
            "SELECT a.id, p.full_name AS patient_name, p.age, p.gender, p.mrn, p.ward,
                    a.triage_level, a.top_diagnosis, a.top_confidence,
                    JSON_LENGTH(a.symptoms_json) AS symptom_count,
                    a.v_temperature, a.v_heart_rate, a.v_systolic_bp, a.v_diastolic_bp,
                    a.v_spo2, a.v_resp_rate, a.v_blood_glucose,
                    a.notes, a.created_at, u.full_name AS assessed_by
             FROM assessments a
             JOIN patients p ON p.id = a.patient_id
             LEFT JOIN users u ON u.id = a.assessed_by
             WHERE $where
             ORDER BY a.created_at DESC
             LIMIT 2000"
        );
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        $out = fopen('php://output', 'w');
        fputcsv($out, ['ID','Patient Name','Age','Gender','MRN','Ward','Triage','Top Diagnosis','Confidence %','Symptoms','Temp(°C)','HR(bpm)','SBP(mmHg)','DBP(mmHg)','SpO2(%)','RR(/min)','Glucose(mg/dL)','Notes','Date','Assessed By']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['id'], $r['patient_name'], $r['age'], $r['gender'],
                $r['mrn'] ?? '', $r['ward'] ?? '',
                strtoupper($r['triage_level']), $r['top_diagnosis'] ?? '', $r['top_confidence'] ?? '',
                $r['symptom_count'],
                $r['v_temperature'] ?? '', $r['v_heart_rate'] ?? '', $r['v_systolic_bp'] ?? '',
                $r['v_diastolic_bp'] ?? '', $r['v_spo2'] ?? '', $r['v_resp_rate'] ?? '',
                $r['v_blood_glucose'] ?? '', $r['notes'] ?? '',
                $r['created_at'], $r['assessed_by'] ?? ''
            ]);
        }
        fclose($out);
        exit;

    // ── USER MANAGEMENT (admin only) ─────────────────────────────────────────
    case 'users':
        if ($user['role'] !== 'admin') jsonResponse(['success' => false, 'error' => 'Admin only'], 403);
        $stmt = $db->query("SELECT id, full_name, email, role, department, is_active, created_at, last_login FROM users ORDER BY created_at DESC");
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'user_toggle':
        if ($user['role'] !== 'admin') jsonResponse(['success' => false, 'error' => 'Admin only'], 403);
        $uid    = (int)($_GET['id'] ?? 0);
        $active = (int)($_GET['active'] ?? 1);
        if (!$uid) jsonResponse(['success' => false, 'error' => 'ID required'], 400);
        $db->prepare('UPDATE users SET is_active = ? WHERE id = ?')->execute([$active, $uid]);
        jsonResponse(['success' => true, 'message' => 'User status updated.']);
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Unknown action'], 400);
}

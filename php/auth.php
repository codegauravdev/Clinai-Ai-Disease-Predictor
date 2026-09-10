<?php
/**
 * ClinAI — Authentication API
 * Endpoints: login, register, logout, check
 */

require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

startSecureSession();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // ── LOGIN ───────────────────────────────────────────────────────────────
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) jsonResponse(['success' => false, 'error' => 'Email and password are required.'], 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonResponse(['success' => false, 'error' => 'Invalid email format.'], 400);

        $db   = getDB();
        $stmt = $db->prepare('SELECT id, full_name, email, password, role, department, avatar_color, is_active FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            jsonResponse(['success' => false, 'error' => 'Invalid email or password.'], 401);
        }
        if (!$user['is_active']) {
            jsonResponse(['success' => false, 'error' => 'Account is disabled. Contact admin.'], 403);
        }

        // Store in session
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['user_dept']  = $user['department'];
        $_SESSION['user_color'] = $user['avatar_color'];

        // Update last login
        $db->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

        jsonResponse([
            'success' => true,
            'redirect' => 'index.php',
            'user' => [
                'name'  => $user['full_name'],
                'role'  => $user['role'],
                'color' => $user['avatar_color'],
            ]
        ]);
        break;

    // ── REGISTER ────────────────────────────────────────────────────────────
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') jsonResponse(['success' => false, 'error' => 'Method not allowed'], 405);

        $name     = trim($_POST['full_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'doctor';
        $dept     = trim($_POST['department'] ?? '');

        if (!$name || !$email || !$password) jsonResponse(['success' => false, 'error' => 'Name, email, and password are required.'], 400);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) jsonResponse(['success' => false, 'error' => 'Invalid email format.'], 400);
        if (strlen($password) < 8) jsonResponse(['success' => false, 'error' => 'Password must be at least 8 characters.'], 400);
        if (!in_array($role, ['doctor', 'nurse', 'admin'])) $role = 'doctor';

        $db   = getDB();
        $chk  = $db->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $chk->execute([$email]);
        if ($chk->fetch()) jsonResponse(['success' => false, 'error' => 'An account with this email already exists.'], 409);

        $hash   = password_hash($password, PASSWORD_BCRYPT);
        $colors = ['#4f6ef7','#06b6d4','#8b5cf6','#10b981','#f59e0b','#ef4444'];
        $color  = $colors[array_rand($colors)];

        $ins = $db->prepare('INSERT INTO users (full_name, email, password, role, department, avatar_color) VALUES (?,?,?,?,?,?)');
        $ins->execute([$name, $email, $hash, $role, $dept, $color]);

        jsonResponse(['success' => true, 'message' => 'Account created. You can now log in.']);
        break;

    // ── LOGOUT ──────────────────────────────────────────────────────────────
    case 'logout':
        session_destroy();
        jsonResponse(['success' => true, 'redirect' => 'login.php']);
        break;

    // ── CHECK SESSION ────────────────────────────────────────────────────────
    case 'check':
        if (isLoggedIn()) {
            jsonResponse(['success' => true, 'user' => currentUser()]);
        } else {
            jsonResponse(['success' => false, 'error' => 'Not authenticated'], 401);
        }
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Unknown action'], 400);
}

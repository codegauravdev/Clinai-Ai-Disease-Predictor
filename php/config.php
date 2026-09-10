<?php
/**
 * ClinAI — Database Configuration
 * Place this project in: C:\xampp\htdocs\disease-predictor\
 * Access via: http://localhost/disease-predictor/
 */

define('DB_HOST',     'localhost');
define('DB_USER',     'root');       // default XAMPP MySQL user
define('DB_PASS',     '');           // default XAMPP MySQL password (empty)
define('DB_NAME',     'clinai_db');
define('DB_CHARSET',  'utf8mb4');

define('APP_NAME',    'ClinAI Disease Predictor');
define('APP_VERSION', '2.0');
define('SESSION_LIFETIME', 7200);   // 2 hours in seconds

// ── PDO connection (singleton) ──────────────────────────────────────────────
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            http_response_code(503);
            die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

// ── Session helpers ─────────────────────────────────────────────────────────
function startSecureSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Strict');
        session_start();
    }
}

function isLoggedIn(): bool {
    startSecureSession();
    return isset($_SESSION['user_id']) && isset($_SESSION['user_email']);
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: ../login.php');
        exit;
    }
}

function currentUser(): array {
    return [
        'id'        => $_SESSION['user_id']   ?? null,
        'name'      => $_SESSION['user_name'] ?? 'Unknown',
        'email'     => $_SESSION['user_email'] ?? '',
        'role'      => $_SESSION['user_role']  ?? 'doctor',
        'dept'      => $_SESSION['user_dept']  ?? '',
        'color'     => $_SESSION['user_color'] ?? '#4f6ef7',
    ];
}

// ── JSON response helper ─────────────────────────────────────────────────────
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

// ── CSRF token ───────────────────────────────────────────────────────────────
function csrfToken(): string {
    startSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

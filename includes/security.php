<?php

// ── Protection CSRF ──────────────────────────
function csrf_token(): string {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function csrf_verify(): void {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Action non autorisée.');
    }
    // Régénère le token après chaque vérification
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// ── Protection XSS ───────────────────────────
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// ── Nettoyage des inputs ─────────────────────
function clean_string(string $str): string {
    return trim(strip_tags($str));
}

function clean_int(mixed $val): int {
    return (int) filter_var($val, FILTER_SANITIZE_NUMBER_INT);
}

function clean_email(string $email): string {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}

// ── Validation email ─────────────────────────
function valid_email(string $email): bool {
    return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
}

// ── Validation date ──────────────────────────
function valid_date(string $date): bool {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// ── Validation heure ─────────────────────────
function valid_time(string $time): bool {
    $t = DateTime::createFromFormat('H:i', $time);
    return $t && $t->format('H:i') === $time;
}

// ── Rate limiting simple (anti-spam) ────────
function check_rate_limit(string $key, int $max = 5, int $window = 60): bool {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $now = time();
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 0, 'start' => $now];
    }
    $rl = &$_SESSION['rate_limit'][$key];
    if ($now - $rl['start'] > $window) {
        $rl = ['count' => 0, 'start' => $now];
    }
    $rl['count']++;
    return $rl['count'] <= $max;
}
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_logged_in(): bool { return isset($_SESSION['user_id']); }
function require_login(): void {
    if (!is_logged_in()) { header('Location: login.php'); exit; }
}

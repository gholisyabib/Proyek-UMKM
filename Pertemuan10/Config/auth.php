<?php
// Sesi portal admin dan pelanggan sengaja dipisah agar bisa aktif bersamaan di browser yang sama.
$madinScriptPath = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? ''));
$madinIsAdminPortal = (strpos($madinScriptPath, '/Admin/') !== false);
$madinSessionName = $madinIsAdminPortal ? 'MADIN_ADMIN_SESSID' : 'MADIN_USER_SESSID';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($madinSessionName);
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'secure' => $https,
        'samesite' => 'Lax'
    ]);
    session_start();
}
function madin_login_required(): void {
    if (empty($_SESSION['id_pengguna'])) {
        $isAdmin = strpos(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '')), '/Admin/') !== false;
        header('Location: ' . ($isAdmin ? 'login.php' : 'login.php?perlu_login=1'));
        exit;
    }
}
function madin_role_required(string $role): void {
    madin_login_required();
    if (($_SESSION['peran'] ?? '') !== $role) {
        http_response_code(403);
        $isAdmin = strpos(str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '')), '/Admin/') !== false;
        $back = $isAdmin ? 'dashboard.php' : 'halaman_menu.php';
        echo '<!doctype html><html lang="id"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Akses ditolak</title><body style="font-family:Segoe UI,sans-serif;padding:40px;color:#333"><h1>403 - Akses ditolak</h1><p>Akun Anda tidak memiliki izin membuka halaman ini.</p><p><a href="' . htmlspecialchars($back, ENT_QUOTES, 'UTF-8') . '">Kembali</a></p></body></html>';
        exit;
    }
}
function madin_e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function madin_csrf_token(): string {
    if (empty($_SESSION['madin_csrf'])) $_SESSION['madin_csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['madin_csrf'];
}
function madin_csrf_check(): void {
    $token = (string)($_POST['csrf_token'] ?? '');
    if ($token === '' || !hash_equals((string)($_SESSION['madin_csrf'] ?? ''), $token)) {
        http_response_code(419);
        exit('Permintaan kedaluwarsa atau token keamanan tidak valid. Silakan kembali dan coba lagi.');
    }
}

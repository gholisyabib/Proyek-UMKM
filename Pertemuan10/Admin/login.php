<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
if (!empty($_SESSION['id_pengguna']) && ($_SESSION['peran'] ?? '') === 'admin') { header('Location: dashboard.php'); exit; }
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim((string)($_POST['email'] ?? '')));
    $password = (string)($_POST['password'] ?? '');
    $stmt = mysqli_prepare($conn, 'SELECT id_pengguna,nama,email,kata_sandi,peran,status_akun FROM pengguna WHERE email=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $akun = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    if (!$akun || !password_verify($password, $akun['kata_sandi'])) $pesan = 'Email atau kata sandi tidak sesuai.';
    elseif (($akun['peran'] ?? '') !== 'admin') $pesan = 'Akun ini bukan akun admin. Silakan masuk melalui website pengguna.';
    elseif (($akun['status_akun'] ?? 'aktif') !== 'aktif') $pesan = 'Akun admin sedang nonaktif.';
    else {
        session_regenerate_id(true);
        $_SESSION['id_pengguna'] = (int)$akun['id_pengguna'];
        $_SESSION['nama_pembeli'] = $akun['nama'];
        $_SESSION['email_pembeli'] = $akun['email'];
        $_SESSION['peran'] = 'admin';
        header('Location: dashboard.php'); exit;
    }
}
?>
<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login Admin - MADIN</title><link rel="stylesheet" href="../Asset/login.css"><style>.admin-login-note{max-width:440px;margin:12px auto;padding:10px 14px;border-radius:8px;background:#f7eaea;color:#6a3434}</style></head><body><main class="main-content"><div class="header"><div class="logo"><img src="../Asset/Group 3.png" alt="Logo MADIN" class="logo-img"></div></div><h1 class="page-title">Masuk Admin</h1><?php if ($pesan): ?><div class="admin-login-note"><?= madin_e($pesan) ?></div><?php endif; ?><?php if (isset($_GET['logout'])): ?><div class="admin-login-note" style="background:#e8f5e9;color:#225c2a">Anda sudah keluar dari akun admin.</div><?php endif; ?><div class="form-container"><form method="post"><div class="input-group"><input type="email" name="email" placeholder="Email admin" required autocomplete="username"></div><div class="input-group"><input type="password" name="password" placeholder="Kata sandi" required autocomplete="current-password"></div><div class="terms-text"><p>Halaman ini khusus untuk akun dengan role admin.</p></div><button type="submit" class="btn-masuk">Masuk Admin</button></form><p style="margin-top:18px;text-align:center"><a href="../Pages/login.php">Kembali ke login pengguna</a></p></div></main></body></html>

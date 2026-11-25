<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
$pesan = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $pesan = 'Nama, email yang valid, dan kata sandi wajib diisi.';
    } elseif (strlen($password) < 8) {
        $pesan = 'Kata sandi minimal 8 karakter.';
    } elseif ($password !== $password_confirm) {
        $pesan = 'Kata sandi tidak cocok!';
    } elseif (empty($_POST['setuju'])) {
        $pesan = 'Anda harus menyetujui syarat dan ketentuan.';
    } else {
        $cek = mysqli_prepare($conn, 'SELECT id_pengguna FROM pengguna WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($cek, 's', $email);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        $sudahAda = mysqli_stmt_num_rows($cek) > 0;
        mysqli_stmt_close($cek);
        if ($sudahAda) {
            $pesan = 'Email sudah terdaftar. Silakan masuk.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO pengguna (nama, email, kata_sandi, peran, status_akun) VALUES (?, ?, ?, 'pelanggan', 'aktif')");
            mysqli_stmt_bind_param($stmt, 'sss', $nama, $email, $hash);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                header('Location: login.php?daftar=berhasil');
                exit;
            }
            mysqli_stmt_close($stmt);
            $pesan = 'Pendaftaran gagal. Silakan coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Daftar - MADIN</title>
    <link rel="stylesheet" href="../Asset/daftar.css">
    <!-- Menggunakan Google Fonts Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Konten Utama (Area Putih) -->
    <div class="main-content">
        
        <!-- Logo Kanan Atas -->
        <div class="header">
            <div class="logo">
                <img src="../Asset/Group 3.png" alt="Logo MADIN" class="logo-img">
            </div>
        </div>

        <!-- Judul Form -->
        <h1 class="page-title">Daftar</h1>

        <!-- Notifikasi PHP -->
        <?php if ($pesan): ?>
            <div class="alert-message"><?php echo madin_e($pesan); ?></div>
        <?php endif; ?>

        <!-- Formulir Pendaftaran -->
        <div class="form-container">
            <form action="" method="POST">
                
                <div class="input-group">
                    <input type="text" name="nama" placeholder="Nama" required>
                </div>

                <div class="input-group email-group">
                    <input type="email" name="email" placeholder="Masukkan Email" required>
                    <button type="button" class="btn-otp" title="Verifikasi OTP email belum terhubung">Kirim OTP</button>
                </div>

                <div class="input-group short-input-group">
                    <input type="text" name="kode_otp" placeholder="Kode OTP (belum tersedia)" disabled>
                </div>

                <div class="input-group">
                    <input type="password" name="password" placeholder="Masukkan Kata Sandi" required>
                </div>

                <div class="input-group">
                    <input type="password" name="password_confirm" placeholder="Masukkan Kata Sandi 1X" required>
                </div>

                <!-- Bagian Bawah: Syarat & Tombol Kirim -->
                <div class="bottom-actions">
                    <label class="terms-box">
                        <span>Menyetujui Syarat dan<br>ketentuan</span>
                        <input type="checkbox" name="setuju" required>
                    </label>
                    
                    <button type="submit" class="btn-submit">Kirim</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="footer-left">
            <!-- Menu Navigasi Horizontal (Sesuai Gambar) -->
            <div class="footer-nav">
                <a href="#">Tentang Kami</a>
                <a href="#">Blog</a>
            </div>
            
            <!-- Kontak -->
            <div class="footer-contact">
                <p class="contact-title">Kontak Kami</p>
                <div class="contact-item">
                    <!-- Ikon WhatsApp SVG -->
                    <svg class="icon icon-wa" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.04 20.15C10.55 20.15 9.09 19.75 7.8 18.98L7.5 18.8L4.38 19.62L5.21 16.57L5.02 16.26C4.18 14.93 3.73 13.44 3.73 11.91C3.73 7.33 7.46 3.6 12.04 3.6C14.25 3.6 16.32 4.46 17.88 6.02C19.44 7.58 20.3 9.65 20.3 11.86C20.3 16.44 16.57 20.15 12.04 20.15ZM16.5 14.18C16.25 14.06 15.03 13.46 14.8 13.37C14.57 13.29 14.41 13.24 14.24 13.49C14.08 13.74 13.6 14.29 13.46 14.46C13.32 14.62 13.18 14.64 12.93 14.52C12.68 14.4 11.88 14.14 10.94 13.3C10.2 12.64 9.71 11.83 9.57 11.58C9.43 11.33 9.55 11.2 9.67 11.08C9.77 10.98 9.91 10.8 10.03 10.66C10.15 10.52 10.19 10.42 10.27 10.26C10.35 10.1 10.31 9.96 10.25 9.84C10.19 9.72 9.69 8.5 9.48 8C9.27 7.51 9.06 7.57 8.9 7.56C8.75 7.55 8.58 7.55 8.41 7.55C8.24 7.55 7.96 7.61 7.73 7.86C7.5 8.11 6.87 8.7 6.87 9.9C6.87 11.1 7.75 12.26 7.87 12.42C7.99 12.58 9.6 15.06 12.06 16.12C12.65 16.37 13.11 16.52 13.47 16.63C14.06 16.82 14.6 16.79 15.03 16.73C15.51 16.66 16.5 16.14 16.71 15.56C16.92 14.98 16.92 14.49 16.86 14.39C16.8 14.29 16.63 14.23 16.5 14.18Z"/></svg>
                    <span>WhatsApp 085772137034</span>
                </div>
                <div class="contact-item">
                    <!-- Ikon Telepon SVG -->
                    <svg class="icon icon-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span>Telepon 085860709935</span>
                </div>
            </div>
        </div>
        
        <!-- Logo Footer Kanan -->
        <div class="footer-right">
            <img src="../Asset/Group 2.png" alt="Logo MADIN" class="logo-img-footer footer-logo-white">
        </div>
    </footer>

</body>
</html>
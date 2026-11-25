<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

// Daftar pesan masuk (Kotak Masuk)
$daftar_pesan = [
    [
        'id' => 1,
        'judul' => 'Selamat Datang Di web Madin',
        'waktu' => '1 Menit Lalu',
        'ringkasan' => 'Halo, Sobat Kuliner! Selamat datang di Madin, Kami hadir untuk memanjakan perut Anda...',
        'isi_lengkap' => 'Halo, Sobat Kuliner! Selamat datang di Madin, Kami hadir untuk memanjakan perut Anda tanpa membuat kantong jebol. Gunakan kolom pencarian untuk menemukan hidangan yang sedang Anda idamkan, tambahkan ke keranjang, dan pantau pesanan Anda dengan praktis. Selamat menikmati pengalaman pesan instan yang mudah dengan harga yang terjangkau!',
        'pengirim' => 'Dear_Admin'
    ],
    [
        'id' => 2,
        'judul' => 'Pesanan Berhasil Dibuat (#MDN00125)',
        'waktu' => '10 Menit Lalu',
        'ringkasan' => 'Pesanan Anda dengan nomor #MDN00125 telah berhasil dibuat dan dikonfirmasi...',
        'isi_lengkap' => 'Pesanan Anda dengan nomor #MDN00125 telah berhasil dibuat dan dikonfirmasi. Tim dapur kami sedang menyiapkan menu pesanan Anda dengan higienis dan cepat. Silakan cek menu Pesanan untuk melihat status pengiriman secara real-time.',
        'pengirim' => 'Sistem MADIN'
    ]
];

// Cek apakah ada pesan yang dipilih, jika belum default ke pesan pertama (id = 1)
$selected_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$pesan_aktif = $daftar_pesan[0];
foreach ($daftar_pesan as $p) {
    if ($p['id'] === $selected_id) {
        $pesan_aktif = $p;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan - MADIN</title>
    <link rel="stylesheet" href="../Asset/pembayaran.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .inbox-page-container {
            width: 94%;
            max-width: 1350px;
            margin: 30px auto;
            font-family: 'Poppins', sans-serif;
        }
        .inbox-wrapper {
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 25px;
            align-items: start;
        }
        .inbox-list-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .inbox-card-item {
            padding: 16px;
            border-radius: 8px;
            border: 1px solid #eee;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: #fafafa;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        .inbox-card-item:hover, .inbox-card-item.active {
            background: #e8f5e9;
            border-color: #27ae60;
            box-shadow: 0 2px 8px rgba(39, 174, 96, 0.1);
        }
        .inbox-detail-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            min-height: 450px;
        }
        @media (max-width: 768px) {
            .inbox-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header>
        <div class="header-left">
            <div class="logo-header">
                <img src="../Asset/Group 2.png" alt="Logo MADIN" class="logo-img-header">
            </div>
        </div>
        
        <div class="header-center">
            <div class="search-box">
                <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <input type="text" placeholder="Mencari pesan...">
            </div>
        </div>
    </header>

    <!-- Navigasi Atas (Dashboard dihapus & Pesanan diarahkan ke Riwayat Pesanan) -->
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item" style="color: #27ae60; font-weight: 600;">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item">Pesanan</a>
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="profil.php" class="nav-item">Profile</a>
    </nav>

    <!-- Konten Utama Halaman Pesan -->
    <main class="main-container">
        <div class="inbox-page-container">
            <h2 style="margin-top: 0; margin-bottom: 20px; font-size: 24px; color: #333;">Pesan Masuk & Informasi</h2>
            
            <div class="inbox-wrapper">
                <!-- Sisi Kiri: Daftar Kotak Masuk Pesan -->
                <div class="inbox-list-box">
                    <h4 style="margin-top: 0; color: #333; font-size: 16px; border-bottom: 1px solid #eee; padding-bottom: 12px; margin-bottom: 15px;">Daftar Pesan</h4>
                    
                    <?php foreach ($daftar_pesan as $msg): ?>
                    <a href="pesan.php?id=<?php echo $msg['id']; ?>" class="inbox-card-item <?php echo ($msg['id'] === $selected_id) ? 'active' : ''; ?>">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="font-size: 14px; color: #333;"><?php echo $msg['judul']; ?></strong>
                            <span style="font-size: 11px; color: #888;"><?php echo $msg['waktu']; ?></span>
                        </div>
                        <p style="margin: 0; font-size: 12px; color: #666; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo $msg['ringkasan']; ?></p>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Sisi Kanan: Isi Detail Pesan yang Diklik -->
                <div class="inbox-detail-box">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                        <div>
                            <h3 style="margin: 0 0 5px 0; font-size: 20px; color: #333;"><?php echo $pesan_aktif['judul']; ?></h3>
                            <span style="font-size: 12px; color: #888;">Waktu: <?php echo $pesan_aktif['waktu']; ?></span>
                        </div>
                    </div>
                    
                    <div style="font-size: 15px; color: #444; line-height: 1.8; margin-bottom: 40px;">
                        <?php echo nl2br($pesan_aktif['isi_lengkap']); ?>
                    </div>

                    <div style="font-size: 13px; color: #666; font-style: italic; border-top: 1px dashed #eee; padding-top: 15px;">
                        Pengirim: <strong><?php echo $pesan_aktif['pengirim']; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="footer-left">
            <div class="footer-nav">
                <a href="tentang_kami.php">Tentang Kami</a>
                <a href="#">Blog</a>
            </div>
            <div class="footer-contact">
                <p class="contact-title">Kontak Kami</p>
                <div class="contact-item">
                    <span>WhatsApp 085772137034</span>
                </div>
                <div class="contact-item">
                    <span>Telepon 085860709935</span>
                </div>
            </div>
        </div>
        <div class="footer-right">
            <div class="logo-footer">
                <img src="../Asset/Group 2.png" alt="Logo MADIN" class="logo-img-footer">
            </div>
        </div>
    </footer>

</body>
</html>
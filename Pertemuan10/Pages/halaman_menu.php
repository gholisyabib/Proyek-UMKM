<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
$menu_items = [];
$result = mysqli_query($conn, "SELECT id_produk,nama_produk,harga,foto,deskripsi,stok FROM produk WHERE status_produk='tersedia' AND stok>0 ORDER BY dibuat_pada DESC");
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $menu_items[] = [
            'id' => (int)$row['id_produk'],
            'nama' => $row['nama_produk'],
            'harga' => 'Rp' . number_format((float)$row['harga'], 0, ',', '.'),
            'gambar' => $row['foto'] ?: 'aaa.png',
            'deskripsi' => $row['deskripsi'] ?? ''
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - MADIN</title>
    <link rel="stylesheet" href="../Asset/halaman_menu.css">
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                <input type="text" placeholder="Mencari">
            </div>
        </div>
    </header>

    <!-- Navigasi Atas -->
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item">Pesanan</a>
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="profil.php?tab=profil" class="nav-item">Profile</a>
    </nav>

    <!-- Konten Utama -->
    <main class="main-container">
        
        <!-- Panel Kiri (Gambar Besar) -->
        <section class="left-panel">
            <div class="hero-image" style="background-image: url('../Asset/image 1.png');"></div>
        </section>

        <!-- Panel Kanan (Menu) -->
        <section class="right-panel">
            
            <!-- Judul Menu -->
            <div class="menu-header">
                <h2>Beranda Menu</h2>
                <hr class="menu-divider">
            </div>

            <!-- Filter Kategori -->
            <div class="filter-container">
                <button class="filter-btn active">Terbaru</button>
            </div>

            <!-- Grid Produk -->
            <div class="menu-grid">
                <?php if (!$menu_items): ?><p style="grid-column:1/-1;padding:24px;text-align:center;color:#777">Belum ada produk yang tersedia. Silakan kembali lagi nanti.</p><?php endif; ?>
                <?php foreach ($menu_items as $item): ?>
                <!-- Mengirimkan parameter ID atau nama ke detail_menu.php -->
                <a href="detail_menu.php?id=<?php echo $item['id']; ?>" class="menu-link">
                    <div class="menu-card">
                        <div class="menu-img-container">
                            <img src="<?php
                                $img = (string)$item['gambar'];
                                if (preg_match('/^(https?:)?\/\//i', $img)) echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                                elseif (strpos($img, 'uploads/') === 0) echo '../' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                                elseif (strpos($img, 'Asset/') === 0) echo '../' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                                elseif (strpos($img, '../') === 0) echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                                else echo '../Asset/' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                            ?>" alt="<?php echo htmlspecialchars($item['nama'], ENT_QUOTES, 'UTF-8'); ?>">
                        </div>
                        <h3 class="menu-title"><?php echo $item['nama']; ?></h3>
                        <p class="menu-price"><?php echo $item['harga']; ?></p>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

        </section>
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
                    <svg class="icon icon-wa" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.04 20.15C10.55 20.15 9.09 19.75 7.8 18.98L7.5 18.8L4.38 19.62L5.21 16.57L5.02 16.26C4.18 14.93 3.73 13.44 3.73 11.91C3.73 7.33 7.46 3.6 12.04 3.6C14.25 3.6 16.32 4.46 17.88 6.02C19.44 7.58 20.3 9.65 20.3 11.86C20.3 16.44 16.57 20.15 12.04 20.15ZM16.5 14.18C16.25 14.06 15.03 13.46 14.8 13.37C14.57 13.29 14.41 13.24 14.24 13.49C14.08 13.74 13.6 14.29 13.46 14.46C13.32 14.62 13.18 14.64 12.93 14.52C12.68 14.4 11.88 14.14 10.94 13.3C10.2 12.64 9.71 11.83 9.57 11.58C9.43 11.33 9.55 11.2 9.67 11.08C9.77 10.98 9.91 10.8 10.03 10.66C10.15 10.52 10.19 10.42 10.27 10.26C10.35 10.1 10.31 9.96 10.25 9.84C10.19 9.72 9.69 8.5 9.48 8C9.27 7.51 9.06 7.57 8.9 7.56C8.75 7.55 8.58 7.55 8.41 7.55C8.24 7.55 7.96 7.61 7.73 7.86C7.5 8.11 6.87 8.7 6.87 9.9C6.87 11.1 7.75 12.26 7.87 12.42C7.99 12.58 9.6 15.06 12.06 16.12C12.65 16.37 13.11 16.52 13.47 16.63C14.06 16.82 14.6 16.79 15.03 16.73C15.51 16.66 16.5 16.14 16.71 15.56C16.92 14.98 16.92 14.49 16.86 14.39C16.8 14.29 16.63 14.23 16.5 14.18Z"/>
                    </svg>
                    <span>WhatsApp 085772137034</span>
                </div>
                <div class="contact-item">
                    <svg class="icon icon-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                    </svg>
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
    
<!-- Floating Notification / Toast Popup -->
<div id="floatingToast" style="position: fixed; bottom: 25px; right: 25px; background: #ffffff; border-left: 4px solid #27ae60; box-shadow: 0 4px 20px rgba(0,0,0,0.15); padding: 15px 20px; border-radius: 8px; z-index: 9999; max-width: 320px; display: none; font-family: 'Poppins', sans-serif; animation: slideInUp 0.3s ease;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
        <strong style="font-size: 14px; color: #27ae60; display: flex; align-items: center; gap: 6px;">
            <span>🛵</span> Info Status Kurir
        </strong>
        <span onclick="closeToast()" style="cursor: pointer; font-size: 16px; color: #888; font-weight: bold;">&times;</span>
    </div>
    <p style="margin: 0; font-size: 13px; color: #555; line-height: 1.4;">
        Kurir <strong>Budi Santoso</strong> sedang dalam perjalanan mengantar pesanan Anda ke alamat tujuan.
    </p>
    <a href="lacak_kurir.php" style="display: inline-block; margin-top: 8px; font-size: 12px; color: #0288d1; font-weight: 600; text-decoration: none;">
        Lacak di Peta &rarr;
    </a>
</div>

<style>
@keyframes slideInUp {
    from { transform: translateY(100px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
</style>

<script>
    // Munculkan notifikasi mengambang otomatis setelah 2 detik halaman dibuka (simulasi real-time update)
    window.addEventListener('DOMContentLoaded', () => {
        setTimeout(() => {
            const toast = document.getElementById('floatingToast');
            if (toast) {
                toast.style.display = 'block';
            }
        }, 2000);
    });

    function closeToast() {
        const toast = document.getElementById('floatingToast');
        if (toast) {
            toast.style.display = 'none';
        }
    }
</script>

</body>
</html>
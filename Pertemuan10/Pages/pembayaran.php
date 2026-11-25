<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

// Ringkasan selalu dihitung dari keranjang sesi yang benar.
$daftar_belanja = $_SESSION['keranjang'] ?? [];
if (empty($daftar_belanja)) { header('Location: keranjang.php?kosong=1'); exit; }
$total_barang = 0;
foreach ($daftar_belanja as $item) {
    $jumlah = max(1, (int)($item['jumlah'] ?? $item['qty'] ?? 1));
    $total_barang += max(0, (int)($item['harga'] ?? 0)) * $jumlah;
}
$ringkasan = ['barang'=>$total_barang, 'layanan'=>1000, 'pajak'=>0, 'ongkir'=>5000];
$pengiriman = ['metode'=>'Pengantaran', 'biaya'=>$ringkasan['ongkir']];
$total = array_sum($ringkasan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - MADIN</title>
    <link rel="stylesheet" href="../Asset/pembayaran.css">
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
        <!-- Dashboard / Beranda -->
        <a href="halaman_menu.php" class="nav-item">Dashboard</a>
        <!-- BAGIAN INI DIUBAH MENJADI pesan.php -->
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="#" class="nav-item">Pesanan</a>
        <!-- BAGIAN INI DIUBAH MENJADI keranjang.php -->
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="#" class="nav-item">Profile</a>
    </nav>

    <!-- Konten Utama -->
    <main class="main-container">
        
        <!-- Kolom Kiri (Detail Pengiriman & Pembayaran) -->
        <div class="left-section">
            
            <!-- Alamat Pengiriman -->
            <h3 class="section-title">Alamat Pengiriman</h3>
            <div class="address-card">
                <div class="address-info">
                    <h4>Rakha Kurniawan</h4>
                    <p>Jl.Kampung Asep Kong Rt003/Rw010</p>
                </div>
                <a href="#" class="ubah-link">Ubah</a>
            </div>

            <!-- Produk Pesanan (Tampil Semua Menu dari Keranjang) -->
            <?php foreach ($daftar_belanja as $produk): ?>
            <div class="product-card" style="margin-bottom: 15px;">
                <div class="product-image">
                    <img src="../Asset/<?php echo $produk['gambar']; ?>" alt="<?php echo $produk['nama']; ?>">
                </div>
                <div class="product-details">
                    <h4><?php echo htmlspecialchars($produk['nama'] ?? 'Produk', ENT_QUOTES, 'UTF-8'); ?> x<?php echo max(1,(int)($produk['jumlah'] ?? $produk['qty'] ?? 1)); ?></h4>
                    <input type="text" placeholder="Tambahkan catatan untuk penjual" class="note-input">
                </div>
                <div class="product-price">
                    Rp<?php echo number_format((int)($produk['harga'] ?? 0) * max(1,(int)($produk['jumlah'] ?? $produk['qty'] ?? 1)), 0, ',', '.'); ?>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Metode Pengiriman -->
            <h3 class="section-title">Metode Pengiriman</h3>
            <div class="shipping-card">
                <div class="shipping-info">
                    <h4><?php echo $pengiriman['metode']; ?></h4>
                    <p>Rp<?php echo number_format($pengiriman['biaya'], 0, ',', '.'); ?></p>
                </div>
                <div class="shipping-icon">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
            </div>

            <!-- Metode Pembayaran -->
            <h3 class="section-title">Metode Pembayaran</h3>
            <div class="payment-card">
                <label class="payment-option">
                    <div class="payment-label">
                        <img src="../Asset/image 17.png" alt="QRIS" class="payment-logo">
                        <span>Qris</span>
                    </div>
                    <input type="radio" name="metode_pembayaran" value="qris" id="pilihan_qris" checked>
                </label>
                
                <label class="payment-option">
                    <div class="payment-label">
                        <img src="../Asset/iconoir_cash-solid.png" alt="COD" class="payment-logo">
                        <span>COD</span>
                    </div>
                    <input type="radio" name="metode_pembayaran" value="cod" id="pilihan_cod">
                </label>
            </div>

        </div>

        <!-- Kolom Kanan (Ringkasan Pesanan) -->
        <div class="right-section">
            <div class="summary-card">
                <h3>Ringkasan pesanan</h3>
                
                <div class="summary-row">
                    <span>Barang</span>
                    <span>Rp<?php echo number_format($ringkasan['barang'], 0, ',', '.'); ?></span>
                </div>
                <div class="summary-row">
                    <span>Layanan Biaya</span>
                    <span>Rp<?php echo number_format($ringkasan['layanan'], 0, ',', '.'); ?></span>
                </div>
                <div class="summary-row">
                    <span>Pajak Penjualan</span>
                    <span>Rp<?php echo $ringkasan['pajak'] > 0 ? number_format($ringkasan['pajak'], 0, ',', '.') : '-'; ?></span>
                </div>
                <div class="summary-row">
                    <span>Ongkir</span>
                    <span>Rp<?php echo number_format($ringkasan['ongkir'], 0, ',', '.'); ?></span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>Rp<?php echo number_format($total, 0, ',', '.'); ?></span>
                </div>
            </div>

            <form action="proses_pesanan.php" method="post" id="formPesanan"><input type="hidden" name="alamat_pengantaran" value=""><input type="hidden" name="metode_pembayaran" id="metode_pembayaran_form" value="qris"><button type="submit" class="btn-pesan">Pesan Sekarang</button></form>
            <script>document.querySelectorAll("input[name=metode_pembayaran]").forEach(function(el){el.addEventListener("change",function(){document.getElementById("metode_pembayaran_form").value=this.value;});});</script>
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
                    <svg class="icon icon-wa" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.04 20.15C10.55 20.15 9.09 19.75 7.8 18.98L7.5 18.8L4.38 19.62L5.21 16.57L5.02 16.26C4.18 14.93 3.73 13.44 3.73 11.91C3.73 7.33 7.46 3.6 12.04 3.6C14.25 3.6 16.32 4.46 17.88 6.02C19.44 7.58 20.3 9.65 20.3 11.86C20.3 16.44 16.57 20.15 12.04 20.15ZM16.5 14.18C16.25 14.06 15.03 13.46 14.8 13.37C14.57 13.29 14.41 13.24 14.24 13.49C14.08 13.74 13.6 14.29 13.46 14.46C13.32 14.62 13.18 14.64 12.93 14.52C12.68 14.4 11.88 14.14 10.94 13.3C10.2 12.64 9.71 11.83 9.57 11.58C9.43 11.33 9.55 11.2 9.67 11.08C9.77 10.98 9.91 10.8 10.03 10.66C10.15 10.52 10.19 10.42 10.27 10.26C10.35 10.1 10.31 9.96 10.25 9.84C10.19 9.72 9.69 8.5 9.48 8C9.27 7.51 9.06 7.57 8.9 7.56C8.75 7.55 8.58 7.55 8.41 7.55C8.24 7.55 7.96 7.61 7.73 7.86C7.5 8.11 6.87 8.7 6.87 9.9C6.87 11.1 7.75 12.26 7.87 12.42C7.99 12.58 9.6 15.06 12.06 16.12C12.65 16.37 13.11 16.52 13.47 16.63C14.06 16.82 14.6 16.79 15.03 16.73C15.51 16.66 16.5 16.14 16.71 15.56C16.92 14.98 16.92 14.49 16.86 14.39C16.8 14.29 16.63 14.23 16.5 14.18Z"/></svg>
                    <span>WhatsApp 085772137034</span>
                </div>
                <div class="contact-item">
                    <svg class="icon icon-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
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
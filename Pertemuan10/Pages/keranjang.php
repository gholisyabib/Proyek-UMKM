<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

// Proses Hapus Item dari Keranjang jika tombol hapus diklik
if (isset($_GET['hapus'])) {
    $index = $_GET['hapus'];
    if (isset($_SESSION['keranjang'][$index])) {
        unset($_SESSION['keranjang'][$index]);
        $_SESSION['keranjang'] = array_values($_SESSION['keranjang']); // Merapikan index array
    }
    header("Location: keranjang.php");
    exit();
}

$daftar_keranjang = isset($_SESSION['keranjang']) ? $_SESSION['keranjang'] : [];
$total_harga = 0;
foreach ($daftar_keranjang as $item) {
    $total_harga += (int)($item['harga'] ?? 0) * max(1, (int)($item['jumlah'] ?? $item['qty'] ?? 1));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - MADIN</title>
    <link rel="stylesheet" href="../Asset/keranjang.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-left"><div class="logo-header"><img src="../Asset/Group 2.png" alt="Logo MADIN" class="logo-img-header"></div></div>
    </header>

    <!-- Navigasi Atas (Semua menu dapat diklik dengan benar) -->
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item">Pesanan</a>
        <a href="keranjang.php" class="nav-item active">Keranjang</a>
        <a href="profil.php?tab=profil" class="nav-item">Profile</a>
    </nav>

    <main class="main-container">
        <div class="page-header">
            <h2>Keranjang</h2>
            <hr class="page-divider">
        </div>

        <?php if (!empty($daftar_keranjang)): ?>
            <?php foreach ($daftar_keranjang as $index => $produk): ?>
            <div class="cart-card" style="margin-bottom: 20px;">
                <div class="card-left">
                    <div class="product-image-wrapper">
                        <?php
                        // Gambar dapat berupa URL, path uploads/, path Asset/, atau nama file di Asset.
                        $gambar_produk = $produk['gambar'] ?? '';
                        if ($gambar_produk !== '' && !preg_match('/^(https?:)?\/\//i', $gambar_produk) && strpos($gambar_produk, 'data:') !== 0) {
                            if (strpos($gambar_produk, 'uploads/') === 0) {
                                $gambar_produk = '../' . $gambar_produk;
                            } elseif (strpos($gambar_produk, 'Asset/') === 0) {
                                $gambar_produk = '../' . $gambar_produk;
                            } elseif (strpos($gambar_produk, '../') !== 0) {
                                $gambar_produk = '../Asset/' . ltrim($gambar_produk, '/');
                            }
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($gambar_produk, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($produk['nama'] ?? 'Produk', ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <p class="product-price">Rp<?php echo number_format((int)($produk['harga'] ?? 0) * max(1,(int)($produk['jumlah'] ?? $produk['qty'] ?? 1)), 0, ',', '.'); ?></p>
                </div>
                <div class="card-middle">
                    <h3 class="product-title"><?php echo $produk['nama']; ?></h3>
                    <p class="product-description"><?php echo $produk['deskripsi']; ?></p>
                </div>
                <div class="card-right">
                    <a href="keranjang.php?hapus=<?php echo $index; ?>" class="btn-hapus" onclick="return confirm('Hapus produk ini?')">Hapus</a>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="cart-footer">
                <h3 class="total-text">Total : Rp<?php echo number_format($total_harga, 0, ',', '.'); ?></h3>
                <a href="pembayaran.php" class="btn-beli">Beli Sekarang</a>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px;">
                <p style="font-size: 18px; color: #666;">Keranjang belanja Anda masih kosong.</p>
                <a href="halaman_menu.php" class="btn-beli" style="margin-top: 15px; display: inline-block; text-decoration: none;">Pilih Menu Lain</a>

            </div>
        <?php endif; ?>
    </main>

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
        // Munculkan notifikasi mengambang otomatis setelah 2 detik halaman dibuka
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
<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
$id_produk = (int)($_GET['id'] ?? 0);
$stmt = mysqli_prepare($conn, "SELECT id_produk,nama_produk,harga,foto,deskripsi,stok,status_produk FROM produk WHERE id_produk=? AND status_produk='tersedia' AND stok>0 LIMIT 1");
mysqli_stmt_bind_param($stmt, 'i', $id_produk);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if (!$row) { http_response_code(404); echo '<!doctype html><html lang="id"><meta charset="utf-8"><title>Produk tidak ditemukan</title><body style="font-family:Arial;padding:40px"><h1>Produk tidak ditemukan</h1><p>Produk mungkin sudah tidak tersedia.</p><a href="halaman_menu.php">Kembali ke menu</a></body></html>'; exit; }
$gambar = (string)($row['foto'] ?: 'aaa.png');
$daftar_komposisi = [
    'Burger Beef' => ['Roti Bun', 'Daging Beef', 'Keju', 'Saus'],
    'Es Campur' => ['Buah Segar', 'Cincau', 'Sirup', 'Susu', 'Es Serut'],
    'Es Jeruk Peras' => ['Jeruk Asli', 'Gula Murni', 'Es Batu'],
    'Es Teh' => ['Teh Melati', 'Gula Pasir', 'Es Batu'],
    'Mie Ayam Bakso Biasa' => ['Bakso', 'Mie', 'Ayam', 'Sawi', 'Kuah'],
    'Mie Ayam Bakso' => ['Bakso Spesial', 'Mie', 'Ayam', 'Sawi', 'Pangsit'],
];
$nama_produk = trim((string)$row['nama_produk']);
$produk = [
    'id' => (int)$row['id_produk'],
    'nama' => $nama_produk,
    'harga' => (float)$row['harga'],
    'gambar' => $gambar,
    'deskripsi' => $row['deskripsi'] ?? '',
    'komposisi' => $daftar_komposisi[$nama_produk] ?? [],
    'stok' => (int)$row['stok'],
];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang'] = [];
    $_SESSION['keranjang'][] = ['id'=>$produk['id'],'nama'=>$produk['nama'],'harga'=>$produk['harga'],'gambar'=>$produk['gambar'],'deskripsi'=>$produk['deskripsi'],'jumlah'=>1];
    header('Location: keranjang.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Menu - MADIN</title>
    <link rel="stylesheet" href="../Asset/detail_menu.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <!-- Header & Navigasi -->
    <header>
        <div class="header-left"><div class="logo-header"><img src="../Asset/Group 2.png" alt="Logo MADIN" class="logo-img-header"></div></div>
    </header>
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item">Pesanan</a>
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="profil.php?tab=profil" class="nav-item">Profile</a>
    </nav>

    <main class="main-container">
        <div class="product-detail-container">
            <div class="product-image-card">
                <h2 class="product-title-left"><?php echo htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <div class="product-image-wrapper">
                    <img src="<?php
                    $img = (string)$produk['gambar'];
                    if (preg_match('/^(https?:)?\/\//i', $img)) echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                    elseif (strpos($img, 'uploads/') === 0) echo '../' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                    elseif (strpos($img, 'Asset/') === 0) echo '../' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                    elseif (strpos($img, '../') === 0) echo htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                    else echo '../Asset/' . htmlspecialchars($img, ENT_QUOTES, 'UTF-8');
                ?>" alt="<?php echo htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                <p class="product-price-left">Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?></p>
            </div>

            <div class="product-info-right">
                <h2 class="product-title-right"><?php echo htmlspecialchars($produk['nama'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p class="product-description"><?php echo nl2br(htmlspecialchars($produk['deskripsi'], ENT_QUOTES, 'UTF-8')); ?></p>
                
                <h3 class="composition-title">Komposisi:</h3>
                <div class="composition-tags">
                    <?php if (!empty($produk['komposisi'])): ?>
                        <?php foreach ($produk['komposisi'] as $item): ?>
                            <span class="tag-item"><?php echo htmlspecialchars($item, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="tag-item">Informasi komposisi belum tersedia</span>
                    <?php endif; ?>
                </div>

                <!-- Form untuk mengirim data ke keranjang menggunakan method POST -->
                <form method="POST" class="action-buttons">
                    <button type="submit" name="tambah_keranjang" class="btn-cart">
                        Tambahkan Ke Keranjang
                    </button>
                    <a href="pembayaran.php?id=<?php echo $produk['id']; ?>" class="btn-buy" style="text-decoration: none; text-align: center; display: inline-block;">Beli Sekarang</a>
                </form>
            </div>
        </div>
    </main>
</body>
</html>
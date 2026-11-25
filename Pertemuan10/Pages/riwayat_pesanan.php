<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');
$nama_pembeli = $_SESSION['nama_pembeli'] ?? $_SESSION['nama'] ?? 'Pelanggan';

// Ambil hanya pesanan milik akun yang sedang login.
$riwayat_pesanan = [];
$stmt = mysqli_prepare($conn, "SELECT id_pesanan,kode_pesanan,dibuat_pada,metode_pembayaran,status_pesanan,status_pembayaran,total FROM pesanan WHERE id_pengguna=? ORDER BY dibuat_pada DESC");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_pengguna']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($row = mysqli_fetch_assoc($res)) {
    $d = mysqli_prepare($conn, 'SELECT nama_produk,jumlah FROM detail_pesanan WHERE id_pesanan=? ORDER BY id_detail');
    mysqli_stmt_bind_param($d, 'i', $row['id_pesanan']); mysqli_stmt_execute($d); $dr = mysqli_stmt_get_result($d); $parts=[];
    while ($it=mysqli_fetch_assoc($dr)) $parts[]=(int)$it['jumlah'].'x '. $it['nama_produk'];
    mysqli_stmt_close($d);
    $row['no_pesanan']=$row['kode_pesanan'];
    $row['tanggal']=date('d M Y, H:i',strtotime($row['dibuat_pada']));
    $row['item']=implode(', ',$parts);
    $row['total_harga']='Rp '.number_format((float)$row['total'],0,',','.');
    $row['metode']=$row['metode_pembayaran']==='tunai' ? 'COD' : strtoupper($row['metode_pembayaran']);
    $row['status']=$row['status_pesanan'];
    $row['keterangan_status'] = ($row['status_pembayaran']==='berhasil' ? 'Pembayaran telah dikonfirmasi.' : ($row['metode_pembayaran']==='qris' ? 'Menunggu konfirmasi pembayaran QRIS.' : 'Pesanan tercatat; pembayaran dilakukan saat diterima.'));
    $riwayat_pesanan[]=$row;
}
mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - MADIN</title>
    <link rel="stylesheet" href="../Asset/pembayaran.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .riwayat-container {
            width: 90%;
            max-width: 900px;
            margin: 30px auto;
            font-family: 'Poppins', sans-serif;
        }
        .pesanan-card {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .pesanan-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }
        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
        /* Warna Badge Status */
        .badge-diproses { background: #fff3e0; color: #f57c00; }
        .badge-dikirim { background: #e1f5fe; color: #0288d1; }
        .badge-selesai { background: #e8f5e9; color: #27ae60; }

        .pesanan-body p {
            margin: 6px 0;
            font-size: 14px;
            color: #555;
        }
        .btn-detail {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 16px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
        }
        .btn-detail:hover {
            background: #219653;
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
                <input type="text" placeholder="Cari pesanan...">
            </div>
        </div>
    </header>

    <!-- Navigasi Atas -->
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item" style="color: #27ae60; font-weight: 600;">Pesanan</a>
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="profil.php" class="nav-item">Profile</a>
    </nav>

    <!-- Konten Utama Riwayat Pesanan -->
    <main class="main-container">
        <div class="riwayat-container">
            <h2 style="margin-top: 0; color: #333; font-size: 24px;">Riwayat Pesanan Saya</h2>
            <p style="color: #666; font-size: 14px; margin-bottom: 25px;">Halo, <strong><?php echo htmlspecialchars($nama_pembeli); ?></strong>. Berikut adalah daftar makanan/minuman yang telah Anda pesan.</p>

            <?php if (empty($riwayat_pesanan)): ?>
                <div style="text-align: center; padding: 40px; background: #fff; border-radius: 12px; border: 1px solid #e0e0e0;">
                    <p style="color: #777;">Belum ada riwayat pesanan yang dibuat.</p>
                    <a href="halaman_menu.php" class="btn-detail">Mulai Pesan Sekarang</a>
                </div>
            <?php else: ?>
                <?php foreach ($riwayat_pesanan as $pesanan): ?>
                    <div class="pesanan-card">
                        <div class="pesanan-header">
                            <div>
                                <strong style="font-size: 16px; color: #333;">No. Pesanan: #<?php echo $pesanan['no_pesanan']; ?></strong>
                                <div style="font-size: 12px; color: #888; margin-top: 2px;"><?php echo $pesanan['tanggal']; ?></div>
                            </div>
                            <div>
                                <?php 
                                    $status = $pesanan['status'];
                                    $badge_class = 'badge-diproses';
                                    $teks_status = 'Sedang Diproses';
                                    
                                    if ($status == 'dikirim') {
                                        $badge_class = 'badge-dikirim';
                                        $teks_status = 'Sedang Dikirim';
                                    } elseif ($status == 'selesai') {
                                        $badge_class = 'badge-selesai';
                                        $teks_status = 'Selesai';
                                    }
                                ?>
                                <span class="badge-status <?php echo $badge_class; ?>"><?php echo $teks_status; ?></span>
                            </div>
                        </div>

                        <div class="pesanan-body">
                            <p><strong>Menu Makanan/Minuman:</strong> <?php echo htmlspecialchars($pesanan['item']); ?></p>
                            <p><strong>Total Pembayaran:</strong> <?php echo $pesanan['total_harga']; ?> (<?php echo $pesanan['metode']; ?>)</p>
                            <p style="color: #e67e22; font-size: 13px; font-weight: 500; margin-top: 8px;">
                                ℹ️ Status: <?php echo htmlspecialchars($pesanan['keterangan_status']); ?>
                            </p>
                        </div>

                        <a href="status_pesanan.php?order=<?php echo (int)$pesanan['id_pesanan']; ?>" class="btn-detail">Lihat Detail Status</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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
                <div class="contact-item"><span>WhatsApp 085772137034</span></div>
                <div class="contact-item"><span>Telepon 085860709935</span></div>
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
<?php
session_start();

$alamat = $_SESSION['alamat'] ?? null;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alamat - MADIN</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="halaman">

    <!-- HEADER -->
    <header class="header">

        <div class="logo">
            MADIN<span>♥</span>
        </div>

        <div class="search-box">
            <span>⌕</span>
            <input type="text" placeholder="Mencari">
        </div>

        <div class="header-kanan">
            <div class="tagline">
                Nikmatin Harga Terjangkau
            </div>
        </div>

        <nav class="header-menu">
            <a href="pesan.php">Pesan</a>
            <a href="pesanan.php">Pesanan</a>
            <a href="keranjang.php">Keranjang</a>
            <a href="profil.php">Profile</a>
        </nav>

    </header>


    <!-- AREA -->
    <main class="area-utama">

        <!-- SIDEBAR -->
        <aside class="sidebar">

            <div class="sidebar-profil">

                <div class="avatar-sidebar">
                    ♙
                </div>

                <h4>User</h4>
                <p>user@gmail.com</p>

            </div>

            <div class="sidebar-menu">

                <a href="profil.php" class="aktif">
                    👤 Profil Saya
                </a>

                <a href="#">
                    ⟳ Riwayat Pesanan
                </a>

                <a href="#">
                    ♧ Notifikasi
                </a>

                <a href="#">
                    ♧ Tentang Kami
                </a>

                <a href="#">
                    ▣ Kontak Kami
                </a>

                <a href="#">
                    ▣ FAQ
                </a>

                <div class="garis"></div>

                <a href="#">
                    ⇥ Keluar
                </a>

            </div>

        </aside>


        <!-- KONTEN -->
        <section class="konten">

            <div class="judul-konten">

                <span class="icon-lokasi">⌾</span>

                <div>
                    <h2>Alamat Saya</h2>

                    <p class="deskripsi">
                        Kelola alamat pengiriman Anda.
                    </p>
                </div>

            </div>


            <a href="tambah-alamat.php" class="btn-tambah">
                + Tambah Alamat
            </a>


            <?php if (!$alamat): ?>

                <!-- BELUM ADA ALAMAT -->

                <div style="
                    height: 250px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:16px;
                    font-weight:600;
                ">
                    Tambahkan alamat Anda terlebih dahulu
                </div>

            <?php else: ?>

                <!-- SUDAH ADA ALAMAT -->

                <div class="kartu-alamat">

                    <div class="icon-rumah">
                        ⌂
                    </div>

                    <div class="data-alamat">

                        <h3>
                            <?= htmlspecialchars($alamat['nama']) ?>
                        </h3>

                        <p>
                            <?= htmlspecialchars($alamat['telepon']) ?>
                        </p>

                        <p class="alamat-text">
                            <?= nl2br(htmlspecialchars($alamat['alamat'])) ?>
                        </p>

                        <?php if ($alamat['tetap']): ?>

                            <p style="
                                color:#503232;
                                font-size:12px;
                                font-weight:600;
                            ">
                                ✓ Alamat tetap
                            </p>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endif; ?>

        </section>

    </main>


    <!-- FOOTER -->
    <footer class="footer">

        <div class="footer-link">
            <a href="#">Tentang Kami</a>
            <a href="#">Blog</a>
            <a href="#">Kontak Kami</a>
        </div>

        <div class="footer-kontak">
            <p>◉ WhatsApp 085772137034</p>
            <p>♧ Telepon 085860709935</p>
        </div>

        <div class="footer-logo">
            MADIN♥
        </div>

    </footer>

</div>

</body>
</html>
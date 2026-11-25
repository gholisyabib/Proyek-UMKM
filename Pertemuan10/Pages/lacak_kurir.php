
<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';

madin_role_required('pelanggan');

// Ambil ID pesanan yang sedang dilacak
$order_id = (int)($_GET['order'] ?? ($_SESSION['pesanan_terakhir'] ?? 0));
$id_user = (int)($_SESSION['id_pengguna'] ?? 0);

// Data kurir masih berupa simulasi
$kurir = [
    'nama' => 'Budi Santoso',
    'no_hp' => '081234567890',
    'kendaraan' => 'Honda Beat - B 4567 XYZ',
    'estimasi' => 'Tiba di tujuan',
    'status_perjalanan' => 'Pesanan telah sampai di alamat tujuan.'
];

// Ambil nama dan alamat dari pesanan milik pengguna login
$stmt = mysqli_prepare(
    $conn,
    "SELECT nama_pelanggan, alamat_pengantaran
     FROM pesanan
     WHERE id_pesanan = ? AND id_pengguna = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, 'ii', $order_id, $id_user);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data_pesanan = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$data_pesanan) {
    header('Location: riwayat_pesanan.php');
    exit;
}

$penerima = [
    'nama' => $data_pesanan['nama_pelanggan'] ?: ($_SESSION['nama_pembeli'] ?? 'Pelanggan'),
    'alamat' => $data_pesanan['alamat_pengantaran'] ?: 'Alamat belum diisi'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Kurir - MADIN</title>
    <link rel="stylesheet" href="../Asset/pembayaran.css">
    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .tracking-container {
            max-width: 800px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            font-family: 'Poppins', sans-serif;
        }
        .tracking-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .courier-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .courier-avatar {
            width: 50px;
            height: 50px;
            background: #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #555;
        }
        .map-box {
            width: 100%;
            height: 300px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .map-box iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        .destination-card {
            background: #e1f5fe;
            padding: 15px;
            border-radius: 8px;
            color: #0288d1;
            margin-bottom: 20px;
        }
        /* Bagian Penilaian & Konfirmasi Pesanan Selesai */
        .arrived-section {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .star-rating {
            display: flex;
            justify-content: center;
            gap: 8px;
            font-size: 28px;
            cursor: pointer;
            color: #ccc;
            margin: 10px 0;
        }
        .star-rating span:hover,
        .star-rating span.selected {
            color: #f1c40f;
        }
        .review-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-bottom: 12px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }
        .btn-finish {
            display: block;
            width: 100%;
            padding: 12px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
        }
        .btn-finish:hover {
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
                <input type="text" placeholder="Mencari">
            </div>
        </div>
    </header>

    <!-- Navigasi Atas (Dashboard dihapus, menu lain dihubungkan) -->
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item">Pesanan</a>
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="profil.php?tab=profil" class="nav-item">Profile</a>
    </nav>

    <!-- Konten Utama Pelacakan Kurir -->
    <main class="main-container">
        <div class="tracking-container">
            <div class="tracking-header">
                <h2 style="margin: 0; font-size: 22px; color: #333;">Lacak Posisi Kurir</h2>
                <span style="background: #e8f5e9; color: #2e7d32; padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 14px;">Tiba di Tujuan</span>
            </div>

            <!-- Informasi Kurir -->
            <div class="courier-info">
                <div class="courier-avatar">BS</div>
                <div style="flex-grow: 1;">
                    <h4 style="margin: 0; font-size: 16px; color: #333;"><?php echo $kurir['nama']; ?></h4>
                    <p style="margin: 3px 0 0; color: #666; font-size: 13px;"><?php echo $kurir['kendaraan']; ?> | <a href="tel:<?php echo $kurir['no_hp']; ?>" style="color: #0288d1; text-decoration: none; font-weight: 600;">Hubungi Kurir</a></p>
                </div>
                <div style="text-align: right;">
                    <span style="font-size: 12px; color: #888; display: block;">Status</span>
                    <strong style="color: #27ae60; font-size: 15px;">Sampai Tujuan</strong>
                </div>
            </div>

            <!-- Google Maps Embed ke Alamat Pembeli -->
            <div class="map-box">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.2575456456!2d106.9800!3d-6.4000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjQnMDAuMCJTIDEwNsKwNTgnNDguMCJF!5e0!3m2!1sid!2sid!4v1600000000000!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
            </div>

            <!-- Alamat Tujuan Pengiriman -->
            <div class="destination-card">
                <h4 style="margin: 0 0 5px 0; font-size: 15px;">Alamat Tujuan Pengiriman:</h4>
                <p style="margin: 0; font-size: 14px; font-weight: 500;"><?php echo $penerima['nama']; ?> (<?php echo $penerima['alamat']; ?>)</p>
            </div>

            <!-- Bagian Konfirmasi Pesanan Tiba & Ulasan -->
            <div class="arrived-section" id="section-ulasan">
                <h3 style="margin-top: 0; color: #2e7d32; font-size: 18px;">Pesanan Sudah Tiba di Tangan Anda?</h3>
                <p style="font-size: 13px; color: #555; margin-bottom: 15px;">Silakan konfirmasi penerimaan pesanan dan berikan ulasan Anda:</p>
                
                <!-- Rating Bintang -->
                <div class="star-rating" id="starContainer">
                    <span data-value="1">&#9733;</span>
                    <span data-value="2">&#9733;</span>
                    <span data-value="3">&#9733;</span>
                    <span data-value="4">&#9733;</span>
                    <span data-value="5">&#9733;</span>
                </div>

                <textarea class="review-input" id="catatanUlasan" rows="3" placeholder="Tulis ulasan untuk makanan atau kurir (opsional)..."></textarea>
                
                <button class="btn-finish" onclick="selesaikanPesanan()">Konfirmasi Pesanan Selesai</button>
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
                <img src="../Asset/Group 2.png" alt="Logo MADIN" class="logo-img-header">
            </div>
        </div>
    </footer>

    <!-- JavaScript Interaktif untuk Rating & Konfirmasi -->
    <script>
        const stars = document.querySelectorAll('#starContainer span');
        let selectedRating = 5; // Default 5 bintang

        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                selectedRating = index + 1;
                stars.forEach((s, i) => {
                    if (i < selectedRating) {
                        s.classList.add('selected');
                    } else {
                        s.classList.remove('selected');
                    }
                });
            });
        });

        // Set default 5 bintang aktif saat pertama kali buka
        for(let i=0; i<5; i++) {
            stars[i].classList.add('selected');
        }

        function selesaikanPesanan() {
            alert('Terima kasih! Pesanan telah dikonfirmasi selesai dan ulasan Anda telah dikirim.');
            window.location.href = 'riwayat_pesanan.php'; // Mengarah ke riwayat pesanan
        }
    </script>

</body>
</html>
<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

// Proses Hapus Alamat jika parameter ?hapus_alamat=1 diklik
if (isset($_GET['hapus_alamat']) && $_GET['hapus_alamat'] == '1') {
    unset($_SESSION['alamat_tersimpan']);
    header("Location: profil.php?tab=alamat&status=hapus");
    exit();
}

// Simulasi data user awal jika session belum ada
$user = [
    'nama' => isset($_SESSION['nama_pembeli']) ? $_SESSION['nama_pembeli'] : 'Sobat Kuliner',
    'email' => isset($_SESSION['email_pembeli']) ? $_SESSION['email_pembeli'] : 'sobatkuliner@example.com',
    'telepon' => isset($_SESSION['telepon_pembeli']) ? $_SESSION['telepon_pembeli'] : '085772137034',
    'kelamin' => isset($_SESSION['kelamin_pembeli']) ? $_SESSION['kelamin_pembeli'] : 'Laki-laki',
    'foto' => isset($_SESSION['foto_profil']) ? $_SESSION['foto_profil'] : null,
    'alamat_tersimpan' => isset($_SESSION['alamat_tersimpan']) ? $_SESSION['alamat_tersimpan'] : null
];

// Mengambil pengaturan notifikasi dari session yang tersimpan
$notif = isset($_SESSION['notif_settings']) ? $_SESSION['notif_settings'] : [
    'aktifkan_semua' => true,
    'notif_pesanan' => true,
    'notif_promo' => true,
    'notif_stok' => true,
    'notif_akun' => true,
    'notif_info' => true,
];

// Menangkap tab aktif dari URL, default ke 'profil'
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'profil';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - MADIN</title>
    <link rel="stylesheet" href="../Asset/pembayaran.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        .profile-page-container {
            width: 94%;
            max-width: 1350px;
            margin: 15px auto;
            font-family: 'Poppins', sans-serif;
        }
        .profile-wrapper {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 25px;
            align-items: stretch;
        }
        /* Sidebar Menu Profil */
        .profile-sidebar {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }
        .profile-sidebar-user {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
            margin-bottom: 15px;
        }
        .profile-avatar-container {
            position: relative;
            cursor: pointer;
            margin-bottom: 10px;
        }
        .profile-avatar {
            width: 65px;
            height: 65px;
            background: #e0e0e0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 18px;
            color: #555;
            object-fit: cover;
            overflow: hidden;
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .ubah-profil-link {
            font-size: 11px;
            color: #27ae60;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }
        .ubah-profil-link:hover {
            text-decoration: underline;
        }
        .sidebar-menu-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu-item {
            margin-bottom: 8px;
        }
        .sidebar-menu-link {
            display: block;
            padding: 10px 14px;
            border-radius: 8px;
            color: #444;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sidebar-menu-link:hover, .sidebar-menu-link.active {
            background: #e8f5e9;
            color: #27ae60;
            font-weight: 600;
        }
        .sidebar-menu-link.logout {
            color: #e74c3c;
        }
        .sidebar-menu-link.logout:hover {
            background: #fde8e8;
            color: #e74c3c;
            font-weight: 600;
        }
        .sidebar-menu-link.logout.active {
            background: #fde8e8;
            color: #e74c3c;
            font-weight: 600;
        }

        /* Kotak Konten Utama */
        .profile-content-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            box-sizing: border-box;
        }
        .content-header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 12px;
            margin-bottom: 25px;
        }
        .content-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .btn-tambah {
            background: #4a2e2b;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
        }
        .btn-tambah:hover {
            background: #35201e;
        }
        .btn-hapus-alamat {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .btn-hapus-alamat:hover {
            background: #c0392b;
        }
        /* Peta Patokan */
        .map-preview-box {
            width: 100%;
            height: 180px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #ddd;
            margin-top: 15px;
        }
        .map-preview-box iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        /* Form styling */
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #555;
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
        }
        .btn-save {
            background: #4a2e2b;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .btn-save:hover {
            background: #35201e;
        }
        /* Toggle Switch Styling */
        .switch {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 24px;
            flex-shrink: 0;
        }
        .switch input { 
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .3s;
            border-radius: 24px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .3s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #4a2e2b;
        }
        input:checked + .slider:before {
            transform: translateX(24px);
        }

        /* FAQ Interactive Accordion Styling */
        .faq-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .faq-header-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 20px;
            cursor: pointer;
            background: #fff;
            transition: background 0.2s;
        }
        .faq-header-row:hover {
            background: #fafafa;
        }
        .faq-title-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .faq-number-badge {
            width: 28px;
            height: 28px;
            background: #4a2e2b;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }
        .faq-question-text {
            font-size: 14.5px;
            font-weight: 600;
            color: #333;
        }
        .faq-arrow-icon {
            font-size: 16px;
            color: #555;
            transition: transform 0.3s ease;
        }
        .faq-body-content {
            display: none;
            padding: 0 20px 20px 63px;
            font-size: 13.5px;
            color: #555;
            line-height: 1.6;
        }
        .faq-card.active .faq-body-content {
            display: block;
        }
        .faq-card.active .faq-arrow-icon {
            transform: rotate(180deg);
        }

        @media (max-width: 768px) {
            .profile-wrapper {
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
                <input type="text" placeholder="Pencarian...">
            </div>
        </div>
    </header>

    <!-- Navigasi Atas -->
    <nav class="top-nav-bar">
        <a href="pesan.php" class="nav-item">Pesan</a>
        <a href="riwayat_pesanan.php" class="nav-item">Pesanan</a>
        <a href="keranjang.php" class="nav-item">Keranjang</a>
        <a href="profil.php?tab=profil" class="nav-item" style="color: #27ae60; font-weight: 600;">Profile</a>
    </nav>

    <!-- Konten Utama Profil -->
    <main class="main-container">
        <div class="profile-page-container">
            <div class="profile-wrapper">
                
                <!-- Sidebar Sisi Kiri -->
                <div class="profile-sidebar">
                    <div class="profile-sidebar-user">
                        <!-- Form Upload Foto Profil -->
                        <form action="upload_foto.php" method="POST" enctype="multipart/form-data" id="formFotoProfil">
                            <label for="inputFoto" class="profile-avatar-container" title="Klik untuk ubah foto profil">
                                <div class="profile-avatar">
                                    <?php if (!empty($user['foto'])): ?>
                                        <img src="<?php echo $user['foto']; ?>" alt="Foto Profil">
                                    <?php else: ?>
                                        SK
                                    <?php endif; ?>
                                </div>
                            </label>
                            <input type="file" name="foto_profil" id="inputFoto" accept="image/*" style="display: none;" onchange="document.getElementById('formFotoProfil').submit();">
                        </form>

                        <div style="font-weight: 600; font-size: 14px; color: #333; margin-bottom: 2px;"><?php echo $user['nama']; ?></div>
                        
                        <!-- Tombol Ubah Foto Profil -->
                        <label for="inputFoto" class="ubah-profil-link">Ubah Profil</label>
                    </div>

                    <ul class="sidebar-menu-list">
                        <li class="sidebar-menu-item">
                            <a href="profil.php?tab=profil" class="sidebar-menu-link <?php echo ($tab == 'profil') ? 'active' : ''; ?>">Profil</a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="profil.php?tab=alamat" class="sidebar-menu-link <?php echo ($tab == 'alamat') ? 'active' : ''; ?>">Alamat</a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="profil.php?tab=notifikasi" class="sidebar-menu-link <?php echo ($tab == 'notifikasi') ? 'active' : ''; ?>">Notifikasi</a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="profil.php?tab=kontak" class="sidebar-menu-link <?php echo ($tab == 'kontak') ? 'active' : ''; ?>">Kontak Kami</a>
                        </li>
                        <li class="sidebar-menu-item">
                            <a href="profil.php?tab=faq" class="sidebar-menu-link <?php echo ($tab == 'faq') ? 'active' : ''; ?>">FAQ</a>
                        </li>
                        <li class="sidebar-menu-item" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 10px;">
                            <a href="profil.php?tab=keluar" class="sidebar-menu-link logout <?php echo ($tab == 'keluar') ? 'active' : ''; ?>">Keluar</a>
                        </li>
                    </ul>
                </div>

                <!-- Konten Sisi Kanan (Berubah Sesuai Tab yang Diklik) -->
                <div class="profile-content-box">
                    
                    <?php if ($tab == 'profil'): ?>
                        <!-- 1. HALAMAN PROFIL -->
                        <div class="content-header-flex">
                            <h3 class="content-title">Biodata Diri</h3>
                        </div>

                        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                            <div style="background: #e8f5e9; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c8e6c9;">
                                ✅ Perubahan biodata berhasil disimpan!
                            </div>
                        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'foto_sukses'): ?>
                            <div style="background: #e8f5e9; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c8e6c9;">
                                ✅ Foto profil berhasil diubah dan disimpan!
                            </div>
                        <?php endif; ?>

                        <form action="update_profil.php" method="POST">
                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?php echo $user['nama']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="text" name="telepon" class="form-control" value="<?php echo $user['telepon']; ?>">
                            </div>
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="kelamin" class="form-control">
                                    <option value="Laki-laki" <?php echo ($user['kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                    <option value="Perempuan" <?php echo ($user['kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-save">Simpan Perubahan</button>
                        </form>

                    <?php elseif ($tab == 'alamat'): ?>
                        <!-- 2. HALAMAN ALAMAT -->
                        <div class="content-header-flex">
                            <h3 class="content-title">Alamat Saya</h3>
                            <?php if (empty($user['alamat_tersimpan'])): ?>
                                <a href="profil.php?tab=tambah_alamat" class="btn-tambah">+ Tambah Alamat</a>
                            <?php endif; ?>
                        </div>

                        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                            <div style="background: #e8f5e9; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c8e6c9;">
                                ✅ Alamat utama berhasil disimpan!
                            </div>
                        <?php elseif (isset($_GET['status']) && $_GET['status'] == 'hapus'): ?>
                            <div style="background: #ffebee; color: #c62828; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #ffcdd2;">
                                🗑️ Alamat berhasil dihapus!
                            </div>
                        <?php endif; ?>

                        <?php if (empty($user['alamat_tersimpan'])): ?>
                            <div style="text-align: center; padding: 100px 20px; color: #777; font-size: 14px;">
                                Tambahkan alamat Anda terlebih dahulu
                            </div>
                        <?php else: ?>
                            <div style="background: #fafafa; border: 1px solid #ddd; border-radius: 8px; padding: 15px;">
                                <strong style="color: #333; font-size: 15px;"><?php echo $user['alamat_tersimpan']['nama_penerima']; ?> (Utama)</strong>
                                <p style="margin: 5px 0; font-size: 13px; color: #555;">📞 <?php echo $user['alamat_tersimpan']['no_hp']; ?></p>
                                <p style="margin: 5px 0; font-size: 13px; color: #555;">📍 <?php echo $user['alamat_tersimpan']['detail_alamat']; ?></p>
                                
                                <div class="map-preview-box">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.2575456456!2d106.9800!3d-6.4000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjQnMDAuMCJTIDEwNsKwNTgnNDguMCJF!5e0!3m2!1sid!2sid!4v1600000000000!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
                                </div>

                                <div>
                                    <a href="profil.php?tab=alamat&hapus_alamat=1" class="btn-hapus-alamat" onclick="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">Hapus Alamat</a>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($tab == 'tambah_alamat'): ?>
                        <!-- 2.1 FORM TAMBAH ALAMAT -->
                        <div class="content-header-flex">
                            <h3 class="content-title">Tambah Alamat</h3>
                            <a href="profil.php?tab=alamat" class="btn-tambah">Kembali</a>
                        </div>
                        <form action="simpan_alamat.php" method="POST">
                            <div class="form-group">
                                <label>Nama Penerima</label>
                                <input type="text" name="nama_penerima" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>No. HP</label>
                                <input type="text" name="no_hp" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Alamat Lengkap</label>
                                <textarea name="detail_alamat" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="form-group">
                                <label>Titik Patokan Lokasi (Peta)</label>
                                <div class="map-preview-box" style="margin-top: 0;">
                                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.2575456456!2d106.9800!3d-6.4000!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNsKwMjQnMDAuMCJTIDEwNsKwNTgnNDguMCJF!5e0!3m2!1sid!2sid!4v1600000000000!5m2!1sid!2sid" allowfullscreen="" loading="lazy"></iframe>
                                </div>
                                <span style="font-size: 11px; color: #888; display: block; margin-top: 4px;">Peta otomatis disesuaikan dengan wilayah Klapanunggal, Bogor.</span>
                            </div>
                            <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                                <input type="checkbox" name="utama" id="utama" value="1" checked style="width: 18px; height: 18px; accent-color: #4a2e2b;">
                                <label for="utama" style="margin: 0; cursor: pointer;">Jadikan alamat utama</label>
                            </div>
                            <button type="submit" class="btn-save">Simpan</button>
                        </form>

                    <?php elseif ($tab == 'notifikasi'): ?>
                        <!-- 3. HALAMAN NOTIFIKASI -->
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin: 0 0 5px 0; font-size: 20px; color: #333; display: flex; align-items: center; gap: 8px;">
                                <span>🔔</span> Notifikasi
                            </h3>
                            <p style="margin: 0; font-size: 13px; color: #666;">Atur jenis notifikasi yang ingin Anda terima.</p>
                        </div>

                        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
                            <div style="background: #e8f5e9; color: #27ae60; padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; border: 1px solid #c8e6c9;">
                                ✅ Pengaturan notifikasi berhasil diperbarui!
                            </div>
                        <?php endif; ?>

                        <form action="simpan_notifikasi.php" method="POST">
                            <div style="background: #fafafa; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                                <div style="display: flex; align-items: center; gap: 15px;">
                                    <div style="font-size: 24px;">🔔</div>
                                    <div>
                                        <strong style="font-size: 15px; color: #333; display: block;">Aktifkan Notifikasi</strong>
                                        <span style="font-size: 13px; color: #666;">Menerima semua notifikasi dari MADIN.</span>
                                    </div>
                                </div>
                                <label class="switch">
                                    <input type="checkbox" name="aktifkan_semua" <?php echo $notif['aktifkan_semua'] ? 'checked' : ''; ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px; margin-bottom: 25px;">
                                <h4 style="margin: 0 0 15px 0; font-size: 15px; color: #333; font-weight: 600;">Jenis Notifikasi</h4>
                                
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee;">
                                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                                        <div style="font-size: 20px; margin-top: 2px;">🛒</div>
                                        <div>
                                            <strong style="font-size: 14px; color: #333; display: block;">Pesanan</strong>
                                            <span style="font-size: 12px; color: #666;">Notifikasi terkait status pesanan Anda (diproses, dikirim, selesai).</span>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notif_pesanan" <?php echo $notif['notif_pesanan'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee;">
                                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                                        <div style="font-size: 20px; margin-top: 2px;">🏷️</div>
                                        <div>
                                            <strong style="font-size: 14px; color: #333; display: block;">Promo & Diskon</strong>
                                            <span style="font-size: 12px; color: #666;">Informasi promo, voucher, dan penawaran menarik terbaru.</span>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notif_promo" <?php echo $notif['notif_promo'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee;">
                                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                                        <div style="font-size: 20px; margin-top: 2px;">📦</div>
                                        <div>
                                            <strong style="font-size: 14px; color: #333; display: block;">Stok & Produk</strong>
                                            <span style="font-size: 12px; color: #666;">Pemberitahuan ketersediaan stok dan produk favorit Anda.</span>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notif_stok" <?php echo $notif['notif_stok'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #eee;">
                                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                                        <div style="font-size: 20px; margin-top: 2px;">📄</div>
                                        <div>
                                            <strong style="font-size: 14px; color: #333; display: block;">Aktivitas Akun</strong>
                                            <span style="font-size: 12px; color: #666;">Notifikasi keamanan, perubahan akun, dan aktivitas penting lainnya.</span>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notif_akun" <?php echo $notif['notif_akun'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                                    <div style="display: flex; align-items: flex-start; gap: 15px;">
                                        <div style="font-size: 20px; margin-top: 2px;">📢</div>
                                        <div>
                                            <strong style="font-size: 14px; color: #333; display: block;">Info & Pengumuman</strong>
                                            <span style="font-size: 12px; color: #666;">Informasi terbaru seputar layanan, fitur, dan pengumuman dari MADIN.</span>
                                        </div>
                                    </div>
                                    <label class="switch">
                                        <input type="checkbox" name="notif_info" <?php echo $notif['notif_info'] ? 'checked' : ''; ?>>
                                        <span class="slider"></span>
                                    </label>
                                </div>
                            </div>

                            <div style="display: flex; justify-content: flex-end; gap: 12px;">
                                <a href="profil.php?tab=notifikasi" style="background: #fff; color: #333; border: 1px solid #ccc; padding: 10px 20px; border-radius: 8px; font-weight: 600; text-decoration: none; font-size: 14px; font-family: 'Poppins', sans-serif;">Batal</a>
                                <button type="submit" class="btn-save">Simpan Perubahan</button>
                            </div>
                        </form>

                    <?php elseif ($tab == 'kontak'): ?>
                        <!-- 4. HALAMAN KONTAK KAMI -->
                        <div class="content-header-flex" style="margin-bottom: 20px;">
                            <div>
                                <h3 class="content-title" style="display: flex; align-items: center; gap: 8px;">
                                    <span>📞</span> Kontak Kami
                                </h3>
                                <span style="font-size: 13px; color: #666;">Kedai Madin</span>
                            </div>
                        </div>

                        <div style="background: #fafafa; border: 1px solid #e0e0e0; border-radius: 10px; padding: 25px; color: #333;">
                            <h4 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 600; color: #222;">Yuk, Terhubung dengan Kedai Madin!</h4>
                            <p style="font-size: 13.5px; color: #555; line-height: 1.6; margin-bottom: 20px;">
                                Ada pertanyaan tentang menu, ingin melakukan pemesanan, memberikan saran, atau ingin mengetahui informasi lebih lanjut tentang Kedai Madin? Kami siap membantu! Hubungi kami melalui kontak yang tersedia di bawah ini.
                            </p>

                            <div style="margin-bottom: 20px;">
                                <div style="font-weight: 600; font-size: 14px; color: #222; display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                    <span>📱</span> WhatsApp
                                </div>
                                <div style="font-size: 14px; font-weight: 600; color: #27ae60; margin-bottom: 3px;">0857 7137 1034</div>
                                <p style="font-size: 12.5px; color: #666; margin: 0; line-height: 1.5;">
                                    Untuk pemesanan makanan, menanyakan ketersediaan menu, atau informasi lainnya, kamu dapat menghubungi kami melalui WhatsApp.
                                </p>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <div style="font-weight: 600; font-size: 14px; color: #222; display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                    <span>📞</span> Telepon
                                </div>
                                <div style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 3px;">0858 6070 9935</div>
                                <p style="font-size: 12.5px; color: #666; margin: 0; line-height: 1.5;">
                                    Butuh informasi dengan lebih cepat? Hubungi kami melalui telepon dan tim kami akan membantu menjawab pertanyaanmu.
                                </p>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <div style="font-weight: 600; font-size: 14px; color: #222; display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                    <span>📍</span> Alamat Kedai
                                </div>
                                <div style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 3px;">Kedai Madin</div>
                                <p style="font-size: 12.5px; color: #666; margin: 0 0 5px 0; line-height: 1.5;">
                                    Datang langsung ke kedai dan nikmati berbagai pilihan makanan dan minuman favoritmu, mulai dari mie ayam, burger, es campur, es teh, hingga es jeruk.
                                </p>
                                <span style="font-size: 13px; font-style: italic; color: #444; display: block;">“ Grand Kahuripan</span>
                            </div>

                            <div style="margin-bottom: 20px;">
                                <div style="font-weight: 600; font-size: 14px; color: #222; display: flex; align-items: center; gap: 8px; margin-bottom: 3px;">
                                    <span>⏰</span> Jam Operasional
                                </div>
                                <div style="font-size: 14px; font-weight: 600; color: #333; margin-bottom: 3px;">Senin – Minggu</div>
                                <div style="font-size: 13.5px; font-weight: 600; color: #27ae60; margin-bottom: 3px;">10.00 – 21.00 WIB</div>
                                <p style="font-size: 12.5px; color: #666; margin: 0; line-height: 1.5;">
                                    Kami siap melayani kamu selama jam operasional.
                                </p>
                            </div>

                            <div style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 15px;">
                                <div style="font-weight: 600; font-size: 14px; color: #222; display: flex; align-items: center; gap: 8px; margin-bottom: 5px;">
                                    <span>💬</span> Ada yang Ingin Ditanyakan?
                                </div>
                                <p style="font-size: 12.5px; color: #666; line-height: 1.6; margin: 0 0 10px 0;">
                                    Kami terbuka untuk menerima pertanyaan, kritik, dan saran dari pelanggan. Masukan dari kamu membantu Kedai Madin untuk terus meningkatkan kualitas makanan dan pelayanan. Jangan ragu untuk menghubungi kami. Kami tunggu kedatanganmu di Kedai Madin! 🍜🍷
                                </p>
                                <strong style="font-size: 13px; color: #333; display: block;">Kedai Madin — Lezat, Segar, dan Terjangkau.</strong>
                            </div>
                        </div>

                    <?php elseif ($tab == 'faq'): ?>
                        <!-- 5. HALAMAN FAQ -->
                        <div style="margin-bottom: 20px;">
                            <h3 style="margin: 0 0 5px 0; font-size: 20px; color: #333; display: flex; align-items: center; gap: 8px;">
                                <span>ℹ️</span> FAQ
                            </h3>
                            <p style="margin: 0; font-size: 13px; color: #666;">Kenali lebih dekat dengan Kedai Madin</p>
                        </div>

                        <div class="faq-card" onclick="toggleFaq(this)">
                            <div class="faq-header-row">
                                <div class="faq-title-left">
                                    <div class="faq-number-badge">1</div>
                                    <div class="faq-question-text">Bagaimana cara memesan makanan?</div>
                                </div>
                                <div class="faq-arrow-icon">⌄</div>
                            </div>
                            <div class="faq-body-content">
                                <ol style="margin: 0; padding-left: 18px;">
                                    <li>Pilih makanan yang diinginkan.</li>
                                    <li>Tentukan jumlah yang ingin dibeli.</li>
                                    <li>Lalu masukkan ke keranjang.</li>
                                    <li>Setelah itu, periksa pesanan Anda dan lakukan pemesanan.</li>
                                    <li>Masukkan alamat secara manual dan bisa secara otomatis yang terdapat pada menu profil.</li>
                                </ol>
                            </div>
                        </div>

                        <div class="faq-card" onclick="toggleFaq(this)">
                            <div class="faq-header-row">
                                <div class="faq-title-left">
                                    <div class="faq-number-badge">2</div>
                                    <div class="faq-question-text">Bagaimana cara melihat pesanan saya?</div>
                                </div>
                                <div class="faq-arrow-icon">⌄</div>
                            </div>
                            <div class="faq-body-content">
                                <div style="display: flex; align-items: flex-start; gap: 10px; background: #fff8f8; border: 1px solid #ffd6d6; padding: 10px; border-radius: 6px; margin-bottom: 8px;">
                                    <span style="color: #e74c3c;">⚠️</span>
                                    <span style="color: #c0392b; font-weight: 500;">Tidak, pesanan yang sudah dibuat tidak dapat dibatalkan.</span>
                                </div>
                                Pastikan makanan, jumlah pesanan, dan detail pesanan Anda sudah benar sebelum melakukan pemesanan.
                            </div>
                        </div>

                        <div class="faq-card" onclick="toggleFaq(this)">
                            <div class="faq-header-row">
                                <div class="faq-title-left">
                                    <div class="faq-number-badge">3</div>
                                    <div class="faq-question-text">Apakah pesanan bisa dibatalkan?</div>
                                </div>
                                <div class="faq-arrow-icon">⌄</div>
                            </div>
                            <div class="faq-body-content">
                                Anda dapat melihat daftar pesanan dan statusnya melalui menu <strong>Riwayat Pesanan</strong> bisa juga pada menu <strong>Pesanan</strong> pada profil Anda.
                            </div>
                        </div>

                        <div class="faq-card" onclick="toggleFaq(this)">
                            <div class="faq-header-row">
                                <div class="faq-title-left">
                                    <div class="faq-number-badge">4</div>
                                    <div class="faq-question-text">Bagaimana jika pesanan saya belum datang?</div>
                                </div>
                                <div class="faq-arrow-icon">⌄</div>
                            </div>
                            <div class="faq-body-content">
                                Periksa status pesanan melalui menu <strong>Riwayat Pesanan</strong> dan menu <strong>Pesanan</strong>. Jika pesanan sudah melewati waktu yang diperkirakan namun belum diterima, silakan hubungi Kontak Kami.
                            </div>
                        </div>

                        <div class="faq-card" onclick="toggleFaq(this)">
                            <div class="faq-header-row">
                                <div class="faq-title-left">
                                    <div class="faq-number-badge">5</div>
                                    <div class="faq-question-text">Bagaimana jika pesanan yang diterima tidak sesuai?</div>
                                </div>
                                <div class="faq-arrow-icon">⌄</div>
                            </div>
                            <div class="faq-body-content">
                                Segera hubungi <strong>Kontak Kami</strong> dan berikan informasi nomor pesanan serta detail masalah agar dapat segera kami bantu.
                            </div>
                        </div>

                        <div class="faq-card" onclick="toggleFaq(this)">
                            <div class="faq-header-row">
                                <div class="faq-title-left">
                                    <div class="faq-number-badge">6</div>
                                    <div class="faq-question-text">Bagaimana cara mengubah data akun?</div>
                                </div>
                                <div class="faq-arrow-icon">⌄</div>
                            </div>
                            <div class="faq-body-content">
                                Masuk ke menu <strong>Profil Saya</strong>, lalu pilih <strong>Pengaturan Akun</strong> untuk mengubah data pribadi seperti nama, email, nomor HP, atau password.
                            </div>
                        </div>

                        <script>
                            function toggleFaq(card) {
                                card.classList.toggle('active');
                            }
                        </script>

                    <?php elseif ($tab == 'keluar'): ?>
                        <!-- 6. HALAMAN KONFIRMASI KELUAR AKUN -->
                        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 50px 20px; text-align: center;">
                            <!-- Ikon Keluar -->
                            <div style="margin-bottom: 25px;">
                                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                    <polyline points="10 17 15 12 10 7"></polyline>
                                    <line x1="15" y1="12" x2="3" y2="12"></line>
                                </svg>
                            </div>

                            <h3 style="font-size: 20px; font-weight: 600; color: #222; margin: 0 0 8px 0;">Keluar dari akun?</h3>
                            <p style="font-size: 14px; color: #666; margin: 0 0 30px 0;">Apakah anda yakin ingin keluar dari akun kedai madin?</p>

                            <!-- Tombol Batal & Ya -->
                            <div style="display: flex; gap: 15px; justify-content: center;">
                                <a href="profil.php?tab=profil" style="display: inline-block; padding: 10px 30px; border: 1px solid #ccc; border-radius: 8px; color: #333; text-decoration: none; font-size: 14px; font-weight: 500; background: #fff;">Batal</a>
                                <a href="logout.php" style="display: inline-block; padding: 10px 35px; border: none; border-radius: 8px; color: #fff; text-decoration: none; font-size: 14px; font-weight: 500; background: #4a2e2b;">Ya</a>
                            </div>
                        </div>

                    <?php endif; ?>

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
                    <svg class="icon icon-wa" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.04 20.15C10.55 20.15 9.09 19.75 7.8 18.98L7.5 18.8L4.38 19.62L5.21 16.57L5.02 16.26C4.18 14.93 3.73 13.44 3.73 11.91C3.73 7.33 7.46 3.6 12.04 3.6C14.25 3.6 16.32 4.46 17.88 6.02C19.44 7.58 20.3 9.65 20.3 11.86C20.3 16.44 16.57 20.15 12.04 20.15ZM16.5 14.18C16.25 14.06 15.03 13.46 14.8 13.37C14.57 13.29 14.41 13.24 14.24 13.49C14.08 13.74 13.6 14.29 13.46 14.46C13.32 14.62 13.18 14.64 12.93 14.52C12.68 14.4 11.88 14.14 10.94 13.3C10.2 12.64 9.71 11.83 9.57 11.58C9.43 11.33 9.55 11.2 9.67 11.08C9.77 10.98 9.91 10.8 10.03 10.66C10.15 10.52 10.19 10.42 10.27 10.26C10.35 10.1 10.31 9.96 10.25 9.84C10.19 9.72 9.69 8.5 9.48 8C9.27 7.51 9.06 7.57 8.9 7.56C8.75 7.55 8.58 7.55 8.41 7.55C8.24 7.55 7.96 7.61 7.73 7.86C7.5 8.11 6.87 8.7 6.87 9.9C6.87 11.1 7.75 12.26 7.87 12.42C7.99 12.58 9.6 15.06 12.06 16.12C12.65 16.37 13.11 16.52 13.47 16.63C14.06 16.82 14.6 16.79 15.03 16.73C15.51 16.66 16.5 16.14 16.71 15.56C16.92 14.98 16.92 14.49 16.86 14.39C16.8 14.29 16.63 14.23 16.5 14.18Z"/></svg>
                    <span>WhatsApp 085772137034</span>
                </div>
                <div class="contact-item">
                    <svg class="icon icon-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" xmlns="http://www.w3.org/2000/svg"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0_1 22 16.92z"/></svg>
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

</body>
</html>
<?php
function madin_admin_header(string $title, string $active): void {
    $links = [
        'dashboard' => ['Dashboard', 'dashboard.php', 'fa-chart-line'],
        'produk' => ['Manajemen Produk', 'produk.php', 'fa-boxes-stacked'],
        'stok' => ['Manajemen Stok', 'stok.php', 'fa-box'],
        'pesanan' => ['Pesanan', 'pesanan.php', 'fa-calendar-check'],
        'pengguna' => ['Pengguna', 'pengguna.php', 'fa-users'],
        'laporan' => ['Laporan Penjualan', 'laporan.php', 'fa-file-invoice'],
        'pengaturan' => ['Pengaturan', 'pengaturan.php', 'fa-gear'],
    ];
    ?>
    <!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= madin_e($title) ?> - MADIN Admin</title><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"><link rel="stylesheet" href="../Asset/dashboard_admin.css"><link rel="stylesheet" href="../Asset/admin_extra.css"></head><body>
    <header class="header"><div class="logo"><img src="../Asset/Group 2.png" alt="Logo MADIN"></div><div class="search-bar"><i class="fa-solid fa-shield-halved"></i><span>Panel Administrasi MADIN</span></div><div class="dashboard-label"><?= madin_e($title) ?></div></header>
    <div class="container"><aside class="sidebar"><div class="profile-section"><div class="avatar"><i class="fa-solid fa-user-tie"></i></div><div class="profile-info"><h3><?= madin_e($_SESSION['nama_pembeli'] ?? 'Admin') ?></h3><p>Administrator</p></div></div><ul class="menu">
    <?php foreach ($links as $key => $item): ?><li><a href="<?= madin_e($item[1]) ?>" class="<?= $key === $active ? 'active' : '' ?>"><i class="fa-solid <?= madin_e($item[2]) ?> menu-icon"></i><?= madin_e($item[0]) ?></a></li><?php endforeach; ?>
    </ul><div class="logout"><a href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket menu-icon"></i> Keluar</a><p class="portal-switch"><a href="../Pages/halaman_menu.php">Lihat website pengguna ↗</a></p></div></aside><main class="main-content"><div class="admin-page-heading"><div><h1><?= madin_e($title) ?></h1><p>Kelola data MADIN dari satu tempat.</p></div><span class="admin-date"><?= date('d/m/Y') ?></span></div>
    <?php if (!empty($_GET['msg'])): ?><div class="notice success"><?= madin_e($_GET['msg']) ?></div><?php endif; ?>
    <?php if (!empty($_GET['err'])): ?><div class="notice error"><?= madin_e($_GET['err']) ?></div><?php endif; ?>
    <?php
}
function madin_admin_footer(): void { echo '</main></div></body></html>'; }
function madin_money($value): string { return 'Rp' . number_format((float)$value, 0, ',', '.'); }
function madin_status_label(string $status): string { return ucwords(str_replace('_', ' ', $status)); }
?>

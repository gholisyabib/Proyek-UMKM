<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Jika checkbox dicentang bernilai true, jika tidak dicentang bernilai false
    $_SESSION['notif_settings'] = [
        'aktifkan_semua' => isset($_POST['aktifkan_semua']) ? true : false,
        'notif_pesanan' => isset($_POST['notif_pesanan']) ? true : false,
        'notif_promo' => isset($_POST['notif_promo']) ? true : false,
        'notif_stok' => isset($_POST['notif_stok']) ? true : false,
        'notif_akun' => isset($_POST['notif_akun']) ? true : false,
        'notif_info' => isset($_POST['notif_info']) ? true : false,
    ];

    // Redirect kembali ke halaman profil tab notifikasi dengan status sukses
    header("Location: profil.php?tab=notifikasi&status=sukses");
    exit();
} else {
    header("Location: profil.php?tab=notifikasi");
    exit();
}
?>
<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangkap data dari form tambah alamat
    $nama_penerima = isset($_POST['nama_penerima']) ? $_POST['nama_penerima'] : '';
    $no_hp = isset($_POST['no_hp']) ? $_POST['no_hp'] : '';
    $detail_alamat = isset($_POST['detail_alamat']) ? $_POST['detail_alamat'] : '';

    // Simpan ke dalam session alamat_tersimpan
    $_SESSION['alamat_tersimpan'] = [
        'nama_penerima' => $nama_penerima,
        'no_hp' => $no_hp,
        'detail_alamat' => $detail_alamat
    ];

    // Redirect kembali ke halaman profil tab alamat dengan status sukses
    header("Location: profil.php?tab=alamat&status=sukses");
    exit();
} else {
    header("Location: profil.php?tab=alamat");
    exit();
}
?>
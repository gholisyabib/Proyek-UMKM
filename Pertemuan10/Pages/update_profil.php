<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

// Memastikan form dikirimkan menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Menangkap data dari form biodata diri
    $nama_baru    = trim($_POST['nama']);
    $email_baru   = trim($_POST['email']);
    $telepon_baru = trim($_POST['telepon']);
    $kelamin_baru = trim($_POST['kelamin']);

    // Validasi sederhana (pastikan nama tidak kosong)
    if (!empty($nama_baru)) {
        // Simpan pembaruan ke dalam session
        $_SESSION['nama_pembeli'] = $nama_baru;
        $_SESSION['email_pembeli'] = $email_baru;
        $_SESSION['telepon_pembeli'] = $telepon_baru;
        $_SESSION['kelamin_pembeli'] = $kelamin_baru;

        /* 
          CATATAN PENTING UNTUK DATABASE MYSQL:
          Jika Anda sudah menghubungkan proyek ini ke database, Anda bisa menjalankan query UPDATE di sini:
          
          include "koneksi.php";
          $id_user = $_SESSION['id_user'];
          mysqli_query($conn, "UPDATE users SET nama='$nama_baru', email='$email_baru', telepon='$telepon_baru', jenis_kelamin='$kelamin_baru' WHERE id='$id_user'");
        */

        // Berhasil, arahkan kembali ke halaman profil dengan parameter sukses
        header("Location: profil.php?tab=profil&status=sukses");
        exit();
    } else {
        // Jika nama kosong, kembali dengan pesan error
        header("Location: profil.php?tab=profil&status=error");
        exit();
    }
} else {
    // Jika diakses langsung tanpa POST, kembalikan ke profil
    header("Location: profil.php?tab=profil");
    exit();
}
?>
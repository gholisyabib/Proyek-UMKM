<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_profil'])) {
    $file = $_FILES['foto_profil'];

    // Periksa apakah ada error saat upload
    if ($file['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        // Validasi ekstensi file gambar
        if (in_array($filetype, $allowed)) {
            $uploadDir = __DIR__ . '/../uploads/';

            // BUAT FOLDER SECARA OTOMATIS JIKA BELUM ADA
            if (!is_dir($uploadDir)) {
                // Membuat folder dengan izin akses penuh (0777)
                mkdir($uploadDir, 0777, true);
            }

            // Buat nama file unik agar tidak bentrok
            $newFileName = 'avatar_' . time() . '.' . $filetype;
            $destination = $uploadDir . $newFileName;

            // Pindahkan file yang diunggah ke folder uploads/
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Simpan path foto ke dalam session
                $_SESSION['foto_profil'] = '../uploads/' . $newFileName;
                header("Location: profil.php?tab=profil&status=foto_sukses");
                exit();
            } else {
                // Gagal memindahkan file (masalah izin folder/server)
                header("Location: profil.php?tab=profil&status=gagal_pindah");
                exit();
            }
        } else {
            // Format file tidak diizinkan
            header("Location: profil.php?tab=profil&status=format_salah");
            exit();
        }
    } else {
        // Error saat upload dari sisi file
        header("Location: profil.php?tab=profil&status=error_upload");
        exit();
    }
} else {
    header("Location: profil.php?tab=profil");
    exit();
}
?>
<?php
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['foto_profil'])) {
    $file = $_FILES['foto_profil'];

    // Validasi jika ada file yang diunggah tanpa error
    if ($file['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);

        if (in_array(strtolower($filetype), $allowed)) {
            // Buat folder uploads jika belum ada
            $uploadDir = __DIR__ . '/../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Beri nama unik pada file foto
            $newFileName = 'avatar_' . time() . '.' . $filetype;
            $destination = $uploadDir . $newFileName;

            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Simpan path foto ke session
                $_SESSION['foto_profil'] = '../uploads/' . $newFileName;
                header("Location: profil.php?status=foto_sukses");
                exit();
            }
        }
    }
}

header("Location: profil.php?status=foto_gagal");
exit();
?>
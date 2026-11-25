<?php
require_once __DIR__ . '/../Config/auth.php';
if (empty($_SESSION['id_pengguna']) || ($_SESSION['peran'] ?? '') !== 'admin') {
    http_response_code(403); exit('Halaman ini khusus admin.');
}
header('Location: ../Admin/pengguna.php'); exit;

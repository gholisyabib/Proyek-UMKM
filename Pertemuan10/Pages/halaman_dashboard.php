<?php
require_once __DIR__ . '/../Config/auth.php';
if (($_SESSION['peran'] ?? '') === 'admin') { header('Location: ../Admin/dashboard.php'); exit; }
if (($_SESSION['peran'] ?? '') === 'pelanggan') { header('Location: halaman_menu.php'); exit; }
header('Location: login.php'); exit;

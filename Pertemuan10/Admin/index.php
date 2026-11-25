<?php
require_once __DIR__ . '/../Config/auth.php';
if (!empty($_SESSION['id_pengguna']) && ($_SESSION['peran'] ?? '') === 'admin') { header('Location: dashboard.php'); exit; }
header('Location: login.php'); exit;

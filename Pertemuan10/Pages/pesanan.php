<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');
// Halaman pesanan pelanggan menggunakan sumber data yang sama dengan riwayat pesanan.
if (isset($_GET['reorder'])) {
    $label = trim((string)$_GET['reorder']);
    $candidates = [];
    if (stripos($label, 'Paket Burger') !== false) $candidates = [['Burger',1],['Es Teh',1]];
    elseif (stripos($label, 'Mie Ayam Bakso') !== false) $candidates = [['Mie Ayam Bakso',2]];
    foreach ($candidates as [$needle,$qty]) {
        $like = '%' . $needle . '%';
        $stmt = mysqli_prepare($conn, "SELECT id_produk,nama_produk,harga,foto,deskripsi,stok FROM produk WHERE nama_produk LIKE ? AND status_produk='tersedia' AND stok>0 ORDER BY id_produk LIMIT 1");
        mysqli_stmt_bind_param($stmt,'s',$like); mysqli_stmt_execute($stmt); $product=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
        if ($product) {
            if (!isset($_SESSION['keranjang'])) $_SESSION['keranjang']=[];
            $qty=min($qty,(int)$product['stok']);
            $_SESSION['keranjang'][]=['id'=>(int)$product['id_produk'],'nama'=>$product['nama_produk'],'harga'=>(float)$product['harga'],'gambar'=>$product['foto']?:'aaa.png','deskripsi'=>$product['deskripsi']??'','jumlah'=>$qty];
        }
    }
    header('Location: keranjang.php'); exit;
}
header('Location: riwayat_pesanan.php'); exit;

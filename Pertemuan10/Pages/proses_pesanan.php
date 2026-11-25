<?php
require_once __DIR__ . '/../Config/koneksi.php';
require_once __DIR__ . '/../Config/auth.php';
madin_role_required('pelanggan');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: pembayaran.php'); exit; }
$cart = $_SESSION['keranjang'] ?? [];
if (!$cart) { header('Location: keranjang.php?kosong=1'); exit; }
$method = strtolower($_POST['metode_pembayaran'] ?? 'qris');
if (!in_array($method, ['qris','cod'], true)) $method = 'qris';
$items=[]; $subtotal=0;
foreach ($cart as $item) {
  $name=trim((string)($item['nama'] ?? $item['nama_produk'] ?? 'Produk'));
  $price=max(0,(int)($item['harga'] ?? 0));
  $qty=max(1,(int)($item['jumlah'] ?? $item['qty'] ?? 1));
  if ($price <= 0) continue;
  $line=$price*$qty; $subtotal += $line;
  $items[]=['nama'=>$name,'harga'=>$price,'jumlah'=>$qty,'subtotal'=>$line];
}
if (!$items) { header('Location: keranjang.php?kosong=1'); exit; }
$service=1000; $shipping=5000; $total=$subtotal+$service+$shipping;
$id_user=(int)$_SESSION['id_pengguna'];
$name=trim((string)($_SESSION['nama_pembeli'] ?? 'Pelanggan'));
$phone=''; $pickup='pengantaran'; $address=trim((string)($_POST['alamat_pengantaran'] ?? ''));
$note=trim((string)($_POST['catatan'] ?? ''));
$code='MDN'.date('ymdHis').strtoupper(bin2hex(random_bytes(2)));
$payment_status='menunggu';
$db_method = $method === 'cod' ? 'tunai' : 'qris';
mysqli_begin_transaction($conn);
try {
 $status='menunggu';
 $stmt=mysqli_prepare($conn,"INSERT INTO pesanan (kode_pesanan,id_pengguna,nama_pelanggan,telepon_pelanggan,jenis_pengambilan,alamat_pengantaran,metode_pembayaran,status_pembayaran,status_pesanan,subtotal,biaya_pengantaran,total,catatan) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
 mysqli_stmt_bind_param($stmt,'sisssssssddds',$code,$id_user,$name,$phone,$pickup,$address,$db_method,$payment_status,$status,$subtotal,$shipping,$total,$note);
 if(!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_stmt_error($stmt));
 $order_id=mysqli_insert_id($conn); mysqli_stmt_close($stmt);
 $detail=mysqli_prepare($conn,'INSERT INTO detail_pesanan (id_pesanan,nama_produk,harga_satuan,jumlah,subtotal) VALUES (?,?,?,?,?)');
 foreach($items as $it){ mysqli_stmt_bind_param($detail,'isdid',$order_id,$it['nama'],$it['harga'],$it['jumlah'],$it['subtotal']); if(!mysqli_stmt_execute($detail)) throw new Exception(mysqli_stmt_error($detail)); }
 mysqli_stmt_close($detail); mysqli_commit($conn);
 $_SESSION['pesanan_terakhir']=$order_id;
 $_SESSION['keranjang']=[]; unset($_SESSION['cart']);
 header('Location: '.($method==='qris'?'qris.php?order='.$order_id:'status_pesanan.php?order='.$order_id)); exit;
} catch(Throwable $e) { mysqli_rollback($conn); http_response_code(500); echo 'Pesanan gagal disimpan. Periksa struktur database dan coba lagi. Detail: '.htmlspecialchars($e->getMessage(),ENT_QUOTES,'UTF-8'); exit; }

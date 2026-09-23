-- =========================================================
-- database.sql
-- Rekonstruksi dari foto layar (mulai baris ~33).
-- Bagian sebelum baris ini (mis. CREATE DATABASE, SET FOREIGN_KEY_CHECKS = 0;,
-- dan TABEL sebelum `pengguna` jika ada) TIDAK tertangkap di foto.
-- Silakan tambahkan sendiri jika diperlukan, contoh:
-- SET FOREIGN_KEY_CHECKS = 0;
-- =========================================================

-- ---------------------------------------
-- TABEL 1: PENGGUNA
-- ---------------------------------------
CREATE TABLE `pengguna` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_lengkap` VARCHAR(100) NOT NULL COMMENT 'Nama lengkap pemilik akun',
  `email` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email aktif untuk login',
  `kata_sandi` VARCHAR(255) NOT NULL COMMENT 'Password yang dienkripsi dengan password_hash()',
  `no_telp` VARCHAR(20) DEFAULT NULL COMMENT 'Nomor kontak WhatsApp / HP',
  `alamat` TEXT DEFAULT NULL COMMENT 'Alamat lengkap pengiriman atau tempat tinggal',
  `peran` ENUM('superadmin', 'gudang', 'kasir', 'pelanggan') NOT NULL DEFAULT 'pelanggan' COMMENT 'Hak akses dalam sistem',
  `status_aktif` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1 = Aktif, 0 = Diblokir/Nonaktif',
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pendaftaran akun',
  `diperbarui_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel data pengguna dan hak akses ERP';

-- ---------------------------------------
-- TABEL 2: KATEGORI (Tabel Master Kategori Sepatu)
-- ---------------------------------------
CREATE TABLE `kategori` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_kategori` VARCHAR(100) NOT NULL COMMENT 'Nama kategori Menu makananan (misal: Makanan, Minuman)',
  `slug` VARCHAR(120) NOT NULL UNIQUE COMMENT 'Slug ramah URL',
  `deskripsi` TEXT DEFAULT NULL COMMENT 'Penjelasan singkat kategori',
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master Kategori Sepatu';

-- ---------------------------------------
-- TABEL 3: PRODUK (Master Data Sepatu & Persediaan)
-- Mencatat HPP (Harga Pokok Pembelian) untuk kalkulasi akuntansi laba kotor.
-- ---------------------------------------
CREATE TABLE `produk` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `kategori_id` INT NOT NULL COMMENT 'Relasi ke tabel kategori',
  `kode_produk` VARCHAR(30) NOT NULL UNIQUE COMMENT 'SKU / Barcode unik produk (misal: SPT-SNK-001)',
  `nama_produk` VARCHAR(150) NOT NULL COMMENT 'Nama komersial sepatu',
  `slug` VARCHAR(180) NOT NULL UNIQUE COMMENT 'Slug untuk URL detail produk',
  `deskripsi` TEXT DEFAULT NULL COMMENT 'Deskripsi detail spesifikasi dan bahan sepatu',
  `ukuran` VARCHAR(50) NOT NULL DEFAULT 'besar, kecil' COMMENT 'Pilihan ukuran sepatu yang tersedia',
  `harga_beli` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'HPP (Harga Pokok Pembelian) satuan saat restock',
  `harga_jual` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Harga jual ke konsumen e-commerce',
  `stok` INT NOT NULL DEFAULT 0 COMMENT 'Jumlah stok fisik saat ini',
  `stok_minimum` INT NOT NULL DEFAULT 5 COMMENT 'Batas peringatan stok menipis pada dashboard ERP',
  `gambar` VARCHAR(255) DEFAULT 'default_sepatu.jpg' COMMENT 'Nama file foto produk',
  `status` ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'aktif' COMMENT 'Status tayang di katalog',
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Master Data Produk dan Persediaan Sepatu';

-- ---------------------------------------
-- TABEL 4: PESANAN (Header Transaksi Penjualan E-Commerce)
-- ---------------------------------------
CREATE TABLE `pesanan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_pesanan` VARCHAR(40) NOT NULL UNIQUE COMMENT 'Nomor unik pesanan (contoh: ORD-20260923-001)',
  `pengguna_id` INT NOT NULL COMMENT 'ID pelanggan yang melakukan pemesanan',
  `tanggal_pesanan` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu pelanggan melakukan pemesanan',
  `total_belanja` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Total harga seluruh menu',
  `total_bayar` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Total yang harus dibayar pelanggan',
  `status_pesanan` ENUM('menunggu_konfirmasi', 'diproses', 'siap_diambil', 'selesai', 'dibatalkan') 
  NOT NULL DEFAULT 'menunggu_konfirmasi' COMMENT 'Status pesanan ambil di tempat',
  `status_pembayaran` ENUM('belum_lunas', 'lunas') 
  NOT NULL DEFAULT 'belum_lunas' COMMENT 'Status pembayaran',
  `metode_pembayaran` ENUM('cash', 'qris', 'transfer_bank') 
  NOT NULL DEFAULT 'cash' COMMENT 'Metode pembayaran pelanggan',
  `nama_pemesan` VARCHAR(100) NOT NULL COMMENT 'Nama pelanggan yang melakukan pemesanan',
  `no_telp_pemesan` VARCHAR(20) NOT NULL COMMENT 'Nomor WhatsApp pelanggan',
  `catatan` TEXT DEFAULT NULL COMMENT 'Catatan tambahan dari pembeli',
  `bukti_transfer` VARCHAR(255) DEFAULT NULL COMMENT 'Bukti pembayaran jika menggunakan transfer',
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `fk_pesanan_pengguna`
    FOREIGN KEY (`pengguna_id`)
    REFERENCES `pengguna` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Pesanan UMKM dengan metode ambil di tempat';   

-- ---------------------------------------
-- TABEL 5: DETAIL_PESANAN (Rincian Item yang Dipesan)
-- Menyimpan HPP satuan pada saat pesanan dibuat agar laporan laba rugi akurat.
-- ---------------------------------------
CREATE TABLE `detail_pesanan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `pesanan_id` INT NOT NULL COMMENT 'Relasi ke tabel pesanan',
  `produk_id` INT NOT NULL COMMENT 'Relasi ke tabel produk',
  `kuantitas` INT NOT NULL DEFAULT 1 COMMENT 'Jumlah pasang yang dibeli',
  `harga_satuan` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Harga jual satuan saat transaksi',
  `hpp_satuan` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Harga Pokok Pembelian satuan saat transaksi (untuk Laba/Rugi)',
  `subtotal` DECIMAL(12,2) NOT NULL DEFAULT 0.00 COMMENT 'kuantitas * harga_satuan',
  CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Detail Item Produk per Pesanan';

-- ---------------------------------------
-- TABEL 6: MUTASI_STOK (Kartu Stok & Audit Trail Inventaris)
-- Setiap barang masuk, barang keluar, atau penyesuaian opname dicatat di sini.
-- ---------------------------------------
CREATE TABLE `mutasi_stok` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `produk_id` INT NOT NULL COMMENT 'ID produk yang termutasi',
  `jenis_mutasi` ENUM('masuk_restock', 'keluar_penjualan', 'penyesuaian_opname', 'retur_masuk') NOT NULL COMMENT 'Jenis perubahan stok',
  `jumlah` INT NOT NULL COMMENT 'Banyaknya barang yang bertambah (+) atau berkurang (-)',
  `stok_sebelum` INT NOT NULL COMMENT 'Jumlah stok sebelum aksi dilakukan',
  `stok_sesudah` INT NOT NULL COMMENT 'Jumlah stok setelah aksi dilakukan',
  `nomor_referensi` VARCHAR(50) DEFAULT NULL COMMENT 'Nomor Pesanan atau No Faktur Supplier',
  `keterangan` TEXT DEFAULT NULL COMMENT 'Catatan alasan mutasi atau nama supplier',
  `tanggal` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `pengguna_id` INT DEFAULT NULL COMMENT 'Staf yang melakukan eksekusi mutasi',
  CONSTRAINT `fk_mutasi_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_mutasi_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Log Kartu Mutasi Stok Fisik';

-- ---------------------------------------
-- TABEL 7: AKUN_REKENING (Chart of Accounts / Bagan Akun Standar ERP)
-- Standar Akuntansi Keuangan Sederhana untuk Perusahaan Dagang.
-- ---------------------------------------
CREATE TABLE `akun_rekening` (
  `kode_akun` VARCHAR(20) PRIMARY KEY COMMENT 'Kode unik akun (misal: 101, 401, 501)',
  `nama_akun` VARCHAR(100) NOT NULL COMMENT 'Nama akun akuntansi',
  `kelompok_akun` ENUM('Aset', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban_Pokok', 'Beban_Operasional') NOT NULL COMMENT 'Klasifikasi laporan keuangan',
  `posisi_normal` ENUM('Debit', 'Kredit') NOT NULL COMMENT 'Saldo normal akun',
  `saldo_awal` DECIMAL(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Saldo awal periode',
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Bagan Akun Keuangan (Chart of Accounts)';

-- ---------------------------------------
-- TABEL 8: JURNAL_KEUANGAN (Buku Jurnal Umum Double-Entry)
-- Menampung baris Debit dan Kredit yang harus seimbang (Balance).
-- ---------------------------------------
CREATE TABLE `jurnal_keuangan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `no_jurnal` VARCHAR(40) NOT NULL COMMENT 'Nomor referensi voucher jurnal (misal: JRN-202609-001)',
  `tanggal` DATE NOT NULL COMMENT 'Tanggal transaksi akuntansi',
  `kode_akun` VARCHAR(20) NOT NULL COMMENT 'Relasi ke tabel akun_rekening',
  `debit` DECIMAL(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai mutasi di sisi Debit',
  `kredit` DECIMAL(14,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai mutasi di sisi Kredit',
  `keterangan` VARCHAR(255) NOT NULL COMMENT 'Uraian keterangan transaksi',
  `referensi_transaksi` VARCHAR(50) DEFAULT NULL COMMENT 'No Pesanan atau No Faktur Beban',
  `pengguna_id` INT DEFAULT NULL COMMENT 'Staf yang membukukan transaksi',
  `dibuat_pada` DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_jurnal_akun` FOREIGN KEY (`kode_akun`) REFERENCES `akun_rekening` (`kode_akun`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_jurnal_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Tabel Jurnal Umum Keuangan Double-Entry';

-- Kembalikan pengecekan foreign key
SET FOREIGN_KEY_CHECKS = 1;

-- =========================================================
-- DATA DUMMY / SAMPLE DATA UNTUK PEMBELAJARAN & PENGUJIAN MAHASISWA
-- =========================================================

-- 1. Data Akun Pengguna (Password default: 'password123' untuk semua akun demo)
-- Hash password valid dibuat menggunakan password_hash('password123', PASSWORD_BCRYPT)
-- Hash: $2y$10$h6wYCDRN.TkSvdHDkb.lJ.mAJVXewX4wisVkDbWbgI0Lr.3Gj8/Gq
INSERT INTO `pengguna` (`id`, `nama_lengkap`, `email`, `kata_sandi`, `no_telp`, `alamat`, `peran`, `status_aktif`) VALUES
(1, 'Administrator Utama ERP', 'admin@sepatuerp.com', '$2y$10$h6wYCDRN.TkSvdHDkb.lJ.mAJVXewX4wisVkDbWbgI0Lr.3Gj8/Gq', -- TODO: lengkapi no_telp, alamat, peran, status_aktif (terpotong di foto)
(2, 'Staf Gudang & Logistik', 'gudang@sepatuerp.com', '$2y$10$h6wYCDRN.TkSvdHDkb.lJ.mAJVXewX4wisVkDbWbgI0Lr.3Gj8/Gq', -- TODO: lengkapi (terpotong di foto)
(3, 'Kasir & Keuangan', 'kasir@sepatuerp.com', '$2y$10$h6wYCDRN.TkSvdHDkb.lJ.mAJVXewX4wisVkDbWbgI0Lr.3Gj8/Gq'; -- TODO: lengkapi (terpotong di foto)

-- TODO: sisa INSERT (produk, kategori, pesanan dummy, dst.) tidak terlihat di foto yang diberikan.
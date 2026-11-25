-- Database baru untuk proyek MADIN dari codingan teman
-- Impor file ini melalui phpMyAdmin. Tidak menghapus database lain.
CREATE DATABASE IF NOT EXISTS madin_baru
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE madin_baru;

CREATE TABLE IF NOT EXISTS pengguna (
  id_pengguna INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  kata_sandi VARCHAR(255) NOT NULL,
  telepon VARCHAR(25) NULL,
  jenis_kelamin ENUM('Laki-laki','Perempuan','Tidak disebutkan') NULL,
  foto_profil VARCHAR(255) NULL,
  peran ENUM('pelanggan','admin') NOT NULL DEFAULT 'pelanggan',
  status_akun ENUM('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS kategori (
  id_kategori INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nama_kategori VARCHAR(100) NOT NULL,
  deskripsi TEXT NULL,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS produk (
  id_produk INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_kategori INT UNSIGNED NULL,
  nama_produk VARCHAR(150) NOT NULL,
  deskripsi TEXT NULL,
  harga DECIMAL(12,2) NOT NULL DEFAULT 0,
  stok INT UNSIGNED NOT NULL DEFAULT 0,
  foto VARCHAR(255) NULL,
  status_produk ENUM('tersedia','habis','nonaktif') NOT NULL DEFAULT 'tersedia',
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_produk_kategori FOREIGN KEY (id_kategori)
    REFERENCES kategori(id_kategori) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS alamat (
  id_alamat INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT UNSIGNED NOT NULL,
  label_alamat VARCHAR(60) NULL,
  nama_penerima VARCHAR(100) NULL,
  telepon_penerima VARCHAR(25) NULL,
  alamat_lengkap TEXT NOT NULL,
  catatan TEXT NULL,
  alamat_utama TINYINT(1) NOT NULL DEFAULT 0,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_alamat_pengguna FOREIGN KEY (id_pengguna)
    REFERENCES pengguna(id_pengguna) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pesanan (
  id_pesanan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  kode_pesanan VARCHAR(40) NOT NULL UNIQUE,
  id_pengguna INT UNSIGNED NULL,
  nama_pelanggan VARCHAR(100) NOT NULL,
  telepon_pelanggan VARCHAR(25) NULL,
  jenis_pengambilan ENUM('ambil_sendiri','pengantaran') NOT NULL DEFAULT 'ambil_sendiri',
  alamat_pengantaran TEXT NULL,
  metode_pembayaran ENUM('qris','tunai','transfer','lainnya') NOT NULL DEFAULT 'tunai',
  status_pembayaran ENUM('menunggu','berhasil','gagal','dikembalikan') NOT NULL DEFAULT 'menunggu',
  status_pesanan ENUM('menunggu','diproses','siap_diambil','dikirim','selesai','dibatalkan') NOT NULL DEFAULT 'menunggu',
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  biaya_pengantaran DECIMAL(12,2) NOT NULL DEFAULT 0,
  total DECIMAL(12,2) NOT NULL DEFAULT 0,
  catatan TEXT NULL,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  diperbarui_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_pesanan_pengguna FOREIGN KEY (id_pengguna)
    REFERENCES pengguna(id_pengguna) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS detail_pesanan (
  id_detail BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pesanan BIGINT UNSIGNED NOT NULL,
  id_produk INT UNSIGNED NULL,
  nama_produk VARCHAR(150) NOT NULL,
  harga_satuan DECIMAL(12,2) NOT NULL DEFAULT 0,
  jumlah INT UNSIGNED NOT NULL DEFAULT 1,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  CONSTRAINT fk_detail_pesanan FOREIGN KEY (id_pesanan)
    REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE CASCADE,
  CONSTRAINT fk_detail_produk FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifikasi (
  id_notifikasi BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT UNSIGNED NULL,
  judul VARCHAR(150) NOT NULL,
  pesan TEXT NOT NULL,
  jenis VARCHAR(50) NULL,
  sudah_dibaca TINYINT(1) NOT NULL DEFAULT 0,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_notifikasi_pengguna FOREIGN KEY (id_pengguna)
    REFERENCES pengguna(id_pengguna) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ulasan (
  id_ulasan BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT UNSIGNED NULL,
  id_produk INT UNSIGNED NULL,
  id_pesanan BIGINT UNSIGNED NULL,
  rating TINYINT UNSIGNED NOT NULL,
  komentar TEXT NULL,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT chk_ulasan_rating CHECK (rating BETWEEN 1 AND 5),
  CONSTRAINT fk_ulasan_pengguna FOREIGN KEY (id_pengguna)
    REFERENCES pengguna(id_pengguna) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_ulasan_produk FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk) ON UPDATE CASCADE ON DELETE SET NULL,
  CONSTRAINT fk_ulasan_pesanan FOREIGN KEY (id_pesanan)
    REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS mutasi_stok (
  id_mutasi BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_produk INT UNSIGNED NOT NULL,
  jenis_mutasi ENUM('masuk','keluar','penyesuaian') NOT NULL,
  jumlah INT UNSIGNED NOT NULL,
  stok_sebelum INT UNSIGNED NOT NULL,
  stok_sesudah INT UNSIGNED NOT NULL,
  keterangan VARCHAR(255) NULL,
  id_pesanan BIGINT UNSIGNED NULL,
  dibuat_pada TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_mutasi_produk FOREIGN KEY (id_produk)
    REFERENCES produk(id_produk) ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_mutasi_pesanan FOREIGN KEY (id_pesanan)
    REFERENCES pesanan(id_pesanan) ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- Kategori awal; aman dijalankan sekali pada database baru.
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Makanan', 'Menu makanan MADIN'),
('Minuman', 'Menu minuman MADIN')
ON DUPLICATE KEY UPDATE nama_kategori = VALUES(nama_kategori);

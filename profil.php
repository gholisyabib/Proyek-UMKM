<?php

session_start();

$profil = $_SESSION['profil'] ?? [

    'nama' => '',
    'email' => 'user@gmail.com',
    'telepon' => '',
    'tanggal' => '',
    'jenis_kelamin' => '',
    'alamat' => '',
    'foto' => ''

];

$alamat = $_SESSION['alamat'] ?? null;

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Profil - MADIN</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body>

<div class="halaman">


<!-- HEADER -->

<header class="header">

    <div class="logo">
        MADIN<span>♥</span>
    </div>

    <div class="search-box">

        <span>⌕</span>

        <input
            type="text"
            placeholder="Mencari"
        >

    </div>

    <div class="header-kanan">

        <div class="tagline">
            Nikmatin Harga Terjangkau
        </div>

    </div>

    <nav class="header-menu">

        <a href="pesan.php">
            Pesan
        </a>

        <a href="pesanan.php">
            Pesanan
        </a>

        <a href="keranjang.php">
            Keranjang
        </a>

        <a href="profil.php" class="aktif">
            Profile
        </a>

    </nav>

</header>


<!-- AREA -->

<main class="area-utama">


<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="sidebar-profil">

        <div class="avatar-sidebar">

            <?php if (!empty($profil['foto'])): ?>

                <img
                    src="<?= htmlspecialchars($profil['foto']) ?>"
                    style="
                    width:74px;
                    height:74px;
                    object-fit:cover;
                    border-radius:50%;
                    "
                >

            <?php else: ?>

                ♙

            <?php endif; ?>

        </div>

        <h4>
            <?= $profil['nama'] !== ''
                ? htmlspecialchars($profil['nama'])
                : 'User'
            ?>
        </h4>

        <p>
            <?= htmlspecialchars($profil['email']) ?>
        </p>

    </div>


    <div class="sidebar-menu">

        <a
            href="profil.php"
            class="aktif"
        >
            👤 Profil Saya
        </a>

        <a href="#">
            ⟳ Riwayat Pesanan
        </a>

        <a href="#">
            ♧ Notifikasi
        </a>

        <!-- MENU ALAMAT -->

        <a href="alamat.php">
            ⌾ Alamat Saya
        </a>

        <a href="#">
            ♧ Tentang Kami
        </a>

        <a href="#">
            ▣ Kontak Kami
        </a>

        <a href="#">
            ▣ FAQ
        </a>

        <div class="garis"></div>

        <a href="#">
            ⇥ Keluar
        </a>

    </div>

</aside>


<!-- PROFIL -->

<section class="profil-konten">

    <div class="profil-header">

        <h2>
            Profil Saya
        </h2>

        <p>
            Kelola informasi akun Anda agar proses pemesanan lebih mudah.
        </p>

    </div>


    <form
        action="simpan-profil.php"
        method="POST"
        enctype="multipart/form-data"
        class="form-profil"
    >


        <!-- FOTO -->

        <div class="foto-profil">

            <?php if (!empty($profil['foto'])): ?>

                <img
                    id="previewFoto"
                    src="<?= htmlspecialchars($profil['foto']) ?>"
                >

            <?php else: ?>

                <img
                    id="previewFoto"
                    src="https://placehold.co/112x112/503232/ffffff?text=User"
                >

            <?php endif; ?>


            <label
                for="foto"
                class="btn-foto"
            >
                Ubah Foto
            </label>

            <input
                type="file"
                id="foto"
                name="foto"
                accept="image/*"
            >

        </div>


        <!-- NAMA -->

        <div class="profil-field">

            <label>
                Nama Lengkap
            </label>

            <input
                type="text"
                name="nama"
                value="<?= htmlspecialchars($profil['nama']) ?>"
                required
            >

        </div>


        <!-- EMAIL -->

        <div class="profil-field">

            <label>
                Email
            </label>

            <input
                type="email"
                name="email"
                value="<?= htmlspecialchars($profil['email']) ?>"
                required
            >

        </div>


        <div class="dua-kolom">


            <!-- TELEPON -->

            <div class="profil-field">

                <label>
                    Nomor Telepon
                </label>

                <input
                    type="tel"
                    name="telepon"
                    value="<?= htmlspecialchars($profil['telepon']) ?>"
                >

            </div>


            <!-- TANGGAL -->

            <div class="profil-field">

                <label>
                    Tanggal Lahir
                </label>

                <input
                    type="date"
                    name="tanggal"
                    value="<?= htmlspecialchars($profil['tanggal']) ?>"
                >

            </div>

        </div>


        <!-- JENIS KELAMIN -->

        <div class="profil-field">

            <label>
                Jenis Kelamin
            </label>

            <select name="jenis_kelamin">

                <option value="">
                    Pilih Jenis Kelamin
                </option>

                <option
                    value="Laki-laki"
                    <?= $profil['jenis_kelamin'] === 'Laki-laki'
                        ? 'selected'
                        : '' ?>
                >
                    Laki-laki
                </option>

                <option
                    value="Perempuan"
                    <?= $profil['jenis_kelamin'] === 'Perempuan'
                        ? 'selected'
                        : '' ?>
                >
                    Perempuan
                </option>

            </select>

        </div>


        <!-- ALAMAT -->

        <div class="profil-field">

            <label>
                Alamat Lengkap
            </label>

            <textarea
                name="alamat"
            ><?= htmlspecialchars(
                $alamat['alamat'] ?? $profil['alamat']
            ) ?></textarea>

        </div>


        <div class="tombol-simpan">

            <button
                type="submit"
                class="btn-simpan"
            >
                Simpan
            </button>

        </div>

    </form>

</section>

</main>


<!-- FOOTER -->

<footer class="footer">

    <div class="footer-link">

        <a href="#">
            Tentang Kami
        </a>

        <a href="#">
            Blog
        </a>

        <a href="#">
            Kontak Kami
        </a>

    </div>


    <div class="footer-kontak">

        <p>
            ◉ WhatsApp 085772137034
        </p>

        <p>
            ♧ Telepon 085860709935
        </p>

    </div>


    <div class="footer-logo">
        MADIN♥
    </div>

</footer>

</div>


<script>

document
.getElementById('foto')
.addEventListener('change', function(event) {

    const file = event.target.files[0];

    if (!file) {
        return;
    }

    const reader = new FileReader();

    reader.onload = function(e) {

        document
        .getElementById('previewFoto')
        .src = e.target.result;

    };

    reader.readAsDataURL(file);

});

</script>

</body>

</html>
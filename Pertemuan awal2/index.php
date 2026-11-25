<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MADIN - Pesan Makanan</title>
    <link rel="stylesheet" href="style.css">
    
    <!-- 1. Menambahkan Google Fonts: Merienda One -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merienda+One&display=swap" rel="stylesheet">
    
    <!-- Ikon WhatsApp dan Telepon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <!-- Bagian Utama (Hero Section) -->
    <main class="hero-section">
        <!-- Kolom Kiri: Teks dan Tombol -->
        <div class="hero-left">
            <!-- 2. Bagian Logo menggunakan font Merienda One -->
            <div class="logo">
                MADIN
                <!-- Ikon mangkuk dan sumpit (SVG disesuaikan) -->
                <svg class="logo-icon" viewBox="0 0 120 100" width="55" height="45">
                    <!-- Mangkuk -->
                    <path d="M20,50 C20,85 40,95 60,95 C80,95 100,85 100,50 L20,50 Z" fill="#000"/>
                    <rect x="15" y="45" width="90" height="8" rx="4" fill="#000"/>
                    <!-- Sumpit -->
                    <line x1="60" y1="20" x2="115" y2="35" stroke="#000" stroke-width="5" stroke-linecap="round"/>
                    <line x1="65" y1="30" x2="115" y2="45" stroke="#000" stroke-width="5" stroke-linecap="round"/>
                    <!-- Uap -->
                    <path d="M35,35 Q40,25 45,35 T55,35" stroke="#000" stroke-width="4" fill="none"/>
                    <path d="M55,35 Q60,25 65,35 T75,35" stroke="#000" stroke-width="4" fill="none"/>
                    <path d="M75,35 Q80,25 85,35 T95,35" stroke="#000" stroke-width="4" fill="none"/>
                </svg>
            </div>
            
            <p class="description">
                Nikmati kelezatan mie ayam gurih berlimpah topping dan burger empuk penuh rasa, disempurnakan dengan kesegaran es teh manis yang siap membangkitkan kembali semangat harimu
            </p>
            
            <button class="btn-masuk">Masuk</button>
        </div>

        <!-- Kolom Kanan: Kolase Gambar -->
        <div class="hero-right">
            <div class="collage-grid">
                <!-- Ganti src dengan lokasi gambar burger Anda -->
                <img src="https://images.unsplash.com/photo-1568901346375-23c9450c58cd?auto=format&fit=crop&q=80&w=800" alt="Burger" class="img-burger">
                <!-- Ganti src dengan lokasi gambar mie Anda -->
                <img src="https://images.unsplash.com/photo-1585032226651-759b368d7246?auto=format&fit=crop&q=80&w=800" alt="Mie Ayam" class="img-noodles">
                <!-- Ganti src dengan lokasi gambar es teh Anda -->
                <img src="https://images.unsplash.com/photo-1556679343-c7306c1976bc?auto=format&fit=crop&q=80&w=800" alt="Es Teh" class="img-tea">
            </div>
        </div>
    </main>

    <!-- Bagian Footer -->
    <footer class="footer-section">
        <div class="footer-content">
            <div class="footer-info">
                <div class="footer-links">
                    <a href="#">Tentang Kami</a>
                    <a href="#">Blog</a>
                    <a href="#">Kontak Kami</a>
                </div>
                <div class="footer-contact">
                    <p><i class="fab fa-whatsapp"></i> WhatsApp 085772137034</p>
                    <p><i class="fas fa-phone-alt"></i> Telepon 085860709935</p>
                </div>
            </div>
            
            <!-- Logo Footer (Menggunakan Merienda One juga) -->
            <div class="logo footer-logo">
                MADIN
                <svg class="logo-icon" viewBox="0 0 120 100" width="40" height="35">
                    <path d="M20,50 C20,85 40,95 60,95 C80,95 100,85 100,50 L20,50 Z" fill="#fff"/>
                    <rect x="15" y="45" width="90" height="8" rx="4" fill="#fff"/>
                    <line x1="60" y1="20" x2="115" y2="35" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
                    <line x1="65" y1="30" x2="115" y2="45" stroke="#fff" stroke-width="5" stroke-linecap="round"/>
                    <path d="M35,35 Q40,25 45,35 T55,35" stroke="#fff" stroke-width="4" fill="none"/>
                    <path d="M55,35 Q60,25 65,35 T75,35" stroke="#fff" stroke-width="4" fill="none"/>
                    <path d="M75,35 Q80,25 85,35 T95,35" stroke="#fff" stroke-width="4" fill="none"/>
                </svg>
            </div>
        </div>
    </footer>

</body>
</html>
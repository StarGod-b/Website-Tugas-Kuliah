<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda | Rental Mobil StarGod</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0ea5e9; 
            --accent: #f97316;
            --light: #f8fafc;
            --dark: #0f172a;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8fafc;
            color: #334155;
            overflow-x: hidden;
        }
        
        /* Header */
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            font-weight: 700;
            font-size: 1.8rem;
            background: linear-gradient(to right, #fff, #e0f2fe);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }
        
        /* Hero Section */
        .hero {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.9)), 
                        url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1740&q=80');
            background-size: cover;
            background-position: center;
            min-height: 85vh;
            display: flex;
            align-items: center;
            position: relative;
        }
        
        .hero-content {
            max-width: 700px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }
        
        .cta-button {
            background: linear-gradient(135deg, var(--accent), #ea580c);
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(249, 115, 22, 0.4);
        }
        
        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(249, 115, 22, 0.6);
        }
        
        /* Features */
        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.1), rgba(14, 165, 233, 0.1));
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .feature-card:hover::before {
            opacity: 1;
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 20px;
        }
        
        /* FAQ */
        .faq-item {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 16px;
            transition: all 0.3s ease;
        }
        
        .faq-item.active {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .faq-question {
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        
        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s ease;
        }
        
        .faq-item.active .faq-answer {
            max-height: 500px;
        }
        
        .faq-item.active .faq-icon {
            transform: rotate(180deg);
        }
        
        /* Testimonials */
        .testimonial-card {
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .testimonial-card::before {
            content: """;
            position: absolute;
            top: -30px;
            left: 10px;
            font-size: 120px;
            color: rgba(37, 99, 235, 0.1);
            font-family: Georgia, serif;
            line-height: 1;
        }
        
        .testimonial-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 3px solid var(--primary);
            object-fit: cover;
        }
        
        /* Featured Cars */
        .car-card {
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .car-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .car-image {
            height: 200px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .car-card:hover .car-image {
            transform: scale(1.05);
        }
        
        .car-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        
        /* Footer */
        footer {
            background: linear-gradient(135deg, var(--dark), #1e293b);
            position: relative;
            overflow: hidden;
        }
        
        footer::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0) 70%);
            z-index: 0;
        }
        
        .footer-content {
            position: relative;
            z-index: 1;
        }
        
        .social-icon {
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        
        .delay-100 {
            animation-delay: 0.1s;
        }
        
        .delay-200 {
            animation-delay: 0.2s;
        }
        
        .delay-300 {
            animation-delay: 0.3s;
        }
        
        /* Scroll animations */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }
        
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="py-4 px-6 lg:px-12">
        <div class="container mx-auto flex justify-between items-center">
            <a class="logo flex items-center">
                <i class="fas fa-car mr-2"></i>
                <span>RentalMobil<span class="text-orange-400">.SG</span></span>
            </a>
            
            <div class="hidden lg:flex space-x-1">
                <a href="home.php" class="text-white px-4 py-2 rounded-lg hover:bg-white/10 transition">Beranda</a>
                <a href="mobil.php" class="text-white px-4 py-2 rounded-lg hover:bg-white/10 transition">Mobil</a>
                <a href="promo.php" class="text-white px-4 py-2 rounded-lg hover:bg-white/10 transition">Promo</a>
                <a href="hubungi_kami.php" class="text-white px-4 py-2 rounded-lg hover:bg-white/10 transition">Hubungi Kami</a>
            </div>
            
            <div class="flex items-center space-x-4">
                <a href="login.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition flex items-center">
                    <i class="fas fa-sign-in-alt mr-2"></i> Login
                </a>
                <button class="lg:hidden text-white text-2xl">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container mx-auto px-6 lg:px-12">
            <div class="hero-content text-white animate-fadeInUp">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    Sewa Mobil Bekas Berkualitas dengan Harga Terbaik
                </h1>
                <p class="text-xl mb-10 text-blue-100 max-w-2xl">
                    Cepat, aman, dan terpercaya. Temukan mobil impianmu untuk disewa hari ini juga!
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="mobil.php" class="cta-button px-8 py-4 rounded-full text-lg font-bold inline-flex items-center">
                        Sewa Sekarang <i class="fas fa-arrow-right ml-3"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Floating stats -->
        <div class="absolute bottom-8 left-0 right-0">
            <div class="container mx-auto px-6 lg:px-12">
                <div class="bg-white rounded-xl p-6 max-w-4xl mx-auto shadow-xl grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">500+</div>
                        <div class="text-gray-600">Mobil Tersedia</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">98%</div>
                        <div class="text-gray-600">Kepuasan Pelanggan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">24/7</div>
                        <div class="text-gray-600">Layanan Pelanggan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">50+</div>
                        <div class="text-gray-600">Lokasi Cabang</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kelebihan Menyewa -->
    <section class="py-16 px-6 bg-white">
        <div class="container mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Kenapa Menyewa di RentalMobil.SG?</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Kami memberikan pengalaman terbaik dalam penyewaan mobil dengan layanan unggulan
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="feature-card p-8 text-center reveal">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-car"></i>
                    </div>
                    <h4 class="font-bold text-xl mb-3">Mobil Terawat</h4>
                    <p class="text-gray-600">Kendaraan rutin diservis dan dicek sebelum digunakan untuk kenyamanan dan keamanan Anda.</p>
                </div>
                
                <div class="feature-card p-8 text-center reveal">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-tag"></i>
                    </div>
                    <h4 class="font-bold text-xl mb-3">Harga Transparan</h4>
                    <p class="text-gray-600">Tidak ada biaya tersembunyi. Semua harga jelas di awal tanpa kejutan di akhir.</p>
                </div>
                
                <div class="feature-card p-8 text-center reveal">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h4 class="font-bold text-xl mb-3">Proses Cepat</h4>
                    <p class="text-gray-600">Booking online hanya butuh beberapa menit saja dengan konfirmasi instan.</p>
                </div>
                
                <div class="feature-card p-8 text-center reveal">
                    <div class="feature-icon mx-auto">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4 class="font-bold text-xl mb-3">Asuransi Lengkap</h4>
                    <p class="text-gray-600">Proteksi maksimal untuk setiap penyewaan dengan berbagai pilihan asuransi.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Cars -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Mobil Pilihan Terbaik</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Berbagai pilihan mobil terbaru dengan kondisi prima siap menemani perjalanan Anda
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Car 1 -->
                <div class="car-card bg-white overflow-hidden shadow-md reveal">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" 
                             alt="Toyota Fortuner" class="car-image w-full">
                        <div class="car-badge">POPULER</div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-xl font-bold">Toyota Fortuner</h3>
                                <p class="text-gray-600">SUV 7-Seater</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">Rp 650.000/hari</div>
                                <div class="text-sm text-gray-500">Termasuk asuransi</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-gas-pump mr-2"></i>
                                <span>Diesel</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-cogs mr-2"></i>
                                <span>Automatic</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-friends mr-2"></i>
                                <span>7 Kursi</span>
                            </div>
                        </div>
                        
                        <button href="sewa.php" class="w-full mt-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Sewa Sekarang
                        </button>
                    </div>
                </div>
                
                <!-- Car 2 -->
                <div class="car-card bg-white overflow-hidden shadow-md reveal">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1550355291-bbee04a92027?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1336&q=80" 
                             alt="Honda Civic" class="car-image w-full">
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-xl font-bold">Honda Civic</h3>
                                <p class="text-gray-600">Sedan Sport</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">Rp 450.000/hari</div>
                                <div class="text-sm text-gray-500">Termasuk asuransi</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-gas-pump mr-2"></i>
                                <span>Bensin</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-cogs mr-2"></i>
                                <span>Automatic</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-friends mr-2"></i>
                                <span>5 Kursi</span>
                            </div>
                        </div>
                        
                        <button class="w-full mt-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Sewa Sekarang
                        </button>
                    </div>
                </div>
                
                <!-- Car 3 -->
                <div class="car-card bg-white overflow-hidden shadow-md reveal">
                    <div class="relative">
                        <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" 
                             alt="Toyota Avanza" class="car-image w-full">
                        <div class="car-badge">TERJANGKAU</div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-3">
                            <div>
                                <h3 class="text-xl font-bold">Toyota Avanza</h3>
                                <p class="text-gray-600">MPV Keluarga</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-blue-600">Rp 350.000/hari</div>
                                <div class="text-sm text-gray-500">Termasuk asuransi</div>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-gas-pump mr-2"></i>
                                <span>Bensin</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-cogs mr-2"></i>
                                <span>Manual</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-user-friends mr-2"></i>
                                <span>7 Kursi</span>
                            </div>
                        </div>
                        
                        <button class="w-full mt-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                            Sewa Sekarang
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-12">
                <a href="mobil.php" class="inline-block px-8 py-3 border-2 border-blue-600 text-blue-600 font-medium rounded-full hover:bg-blue-50 transition">
                    Lihat Semua Mobil <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 px-6 bg-white">
        <div class="container mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Pertanyaan yang Sering Diajukan</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Temukan jawaban atas pertanyaan umum seputar layanan penyewaan mobil kami
                </p>
            </div>
            
            <div class="max-w-3xl mx-auto">
                <!-- Item 1 -->
                <div class="faq-item bg-white mb-4 reveal">
                    <div class="faq-question p-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Apakah saya boleh menyetir ke luar kota dengan mobil sewaan?</h3>
                        <div class="faq-icon text-blue-600 transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="px-6 pb-6 text-gray-600">
                            <p>Ya, Anda boleh menyetir ke luar kota, namun pastikan memberi tahu pihak rental untuk keperluan asuransi dan pemantauan kendaraan. Kami juga menyarankan untuk melakukan pengecekan kendaraan sebelum perjalanan jauh.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Item 2 -->
                <div class="faq-item bg-white mb-4 reveal">
                    <div class="faq-question p-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Apa yang terjadi kalau mobil mogok di tengah perjalanan?</h3>
                        <div class="faq-icon text-blue-600 transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="px-6 pb-6 text-gray-600">
                            <p>Segera hubungi nomor darurat kami yang tersedia 24/7. Tim bantuan jalan kami siap memberikan support, termasuk penjemputan atau penggantian unit jika diperlukan. Semua mobil kami dilengkapi dengan asuransi bantuan jalan.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Item 3 -->
                <div class="faq-item bg-white mb-4 reveal">
                    <div class="faq-question p-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Bolehkah orang lain menyetir mobil yang saya sewa?</h3>
                        <div class="faq-icon text-blue-600 transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="px-6 pb-6 text-gray-600">
                            <p>Hanya pengemudi terdaftar yang diperbolehkan menyetir. Jika ingin menambahkan pengemudi tambahan, silakan informasikan saat pemesanan dengan melampirkan fotokopi SIM A yang masih berlaku. Biaya tambahan pengemudi Rp 50.000/hari.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Item 4 -->
                <div class="faq-item bg-white mb-4 reveal">
                    <div class="faq-question p-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Apa saya wajib punya SIM A aktif?</h3>
                        <div class="faq-icon text-blue-600 transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="px-6 pb-6 text-gray-600">
                            <p>Ya. SIM A yang masih berlaku minimal 1 tahun adalah syarat wajib untuk menyewa mobil demi alasan hukum dan keselamatan. Kami akan memverifikasi keabsahan SIM sebelum menyerahkan kendaraan.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Item 5 -->
                <div class="faq-item bg-white mb-4 reveal">
                    <div class="faq-question p-6 flex justify-between items-center">
                        <h3 class="text-lg font-semibold">Bagaimana jika saya perlu memperpanjang masa sewa?</h3>
                        <div class="faq-icon text-blue-600 transition-transform duration-300">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="faq-answer">
                        <div class="px-6 pb-6 text-gray-600">
                            <p>Perpanjangan masa sewa dapat dilakukan dengan menghubungi customer service kami minimal 6 jam sebelum masa sewa berakhir. Tarif perpanjangan mengikuti ketentuan yang berlaku dan ketersediaan mobil.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-16 bg-gradient-to-r from-blue-50 to-indigo-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold mb-4 text-gray-800">Apa Kata Pelanggan Kami</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Ribuan pelanggan telah mempercayakan perjalanan mereka bersama kami
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="testimonial-card bg-white p-8 reveal">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Budi Santoso" class="testimonial-image mr-4">
                        <div>
                            <h4 class="font-bold">Budi Santoso</h4>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Pelayanan sangat memuaskan! Mobilnya bersih dan terawat. Proses sewa cepat, hanya 15 menit sudah bisa bawa mobil. Pasti akan sewa lagi di sini."
                    </p>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="testimonial-card bg-white p-8 reveal">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Siti Rahayu" class="testimonial-image mr-4">
                        <div>
                            <h4 class="font-bold">Siti Rahayu</h4>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Sewa Avanza untuk liburan keluarga. Harganya terjangkau, mobilnya nyaman. Yang paling suka adalah layanan 24 jamnya, sangat membantu saat kami tanya-tanya via WhatsApp tengah malam."
                    </p>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="testimonial-card bg-white p-8 reveal">
                    <div class="flex items-center mb-6">
                        <img src="https://randomuser.me/api/portraits/men/67.jpg" alt="Agus Setiawan" class="testimonial-image mr-4">
                        <div>
                            <h4 class="font-bold">Agus Setiawan</h4>
                            <div class="flex text-yellow-400">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 italic">
                        "Baru pertama kali sewa mobil online dan pengalaman sangat baik. Mobil Fortuner yang saya sewa kondisi sangat bagus, seperti baru. Proses pengembalian juga mudah dan cepat."
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
        <div class="container mx-auto text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">Siap Memulai Perjalanan Anda?</h2>
            <p class="text-xl mb-10 max-w-2xl mx-auto">
                Dapatkan penawaran spesial untuk penyewaan pertama Anda. Diskon 15% dengan kode WELCOME15
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="mobil.php" class="bg-white text-blue-600 px-8 py-4 rounded-full text-lg font-bold hover:bg-blue-50 transition inline-flex items-center">
                    Sewa Mobil Sekarang <i class="fas fa-arrow-right ml-3"></i>
                </a>
                <a href="hubungi_kami.php" class="bg-transparent border-2 border-white text-white px-8 py-4 rounded-full text-lg font-bold hover:bg-white/10 transition inline-flex items-center">
                    <i class="fas fa-phone-alt mr-3"></i> Hubungi Kami
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="pt-16 pb-8 px-6 text-white">
        <div class="container mx-auto">
            <div class="footer-content grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <!-- Column 1 -->
                <div>
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-car mr-3"></i> RentalMobil.SG
                    </h3>
                    <p class="mb-6 text-blue-100">
                        Penyedia layanan sewa mobil terpercaya dengan armada terawat dan harga terjangkau. Melayani berbagai kebutuhan transportasi Anda.
                    </p>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/eben.ebenhaezerjs?mibextid=rS40aB7S9Ucbxw6v" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://www.instagram.com/stargod.id?igsh=MXhzMzh2bGZ4Zm93MA==#" class="social-icon">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="#" class="social-icon">
                            <i class="fab fa-youtube"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Column 2 -->
                <div>
                    <h3 class="text-xl font-bold mb-6">Layanan Kami</h3>
                    <ul class="space-y-3">
                        <li><a href="login.php" class="text-blue-100 hover:text-white transition">Sewa Harian</a></li>
                        <li><a href="login.php" class="text-blue-100 hover:text-white transition">Sewa Bulanan</a></li>
                        <li><a href="login.php" class="text-blue-100 hover:text-white transition">Sewa Dengan Sopir</a></li>
                    </ul>
                </div>
                
                <!-- Column 3 -->
                <div>
                    <h3 class="text-xl font-bold mb-6">Tautan Penting</h3>
                    <ul class="space-y-3">
                        <li><a href="s&k.php" class="text-blue-100 hover:text-white transition">Syarat & Ketentuan</a></li>
                        <li><a href="privasi.php" class="text-blue-100 hover:text-white transition">Kebijakan Privasi</a></li>
                        <li><a href="faq.php" class="text-blue-100 hover:text-white transition">FAQ</a></li>
                    </ul>
                </div>
                
                <!-- Column 4 -->
                <div>
                    <h3 class="text-xl font-bold mb-6">Hubungi Kami</h3>
                    <ul class="space-y-4 text-blue-100">
                        <li class="flex items-start">
                            <i class="fas fa-map-marker-alt mt-1 mr-3"></i>
                            <span>Pantai Indah Kapuk St Boulevard, Penjaringan, Kota Jakarta Utara</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone-alt mr-3"></i>
                            <span>+62 895-1359-5554</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-3"></i>
                            <span>cs@RentalMobil.SG</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-clock mr-3"></i>
                            <span>Buka Setiap Senin - Sabtu</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-gray-700 text-center text-blue-200">
                <p>&copy; <?php echo date("Y"); ?> RentalMobil.SG. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Back to top button -->
    <button id="backToTop" class="fixed bottom-8 right-8 bg-blue-600 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 transition opacity-0 invisible">
        <i class="fas fa-arrow-up"></i>
    </button>

    <script>
        // FAQ Toggle
        document.querySelectorAll('.faq-question').forEach(question => {
            question.addEventListener('click', () => {
                const faqItem = question.parentElement;
                faqItem.classList.toggle('active');
            });
        });
        
        // Back to top button
        const backToTopBtn = document.getElementById('backToTop');
        
        window.addEventListener('scroll', () => {
            if (window.scrollY > 300) {
                backToTopBtn.classList.remove('opacity-0', 'invisible');
                backToTopBtn.classList.add('opacity-100', 'visible');
            } else {
                backToTopBtn.classList.remove('opacity-100', 'visible');
                backToTopBtn.classList.add('opacity-0', 'invisible');
            }
        });
        
        backToTopBtn.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Scroll reveal animation
        function revealOnScroll() {
            const reveals = document.querySelectorAll('.reveal');
            
            for (let i = 0; i < reveals.length; i++) {
                const windowHeight = window.innerHeight;
                const elementTop = reveals[i].getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add('active');
                }
            }
        }
        
        window.addEventListener('scroll', revealOnScroll);
        // Initialize on page load
        window.addEventListener('load', revealOnScroll);
    </script>
</body>
</html>
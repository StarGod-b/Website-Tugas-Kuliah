<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami | RentalMobil.SG</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --olx-orange: #e67e22;
            --olx-orange-hover: #d35400;
            --olx-dark: #2c3e50;
            --olx-light:rgb(8, 177, 219);
        }
        
        .contact-card {
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .contact-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .btn-contact {
            background-color: var(--olx-orange);
            transition: all 0.3s ease;
        }
        
        .btn-contact:hover {
            background-color: var(--olx-orange-hover);
            transform: translateY(-2px);
        }
        
        .social-icon {
            transition: all 0.3s ease;
        }
        
        .social-icon:hover {
            transform: scale(1.1);
        }
        
        .map-container {
            transition: all 0.3s ease;
        }
        
        .map-container:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        
        .input-focus:focus {
            border-color: var(--olx-orange);
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.2);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadein {
            animation: fadeIn 0.6s ease-out forwards;
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
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 25px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            display: none;
            animation: slideIn 0.3s ease-out, fadeOut 0.5s ease-in 2.5s forwards;
        }
        
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Notifikasi -->
    <div class="notification" id="notification">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="notification-text">Pesan berhasil dikirim!</span>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md py-4 px-6 md:px-8 flex justify-between items-center">
        <h1 class="text-xl font-bold text-blue-600">RentalMobil.SG</h1>
        <a href="home.php" class="text-blue-500 hover:text-orange-700 font-medium flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </header>

    <!-- Main Content -->
    <main class="py-8 px-4 md:px-6 max-w-6xl mx-auto">
        <!-- Hero Section -->
        <section class="bg-gradient-to-r from-orange-500 to-orange-600 text-black rounded-xl p-8 mb-10 text-center animate-fadein">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Butuh Bantuan? Hubungi Kami</h1>
            <p class="text-lg opacity-90 max-w-2xl mx-auto">Tim kami siap membantu Anda 24/7 untuk semua pertanyaan tentang rental mobil</p>
        </section>

        <!-- Contact Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Contact Info -->
            <div class="space-y-6">
                <!-- Contact Cards -->
                <div class="contact-card bg-white p-6 rounded-lg animate-fadein delay-100">
                    <div class="flex items-start mb-4">
                        <div class="bg-orange-100 p-3 rounded-full mr-4">
                            <i class="fas fa-phone-alt text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Nomor Telepon</h3>
                            <p class="text-gray-600">+62 895-1359-5554</p>
                            <a href="tel:+6289513595554" class="mt-2 btn-contact text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-phone mr-2"></i> Telepon Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <div class="contact-card bg-white p-6 rounded-lg animate-fadein delay-200">
                    <div class="flex items-start mb-4">
                        <div class="bg-orange-100 p-3 rounded-full mr-4">
                            <i class="fas fa-envelope text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Email</h3>
                            <p class="text-gray-600">cs@RentalMobil.SG</p>
                            <a href="mailto:cs@RentalMobil.SG" class="mt-2 btn-contact text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                                <i class="fas fa-envelope mr-2"></i> Kirim Email
                            </a>
                        </div>
                    </div>
                </div>

                <div class="contact-card bg-white p-6 rounded-lg animate-fadein delay-300">
                    <div class="flex items-start">
                        <div class="bg-orange-100 p-3 rounded-full mr-4">
                            <i class="fas fa-map-marker-alt text-orange-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-gray-800">Alamat Kantor</h3>
                            <p class="text-gray-600 mb-2">Pantai Indah Kapuk St Boulevard, RT.6/RW.2, Kamal Muara, Penjaringan, North Jakarta City, Jakarta 14470</p>
                            <h4 class="font-semibold text-gray-800 mt-3">Jam Operasional</h4>
                            <p class="text-gray-600">Senin - Sabtu, 07.00 - 17.00 WIB</p>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="bg-white p-6 rounded-lg shadow mt-6 animate-fadein">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Media Sosial Kami</h3>
                    <div class="flex space-x-4">
                        <a href="https://www.facebook.com/eben.ebenhaezerjs?mibextid=rS40aB7S9Ucbxw6v" class="social-icon bg-blue-600 text-white p-3 rounded-full w-12 h-12 flex items-center justify-center">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="social-icon bg-blue-400 text-white p-3 rounded-full w-12 h-12 flex items-center justify-center">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://www.instagram.com/stargod.id?igsh=MXhzMzh2bGZ4Zm93MA==" class="social-icon bg-pink-600 text-white p-3 rounded-full w-12 h-12 flex items-center justify-center">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/6289513595554" class="social-icon bg-green-500 text-white p-3 rounded-full w-12 h-12 flex items-center justify-center">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form & Map -->
            <div class="space-y-6">
                <!-- Contact Form -->
                <div class="bg-white p-6 rounded-lg shadow animate-fadein delay-100">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Kirim Pesan</h3>
                    <form id="contactForm">
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                            <input type="text" id="name" class="w-full px-4 py-2 border rounded-lg input-focus focus:outline-none" placeholder="Masukkan nama Anda" required>
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" class="w-full px-4 py-2 border rounded-lg input-focus focus:outline-none" placeholder="Masukkan email Anda" required>
                        </div>
                        <div class="mb-4">
                            <label for="subject" class="block text-gray-700 font-medium mb-2">Subjek</label>
                            <select id="subject" class="w-full px-4 py-2 border rounded-lg input-focus focus:outline-none" required>
                                <option value="" disabled selected>Pilih subjek</option>
                                <option value="rental">Pertanyaan Rental</option>
                                <option value="payment">Pembayaran</option>
                                <option value="complaint">Keluhan</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="message" class="block text-gray-700 font-medium mb-2">Pesan</label>
                            <textarea id="message" rows="4" class="w-full px-4 py-2 border rounded-lg input-focus focus:outline-none" placeholder="Tulis pesan Anda..." required></textarea>
                        </div>
                        <button type="submit" class="btn-contact text-white px-6 py-3 rounded-lg font-medium w-full flex items-center justify-center">
                            <i class="fas fa-paper-plane mr-2"></i> Kirim Pesan
                        </button>
                    </form>
                </div>

                <!-- Map -->
                <div class="map-container bg-white p-4 rounded-lg shadow overflow-hidden animate-fadein delay-200">
                    <h3 class="font-bold text-lg text-gray-800 mb-4">Lokasi Kami</h3>
                    <div class="rounded-lg overflow-hidden h-64">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.879357601615!2d106.7373971147861!3d-6.110883395565735!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6a1d56cf86ed13%3A0x7e81b60ee12d04e5!2sPIK%20Avenue!5e0!3m2!1sid!2sid!4v1717573190326!5m2!1sid!2sid"
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade"
                            class="rounded-lg">
                        </iframe>
                    </div>
                    <div class="mt-4 flex justify-between items-center">
                        <a href="#" class="text-orange-600 font-medium flex items-center">
                            <i class="fas fa-directions mr-2"></i> Dapatkan Petunjuk
                        </a>
                        <a href="#" class="text-orange-600 font-medium flex items-center">
                            <i class="fas fa-share-alt mr-2"></i> Bagikan Lokasi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white text-center py-6 text-sm text-gray-500 mt-10 border-t">
        <div class="max-w-6xl mx-auto px-4">
            <p>&copy; 2024 RentalMobil.SG. All rights reserved.</p>
            <div class="mt-2 flex justify-center space-x-4">
                <a href="#" class="text-gray-500 hover:text-orange-600">Kebijakan Privasi</a>
                <a href="#" class="text-gray-500 hover:text-orange-600">Syarat & Ketentuan</a>
                <a href="#" class="text-gray-500 hover:text-orange-600">FAQ</a>
            </div>
        </div>
    </footer>

    <script>
        // Form submission handler
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('name').value;
            const email = document.getElementById('email').value;
            const subject = document.getElementById('subject').value;
            const message = document.getElementById('message').value;
            
            // Simulasi pengiriman email
            console.log(`Email dikirim ke cs@RentalMobil.SG`);
            console.log(`Dari: ${name} <${email}>`);
            console.log(`Subjek: ${subject}`);
            console.log(`Pesan: ${message}`);
            
            // Tampilkan notifikasi
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notification-text');
            
            notificationText.textContent = "Pesan berhasil dikirim ke cs@RentalMobil.SG!";
            notification.style.display = 'flex';
            
            // Reset form setelah pengiriman
            this.reset();
            
            // Sembunyikan notifikasi setelah beberapa detik
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        });
        
        // Animate elements when they come into view
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadein');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.contact-card, .map-container, form').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
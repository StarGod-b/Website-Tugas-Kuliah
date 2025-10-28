<?php
session_start();
require_once 'config/Database.php';
require_once 'classes/User.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Jika bukan admin, coba login sebagai user biasa
    $db = new Database();
    $conn = $db->connect();
    $user = new User($conn);
    
    if ($user->login($email, $password)) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animasi background
            const background = document.getElementById('background');
            const colors = ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981'];
            let currentIndex = 0;
            
            setInterval(() => {
                background.style.background = `linear-gradient(135deg, ${colors[currentIndex]} 0%, ${colors[(currentIndex + 1) % colors.length]} 100%)`;
                currentIndex = (currentIndex + 1) % colors.length;
            }, 5000);
            
            // Animasi form saat error
            <?php if ($error): ?>
                const loginForm = document.getElementById('login-form');
                loginForm.classList.add('animate-shake');
                setTimeout(() => {
                    loginForm.classList.remove('animate-shake');
                }, 1000);
            <?php endif; ?>
            
            // Toggle password visibility
            const passwordInput = document.getElementById('password');
            const togglePassword = document.getElementById('toggle-password');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            // Flip card animation
            const flipCard = document.getElementById('card-3d-inner');
            const flipBackBtn = document.getElementById('flip-back');
            const flipToBackBtn = document.getElementById('flip-to-back');
            
            // Flip to back when button clicked
            flipToBackBtn.addEventListener('click', () => {
                flipCard.classList.add('flipped');
            });
            
            // Flip back when button clicked
            flipBackBtn.addEventListener('click', () => {
                flipCard.classList.remove('flipped');
            });
            
            // Loading spinner on form submit
            const loginForm = document.getElementById('login-form');
            loginForm.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                submitBtn.disabled = true;
            });
        });
    </script>
    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }
        
        .animate-shake {
            animation: shake 0.5s ease-in-out;
        }
        
        .card-3d {
            transform-style: preserve-3d;
            perspective: 1000px;
            width: 100%;
            max-width: 420px;
        }
        
        .card-3d-inner {
            transform: rotateY(0deg);
            transition: transform 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            transform-style: preserve-3d;
            position: relative;
            width: 100%;
            height: 100%;
        }
        
        .flipped {
            transform: rotateY(180deg);
        }
        
        .card-front, .card-back {
            backface-visibility: hidden;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        
        .card-back {
            transform: rotateY(180deg);
        }
        
        .slide-in {
            animation: slideIn 0.5s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateY(50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            transition: background 2s ease-in-out;
        }
        
        /* Memastikan konten di tengah vertikal */
        .flex-center {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        
        /* Animasi tambahan untuk keterangan */
        .fade-in {
            animation: fadeIn 1s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Tombol flip khusus */
        .flip-btn {
            background: transparent;
            border: none;
            color: #3b82f6;
            font-weight: 600;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
            margin: 0;
            display: inline;
        }
        
        .flip-btn:hover {
            color: #2563eb;
        }
        
        /* Perbaikan tata letak */
        .card-content {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
        }
        
        /* Loading spinner */
        .fa-spin {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Back button styling */
        .back-btn {
            position: absolute;
            top: 1.5rem;
            left: 1.5rem;
            z-index: 10;
        }
        
        /* Logo styling */
        .logo-circle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            border-radius: 75%;
            background: rgba(255, 255, 255, 0.2);
            margin: 0 auto 1.5rem;
        }
        
        .logo-circle i {
            font-size: 2.5rem;
            color: white;
        }
        
        /* Footer styling */
        .footer-info {
            position: absolute;
            bottom: 1.5rem;
            left: 0;
            right: 0;
            text-align: bottom;
        }
        
        /* Responsive adjustments */
        @media (max-width: 480px) {
            .card-3d {
                max-width: 100%;
            }
            
            .back-btn {
                top: 1rem;
                left: 1rem;
            }
            
            .footer-info {
                bottom: 1rem;
                padding: 0 1rem;
            }
            
            .logo-circle {
                width: 70px;
                height: 70px;
            }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex-center p-4 relative" id="background">
    <div class="back-btn">
        <a href="home.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 shadow-md transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>

    <div class="card-3d">
        <div class="card-3d-inner" id="card-3d-inner">
            <!-- Sisi Depan: Form Login -->
            <div class="card-front">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden slide-in">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                        <h2 class="text-2xl font-bold text-center text-white">Masuk ke Akun Anda</h2>
                    </div>
                    
                    <div class="p-6 md:p-8">
                        <?php if ($error): ?>
                            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-pulse" role="alert">
                                <p class="font-bold">Peringatan</p>
                                <p><?= $error ?></p>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="login-form">
                            <div class="mb-5">
                                <label class="block text-gray-700 mb-2 font-medium">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Email Anda" required>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="block text-gray-700 mb-2 font-medium">Password</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-lock text-gray-400"></i>
                                    </div>
                                    <input type="password" id="password" name="password" class="w-full pl-10 pr-10 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Password" required>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <i id="toggle-password" class="fas fa-eye text-gray-400 cursor-pointer hover:text-blue-500"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center">
                                    <input id="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="remember" class="ml-2 block text-sm text-gray-700">
                                        Ingat Saya
                                    </label>
                                </div>
                                <a href="#" class="text-sm text-blue-600 hover:underline">Lupa Password?</a>
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-800 transition duration-300 shadow-md transform hover:scale-[1.02] flex items-center justify-center">
                                <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                            </button>
                        </form>

                        <div class="mt-6 border-t border-gray-200 pt-6">
                            <div class="flex justify-center space-x-4 mt-4">
                                <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow flex items-center">
                                    <i class="fab fa-google text-red-600 mr-2"></i> Google
                                </button>
                                <button class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow flex items-center">
                                    <i class="fab fa-facebook text-blue-600 mr-2"></i> Facebook
                                </button>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Belum punya akun? 
                                <a href="register.php" class="text-blue-600 font-semibold hover:underline">Daftar Sekarang</a>
                            </p>
                            <p class="mt-3 text-sm text-gray-600">
                                Atau <button id="flip-to-back" class="flip-btn text-blue-600 hover:text-blue-800">lihat informasi lainnya</button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sisi Belakang: Informasi Selamat Datang -->
            <div class="card-back">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl shadow-xl p-6 md:p-8 h-full card-content">
                    <div>
                        <div class="logo-circle">
                            <i class="fas fa-car"></i>
                        </div>
                        
                        <h2 class="text-xl md:text-2xl font-bold mb-4 text-center text-white">Selamat Datang di Rental Mobil</h2>
                        <p class="mb-6 fade-in text-center text-white text-sm md:text-base">Nikmati pengalaman menyewa mobil dengan proses yang mudah dan cepat</p>
                        
                        <div class="w-full max-w-xs mx-auto">
                            <div class="bg-white bg-opacity-20 rounded-lg p-4 mb-6">
                                <h3 class="font-bold mb-3 text-white text-center">Mengapa memilih kami?</h3>
                                <ul class="text-left text-sm text-white space-y-2">
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle mr-2 mt-1 text-green-300"></i> 
                                        <span>Proses sewa mudah dan cepat</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle mr-2 mt-1 text-green-300"></i> 
                                        <span>Harga kompetitif</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle mr-2 mt-1 text-green-300"></i> 
                                        <span>Mobil terawat</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check-circle mr-2 mt-1 text-green-300"></i> 
                                        <span>Layanan pelanggan 24 jam</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-center w-full">
                        <p class="text-sm mb-4 text-white text-center">Belum memiliki akun? Daftar sekarang untuk mulai menyewa!</p>
                        <a href="register.php" class="w-full max-w-[200px] bg-white text-indigo-600 py-2 px-6 rounded-lg font-semibold hover:bg-opacity-90 transition duration-300 mb-4 text-center">
                            Daftar Sekarang
                        </a>
                        
                        <button id="flip-back" class="flip-btn text-white hover:text-blue-200 mt-2">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
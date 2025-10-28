<?php
session_start();
include 'config/Database.php';
include 'classes/User.php';

$db = new Database();
$conn = $db->connect();
$user = new User($conn);

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi input
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } elseif ($password !== $confirm_password) {
        $error = "Konfirmasi password tidak sesuai!";
    } elseif ($email === 'admin@rental.com') {
        $error = "Registrasi admin tidak diizinkan.";   
    } else {
        $result = $user->register($name, $email, $password);
        
        if ($result === true) {
            $_SESSION['register_success'] = true;
            $success = "Registrasi berhasil! Anda akan dialihkan ke halaman login.";
            
            // Set timer untuk redirect
            header("refresh:3;url=login.php");
        } else {
            $error = $result;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun | Rental Mobil</title>
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
                const registerForm = document.getElementById('register-form');
                registerForm.classList.add('animate-shake');
                setTimeout(() => {
                    registerForm.classList.remove('animate-shake');
                }, 1000);
            <?php endif; ?>
            
            // Toggle password visibility
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirm_password');
            const togglePassword = document.getElementById('toggle-password');
            const toggleConfirmPassword = document.getElementById('toggle-confirm-password');
            
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
            
            toggleConfirmPassword.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
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
        }
        
        .card-3d-inner {
            transform: rotateY(0deg);
            transition: transform 0.8s;
            transform-style: preserve-3d;
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
        
        .success-checkmark {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        
        .success-checkmark i {
            font-size: 40px;
            color: white;
        }
        
        .progress-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 3px;
            overflow: hidden;
            margin-top: 20px;
        }
        
        .progress {
            height: 100%;
            background: #10b981;
            width: 100%;
            animation: progress 3s linear forwards;
        }
        
        @keyframes progress {
            0% { width: 100%; }
            100% { width: 0%; }
        }
    </style>
</head>
<body class="gradient-bg min-h-screen flex items-center justify-center p-4" id="background">
    <div class="absolute top-4 left-4">
        <a href="home.php" class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-blue-50 shadow-md transition duration-300 flex items-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Beranda
        </a>
    </div>

    <div class="w-full max-w-md card-3d">
        <div class="card-3d-inner">
            <div class="card-front">
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden slide-in">
                    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6">
                        <h2 class="text-2xl font-bold text-center text-white">Daftar Akun Baru</h2>
                    </div>
                    
                    <div class="p-8">
                        <?php if ($success): ?>
                            <div class="text-center py-6">
                                <div class="success-checkmark">
                                    <i class="fas fa-check"></i>
                                </div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2">Registrasi Berhasil!</h3>
                                <p class="text-gray-600">Anda akan dialihkan ke halaman login dalam 3 detik.</p>
                                
                                <div class="progress-bar">
                                    <div class="progress"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($error): ?>
                                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-pulse" role="alert">
                                    <p class="font-bold">Peringatan</p>
                                    <p><?= htmlspecialchars($error) ?></p>
                                </div>
                            <?php endif; ?>

                            <form method="post" action="" id="register-form">
                                <div class="mb-4">
                                    <label class="block text-gray-700 mb-2 font-medium">Nama Lengkap</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-user text-gray-400"></i>
                                        </div>
                                        <input type="text" name="name" value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '' ?>" 
                                            class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                            placeholder="Nama lengkap Anda" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 mb-2 font-medium">Email</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-envelope text-gray-400"></i>
                                        </div>
                                        <input type="email" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                                            class="w-full pl-10 pr-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                            placeholder="Email Anda" required>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-gray-700 mb-2 font-medium">Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" id="password" name="password" 
                                            class="w-full pl-10 pr-10 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                            placeholder="Password" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i id="toggle-password" class="fas fa-eye text-gray-400 cursor-pointer hover:text-blue-500"></i>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
                                </div>
                                
                                <div class="mb-6">
                                    <label class="block text-gray-700 mb-2 font-medium">Konfirmasi Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-lock text-gray-400"></i>
                                        </div>
                                        <input type="password" id="confirm_password" name="confirm_password" 
                                            class="w-full pl-10 pr-10 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                            placeholder="Konfirmasi password" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i id="toggle-confirm-password" class="fas fa-eye text-gray-400 cursor-pointer hover:text-blue-500"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-800 transition duration-300 shadow-md transform hover:scale-[1.02]">
                                    Daftar
                                </button>
                            </form>
                        <?php endif; ?>

                        <div class="mt-6 text-center">
                            <p class="text-sm text-gray-600">
                                Sudah punya akun? 
                                <a href="login.php" class="text-blue-600 font-semibold hover:underline">Masuk di sini</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$nama = $user['name'];
$role = $user['role'];

// Database connection
include 'config/Database.php';
$db = new Database();
$conn = $db->connect();

$statistics = [];

// Count users (hanya untuk admin)
if ($role === 'admin') {
    $stmt = $conn->query("SELECT COUNT(*) AS total_users FROM users");
    $statistics['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'] ?? 0;
    
    $stmt = $conn->query("SELECT COUNT(*) AS total_cars FROM mobil");
    $statistics['cars'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_cars'] ?? 0;
}

// Count active rentals (gunakan status 'confirmed' atau sesuai kebutuhan)
try {
    $stmt = $conn->query("SELECT COUNT(*) AS active_rentals FROM sewa WHERE status = 'confirmed'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $statistics['rentals'] = $result['active_rentals'] ?? 0;
} catch (Exception $e) {
    $statistics['rentals'] = 0;
}

// Count revenue (ambil dari tabel sewa, status 'completed')
try {
    $stmt = $conn->query("SELECT SUM(total_harga) AS total_revenue FROM sewa WHERE status = 'completed'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $statistics['revenue'] = $result['total_revenue'] ?? 0;
} catch (Exception $e) {
    $statistics['revenue'] = 0;
}
// Get recent cars for user dashboard
if ($role === 'user') {
    $stmt = $conn->prepare("SELECT * FROM mobil ORDER BY created_at DESC LIMIT 3");
    $stmt->execute();
    $recent_cars = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RentalMobil.SG</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0ea5e9;
            --accent: #f97316;
            --light: #f8fafc;
            --dark: #0f172a;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 1px 3px rgba(0, 0, 0, 0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f3f4f6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 1.5rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo i {
            margin-right: 0.8rem;
            color: #ffd700;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }
        
        .dashboard-container {
            flex: 1;
            padding: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            width: 100%;
        }
        
        .welcome-banner {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 1rem;
            padding: 2rem;
            color: white;
            margin-bottom: 2rem;
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }
        
        .welcome-banner h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }
        
        .welcome-banner p {
            font-size: 1.1rem;
            max-width: 600px;
            position: relative;
            z-index: 2;
        }
        
        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 1.5rem 0;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .section-title i {
            color: var(--accent);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
        }
        
        .stat-content {
            flex: 1;
        }
        
        .stat-title {
            font-size: 1rem;
            color: #64748b;
            margin-bottom: 0.3rem;
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .menu-card {
            background: white;
            border-radius: 1rem;
            padding: 1.8rem;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
            border-left: 4px solid var(--accent);
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .menu-card i {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 1.2rem;
        }
        
        .menu-card h3 {
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 0.8rem;
            color: var(--dark);
        }
        
        .menu-card p {
            color: #64748b;
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }
        
        .menu-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.6rem 1.2rem;
            background: var(--primary);
            color: white;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            width: fit-content;
        }
        
        .menu-btn:hover {
            background: var(--primary-dark);
            transform: translateX(5px);
        }
        
        .menu-btn i {
            font-size: 1rem;
            color: white;
            margin-left: 0.5rem;
            margin-bottom: 0;
        }
        
        .car-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }
        
        .car-card {
            background: white;
            border-radius: 1rem;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s;
        }
        
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .car-image {
            height: 180px;
            position: relative;
        }
        
        .car-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .car-content {
            padding: 1.2rem;
        }
        
        .car-title {
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        
        .car-price {
            color: var(--primary);
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .car-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }
        
        .car-status {
            padding: 0.3rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .status-available {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }
        
        .status-rented {
            background: rgba(239, 68, 68, 0.15);
            color: #ef4444;
        }
        
        .car-btn {
            padding: 0.4rem 0.8rem;
            background: var(--primary);
            color: white;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
            
            .user-info {
                flex-direction: column;
            }
            
            .welcome-banner h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="logo">
            <i class="fas fa-car"></i>
            <span>RentalMobil.SG</span>
        </div>
        
        <div class="user-info">
            <div class="user-avatar">
                <?= strtoupper(substr($nama, 0, 1)) ?>
            </div>
            <div>
                <div class="font-semibold"><?= htmlspecialchars($nama) ?></div>
                <div class="text-sm opacity-80"><?= $role ?></div>
            </div>
            <a href="logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </header>

    <div class="dashboard-container">
        <div class="welcome-banner">
            <h1>Selamat datang, <?= htmlspecialchars($nama) ?>!</h1>
            <p>Anda login sebagai <?= $role ?>. Gunakan dashboard ini untuk mengelola aktivitas Anda.</p>
        </div>

        <?php if ($role === 'admin'): ?>
            <div class="section-title">
                <i class="fas fa-chart-line"></i>
                <h2>Statistik Sistem</h2>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(37, 99, 235, 0.1); color: var(--primary);">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Total Pengguna</div>
                        <div class="stat-value"><?= $statistics['users'] ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                        <i class="fas fa-car"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Total Mobil</div>
                        <div class="stat-value"><?= $statistics['cars'] ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(249, 115, 22, 0.1); color: var(--accent);">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Sewa Aktif</div>
                        <div class="stat-value"><?= $statistics['rentals'] ?></div>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-title">Pendapatan</div>
                        <div class="stat-value">Rp<?= number_format($statistics['revenue']) ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="section-title">
            <i class="fas fa-th-large"></i>
            <h2>Menu Utama</h2>
        </div>
        
        <div class="menu-grid">
            <?php if ($role === 'admin'): ?>
                <div class="menu-card">
                    <i class="fas fa-user-cog"></i>
                    <h3>Kelola Pengguna</h3>
                    <p>Lihat, edit, dan hapus data pengguna sistem. Kelola peran dan akses pengguna.</p>
                    <a href="admin_users.php" class="menu-btn">
                        Buka <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="menu-card">
                    <i class="fas fa-car"></i>
                    <h3>Kelola Mobil</h3>
                    <p>Tambahkan, edit, atau hapus data mobil. Kelola ketersediaan dan status mobil.</p>
                    <a href="admin_mobil.php" class="menu-btn">
                        Buka <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="menu-card">
                    <i class="fas fa-file-invoice-dollar"></i>
                    <h3>Lihat Transaksi</h3>
                    <p>Pantau semua transaksi penyewaan. Verifikasi pembayaran dan kelola status.</p>
                    <a href="admin_transaksi.php" class="menu-btn">
                        Buka <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="menu-card">
                    <i class="fas fa-chart-pie"></i>
                    <h3>Laporan & Analisis</h3>
                    <p>Akses laporan keuangan dan analisis kinerja sistem. Ekspor data untuk analisis lebih lanjut.</p>
                    <a href="admin_reports.php" class="menu-btn">
                        Buka <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php else: ?>
                <div class="menu-card">
                    <i class="fas fa-car"></i>
                    <h3>Lihat Mobil Tersedia</h3>
                    <p>Jelajahi koleksi mobil kami dan temukan yang sesuai dengan kebutuhan perjalanan Anda.</p>
                    <a href="mobil.php" class="menu-btn">
                        Jelajahi <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="menu-card">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>Sewa Saya</h3>
                    <p>Lihat status penyewaan Anda, riwayat sewa, dan kelola pemesanan aktif.</p>
                    <a href="sewa_saya.php" class="menu-btn">
                        Lihat <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="menu-card">
                    <i class="fas fa-headset"></i>
                    <h3>Hubungi Kami</h3>
                    <p>Butuh bantuan? Hubungi tim dukungan kami untuk pertanyaan atau masalah teknis.</p>
                    <a href="hubungi_kami2.php" class="menu-btn">
                        Hubungi <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                
                <div class="menu-card">
                    <i class="fas fa-user-circle"></i>
                    <h3>Profil Saya</h3>
                    <p>Kelola informasi akun Anda, perbarui detail profil, dan ubah kata sandi.</p>
                    <a href="profil.php" class="menu-btn">
                        Kelola <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($role === 'user' && isset($recent_cars)): ?>
            <div class="section-title">
                <i class="fas fa-bolt"></i>
                <h2>Mobil Terbaru</h2>
            </div>
            
            <div class="car-grid">
                <?php foreach ($recent_cars as $car): ?>
                    <?php 
                    $status_raw = strtolower(trim($car['status'] ?? ''));
                    $is_available = ($status_raw === 'available' || $status_raw === 'tersedia');
                    ?>
                    
                    <div class="car-card">
                        <div class="car-image">
                            <img src="<?= htmlspecialchars($car['image']) ?>" alt="<?= htmlspecialchars($car['brand']) ?>">
                        </div>
                        <div class="car-content">
                            <h3 class="car-title"><?= htmlspecialchars($car['brand']) ?> <?= htmlspecialchars($car['model']) ?></h3>
                            <p class="car-price">Rp<?= number_format($car['price_per_day']) ?>/hari</p>
                            
                            <div class="car-footer">
                                <span class="car-status <?= $is_available ? 'status-available' : 'status-rented' ?>">
                                    <?= $is_available ? 'Tersedia' : 'Disewa' ?>
                                </span>
                                <a href="sewa.php?id=<?= $car['id'] ?>" class="car-btn">
                                    Sewa
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Simple animation for cards on load
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.menu-card, .stat-card, .car-card');
            
            cards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
<?php
session_start();
include 'config/Database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

$user_id = $_SESSION['user']['id'];

// Ambil data penyewaan mobil milik user
$query = "SELECT s.id, s.user_id, s.mobil_id, 
                 s.tanggal_mulai AS start_date, 
                 s.tanggal_selesai AS end_date,
                 s.status AS rental_status,
                 s.created_at,
                 s.total_harga,
                 s.rental_days,
                 m.brand, m.model, m.image 
          FROM sewa s 
          INNER JOIN mobil m ON s.mobil_id = m.id 
          WHERE s.user_id = :user_id 
          ORDER BY s.tanggal_mulai DESC";

$stmt = $conn->prepare($query);
$stmt->execute(['user_id' => $user_id]);
$sewas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total pengeluaran user
$total_pengeluaran = 0;
foreach ($sewas as $s) {
    if ($s['rental_status'] == 'completed' || $s['rental_status'] == 'confirmed') {
        $total_pengeluaran += $s['total_harga'];
    }
}

// Status logic
function getStatus($rental_status) {
    switch ($rental_status) {
        case 'confirmed':
            return 'Sedang Dipakai';
        case 'completed':
            return 'Selesai';
        case 'cancelled':
        case 'rejected':
            return 'Dibatalkan';
        case 'awaiting_confirmation':
            return 'Menunggu Konfirmasi Pembayaran';
        case 'pending':
        default:
            return 'Menunggu Konfirmasi';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Sewa Saya</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            padding: 20px;
            color: #333;
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
        }
        
        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e0e0e0;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        h1 {
            color: #2c3e50;
            font-size: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 8px 15px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .back-btn:hover {
            background-color: #2980b9;
        }
        
        .back-btn i {
            margin-right: 5px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #3498db, #8e44ad);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .summary-item {
            text-align: center;
            padding: 0 15px;
        }
        
        .summary-value {
            font-size: 28px;
            font-weight: bold;
            margin: 5px 0;
        }
        
        .summary-label {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .divider {
            height: 50px;
            width: 1px;
            background: rgba(255,255,255,0.3);
        }
        
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 8px 16px;
            border-radius: 20px;
            background: white;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
        
        .card {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .rental-id {
            font-weight: bold;
            color: #7f8c8d;
        }
        
        .order-date {
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .car-info {
            display: flex;
            margin-bottom: 15px;
            gap: 20px;
        }
        
        .car-image {
            width: 180px;
            height: 120px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .car-details {
            flex: 1;
        }
        
        .car-details h3 {
            font-size: 20px;
            margin-bottom: 5px;
            color: #2c3e50;
        }
        
        .car-meta {
            display: flex;
            gap: 15px;
            margin: 10px 0;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .rental-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 15px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .info-group {
            display: flex;
            flex-direction: column;
        }
        
        .info-label {
            font-size: 14px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        
        .info-value {
            font-weight: bold;
            font-size: 16px;
            color: #2c3e50;
        }
        
        .price-value {
            color: #27ae60;
            font-size: 18px;
        }
        
        .status-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-selesai {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-berlangsung {
            background-color: #e3f2fd;
            color: #1565c0;
        }
        
        .status-pending {
            background-color: #fff3e0;
            color: #ef6c00;
        }
        
        .status-cancelled {
            background-color: #ffebee;
            color: #c62828;
        }
        
        .status-awaiting {
            background-color: #e0f7fa;
            color: #00838f;
        }
        
        .btn-end {
            background: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .btn-end:hover {
            background: #c0392b;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .empty-state i {
            font-size: 60px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            color: #7f8c8d;
            margin-bottom: 10px;
        }
        
        @media (max-width: 768px) {
            .car-info {
                flex-direction: column;
            }
            
            .car-image {
                width: 100%;
                height: 180px;
            }
            
            .summary-card {
                flex-direction: column;
                gap: 20px;
            }
            
            .divider {
                height: 1px;
                width: 100%;
            }
            
            .status-container {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }
        
        @media (max-width: 480px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .rental-info {
                grid-template-columns: 1fr;
            }
            
            .summary-item {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <h1><i class="fas fa-history"></i> Riwayat Sewa Saya</h1>
                <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
            </div>
        </header>

        <!-- Summary Card -->
        <div class="summary-card">
            <div class="summary-item">
                <div class="summary-value"><?= count($sewas) ?></div>
                <div class="summary-label">Total Sewa</div>
            </div>
            <div class="divider"></div>
            <div class="summary-item">
                <div class="summary-value">Rp<?= number_format($total_pengeluaran, 0, ',', '.') ?></div>
                <div class="summary-label">Total Pengeluaran</div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filters">
            <button class="filter-btn active">Semua</button>
            <button class="filter-btn">Sedang Dipakai</button>
            <button class="filter-btn">Selesai</button>
            <button class="filter-btn">Menunggu Konfirmasi</button>
            <button class="filter-btn">Dibatalkan</button>
        </div>

        <?php if (empty($sewas)): ?>
            <div class="empty-state">
                <i class="fas fa-car-alt"></i>
                <h3>Belum Ada Riwayat Sewa</h3>
                <p>Anda belum pernah menyewa mobil</p>
            </div>
        <?php else: ?>
            <?php foreach ($sewas as $s): ?>
                <?php 
                    $status = getStatus($s['rental_status']);
                    $statusClass = match($s['rental_status']) {
                        'completed' => 'status-selesai',
                        'confirmed' => 'status-berlangsung',
                        'cancelled' => 'status-cancelled',
                        'rejected' => 'status-cancelled',
                        'awaiting_confirmation' => 'status-awaiting',
                        default => 'status-pending'
                    };
                    
                    $statusIcon = match($s['rental_status']) {
                        'completed' => 'fa-check-circle',
                        'confirmed' => 'fa-car',
                        'cancelled' => 'fa-times-circle',
                        'rejected' => 'fa-times-circle',
                        'awaiting_confirmation' => 'fa-clock',
                        default => 'fa-hourglass-half'
                    };
                ?>
                <div class="card" data-status="<?= $s['rental_status'] ?>">
                    <div class="card-header">
                        <div class="rental-id">ID Sewa: #<?= htmlspecialchars($s['id']) ?></div>
                        <div class="order-date">Tanggal Pesan: <?= date('d M Y', strtotime($s['created_at'])) ?></div>
                    </div>
                    
                    <div class="car-info">
                        <img src="<?= htmlspecialchars($s['image'] ?: 'images/default-car.jpg') ?>" class="car-image">
                        <div class="car-details">
                            <h3><?= htmlspecialchars($s['brand'] . ' ' . $s['model']) ?></h3>
                            
                            <div class="car-meta">
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>Mulai: <?= date('d M Y', strtotime($s['start_date'])) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-calendar-check"></i>
                                    <span>Selesai: <?= date('d M Y', strtotime($s['end_date'])) ?></span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?= $s['rental_days'] ?> hari</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="rental-info">
                        <div class="info-group">
                            <div class="info-label">Total Harga</div>
                            <div class="info-value price-value">Rp<?= number_format($s['total_harga'], 0, ',', '.') ?></div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Status Sewa</div>
                            <div class="status <?= $statusClass ?>">
                                <i class="fas <?= $statusIcon ?>"></i> <?= $status ?>
                            </div>
                        </div>
                        <div class="info-group">
                            <div class="info-label">Lama Sewa</div>
                            <div class="info-value"><?= $s['rental_days'] ?> hari</div>
                        </div>
                    </div>
                    
                    <div class="status-container">
                        <?php if ($s['rental_status'] === 'confirmed'): ?>
                            <form method="POST" action="akhiri_sewa.php" onsubmit="return confirm('Yakin ingin mengakhiri sewa ini? Pastikan mobil sudah dikembalikan.');">
                                <input type="hidden" name="sewa_id" value="<?= $s['id'] ?>">
                                <button type="submit" class="btn-end"><i class="fas fa-flag-checkered"></i> Akhiri Sewa</button>
                            </form>
                        <?php endif; ?>
                        
                        <a href="#" class="back-btn"><i class="fas fa-file-invoice"></i> Detail Sewa</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    
    <script>
        // Filter functionality
        document.addEventListener('DOMContentLoaded', function() {
            const filterButtons = document.querySelectorAll('.filter-btn');
            const rentalCards = document.querySelectorAll('.card');
            
            filterButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove active class from all buttons
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to clicked button
                    this.classList.add('active');
                    
                    const filterValue = this.textContent.toLowerCase();
                    
                    // Show all cards if "Semua" is selected
                    if (filterValue === 'semua') {
                        rentalCards.forEach(card => card.style.display = 'block');
                        return;
                    }
                    
                    // Map filter text to status values
                    const statusMap = {
                        'sedang dipakai': 'confirmed',
                        'selesai': 'completed',
                        'menunggu konfirmasi': 'pending',
                        'dibatalkan': 'cancelled'
                    };
                    
                    const statusValue = statusMap[filterValue] || '';
                    
                    // Filter cards
                    rentalCards.forEach(card => {
                        const cardStatus = card.getAttribute('data-status');
                        
                        if (statusValue === cardStatus) {
                            card.style.display = 'block';
                        } else {
                            card.style.display = 'none';
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
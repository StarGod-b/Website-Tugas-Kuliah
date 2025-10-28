<?php
session_start();
include 'config/Database.php';

$db = new Database();
$conn = $db->connect();

// Check if delete action is requested
if (isset($_GET['delete']) && isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM mobil WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin_mobil.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM mobil ORDER BY created_at DESC");
$stmt->execute();
$mobils = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mobil Tersedia | RentalMobil.SG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --secondary: #0ea5e9;
            --accent: #f97316;
            --light: #f8fafc;
            --dark: #0f172a;
            --success: #10b981;
            --danger: #ef4444;
            --gray: #94a3b8;
            --card-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        }
        
        body {
            background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
            color: var(--dark);
            min-height: 100vh;
            padding-bottom: 40px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 20px 5%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        .logo i {
            margin-right: 10px;
            color: #ffd700;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .btn-primary {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn-primary:hover {
            background: #f1f5f9;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 5%;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }
        
        .page-title h1 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 10px;
        }
        
        .page-title p {
            color: var(--gray);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .page-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--accent);
            margin: 20px auto;
            border-radius: 2px;
        }
        
        .filters {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .filter-group {
            flex: 1;
            min-width: 200px;
        }
        
        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .filter-group select, .filter-group input {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        .card-image {
            height: 200px;
            position: relative;
            overflow: hidden;
        }
        
        .card-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .card:hover .card-image img {
            transform: scale(1.05);
        }
        
        .card-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: var(--accent);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 2;
        }
        
        .card-content {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .card-title {
            font-size: 1.4rem;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .card-subtitle {
            color: var(--gray);
            font-size: 1rem;
            margin-bottom: 15px;
        }
        
        .card-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .detail-item {
            display: flex;
            align-items: center;
        }
        
        .detail-item i {
            color: var(--primary);
            margin-right: 8px;
            width: 20px;
            text-align: center;
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .price span {
            font-size: 1rem;
            font-weight: normal;
            color: var(--gray);
        }
        
        .status {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .status-available {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
        }
        
        .status-rented {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
        }
        
        .card-footer {
            margin-top: auto;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn-rent {
            padding: 10px 20px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-rent:hover {
            background: var(--primary-dark);
        }
        
        .btn-disabled {
            padding: 10px 20px;
            background: #cbd5e1;
            color: #64748b;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: not-allowed;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .added-date {
            font-size: 0.85rem;
            color: var(--gray);
        }
        
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
        }
        
        .empty-state i {
            font-size: 5rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .empty-state p {
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto 30px;
        }
        
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .header-actions {
                width: 100%;
                justify-content: center;
            }
            
            .filters {
                flex-direction: column;
            }
            
            .page-title h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-car"></i>
                <span>RentalMobil.SG</span>
            </div>
            <div class="header-actions">
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                </a>
                <?php if (isset($_SESSION['user'])): ?>
                    <a href="logout.php" class="btn btn-primary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>Temukan Mobil Impian Anda</h1>
            <p>Pilih dari berbagai mobil berkualitas tinggi dengan harga terbaik untuk kebutuhan perjalanan Anda</p>
        </div>
        
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
            <div class="admin-controls">
                <a href="tambah_mobil.php" class="add-car-btn">
                    <i class="fas fa-plus"></i> Tambah Mobil Baru
                </a>
            </div>
        <?php endif; ?>
        
        <div class="filters">
            <div class="filter-group">
                <label for="brand-filter"><i class="fas fa-car"></i> Merek Mobil</label>
                <select id="brand-filter">
                    <option value="all">Semua Merek</option>
                    <option value="Toyota">Toyota</option>
                    <option value="Honda">Honda</option>
                    <option value="Suzuki">Suzuki</option>
                    <option value="Mitsubishi">Mitsubishi</option>
                    <option value="Daihatsu">Daihatsu</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="type-filter"><i class="fas fa-tag"></i> Tipe Mobil</label>
                <select id="type-filter">
                    <option value="all">Semua Tipe</option>
                    <option value="SUV">SUV</option>
                    <option value="Sedan">Sedan</option>
                    <option value="MPV">MPV</option>
                    <option value="Hatchback">Hatchback</option>
                    <option value="Sport">Sport</option>
                    <option value="LCGC">LCGC</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="price-filter"><i class="fas fa-tags"></i> Rentang Harga</label>
                <select id="price-filter">
                    <option value="all">Semua Harga</option>
                    <option value="low">Rp 300.000 - Rp 500.000/hari</option>
                    <option value="medium">Rp 500.000 - Rp 800.000/hari</option>
                    <option value="high">> Rp 800.000/hari</option>
                </select>
            </div>
            
            <div class="filter-group">
                <label for="status-filter"><i class="fas fa-check-circle"></i> Status</label>
                <select id="status-filter">
                    <option value="all">Semua Status</option>
                    <option value="available">Tersedia</option>
                    <option value="rented">Disewa</option>
                </select>
            </div>
        </div>
        
        <div class="grid" id="car-grid">
            <?php if (count($mobils) > 0): ?>
                <?php foreach ($mobils as $mobil): ?>
                    <?php 
                    $status_raw = strtolower(trim($mobil['status'] ?? ''));
                    $is_available = ($status_raw === 'available' || $status_raw === 'tersedia');
                    ?>
                    
                    <div class="card" data-brand="<?= htmlspecialchars($mobil['brand']) ?>" 
                         data-type="<?= htmlspecialchars($mobil['type'] ?? 'SUV') ?>" 
                         data-price="<?= $mobil['price_per_day'] ?>" 
                         data-status="<?= $is_available ? 'available' : 'rented' ?>">
                        <div class="card-image">
                            <img src="<?= htmlspecialchars($mobil['image']) ?>" alt="<?= htmlspecialchars($mobil['brand']) ?> <?= htmlspecialchars($mobil['model']) ?>">
                            <?php if ($is_available): ?>
                                <div class="card-badge">TERSEDIA</div>
                            <?php else: ?>
                                <div class="card-badge" style="background: var(--danger);">DISEWA</div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-content">
                            <h3 class="card-title"><?= htmlspecialchars($mobil['brand']) ?> <?= htmlspecialchars($mobil['model']) ?></h3>
                            <p class="card-subtitle"><?= htmlspecialchars($mobil['type'] ?? 'SUV') ?></p>
                            
                            <div class="card-details">
                                <div class="detail-item">
                                    <i class="fas fa-gas-pump"></i>
                                    <span><?= htmlspecialchars($mobil['fuel_type'] ?? 'Bensin') ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-car"></i>
                                    <span><?= htmlspecialchars($mobil['transmission'] ?? 'Automatic') ?></span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-chair"></i>
                                    <span><?= htmlspecialchars($mobil['seats'] ?? '5') ?> Kursi</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-road"></i>
                                    <span><?= number_format($mobil['mileage'] ?? 0) ?> km</span>
                                </div>
                            </div>
                            
                            <div class="price">Rp<?= number_format($mobil['price_per_day']) ?> <span>/hari</span></div>
                            
                            <div class="status <?= $is_available ? 'status-available' : 'status-rented' ?>">
                                <i class="fas fa-circle"></i> 
                                <?= $is_available ? 'Tersedia untuk disewa' : 'Sedang disewa' ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                                <div class="admin-actions">
                                    <a href="edit_mobil.php?id=<?= $mobil['id'] ?>" class="btn-edit">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <a href="mobil.php?delete=<?= $mobil['id'] ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus mobil ini?')">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="card-footer">
                            <?php if ($is_available): ?>
                                <?php if (isset($_SESSION['user'])): ?>
                                    
                                <?php else: ?>
                                    <a href="login.php" class="btn-rent">
                                        <i class="fas fa-sign-in-alt"></i> Login untuk Sewa
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn-disabled">
                                    <i class="fas fa-times-circle"></i> Tidak Tersedia
                                </button>
                            <?php endif; ?>
                            
                            <div class="added-date">
                                <i class="fas fa-calendar-plus"></i> 
                                <?= date('d M Y', strtotime($mobil['created_at'])) ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-car-alt"></i>
                    <h3>Tidak Ada Mobil Tersedia</h3>
                    <p>Maaf, saat ini tidak ada mobil yang tersedia untuk disewa. Silakan cek kembali nanti atau hubungi kami untuk informasi lebih lanjut.</p>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin'): ?>
                        <a href="tambah_mobil.php" class="add-car-btn" style="display: inline-flex; width: auto;">
                            <i class="fas fa-plus"></i> Tambah Mobil Baru
                        </a>
                    <?php else: ?>
                        <a href="dashboard.php" class="btn-rent" style="display: inline-flex; width: auto;">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Previous JavaScript remains the same
        document.addEventListener('DOMContentLoaded', function() {
            const brandFilter = document.getElementById('brand-filter');
            const typeFilter = document.getElementById('type-filter');
            const priceFilter = document.getElementById('price-filter');
            const statusFilter = document.getElementById('status-filter');
            const carCards = document.querySelectorAll('.card');
            
            [brandFilter, typeFilter, priceFilter, statusFilter].forEach(filter => {
                filter.addEventListener('change', filterCars);
            });
            
            function filterCars() {
                const selectedBrand = brandFilter.value;
                const selectedType = typeFilter.value;
                const selectedPrice = priceFilter.value;
                const selectedStatus = statusFilter.value;
                
                carCards.forEach(card => {
                    const brand = card.dataset.brand;
                    const type = card.dataset.type;
                    const price = parseInt(card.dataset.price);
                    const status = card.dataset.status;
                    
                    const brandMatch = selectedBrand === 'all' || brand === selectedBrand;
                    const typeMatch = selectedType === 'all' || type === selectedType;
                    const statusMatch = selectedStatus === 'all' || status === selectedStatus;
                    
                    let priceMatch = true;
                    if (selectedPrice !== 'all') {
                        if (selectedPrice === 'low') {
                            priceMatch = price >= 300000 && price <= 500000;
                        } else if (selectedPrice === 'medium') {
                            priceMatch = price > 500000 && price <= 800000;
                        } else if (selectedPrice === 'high') {
                            priceMatch = price > 800000;
                        }
                    }
                    
                    if (brandMatch && typeMatch && priceMatch && statusMatch) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }
            
            carCards.forEach((card, index) => {
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
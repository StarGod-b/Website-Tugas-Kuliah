<?php
session_start();
// Sertakan koneksi database
require_once 'config/Database.php';

// Pastikan hanya admin yang bisa mengakses

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// Ambil data penyewaan dari database
$data_penyewaan = [];
$pendapatan_per_bulan = [];
$total_per_brand = [];
$penjualan_per_model = [];
$total_penyewaan = 0;
$total_pendapatan = 0;

try {
    // Query untuk mendapatkan data penyewaan
    $query = "SELECT 
                s.id, 
                m.brand, 
                m.model, 
                m.type,
                m.fuel_type,
                m.transmission,
                m.seats,
                s.total_harga,
                DATE_FORMAT(s.tanggal_mulai, '%M %Y') AS bulan_tahun,
                s.status
            FROM sewa s
            JOIN mobil m ON s.mobil_id = m.id
            WHERE s.status = 'completed'";
    
    $stmt = $conn->query($query);
    $data_penyewaan = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Hitung statistik
    if (!empty($data_penyewaan)) {
        // Hitung total penyewaan
        $total_penyewaan = count($data_penyewaan);
        
        // Hitung pendapatan per bulan
        foreach ($data_penyewaan as $sewa) {
            $bulan = $sewa['bulan_tahun'];
            $total_pendapatan += $sewa['total_harga'];
            
            if (!isset($pendapatan_per_bulan[$bulan])) {
                $pendapatan_per_bulan[$bulan] = 0;
            }
            $pendapatan_per_bulan[$bulan] += $sewa['total_harga'];
            
            // Hitung per brand
            $brand = $sewa['brand'];
            if (!isset($total_per_brand[$brand])) {
                $total_per_brand[$brand] = 0;
            }
            $total_per_brand[$brand]++;
            
            // Hitung per model
            $type = $sewa['type'];
            if (!isset($penjualan_per_model[$type])) {
                $penjualan_per_model[$type] = 0;
            }
            $penjualan_per_model[$type]++;
        }
        
        // Urutkan berdasarkan bulan
        ksort($pendapatan_per_bulan);
    }
    
} catch (PDOException $e) {
    // Tangani error
    $error_message = "Error: " . $e->getMessage();
}

// Siapkan data untuk chart
$brand_labels = !empty($total_per_brand) ? json_encode(array_keys($total_per_brand)) : json_encode([]);
$brand_data = !empty($total_per_brand) ? json_encode(array_values($total_per_brand)) : json_encode([]);
$bulan_labels = !empty($pendapatan_per_bulan) ? json_encode(array_keys($pendapatan_per_bulan)) : json_encode([]);
$bulan_data = !empty($pendapatan_per_bulan) ? json_encode(array_values($pendapatan_per_bulan)) : json_encode([]);
$type_labels = !empty($penjualan_per_model) ? json_encode(array_keys($penjualan_per_model)) : json_encode([]);
$type_data = !empty($penjualan_per_model) ? json_encode(array_values($penjualan_per_model)) : json_encode([]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penyewaan Mobil</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --dark: #2c3e50;
            --light: #ecf0f1;
            --accent: #e74c3c;
            --purple: #9b59b6;
            --orange: #f39c12;
            --teal: #1abc9c;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7ec 100%);
            margin: 0;
            padding: 0;
            color: var(--dark);
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), var(--dark));
            color: white;
            padding: 25px 0;
            text-align: center;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            margin-bottom: 30px;
            position: relative;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        h1 {
            margin: 0;
            font-size: 2.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .subtitle {
            font-weight: 300;
            opacity: 0.9;
            font-size: 1.2rem;
        }
        
        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 3;
        }
        
        .back-btn a {
            display: inline-flex;
            align-items: center;
            background: white;
            color: var(--primary);
            padding: 10px 15px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .back-btn a:hover {
            background: var(--light);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }
        
        .back-btn a i {
            margin-right: 8px;
        }
        
        .dashboard {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        
        .card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            transition: transform 0.4s, box-shadow 0.4s;
            border: 1px solid rgba(0,0,0,0.05);
            overflow: hidden;
            position: relative;
        }
        
        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: var(--primary);
        }
        
        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .card.stat-card::before {
            background: linear-gradient(90deg, var(--primary), var(--teal));
        }
        
        .card.chart-card::before {
            background: linear-gradient(90deg, var(--purple), var(--accent));
        }
        
        .card h3 {
            margin-top: 0;
            color: var(--dark);
            padding-bottom: 15px;
            font-size: 1.4rem;
            display: flex;
            align-items: center;
        }
        
        .card h3 i {
            margin-right: 10px;
            color: var(--primary);
        }
        
        .stat {
            font-size: 2.8rem;
            font-weight: bold;
            color: var(--dark);
            margin: 15px 0;
            text-shadow: 0 2px 3px rgba(0,0,0,0.1);
        }
        
        .stat span {
            font-size: 1.2rem;
            color: #7f8c8d;
            font-weight: normal;
        }
        
        .chart-container {
            position: relative;
            height: 320px;
            margin-top: 20px;
        }
        
        .table-container {
            overflow-x: auto;
            margin-bottom: 40px;
            border-radius: 18px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 18px;
            overflow: hidden;
        }
        
        th, td {
            padding: 18px 15px;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        th {
            background: linear-gradient(135deg, var(--dark), var(--primary));
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        tr:nth-child(even) {
            background-color: #f9fbfd;
        }
        
        tr:hover {
            background-color: #edf7ff;
        }
        
        .badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        
        .badge-primary {
            background: linear-gradient(135deg, var(--primary), #2980b9);
            color: white;
        }
        
        .badge-success {
            background: linear-gradient(135deg, var(--secondary), #27ae60);
            color: white;
        }
        
        .badge-purple {
            background: linear-gradient(135deg, var(--purple), #8e44ad);
            color: white;
        }
        
        .badge-orange {
            background: linear-gradient(135deg, var(--orange), #e67e22);
            color: white;
        }
        
        .badge-teal {
            background: linear-gradient(135deg, var(--teal), #16a085);
            color: white;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #95a5a6;
            font-size: 1.2rem;
        }
        
        .no-data i {
            font-size: 3rem;
            margin-bottom: 20px;
            display: block;
            color: #bdc3c7;
        }
        
        footer {
            text-align: center;
            padding: 30px;
            color: #7f8c8d;
            font-size: 0.95rem;
            background: white;
            border-radius: 18px;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
            margin-top: 20px;
        }
        
        .stats-highlight {
            display: flex;
            justify-content: space-around;
            margin: 30px 0;
            text-align: center;
        }
        
        .stat-item {
            padding: 20px;
            border-radius: 15px;
            background: rgba(255,255,255,0.9);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            flex: 1;
            margin: 0 10px;
            transition: all 0.3s;
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .stat-item h4 {
            margin: 0 0 10px 0;
            color: var(--dark);
            font-size: 1.1rem;
        }
        
        .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        @media (max-width: 768px) {
            .dashboard {
                grid-template-columns: 1fr;
            }
            
            .stats-highlight {
                flex-direction: column;
            }
            
            .stat-item {
                margin: 10px 0;
            }
            
            .back-btn {
                position: relative;
                top: 0;
                left: 0;
                margin-bottom: 20px;
                text-align: center;
            }
            
            .back-btn a {
                display: inline-flex;
                width: auto;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="back-btn">
            <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
        </div>
        <div class="header-content">
            <h1><i class="fas fa-chart-line"></i> Laporan Penyewaan Mobil</h1>
            <p class="subtitle">Analisis dan Visualisasi Data Penyewaan</p>
        </div>
    </header>
    
    <div class="container">
        <?php if (isset($error_message)): ?>
            <div class="card" style="background: #ffebee; border-left: 4px solid #f44336;">
                <h3><i class="fas fa-exclamation-triangle"></i> Error</h3>
                <p><?= $error_message ?></p>
            </div>
        <?php endif; ?>
        
        <div class="stats-highlight">
            <div class="stat-item">
                <h4><i class="fas fa-car"></i> Total Penyewaan</h4>
                <div class="stat-value"><?= number_format($total_penyewaan) ?></div>
                <p>Total transaksi penyewaan</p>
            </div>
            
            <div class="stat-item">
                <h4><i class="fas fa-money-bill-wave"></i> Total Pendapatan</h4>
                <div class="stat-value">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></div>
                <p>Total pendapatan dari penyewaan</p>
            </div>
            
            <div class="stat-item">
                <h4><i class="fas fa-crown"></i> Brand Terlaris</h4>
                <?php if (!empty($total_per_brand)): 
                    arsort($total_per_brand);
                    $top_brand = key($total_per_brand);
                    $top_jumlah = current($total_per_brand);
                ?>
                    <div class="stat-value"><?= $top_brand ?></div>
                    <p><?= $top_jumlah ?> penyewaan</p>
                <?php else: ?>
                    <div class="stat-value">-</div>
                    <p>Tidak ada data</p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="dashboard">
            <div class="card chart-card">
                <h3><i class="fas fa-tags"></i> Penyewaan per Brand</h3>
                <div class="chart-container">
                    <?php if (!empty($total_per_brand)): ?>
                        <canvas id="brandChart"></canvas>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-chart-pie"></i>
                            <p>Tidak ada data penyewaan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card chart-card">
                <h3><i class="fas fa-calendar-alt"></i> Pendapatan per Bulan</h3>
                <div class="chart-container">
                    <?php if (!empty($pendapatan_per_bulan)): ?>
                        <canvas id="bulanChart"></canvas>
                    <?php else: ?>
                        <div class="no-data">
                            <i class="fas fa-chart-bar"></i>
                            <p>Tidak ada data pendapatan</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="card chart-card">
            <h3><i class="fas fa-car-side"></i> Penyewaan per Tipe</h3>
            <div class="chart-container">
                <?php if (!empty($penjualan_per_model)): ?>
                    <canvas id="modelChart"></canvas>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-chart-pie"></i>
                        <p>Tidak ada data model</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="table-container">
            <div class="card">
                <h3><i class="fas fa-list"></i> Detail Penyewaan</h3>
                <?php if (!empty($data_penyewaan)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Tipe</th>
                                <th>Bahan Bakar</th>
                                <th>Transmisi</th>
                                <th>Kursi</th>
                                <th>Total Harga</th>
                                <th>Bulan</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($data_penyewaan as $sewa): ?>
                            <tr>
                                <td><?= $sewa['id'] ?></td>
                                <td><span class="badge badge-primary"><?= $sewa['brand'] ?></span></td>
                                <td><?= $sewa['model'] ?></td>
                                <td><?= $sewa['type'] ?></td>
                                <td><?= $sewa['fuel_type'] ?></td>
                                <td><?= $sewa['transmission'] ?></td>
                                <td><?= $sewa['seats'] ?></td>
                                <td>Rp <?= number_format($sewa['total_harga'], 0, ',', '.') ?></td>
                                <td><?= $sewa['bulan_tahun'] ?></td>
                                <td><span class="badge badge-success"><?= $sewa['status'] ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="no-data">
                        <i class="fas fa-database"></i>
                        <p>Tidak ada data penyewaan yang ditemukan</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Laporan Penyewaan Mobil. Dibuat dengan PHP dan Chart.js</p>
        </div>
    </footer>
    
    <?php if (!empty($total_per_brand) || !empty($pendapatan_per_bulan) || !empty($penjualan_per_model)): ?>
    <script>
        // Chart Penyewaan per Brand
        <?php if (!empty($total_per_brand)): ?>
        const brandCtx = document.getElementById('brandChart')?.getContext('2d');
        if (brandCtx) {
            const brandChart = new Chart(brandCtx, {
                type: 'doughnut',
                data: {
                    labels: <?= $brand_labels ?>,
                    datasets: [{
                        data: <?= $brand_data ?>,
                        backgroundColor: [
                            '#3498db',
                            '#2ecc71',
                            '#e74c3c',
                            '#f39c12',
                            '#9b59b6',
                            '#1abc9c',
                            '#d35400'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' penyewaan';
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }
        <?php endif; ?>
        
        // Chart Pendapatan per Bulan
        <?php if (!empty($pendapatan_per_bulan)): ?>
        const bulanCtx = document.getElementById('bulanChart')?.getContext('2d');
        if (bulanCtx) {
            const bulanChart = new Chart(bulanCtx, {
                type: 'bar',
                data: {
                    labels: <?= $bulan_labels ?>,
                    datasets: [{
                        label: 'Pendapatan (Rp)',
                        data: <?= $bulan_data ?>,
                        backgroundColor: 'rgba(52, 152, 219, 0.7)',
                        borderColor: '#2980b9',
                        borderWidth: 1,
                        borderRadius: 6,
                        barThickness: 40,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + context.raw.toLocaleString();
                                }
                            }
                        },
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }
        <?php endif; ?>
        
        // Chart Penyewaan per Model
        <?php if (!empty($penjualan_per_model)): ?>
        const modelCtx = document.getElementById('modelChart')?.getContext('2d');
        if (modelCtx) {
            const modelChart = new Chart(modelCtx, {
                type: 'polarArea',
                data: {
                    labels: <?= $type_labels ?>,
                    datasets: [{
                        data: <?= $type_data ?>,
                        backgroundColor: [
                            'rgba(52, 152, 219, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(231, 76, 60, 0.8)',
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(243, 156, 18, 0.8)',
                            'rgba(26, 188, 156, 0.8)',
                            'rgba(211, 84, 0, 0.8)'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' penyewaan';
                                }
                            }
                        }
                    }
                }
            });
        }
        <?php endif; ?>
    </script>
    <?php endif; ?>
</body>
</html>
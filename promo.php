<?php
// Data dummy promo (dapat diganti dengan data dari database)
$promo = [
    'harian' => [
        [
            'judul' => 'Weekday Special',
            'deskripsi' => 'Diskon 20% untuk penyewaan hari Senin-Jumat',
            'kode' => 'WD20'
        ],
        [
            'judul' => 'Early Bird',
            'deskripsi' => 'Diskon 15% untuk pemesanan sebelum jam 10 pagi',
            'kode' => 'EARLY15'
        ]
    ],
    'mingguan' => [
        [
            'judul' => 'Paket Liburan',
            'deskripsi' => 'Sewa 5 hari gratis 2 hari (total 7 hari)',
            'kode' => 'LIBUR7'
        ],
        [
            'judul' => 'Family Package',
            'deskripsi' => 'Diskon 30% untuk sewa min. 1 minggu',
            'kode' => 'FAM30'
        ]
    ],
    'bulanan' => [
        [
            'judul' => 'Long Term Rental',
            'deskripsi' => 'Harga khusus untuk sewa bulanan + gratis asuransi',
            'kode' => 'BULANAN'
        ],
        [
            'judul' => 'Corporate Package',
            'deskripsi' => 'Paket khusus perusahaan untuk sewa bulanan',
            'kode' => 'CORP25'
        ]
    ]
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Promo Rental Mobil - XYZ Car Rental</title>
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #e74c3c;
            --light: #ecf0f1;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), #1a2530);
            color: white;
            text-align: center;
            padding: 40px 20px;
            margin-bottom: 30px;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: relative;
        }
        
        h1 {
            font-size: 2.8rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 800px;
            margin: 0 auto;
        }
        
        .promo-section {
            margin-bottom: 50px;
        }
        
        .section-title {
            text-align: center;
            font-size: 2rem;
            color: var(--primary);
            margin: 30px 0;
            position: relative;
            padding-bottom: 15px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--secondary);
            border-radius: 2px;
        }
        
        .promo-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .promo-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .promo-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.15);
        }
        
        .card-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
        }
        
        .card-body {
            padding: 25px;
        }
        
        .promo-title {
            font-size: 1.5rem;
            margin-bottom: 12px;
            color: var(--primary);
        }
        
        .promo-desc {
            margin-bottom: 20px;
            color: #555;
        }
        
        .promo-code {
            background: var(--light);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: bold;
            color: var(--secondary);
            display: inline-block;
            border: 2px dashed var(--secondary);
        }
        
        .disclaimer {
            text-align: center;
            margin-top: 40px;
            padding: 20px;
            background: var(--light);
            border-radius: 10px;
            font-style: italic;
            color: #666;
            border-left: 4px solid var(--secondary);
        }
        
        /* Tombol Kembali */
        .back-button {
            display: inline-block;
            background-color: var(--secondary);
            color: white;
            padding: 12px 25px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px 0;
            transition: all 0.3s ease;
            border: 2px solid var(--secondary);
            cursor: pointer;
            text-align: center;
        }
        
        .back-button:hover {
            background-color: transparent;
            color: var(--secondary);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        }
        
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .header-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .header-button:hover {
            background-color: rgba(231, 76, 60, 0.8);
        }
        
        footer {
            text-align: center;
            margin-top: 20px;
            padding: 20px;
            color: #777;
            border-top: 1px solid #ddd;
        }
        
        @media (max-width: 768px) {
            .promo-container {
                grid-template-columns: 1fr;
            }
            
            h1 {
                font-size: 2.2rem;
            }
            
            .header-button {
                position: relative;
                top: 0;
                left: 0;
                margin-bottom: 15px;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <!-- Tombol Kembali di Header -->
            <a href="home.php" class="back-button header-button">
                &larr; Kembali ke Beranda
            </a>
            
            <h1>Promo Spesial Rental Mobil</h1>
            <p class="subtitle">Nikmati berbagai penawaran menarik untuk penyewaan mobil harian, mingguan, dan bulanan. Promo terbatas, segera pesan kendaraan impian Anda!</p>
        </div>
    </header>

    <main class="container">
        <!-- Promo Harian -->
        <section class="promo-section">
            <h2 class="section-title">Promo Harian</h2>
            <div class="promo-container">
                <?php foreach ($promo['harian'] as $item): ?>
                <div class="promo-card">
                    <div class="card-header">
                        <h3><?= $item['judul'] ?></h3>
                    </div>
                    <div class="card-body">
                        <p class="promo-desc"><?= $item['deskripsi'] ?></p>
                        <div class="promo-code">Kode: <?= $item['kode'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Promo Mingguan -->
        <section class="promo-section">
            <h2 class="section-title">Promo Mingguan</h2>
            <div class="promo-container">
                <?php foreach ($promo['mingguan'] as $item): ?>
                <div class="promo-card">
                    <div class="card-header">
                        <h3><?= $item['judul'] ?></h3>
                    </div>
                    <div class="card-body">
                        <p class="promo-desc"><?= $item['deskripsi'] ?></p>
                        <div class="promo-code">Kode: <?= $item['kode'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Promo Bulanan -->
        <section class="promo-section">
            <h2 class="section-title">Promo Bulanan</h2>
            <div class="promo-container">
                <?php foreach ($promo['bulanan'] as $item): ?>
                <div class="promo-card">
                    <div class="card-header">
                        <h3><?= $item['judul'] ?></h3>
                    </div>
                    <div class="card-body">
                        <p class="promo-desc"><?= $item['deskripsi'] ?></p>
                        <div class="promo-code">Kode: <?= $item['kode'] ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="disclaimer">
            * Syarat dan ketentuan berlaku. Promo tidak dapat digabungkan dengan penawaran lainnya. Hubungi kami untuk detail lebih lanjut.
        </div>
        
        <!-- Tombol Kembali di Bawah -->
        <div class="button-container">
            <a href="home.php" class="back-button">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </main>

    <footer class="container">
        <p>&copy; <?= date('Y') ?> RentalMobil.SG. All rights reserved.</p>
    </footer>
</body>
</html>
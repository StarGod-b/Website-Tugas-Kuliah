<?php $page_title = "Kebijakan Privasi"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - RentCar Express</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --light: #ecf0f1;
        }
        .privacy-header {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)), 
                        url('https://images.unsplash.com/photo-1551836022-d5d88e9218df?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80');
            background-size: cover;
            padding: 100px 0;
            color: white;
        }
        .privacy-card {
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
            border-top: 4px solid var(--secondary);
        }
        .privacy-icon {
            font-size: 2.5rem;
            color: var(--secondary);
            margin-bottom: 20px;
        }
        .data-types {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
        }
        footer {
            background-color: var(--primary);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fa-solid fa-car"></i> RentCar Express
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link active" href="privasi.php">Privasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="s&k.php">S&K</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <section class="privacy-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Kebijakan Privasi</h1>
            <p class="lead">Terakhir diperbarui: <?php echo date('d F Y'); ?></p>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 mx-auto">
                    <div class="privacy-card card p-4 mb-5">
                        <div class="text-center">
                            <div class="privacy-icon">
                                <i class="fa-solid fa-shield-halved"></i>
                            </div>
                            <h2>Komitmen Kami</h2>
                        </div>
                        <p class="text-center">RentCar Express berkomitmen melindungi data pribadi pelanggan sesuai dengan undang-undang perlindungan data pribadi yang berlaku di Indonesia.</p>
                    </div>

                    <h3 class="mb-4">Data yang Kami Kumpulkan</h3>
                    <div class="data-types mb-5">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <h5><i class="fa-solid fa-user me-2"></i> Data Identitas</h5>
                                <ul>
                                    <li>Nama lengkap</li>
                                    <li>Alamat email</li>
                                    <li>Nomor telepon</li>
                                    <li>Alamat tempat tinggal</li>
                                </ul>
                            </div>
                            <div class="col-md-6 mb-4">
                                <h5><i class="fa-solid fa-id-card me-2"></i> Data Dokumen</h5>
                                <ul>
                                    <li>Scan KTP/Paspor</li>
                                    <li>Scan SIM A/SIM C</li>
                                    <li>Foto selfie dengan KTP</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fa-solid fa-credit-card me-2"></i> Data Pembayaran</h5>
                                <ul>
                                    <li>Informasi kartu kredit (dienskripsi)</li>
                                    <li>Rekening bank</li>
                                    <li>Riwayat transaksi</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="fa-solid fa-car me-2"></i> Data Penggunaan</h5>
                                <ul>
                                    <li>Riwayat pemesanan</li>
                                    <li>Lokasi pengambilan/pengembalian</li>
                                    <li>Preferensi kendaraan</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <h3 class="mb-4">Penggunaan Data</h3>
                    <div class="card border-0 shadow-sm mb-5">
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <i class="fa-solid fa-check-circle text-success me-2"></i> 
                                    Memproses pemesanan dan pembayaran
                                </li>
                                <li class="list-group-item">
                                    <i class="fa-solid fa-check-circle text-success me-2"></i> 
                                    Verifikasi identitas penyewa
                                </li>
                                <li class="list-group-item">
                                    <i class="fa-solid fa-check-circle text-success me-2"></i> 
                                    Mengirim notifikasi transaksi
                                </li>
                                <li class="list-group-item">
                                    <i class="fa-solid fa-check-circle text-success me-2"></i> 
                                    Meningkatkan kualitas layanan
                                </li>
                                <li class="list-group-item">
                                    <i class="fa-solid fa-check-circle text-success me-2"></i> 
                                    Analisis data untuk pengembangan bisnis
                                </li>
                                <li class="list-group-item">
                                    <i class="fa-solid fa-check-circle text-success me-2"></i> 
                                    Pemenuhan kewajiban hukum
                                </li>
                            </ul>
                        </div>
                    </div>

                    <h3 class="mb-4">Keamanan Data</h3>
                    <div class="row mb-5">
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fa-solid fa-lock fa-2x text-primary mb-3"></i>
                                    <h5>Enkripsi Data</h5>
                                    <p>Data sensitif dienkripsi menggunakan teknologi AES-256</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fa-solid fa-shield-alt fa-2x text-primary mb-3"></i>
                                    <h5>Firewall & Proteksi</h5>
                                    <p>Sistem keamanan berlapis dengan firewall dan IDS</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <i class="fa-solid fa-user-shield fa-2x text-primary mb-3"></i>
                                    <h5>Akses Terbatas</h5>
                                    <p>Hanya personel berwenang yang mengakses data</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h4><i class="fa-solid fa-user-clock"></i> Hak Pelanggan</h4>
                        <p>Anda berhak untuk mengakses, memperbaiki, atau menghapus data pribadi Anda yang kami simpan. Untuk menggunakan hak ini, silakan hubungi kami melalui email: <strong>privacy@rentcarexpress.id</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fa-solid fa-car"></i> RentCar Express</h5>
                    <p>Komitmen kami adalah memberikan layanan terbaik dengan perlindungan data maksimal.</p>
                </div>
                <div class="col-md-3">
                    <h5>Tautan Cepat</h5>
                    <ul class="list-unstyled">
                        <li><a href="s&k.php" class="text-white">Syarat & Ketentuan</a></li>
                        <li><a href="privasi.php" class="text-white">Kebijakan Privasi</a></li>
                        <li><a href="faq.php" class="text-white">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="fa-solid fa-phone"></i> (021) 1234-5678</li>
                        <li><i class="fa-solid fa-envelope"></i> cs@RentalMobil.SG</li>
                        <li><i class="fa-solid fa-clock"></i> Senin-Minggu, 08:00-22:00 WIB</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> RentalMobil.SG. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
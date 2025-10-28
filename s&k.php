<?php $page_title = "Syarat & Ketentuan"; ?>
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
            --secondary: #e74c3c;
            --light: #ecf0f1;
        }
        .hero-banner {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
        }
        .section-title {
            position: relative;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--secondary);
        }
        .highlight {
            color: var(--secondary);
            font-weight: bold;
        }
        .terms-card {
            border-left: 4px solid var(--secondary);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .terms-card:hover {
            transform: translateY(-5px);
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
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="privasi.php">Privasi</a></li>
                    <li class="nav-item"><a class="nav-link active" href="s&k.php">S&K</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Banner -->
    <section class="hero-banner text-center text-white">
        <div class="container">
            <h1 class="display-4 fw-bold">Syarat & Ketentuan</h1>
            <p class="lead">Ketahui persyaratan sewa kendaraan di RentCar Express</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="terms-card card mb-4 p-4">
                        <h2 class="section-title">Ketentuan Umum</h2>
                        <ol class="list-group list-group-numbered">
                            <li class="list-group-item border-0">Penyewa harus berusia minimal <span class="highlight">21 tahun</span> dan memiliki SIM C atau SIM A yang masih berlaku</li>
                            <li class="list-group-item border-0">Masa sewa minimal <span class="highlight">24 jam</span> untuk semua jenis rental</li>
                            <li class="list-group-item border-0">Pembayaran bisa dilakukan melalui transfer,e-wallet dan cash</li>
                            <li class="list-group-item border-0">Pengembalian kendaraan melebihi batas waktu akan dikenakan denda <span class="highlight">20%/jam</span></li>
                        </ol>
                    </div>

                    <div class="terms-card card mb-4 p-4">
                        <h2 class="section-title">Persyaratan Dokumen</h2>
                        <ul class="list-group">
                            <li class="list-group-item border-0"><i class="fa-solid fa-id-card me-2 text-danger"></i> Fotokopi KTP/Paspor</li>
                            <li class="list-group-item border-0"><i class="fa-solid fa-credit-card me-2 text-danger"></i> Fotokopi SIM A/SIM C asli</li>
                            <li class="list-group-item border-0"><i class="fa-solid fa-receipt me-2 text-danger"></i> Bukti pembayaran DP</li>
                            <li class="list-group-item border-0"><i class="fa-solid fa-house me-2 text-danger"></i> Fotokopi tagihan listrik (untuk sewa bulanan)</li>
                        </ul>
                    </div>

                    <div class="terms-card card mb-4 p-4">
                        <h2 class="section-title">Ketentuan Penggunaan</h2>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-triangle-exclamation"></i> Dilarang keras menggunakan kendaraan untuk:
                        </div>
                        <ul>
                            <li>Balap liar atau kegiatan ilegal</li>
                            <li>Mengangkut barang berbahaya</li>
                            <li>Penyeberangan antar pulau tanpa izin</li>
                            <li>Penggunaan di luar pulau Jawa tanpa pemberitahuan</li>
                        </ul>
                    </div>

                    <div class="terms-card card mb-4 p-4">
                        <h2 class="section-title">Asuransi & Pertanggungan</h2>
                        <table class="table table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>Jenis Kerusakan</th>
                                    <th>Pertanggungan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
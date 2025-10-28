<?php $page_title = "FAQ"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> RentalMobil.SG</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #27ae60;
            --light: #ecf0f1;
        }
        .faq-hero {
            background: linear-gradient(rgba(39, 174, 96, 0.8), rgba(39, 174, 96, 0.8)), 
                        url('https://images.unsplash.com/photo-1544636331-e26879cd4d9b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80');
            background-size: cover;
            padding: 100px 0;
            color: white;
        }
        .faq-category {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .faq-header {
            background-color: var(--primary);
            color: white;
            padding: 15px 20px;
            cursor: pointer;
        }
        .faq-item {
            border-bottom: 1px solid #eee;
        }
        .faq-question {
            padding: 15px 20px;
            font-weight: 500;
            cursor: pointer;
            position: relative;
        }
        .faq-question:after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            position: absolute;
            right: 20px;
            transition: transform 0.3s;
        }
        .faq-question.collapsed:after {
            transform: rotate(0deg);
        }
        .faq-question:not(.collapsed):after {
            transform: rotate(180deg);
        }
        .faq-answer {
            padding: 0 20px 20px;
            background-color: #f9f9f9;
        }
        .contact-box {
            background: linear-gradient(135deg, #2c3e50, #4a6491);
            border-radius: 10px;
            color: white;
            padding: 30px;
        }
        .search-box {
            position: relative;
            margin-bottom: 30px;
        }
        .search-box input {
            padding-right: 50px;
            border-radius: 50px;
        }
        .search-box button {
            position: absolute;
            right: 5px;
            top: 5px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <i class="fa-solid fa-car"></i> RentalMobil.SG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="home.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link active" href="faq.php">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link" href="privasi.php">Privasi</a></li>
                    <li class="nav-item"><a class="nav-link" href="s&k.php">S&K</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="faq-hero text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Pertanyaan Umum</h1>
            <p class="lead">Temukan jawaban atas pertanyaan Anda tentang layanan rental kami</p>
            
            <div class="row justify-content-center mt-4">
                <div class="col-md-8">
                    <div class="search-box">
                        <input type="text" class="form-control form-control-lg" placeholder="Cari pertanyaan...">
                        <button class="btn btn-primary">
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="faq-category">
                        <div class="faq-header">
                            <h4 class="mb-0"><i class="fa-solid fa-file-contract me-2"></i> Proses Pemesanan</h4>
                        </div>
                        <div class="accordion accordion-flush" id="bookingAccordion">
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Bagaimana cara memesan mobil di RentalMobil.SG?
                                </div>
                                <div id="faq1" class="collapse faq-answer" data-bs-parent="#bookingAccordion">
                                    Anda dapat memesan melalui website kami, aplikasi mobile, atau langsung ke outlet kami. Untuk pemesanan online:
                                    <ol class="mt-2">
                                        <li>Pilih lokasi dan tanggal sewa</li>
                                        <li>Pilih mobil yang tersedia</li>
                                        <li>Isi data pribadi dan dokumen</li>
                                        <li>Lakukan pembayaran DP</li>
                                        <li>Terima konfirmasi via email/SMS</li>
                                    </ol>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Berapa minimal DP untuk pemesanan?
                                </div>
                                <div id="faq2" class="collapse faq-answer" data-bs-parent="#bookingAccordion">
                                    Minimal DP adalah 30% dari total biaya sewa. Untuk sewa bulanan, minimal DP 20% dari total biaya.
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Bisakah saya mengubah jadwal setelah pemesanan?
                                </div>
                                <div id="faq3" class="collapse faq-answer" data-bs-parent="#bookingAccordion">
                                    Perubahan jadwal dapat dilakukan maksimal 24 jam sebelum pengambilan mobil. Perubahan akan dikenakan biaya administrasi sebesar Rp 100.000 dan tergantung ketersediaan mobil.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-category">
                        <div class="faq-header">
                            <h4 class="mb-0"><i class="fa-solid fa-money-bill-wave me-2"></i> Pembayaran & Biaya</h4>
                        </div>
                        <div class="accordion accordion-flush" id="paymentAccordion">
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Metode pembayaran apa saja yang diterima?
                                </div>
                                <div id="faq4" class="collapse faq-answer" data-bs-parent="#paymentAccordion">
                                    Kami menerima:
                                    <ul>
                                        <li>Transfer bank (BCA, Mandiri, BNI, BRI)</li>
                                        <li>E-wallet (Gopay, OVO, Dana, ShopeePay)</li>
                                        <li>Tunai di outlet kami</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Apakah harga sudah termasuk pajak dan asuransi?
                                </div>
                                <div id="faq5" class="collapse faq-answer" data-bs-parent="#paymentAccordion">
                                    Ya, semua harga yang tertera sudah termasuk PPN 11% dan asuransi dasar (TLO). Untuk asuransi all-risk dapat ditambahkan dengan biaya tambahan 5% dari total sewa.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="faq-category">
                        <div class="faq-header">
                            <h4 class="mb-0"><i class="fa-solid fa-car me-2"></i> Pengambilan & Pengembalian</h4>
                        </div>
                        <div class="accordion accordion-flush" id="deliveryAccordion">
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Apakah ada layanan antar-jemput mobil?
                                </div>
                                <div id="faq6" class="collapse faq-answer" data-bs-parent="#deliveryAccordion">
                                    Ya, kami menyediakan layanan antar-jemput di lokasi berikut:
                                    <ul>
                                        <li>Bandara Soekarno-Hatta: Rp 150.000</li>
                                        <li>Stasiun Gambir: Rp 100.000</li>
                                        <li>Dalam kota Jakarta: Rp 75.000</li>
                                        <li>Hotel bintang 5: Gratis (min. sewa 3 hari)</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="faq-item">
                                <div class="faq-question collapsed" data-bs-toggle="collapse" data-bs-target="#faq7">
                                    Apa yang harus dilakukan jika terlambat mengembalikan mobil?
                                </div>
                                <div id="faq7" class="collapse faq-answer" data-bs-parent="#deliveryAccordion">
                                    Keterlambatan pengembalian akan dikenakan denda:
                                    <ul>
                                        <li>1-2 jam: 20% dari tarif harian</li>
                                        <li>2-4 jam: 50% dari tarif harian</li>
                                        <li>>4 jam: dihitung 1 hari tambahan</li>
                                    </ul>
                                    Harap menghubungi customer service kami jika terjadi keterlambatan.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="contact-box mb-4">
                        <h3><i class="fa-solid fa-headset me-2"></i> Butuh Bantuan?</h3>
                        <p class="mb-4">Tim support kami siap membantu 24/7</p>
                        
                        <div class="d-grid gap-2 mb-3">
                            <a href="https://wa.me/6281234567890" class="btn btn-success btn-lg">
                                <i class="fa-brands fa-whatsapp"></i> WhatsApp: +62 89513595554   
                            </a>
                        </div>
                        
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fa-solid fa-phone me-2"></i> (021) 1234-5678</li>
                            <li class="mb-2"><i class="fa-solid fa-envelope me-2"></i> cs@RentalMobil.SG</li>
                            <li><i class="fa-solid fa-clock me-2"></i> 10 jam setiap hari</li>
                        </ul>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <h5><i class="fa-solid fa-percent me-2 text-success"></i> Promo Spesial</h5>
                            <div class="alert alert-success">
                                <strong>Sewa 3 Hari Gratis 1 Hari!</strong><br>
                                Berlaku hingga 30 September 2023. Kode promo: <span class="badge bg-dark">GRATIS1</span>
                            </div>
                            <div class="alert alert-info">
                                <strong>Cashback 20% Sewa Bulanan</strong><br>
                                Untuk pemesanan minimal 30 hari. Syarat dan ketentuan berlaku.
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5><i class="fa-solid fa-download me-2 text-primary"></i> Dokumen</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <a href="s&k.php"><i class="fa-solid fa-file-contract me-2"></i> Syarat & Ketentuan</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="privasi.php"><i class="fa-solid fa-lock me-2"></i> Kebijakan Privasi</a>
                                </li>
                                <li class="list-group-item">
                                    <a href="#"><i class="fa-solid fa-receipt me-2"></i> Form Klaim Asuransi</a>
                                </li>
                            </ul>
                        </div>
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
                    <h5><i class="fa-solid fa-car"></i> RentalMobil.SG</h5>
                    <p>Solusi rental mobil terbaik untuk perjalanan bisnis dan liburan Anda.</p>
                </div>
                <div class="col-md-3">
                    <h5>Tautan</h5>
                    <ul class="list-unstyled">
                        <li><a href="s&k.php" class="text-white">Syarat & Ketentuan</a></li>
                        <li><a href="privasi.php" class="text-white">Kebijakan Privasi</a></li>
                        <li><a href="faq.php" class="text-white">FAQ</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Follow Kami</h5>
                    <div class="d-flex gap-3 mt-3">
                        <a href="#" class="text-white"><i class="fa-brands fa-facebook fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fa-brands fa-instagram fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fa-brands fa-twitter fa-2x"></i></a>
                        <a href="#" class="text-white"><i class="fa-brands fa-youtube fa-2x"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <p class="text-center mb-0">&copy; <?php echo date('Y'); ?> RentalMobil.SG. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple search functionality
        document.querySelector('.search-box button').addEventListener('click', function() {
            const searchTerm = document.querySelector('.search-box input').value.toLowerCase();
            const questions = document.querySelectorAll('.faq-question');
            
            questions.forEach(q => {
                const questionText = q.textContent.toLowerCase();
                if(questionText.includes(searchTerm)) {
                    q.closest('.faq-item').style.display = 'block';
                    // Open the accordion
                    const target = q.getAttribute('data-bs-target');
                    new bootstrap.Collapse(document.querySelector(target), { toggle: true });
                } else {
                    q.closest('.faq-item').style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
<?php

session_start();
include 'config/Database.php';

// Cek login user
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();
$user_id = $_SESSION['user']['id'];

// Generate CSRF token jika belum ada - HANYA SEKALI PER SESI
if (empty($_SESSION['csrf_token_sewa'])) {
    $_SESSION['csrf_token_sewa'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID mobil tidak valid.";
    exit;
}

$mobil_id = (int)$_GET['id'];

// Ambil data mobil beserta status sewa terkait
$stmt = $conn->prepare("
    SELECT mobil.*, sewa.id AS sewa_id, sewa.status AS sewa_status, sewa.metode_pembayaran, sewa.bukti_pembayaran,
           sewa.tanggal_mulai, sewa.tanggal_selesai, sewa.total_harga, sewa.rental_days
    FROM mobil
    LEFT JOIN sewa ON mobil.id = sewa.mobil_id 
        AND sewa.user_id = :user_id 
        AND sewa.status IN ('pending', 'awaiting_confirmation', 'confirmed')
    WHERE mobil.id = :id
");
$stmt->bindParam(':id', $mobil_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$mobil = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mobil) {
    echo "Mobil tidak ditemukan.";
    exit;
}

// Inisialisasi variabel
$error = '';
$success = '';
$show_form = true;
$show_payment = false;
$show_upload = false;
$show_confirmed = false;
$sewa_id = null;
$total_harga = 0;
$rental_days = 0;
$tanggal_mulai = '';
$tanggal_selesai = '';
$metode_pembayaran = '';
$bukti_pembayaran = '';

// Cek status sewa yang ada dan tentukan tampilan
if ($mobil['sewa_id']) {
    $sewa_id = $mobil['sewa_id'];
    $total_harga = $mobil['total_harga'];
    $rental_days = $mobil['rental_days'];
    $tanggal_mulai = $mobil['tanggal_mulai'];
    $tanggal_selesai = $mobil['tanggal_selesai'];
    $metode_pembayaran = $mobil['metode_pembayaran'];
    $bukti_pembayaran = $mobil['bukti_pembayaran'];
    
    // Tentukan tampilan berdasarkan status
    switch ($mobil['sewa_status']) {
        case 'pending':
            $show_form = false;
            $show_payment = true;
            break;
        case 'awaiting_confirmation':
            $show_form = false;
            $show_upload = true;
            $success = "Pembayaran sedang menunggu konfirmasi admin.";
            break;
        case 'confirmed':
            $show_form = false;
            $show_confirmed = true;
            $success = "Pembayaran telah dikonfirmasi! Sewa mobil aktif.";
            break;
    }
}

// Proses Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF token
    $csrf_token = $_POST['csrf_token'] ?? '';
    
    if (!hash_equals($_SESSION['csrf_token_sewa'], $csrf_token)) {
        $error = "Token keamanan tidak valid. Silakan refresh halaman dan coba lagi.";
    } elseif (isset($_POST['batalkan_sewa'])) {
        // Proses pembatalan sewa
        $sewa_id = $_POST['sewa_id'] ?? 0;
        
        // Mulai transaksi
        $conn->beginTransaction();
        try {
            // Hapus file bukti pembayaran jika ada
            if (!empty($bukti_pembayaran) && file_exists($bukti_pembayaran)) {
                unlink($bukti_pembayaran);
            }
            
            $delete_sewa = $conn->prepare("DELETE FROM sewa WHERE id = :id");
            $delete_sewa->bindParam(':id', $sewa_id, PDO::PARAM_INT);
            
            $update_mobil = $conn->prepare("UPDATE mobil SET status = 'available' WHERE id = :id");
            $update_mobil->bindParam(':id', $mobil_id, PDO::PARAM_INT);
            
            if ($delete_sewa->execute() && $update_mobil->execute()) {
                $conn->commit();
                $success = "Sewa berhasil dibatalkan.";
                
                // Refresh halaman
                header("Location: sewa.php?id=" . $mobil_id);
                exit;
            } else {
                $conn->rollBack();
                $error = "Gagal membatalkan sewa.";
            }
        } catch (Exception $e) {
            $conn->rollBack();
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        // Proses inisialisasi sewa
        $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
        $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';

        if (!$tanggal_mulai || !$tanggal_selesai) {
            $error = "Tanggal mulai dan selesai sewa harus diisi.";
        } else {
            // Validasi tanggal
            $today = new DateTime();
            $today->setTime(0,0,0);
            
            $start_date = DateTime::createFromFormat('Y-m-d', $tanggal_mulai);
            $end_date = DateTime::createFromFormat('Y-m-d', $tanggal_selesai);
            
            if (!$start_date || !$end_date) {
                $error = "Format tanggal tidak valid.";
            } else {
                $start_date->setTime(0,0,0);
                $end_date->setTime(0,0,0);
                
                if ($start_date <= $today) {
                    $error = "Tanggal mulai sewa minimal besok.";
                } elseif ($start_date >= $end_date) {
                    $error = "Tanggal mulai sewa harus sebelum tanggal selesai sewa.";
                } else {
                    // Hitung jumlah hari sewa
                    $interval = $start_date->diff($end_date);
                    $rental_days = $interval->days + 1;
                    
                    if ($rental_days < 1) {
                        $error = "Minimal sewa 1 hari.";
                    } else {
                        $total_harga = $rental_days * $mobil['price_per_day'];

                        // Mulai transaksi
                        $conn->beginTransaction();
                        try {
                            // Simpan data sewa dengan status pending
                            $insert = $conn->prepare("INSERT INTO sewa (mobil_id, user_id, tanggal_mulai, tanggal_selesai, total_harga, rental_days, created_at, status) 
                                                    VALUES (:mobil_id, :user_id, :tanggal_mulai, :tanggal_selesai, :total_harga, :rental_days, NOW(), 'pending')");
                            $insert->bindParam(':mobil_id', $mobil_id, PDO::PARAM_INT);
                            $insert->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                            $insert->bindParam(':tanggal_mulai', $tanggal_mulai);
                            $insert->bindParam(':tanggal_selesai', $tanggal_selesai);
                            $insert->bindParam(':total_harga', $total_harga);
                            $insert->bindParam(':rental_days', $rental_days, PDO::PARAM_INT);

                            if ($insert->execute()) {
                                $sewa_id = $conn->lastInsertId();
                                
                                // Update status mobil jadi dipesan
                                $update = $conn->prepare("UPDATE mobil SET status = 'booked' WHERE id = :id");
                                $update->bindParam(':id', $mobil_id, PDO::PARAM_INT);
                                
                                if ($update->execute()) {
                                    $conn->commit();
                                    
                                    // Redirect ke halaman konfirmasi pembayaran
                                    header("Location: transaksi.php?sewa_id=" . $sewa_id);
                                    exit;
                                } else {
                                    $conn->rollBack();
                                    $error = "Gagal mengupdate status mobil.";
                                }
                            } else {
                                $conn->rollBack();
                                $error = "Gagal menyimpan data sewa.";
                            }
                        } catch (Exception $e) {
                            $conn->rollBack();
                            $error = "Terjadi kesalahan: " . $e->getMessage();
                        }
                    }
                }
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sewa Mobil - <?= htmlspecialchars($mobil['brand'] . ' ' . $mobil['model']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --secondary: #f97316;
            --light: #f3f4f6;
            --dark: #1f2937;
            --success: #10b981;
            --error: #ef4444;
            --warning: #f59e0b;
            --card-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #e0f2fe, #f0fdfa);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            max-width: 800px;
            width: 100%;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            display: flex;
            flex-direction: column;
        }
        
        .header {
            background: var(--primary);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header i {
            font-size: 1.5rem;
        }
        
        .back-link {
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            transform: translateX(-3px);
        }
        
        .content {
            padding: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        
        .car-details {
            flex: 1;
            min-width: 300px;
        }
        
        .car-image {
            width: 100%;
            height: 250px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border: 1px solid #e5e7eb;
        }
        
        .car-title {
            font-size: 1.8rem;
            color: var(--dark);
            margin-bottom: 15px;
        }
        
        .car-info {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            background: var(--light);
            border-radius: 8px;
        }
        
        .info-item i {
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .price-tag {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            margin: 20px 0;
        }
        
        .rent-form {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
        }
        
        .form-title {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--primary);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
        }
        
        .date-input {
            position: relative;
        }
        
        .date-input i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
        
        input[type="date"] {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 2px solid #d1d5db;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        input[type="date"]:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.2);
        }
        
        .date-hint {
            font-size: 0.85rem;
            color: #6b7280;
            margin-top: 5px;
            display: block;
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 15px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background: var(--error) !important;
        }
        
        .btn-cancel:hover {
            background: #dc2626 !important;
        }
        
        .btn:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            transform: none;
        }
        
        .message {
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .error {
            background: #fee2e2;
            color: #b91c1c;
        }
        
        .success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .info-message {
            background: #dbeafe;
            color: #1e40af;
            margin-bottom: 20px;
        }
        
        .year-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(13, 148, 136, 0.9);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .image-container {
            position: relative;
        }
        
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            
            .header h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="mobil.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1>Sewa Mobil: <?= htmlspecialchars($mobil['brand'] . ' ' . $mobil['model']) ?></h1>
        </div>
        
        <div class="content">
            <div class="car-details">
                <div class="image-container">
                    <img src="<?= htmlspecialchars($mobil['image']) ?>" alt="<?= htmlspecialchars($mobil['brand']) ?>" class="car-image" />
                </div>
                
                <h2 class="car-title"><?= htmlspecialchars($mobil['brand'] . ' ' . $mobil['model']) ?></h2>
                
                <div class="car-info">
                    <div class="info-item">
                        <i class="fas fa-calendar-alt"></i>
                        <div>
                            <div>Tahun Pembuatan</div>
                            <strong><?= htmlspecialchars(date('Y', strtotime($mobil['created_at'])))?></strong>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-gas-pump"></i>
                        <div>
                            <div>Bahan Bakar</div>
                            <strong><?= htmlspecialchars($mobil['fuel_type']) ?></strong>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-cogs"></i>
                        <div>
                            <div>Transmisi</div>
                            <strong><?= htmlspecialchars($mobil['transmission']) ?></strong>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-user-friends"></i>
                        <div>
                            <div>Kapasitas</div>
                            <strong><?= htmlspecialchars($mobil['seats']) ?> Orang</strong>
                        </div>
                    </div>
                </div>
                
                <div class="price-tag">
                    Rp <?= number_format($mobil['price_per_day'], 0, ',', '.') ?> / hari
                </div>
            </div>
            
            <div class="rent-form">
                <h3 class="form-title">Formulir Penyewaan</h3>
                
                <?php if ($error): ?>
                    <div class="message error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="message success">
                        <i class="fas fa-check-circle"></i>
                        <?= htmlspecialchars($success) ?>
                    </div>
                    <a href="dashboard.php" class="btn">
                        <i class="fas fa-tachometer-alt"></i> Kembali ke Dashboard
                    </a>
                <?php else: ?>
                    <?php if ($show_payment): ?>
                        <div class="message info-message">
                            <i class="fas fa-info-circle"></i>
                            Silakan lakukan pembayaran untuk melanjutkan.
                        </div>
                        <a href="transaksi.php?sewa_id=<?= $sewa_id ?>" class="btn">
                            <i class="fas fa-money-bill-wave"></i> Lanjutkan ke Pembayaran
                        </a>
                        <form method="POST" action="" style="margin-top: 10px;">
                            <input type="hidden" name="sewa_id" value="<?= $sewa_id ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token_sewa']) ?>">
                            <button type="submit" name="batalkan_sewa" class="btn btn-cancel">
                                <i class="fas fa-times"></i> Batalkan Sewa
                            </button>
                        </form>
                    <?php elseif ($show_upload): ?>
                        <div class="message info-message">
                            <i class="fas fa-info-circle"></i>
                            Pembayaran sedang menunggu konfirmasi admin.
                        </div>
                        <form method="POST" action="" style="margin-top: 10px;">
                            <input type="hidden" name="sewa_id" value="<?= $sewa_id ?>">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token_sewa']) ?>">
                            <button type="submit" name="batalkan_sewa" class="btn btn-cancel">
                                <i class="fas fa-times"></i> Batalkan Sewa
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="message info-message">
                            <i class="fas fa-info-circle"></i>
                            Silakan tentukan tanggal sewa. Minimal sewa 1 hari.
                        </div>
                        
                        <form method="POST" action="" id="rentForm">
                            <!-- Tambahkan token CSRF -->
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token_sewa']) ?>">
                            
                            <div class="form-group">
                                <label for="tanggal_mulai">Tanggal Mulai Sewa</label>
                                <div class="date-input">
                                    <i class="fas fa-calendar-alt"></i>
                                    <input type="date" id="tanggal_mulai" name="tanggal_mulai" required 
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($tanggal_mulai) ?>">
                                </div>
                                <span class="date-hint">Minimal besok (<?= date('d/m/Y', strtotime('+1 day')) ?>)</span>
                            </div>
                            
                            <div class="form-group">
                                <label for="tanggal_selesai">Tanggal Selesai Sewa</label>
                                <div class="date-input">
                                    <i class="fas fa-calendar-check"></i>
                                    <input type="date" name="tanggal_selesai" id="tanggal_selesai" required 
                                           min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= htmlspecialchars($tanggal_selesai) ?>">
                                </div>
                                <span class="date-hint" id="dateHint">Minimal 1 hari setelah tanggal mulai</span>
                            </div>
                            
                            <button type="submit" id="submitBtn" class="btn">
                                <i class="fas fa-check"></i> Sewa Sekarang
                            </button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const startDateInput = document.getElementById('tanggal_mulai');
            const endDateInput = document.getElementById('tanggal_selesai');
            const dateHint = document.getElementById('dateHint');
            const submitBtn = document.getElementById('submitBtn');
            
            // Fungsi untuk mengupdate min tanggal selesai
            function updateEndDateMin() {
                if (startDateInput.value) {
                    const startDate = new Date(startDateInput.value);
                    const minEndDate = new Date(startDate);
                    minEndDate.setDate(minEndDate.getDate() + 1);
                    
                    // Format minEndDate ke YYYY-MM-DD
                    const minEndDateStr = minEndDate.toISOString().split('T')[0];
                    endDateInput.min = minEndDateStr;
                    
                    // Format tanggal untuk ditampilkan
                    const formattedDate = formatDate(minEndDateStr);
                    dateHint.textContent = `Minimal: ${formattedDate} (1 hari setelah mulai)`;
                    
                    // Reset nilai jika tanggal selesai tidak valid
                    if (endDateInput.value && new Date(endDateInput.value) < minEndDate) {
                        endDateInput.value = '';
                        updateSubmitButton();
                    }
                } else {
                    endDateInput.min = '<?= date('Y-m-d', strtotime('+1 day')) ?>';
                    dateHint.textContent = 'Silakan pilih tanggal mulai terlebih dahulu';
                }
                
                updateSubmitButton();
            }
            
            // Format tanggal menjadi dd/mm/yyyy
            function formatDate(dateString) {
                const [year, month, day] = dateString.split('-');
                return `${day}/${month}/${year}`;
            }
            
            // Fungsi untuk mengupdate status tombol
            function updateSubmitButton() {
                if (startDateInput.value && endDateInput.value && 
                    new Date(endDateInput.value) >= new Date(endDateInput.min)) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }
            
            // Event listeners
            startDateInput.addEventListener('change', updateEndDateMin);
            endDateInput.addEventListener('change', updateSubmitButton);
            
            // Inisialisasi
            updateEndDateMin();
        });
    </script>
</body>
</html>
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

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_GET['sewa_id']) || !is_numeric($_GET['sewa_id'])) {
    die("ID sewa tidak valid.");
}

$sewa_id = (int)$_GET['sewa_id'];

$stmt = $conn->prepare("SELECT sewa.*, mobil.brand, mobil.model, mobil.price_per_day, mobil.image AS foto FROM sewa JOIN mobil ON sewa.mobil_id = mobil.id WHERE sewa.id = :sewa_id AND sewa.user_id = :user_id");
$stmt->bindParam(':sewa_id', $sewa_id, PDO::PARAM_INT);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$sewa = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sewa) {
    die("Data sewa tidak ditemukan.");
}

$allowed_statuses = ['pending', 'awaiting_confirmation', 'processing'];
if (!in_array($sewa['status'], $allowed_statuses)) {
    die("Status sewa tidak valid untuk konfirmasi pembayaran.");
}

$error = '';
$success = '';

$admin_accounts = [
    'transfer_bca' => [
        'bank' => 'BCA', 'name' => 'Rental Mobil Admin', 'number' => '1234-5678-9012'
    ],
    'transfer_bri' => [
        'bank' => 'BRI', 'name' => 'Rental Mobil Admin', 'number' => '3456-7890-1234'
    ],
    'transfer_mandiri' => [
        'bank' => 'Mandiri', 'name' => 'Rental Mobil Admin', 'number' => '5678-9012-3456'
    ]
];

$payment_methods = [
    'transfer_bca' => 'Transfer Bank BCA',
    'transfer_bri' => 'Transfer Bank BRI',
    'transfer_mandiri' => 'Transfer Bank Mandiri',
    'dana' => 'Dana (0812-3456-7890)',
    'ovo' => 'OVO (0813-4567-8901)',
    'gopay' => 'Gopay (0814-5678-9012)',
    'cash' => 'Cash (Uang Tunai)'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batalkan_sewa'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token keamanan tidak valid.";
    } else {
        $update = $conn->prepare("UPDATE sewa SET status = 'cancelled' WHERE id = :id AND user_id = :user_id");
        $update->bindParam(':id', $sewa_id, PDO::PARAM_INT);
        $update->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        
        if ($update->execute()) {
            $update_mobil = $conn->prepare("UPDATE mobil SET status = 'available' WHERE id = :mobil_id");
            $update_mobil->bindParam(':mobil_id', $sewa['mobil_id'], PDO::PARAM_INT);
            $update_mobil->execute();

            $success = "Sewa berhasil dibatalkan! Mengarahkan ke halaman mobil...";
            header("Refresh: 2; URL=mobil.php");
            exit;
        } else {
            $error = "Gagal membatalkan sewa.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi_pembayaran'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token keamanan tidak valid.";
    } else {
        $metode = $_POST['metode_pembayaran'] ?? '';
        if (empty($metode)) {
            $error = "Silakan pilih metode pembayaran.";
        } else {
            $update = $conn->prepare("UPDATE sewa SET metode_pembayaran = :metode, status = 'awaiting_confirmation' WHERE id = :id");
            $update->bindParam(':metode', $metode);
            $update->bindParam(':id', $sewa_id, PDO::PARAM_INT);
            if ($update->execute()) {
                $success = "Metode pembayaran berhasil dipilih! Silakan unggah bukti pembayaran.";
                $stmt->execute();
                $sewa = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $error = "Gagal menyimpan metode pembayaran.";
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['unggah_bukti'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token keamanan tidak valid.";
    } elseif (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['bukti_pembayaran'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = 'payment_' . $sewa_id . '_' . time() . '.' . $ext;
        $upload_dir = 'uploads/payments/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $target_file = $upload_dir . $new_filename;

        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $max_size = 5 * 1024 * 1024;

        if (!in_array(strtolower($ext), $allowed_types)) {
            $error = "Format file tidak didukung. Gunakan JPG, PNG, GIF, atau PDF.";
        } elseif ($file['size'] > $max_size) {
            $error = "Ukuran file terlalu besar. Maksimal 5MB.";
        } else {
            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                $conn->beginTransaction();
                try {
                    $update = $conn->prepare("UPDATE sewa SET bukti_pembayaran = :bukti, status = 'awaiting_confirmation' WHERE id = :id");
                    $update->bindParam(':bukti', $target_file);
                    $update->bindParam(':id', $sewa_id, PDO::PARAM_INT);
                    $update->execute();

                    $updateMobil = $conn->prepare("UPDATE mobil SET status = 'rented' WHERE id = :mobil_id");
                    $updateMobil->bindParam(':mobil_id', $sewa['mobil_id'], PDO::PARAM_INT);
                    $updateMobil->execute();

                    $conn->commit();
                    $success = "Bukti pembayaran berhasil diunggah! Silakan menunggu konfirmasi admin.";
                    header("Refresh: 2; URL=sewa_saya.php");
                    exit;
                } catch (Exception $e) {
                    $conn->rollBack();
                    unlink($target_file);
                    $error = "Gagal menyimpan bukti pembayaran.";
                }
            } else {
                $error = "Gagal mengunggah file.";
            }
        }
    } else {
        $error = "Silakan pilih file bukti pembayaran.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-header { font-weight: 600; }
        .img-thumbnail { max-height: 150px; object-fit: cover; }
        .account-info { background: #f8f9fa; border-left: 4px solid #0d6efd; }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <!-- HEADER DENGAN TOMBOL KEMBALI KE MOBIL.PHP -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">Proses Transaksi</h1>
                    <a href="mobil.php" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Mobil
                    </a>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <!-- Informasi Sewa -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <i class="bi bi-car-front me-2"></i> Detail Mobil
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <?php if (!empty($sewa['foto'])): ?>
                                <img src="<?= htmlspecialchars($sewa['foto']) ?>" 
                                     alt="<?= htmlspecialchars($sewa['brand'] . ' ' . $sewa['model']) ?>" 
                                     class="img-thumbnail me-4">
                            <?php else: ?>
                                <div class="bg-light border d-flex align-items-center justify-content-center me-4" style="width: 150px; height: 100px;">
                                    <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <h5 class="card-title"><?= htmlspecialchars($sewa['brand']) ?> <?= htmlspecialchars($sewa['model']) ?></h5>
                                <p class="card-text mb-1">
                                    <i class="bi bi-calendar-check me-1"></i> 
                                    <?= date('d M Y', strtotime($sewa['tanggal_mulai'])) ?> - 
                                    <?= date('d M Y', strtotime($sewa['tanggal_selesai'])) ?>
                                </p>
                                <p class="card-text mb-1">
                                    <i class="bi bi-wallet2 me-1"></i> 
                                    Total: Rp <?= number_format($sewa['total_harga'], 0, ',', '.') ?>
                                </p>
                                <p class="card-text mb-0">
                                    <i class="bi bi-info-circle me-1"></i> 
                                    Status: 
                                    <span class="badge 
                                        <?= $sewa['status'] == 'pending' ? 'bg-warning' : '' ?>
                                        <?= $sewa['status'] == 'awaiting_confirmation' ? 'bg-info' : '' ?>
                                        <?= $sewa['status'] == 'processing' ? 'bg-primary' : '' ?>
                                        <?= $sewa['status'] == 'completed' ? 'bg-success' : '' ?>
                                        <?= $sewa['status'] == 'cancelled' ? 'bg-danger' : '' ?>
                                    ">
                                        <?= ucfirst($sewa['status']) ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Pembatalan (hanya untuk status pending) -->
                <?php if ($sewa['status'] == 'pending'): ?>
                    <div class="card border-danger mb-4">
                        <div class="card-header bg-danger text-white">
                            <i class="bi bi-x-circle me-2"></i> Batalkan Sewa
                        </div>
                        <div class="card-body">
                            <p class="card-text">
                                Anda dapat membatalkan sewa ini sebelum melakukan pembayaran.
                                Pembatalan akan mengembalikan mobil ke ketersediaan.
                            </p>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                <button type="submit" name="batalkan_sewa" class="btn btn-outline-danger"
                                        onclick="return confirm('Apakah Anda yakin ingin membatalkan sewa ini?')">
                                    <i class="bi bi-trash me-1"></i> Batalkan Sewa
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Metode Pembayaran (hanya untuk status pending) -->
                <?php if ($sewa['status'] == 'pending'): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-credit-card me-2"></i> Pilih Metode Pembayaran
                        </div>
                        <div class="card-body">
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Metode Pembayaran</label>
                                    <select name="metode_pembayaran" class="form-select" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <?php foreach ($payment_methods as $key => $value): ?>
                                            <option value="<?= $key ?>"><?= $value ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <button type="submit" name="konfirmasi_pembayaran" class="btn btn-primary w-100">
                                    <i class="bi bi-check-circle me-1"></i> Konfirmasi Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Form Upload Bukti (hanya untuk status awaiting_confirmation) -->
                <?php if ($sewa['status'] == 'awaiting_confirmation'): ?>
                    <div class="card mb-4">
                        <div class="card-header bg-info text-dark">
                            <i class="bi bi-upload me-2"></i> Unggah Bukti Pembayaran
                        </div>
                        <div class="card-body">
                            <!-- Tampilkan info rekening admin jika metode transfer -->
                            <?php if (array_key_exists($sewa['metode_pembayaran'], $admin_accounts)): ?>
                                <div class="alert alert-info account-info p-3 mb-4">
                                    <h6><i class="bi bi-bank me-2"></i> Transfer ke Rekening Admin:</h6>
                                    <div class="ms-4 mt-2">
                                        <p class="mb-1"><strong>Bank:</strong> <?= $admin_accounts[$sewa['metode_pembayaran']]['bank'] ?></p>
                                        <p class="mb-1"><strong>Nama:</strong> <?= $admin_accounts[$sewa['metode_pembayaran']]['name'] ?></p>
                                        <p class="mb-0"><strong>Nomor Rekening:</strong> <?= $admin_accounts[$sewa['metode_pembayaran']]['number'] ?></p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    Silakan unggah bukti pembayaran untuk konfirmasi admin
                                </div>
                            <?php endif; ?>
                            
                            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Bukti Pembayaran</label>
                                    <input type="file" name="bukti_pembayaran" class="form-control" required
                                           accept=".jpg,.jpeg,.png,.gif,.pdf">
                                    <div class="form-text">
                                        Format: JPG, PNG, GIF, PDF (Maks. 5MB)
                                    </div>
                                </div>
                                
                                <button type="submit" name="unggah_bukti" class="btn btn-info w-100">
                                    <i class="bi bi-cloud-upload me-1"></i> Unggah Bukti
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Setelah mengunggah bukti, harap tunggu konfirmasi admin. 
                        Proses verifikasi membutuhkan waktu 1x24 jam.
                    </div>
                <?php endif; ?>

                <!-- Status Processing (sudah upload bukti) -->
                <?php if ($sewa['status'] == 'processing'): ?>
                    <div class="card border-primary mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-hourglass-split me-2"></i> Menunggu Verifikasi
                        </div>
                        <div class="card-body text-center">
                            <div class="py-3">
                                <i class="bi bi-hourglass-split text-primary" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Pembayaran Sedang Diverifikasi</h5>
                                <p class="text-muted">
                                    Bukti pembayaran Anda telah diterima dan sedang dalam proses verifikasi oleh admin.
                                    Silakan tunggu konfirmasi lebih lanjut.
                                </p>
                                <a href="sewa_saya.php" class="btn btn-outline-primary">
                                    <i class="bi bi-list me-1"></i> Lihat Riwayat Sewa
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto refresh halaman jika ada perubahan status
        setTimeout(() => {
            window.location.reload();
        }, 300000); // Refresh setiap 5 menit
    </script>
</body>
</html>
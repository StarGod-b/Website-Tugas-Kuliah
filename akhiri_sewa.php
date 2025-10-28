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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sewa_id'])) {
    $sewa_id = $_POST['sewa_id'];

    // Ambil data sewa
    $stmt = $conn->prepare("SELECT * FROM sewa WHERE id = :id AND user_id = :user_id");
    $stmt->execute([
        'id' => $sewa_id,
        'user_id' => $user_id
    ]);
    $sewa = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sewa) {
        $message = "Data sewa tidak ditemukan atau bukan milik Anda.";
        $success = false;
    } elseif ($sewa['status'] !== 'confirmed') {
        $message = "Sewa ini tidak bisa diakhiri karena statusnya bukan 'Sedang Disewa'.";
        $success = false;
    } else {
        // Ubah status sewa ke completed
        $update_sewa = $conn->prepare("UPDATE sewa SET status = 'completed' WHERE id = :id");
        $update_sewa->execute(['id' => $sewa_id]);

        // Ubah status mobil ke available
        $update_mobil = $conn->prepare("UPDATE mobil SET status = 'available' WHERE id = :mobil_id");
        $update_mobil->execute(['mobil_id' => $sewa['mobil_id']]);

        $message = "Sewa telah diakhiri. Mobil berhasil dikembalikan.";
        $success = true;
    }
} else {
    $message = "Permintaan tidak valid.";
    $success = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akhiri Sewa - CarRent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --success: #10b981;
            --error: #ef4444;
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --text: #1f2937;
            --light: #f9fafb;
            --card-bg: #ffffff;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7eb 100%);
            min-height: 100vh;
            padding: 20px;
            color: var(--text);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            max-width: 500px;
            width: 100%;
            background: var(--card-bg);
            padding: 40px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            text-align: center;
            position: relative;
            overflow: hidden;
            transform: translateY(0);
            transition: transform 0.3s ease;
        }
        
        .container:hover {
            transform: translateY(-5px);
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: <?= $success ? 'var(--success)' : 'var(--error)' ?>;
        }
        
        .icon-container {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: <?= $success ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' ?>;
            margin-bottom: 25px;
            animation: pulse 2s infinite;
        }
        
        .icon-container i {
            font-size: 50px;
            color: <?= $success ? 'var(--success)' : 'var(--error)' ?>;
        }
        
        h2 {
            color: <?= $success ? 'var(--success)' : 'var(--error)' ?>;
            margin-bottom: 15px;
            font-size: 28px;
            font-weight: 700;
        }
        
        p {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #4b5563;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 28px;
            background: var(--primary);
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px rgba(59, 130, 246, 0.3);
            border: none;
            cursor: pointer;
            gap: 8px;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(59, 130, 246, 0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn i {
            font-size: 14px;
        }
        
        .info-box {
            background: #f3f4f6;
            border-left: 4px solid #d1d5db;
            padding: 15px;
            border-radius: 0 8px 8px 0;
            margin: 25px 0;
            text-align: left;
        }
        
        .info-box p {
            margin-bottom: 0;
            font-size: 14px;
            color: #4b5563;
        }
        
        @keyframes pulse {
            0% {
                transform: scale(1);
                box-shadow: 0 0 0 0 <?= $success ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.4)' ?>;
            }
            70% {
                transform: scale(1.05);
                box-shadow: 0 0 0 12px rgba(0, 0, 0, 0);
            }
            100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(0, 0, 0, 0);
            }
        }
        
        @media (max-width: 576px) {
            .container {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 24px;
            }
            
            p {
                font-size: 16px;
            }
            
            .icon-container {
                width: 80px;
                height: 80px;
            }
            
            .icon-container i {
                font-size: 40px;
            }
            
            .btn {
                padding: 10px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon-container">
            <?php if ($success): ?>
                <i class="fas fa-check-circle"></i>
            <?php else: ?>
                <i class="fas fa-times-circle"></i>
            <?php endif; ?>
        </div>
        
        <h2><?= $success ? 'Berhasil!' : 'Gagal!' ?></h2>
        <p><?= htmlspecialchars($message) ?></p>
        
        <?php if ($success): ?>
            <div class="info-box">
                <p><i class="fas fa-info-circle"></i> Mobil telah berhasil dikembalikan. Terima kasih telah menggunakan layanan kami.</p>
            </div>
        <?php else: ?>
            <div class="info-box">
                <p><i class="fas fa-exclamation-triangle"></i> Pastikan sewa sedang aktif dan milik Anda untuk dapat mengakhirinya.</p>
            </div>
        <?php endif; ?>
        
        <a href="sewa_saya.php" class="btn">
            <i class="fas fa-arrow-left"></i> Kembali ke Riwayat Sewa
        </a>
    </div>
</body>
</html>
<?php
session_start();
include 'config/Database.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

// Generate CSRF token untuk form jika belum ada
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = '';
$success = '';

// Proses aksi konfirmasi, tolak, selesai, ATAU update status
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token keamanan tidak valid.";
    } else {
        $sewa_id = $_POST['sewa_id'] ?? 0;
        $action = $_POST['action'] ?? ''; // 'confirm', 'reject', 'complete', atau 'update_status'
        $new_status = $_POST['new_status'] ?? ''; // Untuk aksi update_status

        if (!$sewa_id || !$action) {
            $error = "Permintaan tidak valid.";
        } else {
            // Ambil data sewa
            $stmt = $conn->prepare("SELECT * FROM sewa WHERE id = :id");
            $stmt->bindParam(':id', $sewa_id, PDO::PARAM_INT);
            $stmt->execute();
            $sewa = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$sewa) {
                $error = "Transaksi tidak ditemukan.";
            } else {
                $conn->beginTransaction();
                try {
                    // ACTION: UPDATE STATUS (FITUR BARU)
                    if ($action === 'update_status') {
                        // Validasi status baru
                        $allowed_statuses = ['pending', 'awaiting_confirmation', 'confirmed', 'completed','rejected'];
                        if (!in_array($new_status, $allowed_statuses)) {
                            throw new Exception("Status tidak valid.");
                        }
                        
                        // Update status sewa
                        $update_sewa = $conn->prepare("UPDATE sewa SET status = :status WHERE id = :id");
                        $update_sewa->bindParam(':status', $new_status, PDO::PARAM_STR);
                        $update_sewa->bindParam(':id', $sewa_id, PDO::PARAM_INT);

                        // Logika perubahan status mobil
                        $mobil_status = 'available'; // Default
                        if ($new_status === 'confirmed') {
                            $mobil_status = 'disewa';
                        } else if ($new_status === 'completed' || $new_status === 'rejected') {
                            $mobil_status = 'available';
                        }

                        // Update status mobil hanya jika diperlukan
                        $update_mobil = $conn->prepare("UPDATE mobil SET status = :status WHERE id = :mobil_id");
                        $update_mobil->bindParam(':status', $mobil_status, PDO::PARAM_STR);
                        $update_mobil->bindParam(':mobil_id', $sewa['mobil_id'], PDO::PARAM_INT);

                        if ($update_sewa->execute() && $update_mobil->execute()) {
                            $conn->commit();
                            $success = "Status transaksi berhasil diubah menjadi " . $new_status;
                        } else {
                            $conn->rollBack();
                            $error = "Gagal mengubah status transaksi.";
                        }
                    } elseif ($action === 'confirm') {
                            // Validasi status harus awaiting_confirmation
                            if ($sewa['status'] !== 'awaiting_confirmation') {
                                throw new Exception("Transaksi tidak dalam status menunggu konfirmasi.");
                            }
                            
                            // Update status sewa menjadi confirmed
                            $update_sewa = $conn->prepare("UPDATE sewa SET status = 'confirmed' WHERE id = :id");
                            $update_sewa->bindParam(':id', $sewa_id, PDO::PARAM_INT);
    
                            // Update status mobil menjadi 'disewa'
                            $update_mobil = $conn->prepare("UPDATE mobil SET status = 'disewa' WHERE id = :mobil_id");
                            $update_mobil->bindParam(':mobil_id', $sewa['mobil_id'], PDO::PARAM_INT);
    
                            if ($update_sewa->execute() && $update_mobil->execute()) {
                                $conn->commit();
                                $success = "Pembayaran berhasil dikonfirmasi. Sewa mobil aktif.";
                            } else {
                                $conn->rollBack();
                                $error = "Gagal mengkonfirmasi pembayaran.";
                            }
                        } elseif ($action === 'reject') {
                            // Validasi status harus awaiting_confirmation
                            if ($sewa['status'] !== 'awaiting_confirmation') {
                                throw new Exception("Transaksi tidak dalam status menunggu konfirmasi.");
                            }
                            
                            // Update status sewa menjadi 'rejected'
                            $update_sewa = $conn->prepare("UPDATE sewa SET status = 'rejected' WHERE id = :id");
                            $update_sewa->bindParam(':id', $sewa_id, PDO::PARAM_INT);
    
                            // Update status mobil menjadi 'available'
                            $update_mobil = $conn->prepare("UPDATE mobil SET status = 'available' WHERE id = :mobil_id");
                            $update_mobil->bindParam(':mobil_id', $sewa['mobil_id'], PDO::PARAM_INT);
    
                            if ($update_sewa->execute() && $update_mobil->execute()) {
                                $conn->commit();
                                $success = "Pembayaran ditolak. Mobil kembali tersedia.";
                            } else {
                                $conn->rollBack();
                                $error = "Gagal menolak pembayaran.";
                            }
                        } elseif ($action === 'complete') {
                            // Validasi status harus confirmed
                            if ($sewa['status'] !== 'confirmed') {
                                throw new Exception("Transaksi belum dikonfirmasi.");
                            }
                            
                            // Update status sewa menjadi 'completed'
                            $update_sewa = $conn->prepare("UPDATE sewa SET status = 'completed' WHERE id = :id");
                            $update_sewa->bindParam(':id', $sewa_id, PDO::PARAM_INT);
    
                            // Update status mobil menjadi 'available'
                            $update_mobil = $conn->prepare("UPDATE mobil SET status = 'available' WHERE id = :mobil_id");
                            $update_mobil->bindParam(':mobil_id', $sewa['mobil_id'], PDO::PARAM_INT);
    
                            if ($update_sewa->execute() && $update_mobil->execute()) {
                                $conn->commit();
                                $success = "Sewa mobil selesai. Mobil kembali tersedia.";
                            } else {
                                $conn->rollBack();
                                $error = "Gagal menyelesaikan transaksi.";
                            }
                        }
                    } catch (Exception $e) {
                        $conn->rollBack();
                        $error = "Terjadi kesalahan: " . $e->getMessage();
                    }
                }
            }
        }
    }

// Filter berdasarkan status
$status_filter = $_GET['status'] ?? 'all';
$status_condition = '';
if (in_array($status_filter, ['pending', 'awaiting_confirmation', 'confirmed', 'rejected', 'completed'])) {
    $status_condition = "WHERE sewa.status = '$status_filter'";
} elseif ($status_filter === 'all') {
    $status_condition = '';
} else {
    $status_filter = 'all';
}

$stmt = $conn->prepare("
    SELECT sewa.*, 
           mobil.brand AS mobil_brand, 
           mobil.model AS mobil_model, 
           users.name AS user_nama,
           users.email AS user_email
    FROM sewa
    INNER JOIN mobil ON sewa.mobil_id = mobil.id
    INNER JOIN users ON sewa.user_id = users.id
    $status_condition
    ORDER BY sewa.created_at DESC
");

$stmt->execute();
$transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$status_counts = [
    'pending' => 0,
    'awaiting_confirmation' => 0,
    'confirmed' => 0,
    'rejected' => 0,
    'completed' => 0,
    'unknown' => 0, // Tambahkan kunci untuk menampung status tidak dikenal
    'total' => 0
];

$total_pendapatan = 0;
$statistics = [];

if (!empty($transactions) && is_array($transactions)) {
    foreach ($transactions as $trx) {
        $status = $trx['status'] ?? 'unknown';

        if (array_key_exists($status, $status_counts)) {
            $status_counts[$status]++;
        } else {
            $status_counts['unknown']++;
        }

        $status_counts['total']++;
    }
}try {
    $stmt = $conn->query("SELECT SUM(total_harga) AS total_revenue FROM sewa WHERE status = 'completed'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $statistics['revenue'] = $result['total_revenue'] ?? 0;
} catch (Exception $e) {
    $statistics['revenue'] = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Transaksi</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --info: #1abc9c;
            --light: #ecf0f1;
            --dark: #34495e;
            --gray: #95a5a6;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, var(--primary), #1a2530);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            display: flex;
            align-items: center;
        }
        
        .logo i {
            margin-right: 12px;
            font-size: 28px;
            color: var(--info);
        }
        
        .admin-nav ul {
            display: flex;
            list-style: none;
        }
        
        .admin-nav ul li {
            margin-left: 15px;
        }
        
        .admin-nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 30px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        
        .admin-nav ul li a i {
            margin-right: 8px;
        }
        
        .admin-nav ul li a:hover {
            background-color: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }
        
        .admin-nav ul li a.active {
            background-color: var(--info);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .user-info {
            display: flex;
            align-items: center;
            background-color: rgba(255,255,255,0.15);
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .user-info:hover {
            background-color: rgba(255,255,255,0.25);
            transform: translateY(-2px);
        }
        
        .user-info img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .page-title {
            margin: 25px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .page-title h1 {
            font-size: 32px;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .page-title h1 i {
            color: var(--info);
            background: rgba(26, 188, 156, 0.15);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .filter-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        
        .filter-btn {
            padding: 10px 20px;
            border-radius: 30px;
            background-color: white;
            border: 2px solid #e0e6ed;
            color: var(--dark);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        
        .filter-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        
        .filter-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .filter-btn i {
            font-size: 18px;
        }
        
        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .alert i {
            font-size: 22px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 35px;
        }
        
        .stat-card {
            background-color: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 5px solid var(--primary);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        
        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 20px;
            font-size: 32px;
            color: white;
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-info h3 {
            font-size: 28px;
            margin-bottom: 5px;
            color: var(--dark);
        }
        
        .stat-info p {
            color: var(--gray);
            font-size: 16px;
            font-weight: 500;
        }
        
        .card {
            background-color: white;
            border-radius: 16px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.05);
            margin-bottom: 35px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-3px);
        }
        
        .card-header {
            padding: 18px 25px;
            background: linear-gradient(135deg, var(--primary), var(--dark));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .card-header h2 {
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .card-header h2 i {
            background: rgba(255,255,255,0.2);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .card-body {
            padding: 0;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }
        
        th, td {
            padding: 16px 20px;
            text-align: left;
            border-bottom: 1px solid #f0f4f8;
        }
        
        th {
            background-color: #f8fafc;
            font-weight: 600;
            color: var(--dark);
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }
        
        tr {
            transition: background-color 0.2s;
        }
        
        tr:hover {
            background-color: #f8fafc;
        }
        
        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
        }
        
        .btn i {
            font-size: 16px;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 13px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, var(--success), #219653);
            color: white;
            box-shadow: 0 4px 10px rgba(39, 174, 96, 0.25);
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #219653, #1e8449);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(39, 174, 96, 0.35);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            color: white;
            box-shadow: 0 4px 10px rgba(231, 76, 60, 0.25);
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.35);
        }
        
        .btn-info {
            background: linear-gradient(135deg, var(--info), #16a085);
            color: white;
            box-shadow: 0 4px 10px rgba(26, 188, 156, 0.25);
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #16a085, #138a72);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(26, 188, 156, 0.35);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
            box-shadow: 0 4px 10px rgba(243, 156, 18, 0.25);
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #e67e22, #d35400);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(243, 156, 18, 0.35);
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }
        
        .modal-content {
            background-color: white;
            border-radius: 16px;
            width: 90%;
            max-width: 800px;
            max-height: 90vh;
            overflow: auto;
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            animation: modalAppear 0.4s ease-out;
        }
        
        @keyframes modalAppear {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .modal-header {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary), var(--dark));
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            border-radius: 16px 16px 0 0;
        }
        
        .modal-header h2 {
            font-size: 22px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 28px;
            cursor: pointer;
            transition: transform 0.3s;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-close:hover {
            background: rgba(255,255,255,0.2);
            transform: rotate(90deg);
        }
        
        .modal-body {
            padding: 25px;
        }
        
        .payment-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }
        
        .detail-group {
            margin-bottom: 20px;
        }
        
        .detail-group label {
            display: block;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
            font-size: 15px;
        }
        
        .detail-group .value {
            padding: 14px;
            background-color: #f8fafc;
            border-radius: 10px;
            border: 1px solid #e0e6ed;
            font-size: 16px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.03);
        }
        
        .payment-proof {
            text-align: center;
            margin: 30px 0;
            padding: 20px;
            background: #f8fafc;
            border-radius: 12px;
            border: 1px dashed #cbd5e0;
        }
        
        .payment-proof img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 10px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            border: 1px solid #e2e8f0;
        }
        
        .modal-footer {
            padding: 20px;
            background-color: #f8f9fa;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            flex-wrap: wrap;
            border-radius: 0 0 16px 16px;
        }
        
        .badge {
            display: inline-block;
            padding: 7px 14px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 600;
        }
        
        .badge-pending {
            background-color: #fef9e7;
            color: #b7950b;
            border: 1px solid #fef5d9;
        }
        
        .badge-waiting {
            background-color: #e3f2fd;
            color: #0b5394;
            border: 1px solid #d0e3f7;
        }
        
        .badge-confirmed {
            background-color: #eafaf1;
            color: var(--success);
            border: 1px solid #d5f5e3;
        }
        
        .badge-rejected {
            background-color: #fdedec;
            color: var(--danger);
            border: 1px solid #fadbd8;
        }
        
        .badge-completed {
            background-color: #eaf2f8;
            color: #2874a6;
            border: 1px solid #d6eaf8;
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }
        
        .empty-state i {
            font-size: 70px;
            color: #d5dbdb;
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 15px;
            color: var(--dark);
        }
        
        .empty-state p {
            font-size: 17px;
            max-width: 500px;
            margin: 0 auto;
        }
        
        @media (max-width: 992px) {
            .admin-container {
                padding: 15px;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .admin-nav ul {
                width: 100%;
                justify-content: center;
            }
            
            .user-info {
                align-self: flex-end;
            }
            
            .stats-container {
                grid-template-columns: 1fr 1fr;
            }
        }
        
        @media (max-width: 768px) {
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .admin-nav ul {
                flex-wrap: wrap;
                justify-content: flex-start;
            }
            
            .admin-nav ul li {
                margin: 5px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .modal-footer {
                flex-direction: column;
            }
            
            .modal-footer .btn {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .filter-container {
                flex-direction: column;
            }
            
            .filter-btn {
                width: 100%;
                justify-content: center;
            }
            
            .page-title h1 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-content">
            <div class="logo">
                <i class="fas fa-car"></i>
                <span>Admin Rental Mobil</span>
            </div>
            
            <nav class="admin-nav">
            <ul>
                <li><a href="dashboard.php"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a></li>
            </ul>
            </nav>
            
            <div class="user-info">
                <img src="https://ui-avatars.com/api/?name=Admin&background=3498db&color=fff" alt="Admin">
                <span>Admin</span>
            </div>
        </div>
    </header>
    
    <div class="admin-container">
        <div class="page-title">
            <h1><i class="fas fa-exchange-alt"></i> Kelola Transaksi Rental</h1>
            <div class="filter-container">
                <a href="?status=all" class="filter-btn <?= $status_filter === 'all' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Semua (<?= $status_counts['total'] ?>)
                </a>
                <a href="?status=pending" class="filter-btn <?= $status_filter === 'pending' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Pending (<?= $status_counts['pending'] ?>)
                </a>
                <a href="?status=awaiting_confirmation" class="filter-btn <?= $status_filter === 'awaiting_confirmation' ? 'active' : '' ?>">
                    <i class="fas fa-hourglass-half"></i> Menunggu (<?= $status_counts['awaiting_confirmation'] ?>)
                </a>
                <a href="?status=confirmed" class="filter-btn <?= $status_filter === 'confirmed' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle"></i> Dikonfirmasi (<?= $status_counts['confirmed'] ?>)
                </a>
                <a href="?status=rejected" class="filter-btn <?= $status_filter === 'rejected' ? 'active' : '' ?>">
                    <i class="fas fa-times-circle"></i> Ditolak (<?= $status_counts['rejected'] ?>)
                </a>
                <a href="?status=completed" class="filter-btn <?= $status_filter === 'completed' ? 'active' : '' ?>">
                    <i class="fas fa-flag-checkered"></i> Selesai (<?= $status_counts['completed'] ?>)
                </a>
            </div>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #3498db, #2980b9);">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $status_counts['awaiting_confirmation'] ?></h3>
                    <p>Menunggu Konfirmasi</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60, #219653);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $status_counts['confirmed'] ?></h3>
                    <p>Transaksi Aktif</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3><?= $status_counts['rejected'] ?></h3>
                    <p>Pembayaran Ditolak</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12, #e67e22);">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($statistics['revenue']) ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-list"></i> Daftar Transaksi</h2>
                <div style="color: #ecf0f1; font-size: 15px;">
                    Menampilkan: <?= $status_filter === 'all' ? 'Semua' : ucfirst(str_replace('_', ' ', $status_filter)) ?> 
                    (<?= isset($transactions) && is_array($transactions) ? count($transactions) : 0 ?> transaksi)
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($transactions)): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <h3>Tidak Ada Transaksi</h3>
                        <p>Tidak ditemukan transaksi dengan status yang dipilih. Silakan pilih filter lain.</p>
                    </div>
                <?php else: ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mobil</th>
                                <th>Penyewa</th>
                                <th>Tanggal Sewa</th>
                                <th>Durasi</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $trx): ?>
                                <?php
                                // Tentukan kelas badge berdasarkan status
                                $badge_class = '';
                                switch ($trx['status']) {
                                    case 'pending':
                                        $badge_class = 'badge-pending';
                                        break;
                                    case 'awaiting_confirmation':
                                        $badge_class = 'badge-waiting';
                                        break;
                                    case 'confirmed':
                                        $badge_class = 'badge-confirmed';
                                        break;
                                    case 'rejected':
                                        $badge_class = 'badge-rejected';
                                        break;
                                    case 'completed':
                                        $badge_class = 'badge-completed';
                                        break;
                                }
                                
                                // Format tanggal
                                $start = date('d M Y', strtotime($trx['tanggal_mulai']));
                                $end = date('d M Y', strtotime($trx['tanggal_selesai']));
                                ?>
                                <tr>
                                    <td>#<?= $trx['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($trx['mobil_brand']) ?></strong><br>
                                        <strong><?= htmlspecialchars($trx['mobil_model']) ?></strong><br>
                                    <td>
                                        <?= htmlspecialchars($trx['user_nama']) ?><br>
                                        <small><?= htmlspecialchars($trx['user_email']) ?></small>
                                    </td>
                                    <td>
                                        <?= $start ?><br>
                                        <small>s/d <?= $end ?></small>
                                    </td>
                                    <td><?= $trx['rental_days'] ?> hari</td>
                                    <td>Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= ucfirst(str_replace('_', ' ', $trx['status'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-info btn-sm view-detail" 
                                                    data-id="<?= $trx['id'] ?>"
                                                    data-user="<?= htmlspecialchars($trx['user_nama']) ?>"
                                                    data-email="<?= htmlspecialchars($trx['user_email']) ?>"
                                                    data-car="<?= htmlspecialchars($trx['mobil_brand']) ?>"
                                                    data-model="<?= htmlspecialchars($trx['mobil_model']) ?>"
                                                    data-method="<?= htmlspecialchars($trx['metode_pembayaran']) ?>"
                                                    data-start="<?= $start ?>"
                                                    data-end="<?= $end ?>"
                                                    data-days="<?= $trx['rental_days'] ?>"
                                                    data-amount="Rp <?= number_format($trx['total_harga'], 0, ',', '.') ?>"
                                                    data-proof="<?= htmlspecialchars($trx['bukti_pembayaran']) ?>"
                                                    data-status="<?= $trx['status'] ?>">
                                                <i class="fas fa-eye"></i> Detail
                                            </button>
                                            
                                            <!-- Form update status untuk semua status -->
                                            <form method="post" class="d-inline">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                                <input type="hidden" name="sewa_id" value="<?= $trx['id'] ?>">
                                                <input type="hidden" name="action" value="update_status">
                                                
                                                <select name="new_status" class="form-select form-select-sm">
                                                    <option value="pending" <?= $trx['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                    <option value="awaiting_confirmation" <?= $trx['status'] === 'awaiting_confirmation' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                                                    <option value="confirmed" <?= $trx['status'] === 'confirmed' ? 'selected' : '' ?>>Dikonfirmasi</option>
                                                    <option value="completed" <?= $trx['status'] === 'completed' ? 'selected' : '' ?>>Selesai</option>
                                                    <option value="rejected" <?= $trx['status'] === 'rejected' ? 'selected' : '' ?>>Ditolak</option>
                                                </select>
                                                <button type="submit" class="btn btn-sm btn-primary mt-1">Update</button>
                                            </form>

                                            <?php if ($trx['status'] === 'awaiting_confirmation'): ?>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="sewa_id" value="<?= $trx['id'] ?>">
                                                    <input type="hidden" name="action" value="reject">
                                                    <button type="submit" class="btn btn-danger btn-sm mt-1">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if ($trx['status'] === 'confirmed'): ?>
                                                <form method="post" class="d-inline">
                                                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                                                    <input type="hidden" name="sewa_id" value="<?= $trx['id'] ?>">
                                                    <input type="hidden" name="action" value="complete">
                                                    <button type="submit" class="btn btn-warning btn-sm mt-1">
                                                        <i class="fas fa-flag-checkered"></i> Selesaikan
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Modal untuk melihat detail transaksi -->
    <div class="modal" id="detailModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-file-invoice-dollar"></i> Detail Transaksi</h2>
                <button class="modal-close">&times;</button>
            </div>
            <div class="modal-body">
                <div class="payment-details">
                    <div class="detail-group">
                        <label>ID Transaksi</label>
                        <div class="value" id="modal-id">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Penyewa</label>
                        <div class="value" id="modal-user">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Email</label>
                        <div class="value" id="modal-email">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Mobil</label>
                        <div class="value" id="modal-car">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Tanggal Sewa</label>
                        <div class="value" id="modal-dates">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Durasi Sewa</label>
                        <div class="value" id="modal-days">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Metode Pembayaran</label>
                        <div class="value" id="modal-method">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Total Pembayaran</label>
                        <div class="value" id="modal-amount">-</div>
                    </div>
                    <div class="detail-group">
                        <label>Status</label>
                        <div class="value">
                            <span id="modal-status-badge">-</span>
                        </div>
                    </div>
                </div>
                
                <div class="payment-proof">
                    <h3 style="margin-bottom: 15px; color: var(--dark);">Bukti Pembayaran</h3>
                    <img id="modal-proof-img" src="uploads/payments/" alt="Bukti Pembayaran" style="max-width: 100%;">
                </div>
            </div>
            <div class="modal-footer">
            <form method="post" action="konfirmasi_pembayaran.php" id="confirmForm" style="display: none;">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                <input type="hidden" name="sewa_id" id="confirm_sewa_id" value="">
                <input type="hidden" name="action" value="confirm">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-check"></i> Konfirmasi Pembayaran
                </button>
            </form>
                <form method="post" id="rejectForm" style="display: none;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="sewa_id" id="reject_sewa_id" value="">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> Tolak Pembayaran
                    </button>
                </form>
                <form method="post" id="completeForm" style="display: none;">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <input type="hidden" name="sewa_id" id="complete_sewa_id" value="">
                    <input type="hidden" name="action" value="complete">
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-flag-checkered"></i> Tandai Selesai
                    </button>
                </form>
                <button class="btn" id="closeModal">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal functionality
            const modal = document.getElementById('detailModal');
            const modalImg = document.getElementById('modal-proof-img');
            const modalId = document.getElementById('modal-id');
            const modalUser = document.getElementById('modal-user');
            const modalEmail = document.getElementById('modal-email');
            const modalCar = document.getElementById('modal-car');
            const modalDates = document.getElementById('modal-dates');
            const modalDays = document.getElementById('modal-days');
            const modalMethod = document.getElementById('modal-method');
            const modalAmount = document.getElementById('modal-amount');
            const modalStatusBadge = document.getElementById('modal-status-badge');
            const closeModal = document.querySelector('.modal-close');
            const closeModalBtn = document.getElementById('closeModal');
            
            const confirmForm = document.getElementById('confirmForm');
            const rejectForm = document.getElementById('rejectForm');
            const completeForm = document.getElementById('completeForm');
            const confirmSewaId = document.getElementById('confirm_sewa_id');
            const rejectSewaId = document.getElementById('reject_sewa_id');
            const completeSewaId = document.getElementById('complete_sewa_id');
            
            const viewButtons = document.querySelectorAll('.view-detail');
            viewButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const user = this.getAttribute('data-user');
                    const email = this.getAttribute('data-email');
                    const car = this.getAttribute('data-car');
                    const method = this.getAttribute('data-method');
                    const start = this.getAttribute('data-start');
                    const end = this.getAttribute('data-end');
                    const days = this.getAttribute('data-days');
                    const amount = this.getAttribute('data-amount');
                    const proof = this.getAttribute('data-proof');
                    const status = this.getAttribute('data-status');
                    
                    modalId.textContent = '#' + id;
                    modalUser.textContent = user;
                    modalEmail.textContent = email;
                    modalCar.textContent = car;
                    modalDates.textContent = start + ' s/d ' + end;
                    modalDays.textContent = days + ' hari';
                    modalMethod.textContent = method;
                    modalAmount.textContent = amount;
                    modalImg.src = proof;
                    
                    // Update status badge
                    let badgeClass = '';
                    let badgeText = '';
                    
                    switch(status) {
                        case 'pending':
                            badgeClass = 'badge-pending';
                            badgeText = 'Pending';
                            break;
                        case 'awaiting_confirmation':
                            badgeClass = 'badge-waiting';
                            badgeText = 'Menunggu Konfirmasi';
                            break;
                        case 'confirmed':
                            badgeClass = 'badge-confirmed';
                            badgeText = 'Dikonfirmasi';
                            break;
                        case 'rejected':
                            badgeClass = 'badge-rejected';
                            badgeText = 'Ditolak';
                            break;
                        case 'completed':
                            badgeClass = 'badge-completed';
                            badgeText = 'Selesai';
                            break;
                    }
                    
                    modalStatusBadge.innerHTML = `<span class="badge ${badgeClass}">${badgeText}</span>`;
                    
                    // Set up form actions
                    confirmForm.style.display = 'none';
                    rejectForm.style.display = 'none';
                    completeForm.style.display = 'none';
                    
                    if (status === 'awaiting_confirmation') {
                        confirmForm.style.display = 'block';
                        rejectForm.style.display = 'block';
                        confirmSewaId.value = id;
                        rejectSewaId.value = id;
                    } else if (status === 'confirmed') {
                        completeForm.style.display = 'block';
                        completeSewaId.value = id;
                    }
                    
                    // Show modal
                    modal.style.display = 'flex';
                });
            });
            
            // Close modal
            closeModal.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            closeModalBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });
            
            window.addEventListener('click', function(event) {
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
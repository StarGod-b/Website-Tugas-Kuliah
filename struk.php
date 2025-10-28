<?php
require 'config/Database.php';
$sewa_id = $_GET['id'] ?? null;

$db = new Database();
$conn = $db->connect();

// Ambil data sewa + data mobil terkait
$stmt = $conn->prepare("
    SELECT 
        s.*, 
        CONCAT(m.brand, ' ', m.model) AS nama_mobil 
    FROM 
        sewa s 
    JOIN 
        mobil m ON s.mobil_id = m.id 
    WHERE 
        s.id = ?
");
$stmt->execute([$sewa_id]);
$data = $stmt->fetch();

if (!$data) {
    die("Transaksi tidak ditemukan.");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk Transaksi</title>
    <style>
        body { font-family: Arial; margin: 40px; }
        .struk {
            border: 1px solid #000;
            padding: 20px;
            width: 350px;
        }
    </style>
</head>
<body onload="window.print()">
    <div class="struk">
        <h2>Struk Penyewaan</h2>
        <hr>
        <p><strong>Nama Mobil:</strong> <?= htmlspecialchars($data['nama_mobil']) ?></p>
        <p><strong>Tanggal Mulai:</strong> <?= htmlspecialchars($data['tanggal_mulai']) ?></p>
        <p><strong>Tanggal Selesai:</strong> <?= htmlspecialchars($data['tanggal_selesai']) ?></p>
        <p><strong>Lama Sewa:</strong> <?= htmlspecialchars($data['rental_days']) ?> hari</p>
        <p><strong>Total Harga:</strong> Rp<?= number_format($data['total_harga'], 0, ',', '.') ?></p>
        <p><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($data['metode_pembayaran']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($data['status']) ?></p>
        <hr>
        <p style="text-align: center;">Terima kasih atas kepercayaan Anda!</p>
    </div>
</body>
</html>

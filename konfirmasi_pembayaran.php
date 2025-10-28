<?php
session_start();
require 'config/Database.php';

// INISIALISASI OBJEK KONEKSI
$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'confirm') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Token keamanan tidak valid.");
    }

    $sewa_id = $_POST['sewa_id'];

    $stmt = $conn->prepare("UPDATE sewa SET status = 'confirmed' WHERE id = ?");
    if ($stmt->execute([$sewa_id])) {
        // Redirect ke struk otomatis
        header("Location: struk.php?id=" . urlencode($sewa_id));
        exit;
    } else {
        echo "Gagal mengonfirmasi pembayaran.";
    }
}
?>

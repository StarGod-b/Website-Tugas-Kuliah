<?php
session_start();
include 'config/Database.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$db = new Database();
$conn = $db->connect();

$errors = [];
$success = false;
$mobil = null;

// Get car data
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM mobil WHERE id = ?");
    $stmt->execute([$id]);
    $mobil = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$mobil) {
        header("Location: admin_mobil.php");
        exit();
    }
} else {
    header("Location: admin_mobil.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $brand = trim($_POST['brand']);
    $model = trim($_POST['model']);
    $type = trim($_POST['type']);
    $fuel_type = trim($_POST['fuel_type']);
    $transmission = trim($_POST['transmission']);
    $seats = (int)$_POST['seats'];

    $price_per_day = (int)$_POST['price_per_day'];
    $status = trim($_POST['status']);
    $image = trim($_POST['image']);

    // Basic validation
    if (empty($brand)) $errors[] = "Merek mobil harus diisi";
    if (empty($model)) $errors[] = "Model mobil harus diisi";
    if ($price_per_day <= 0) $errors[] = "Harga sewa harus lebih dari 0";

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("UPDATE mobil SET 
                                    brand = ?, 
                                    model = ?, 
                                    type = ?, 
                                    fuel_type = ?, 
                                    transmission = ?, 
                                    seats = ?, 
                                    
                                    price_per_day = ?, 
                                    status = ?, 
                                    image = ? 
                                    WHERE id = ?");
            $stmt->execute([$brand, $model, $type, $fuel_type, $transmission, $seats, $price_per_day, $status, $image, $id]);
            $success = true;
            // Refresh car data
            $stmt = $conn->prepare("SELECT * FROM mobil WHERE id = ?");
            $stmt->execute([$id]);
            $mobil = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mobil | RentalMobil.SG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --secondary: #64748b;
            --secondary-hover: #475569;
            --success: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #1e293b;
            --light: #f8fafc;
            --gray: #94a3b8;
            --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f1f5f9;
            color: var(--dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: white;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .logo i {
            font-size: 1.8rem;
        }
        
        .header-actions {
            display: flex;
            gap: 15px;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 6px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            border: 1px solid var(--primary);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
        }
        
        .btn-secondary {
            background-color: white;
            color: var(--secondary);
            border: 1px solid #cbd5e1;
        }
        
        .btn-secondary:hover {
            background-color: #f8fafc;
            color: var(--secondary-hover);
            border-color: #94a3b8;
        }
        
        .page-title {
            margin-bottom: 30px;
        }
        
        .page-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--dark);
        }
        
        .page-title p {
            color: var(--secondary);
            font-size: 1rem;
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: var(--card-shadow);
            max-width: 800px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark);
            font-size: 0.95rem;
        }
        
        .form-group input, 
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            background: white;
            transition: border 0.2s;
        }
        
        .form-group input:focus, 
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-rent {
            background-color: var(--primary);
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-rent:hover {
            background-color: var(--primary-hover);
        }
        
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.15);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .alert ul {
            margin-left: 20px;
            margin-top: 5px;
        }
        
        .alert li {
            margin-bottom: 3px;
        }
        
        .btn-edit {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }
        
        .btn-edit:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo">
                <i class="fas fa-car"></i>
                <span>RentalMobil.SG</span>
            </div>
            <div class="header-actions">
                <a href="admin_mobil.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar Mobil
                </a>
                <a href="logout.php" class="btn btn-primary">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-title">
            <h1>Edit Mobil</h1>
            <p>Perbarui informasi mobil di bawah ini</p>
        </div>
        
        <div class="form-container">
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> Mobil berhasil diperbarui!
                </div>
            <?php elseif (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="brand">Merek Mobil</label>
                    <input type="text" id="brand" name="brand" value="<?= htmlspecialchars($mobil['brand']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="model">Model Mobil</label>
                    <input type="text" id="model" name="model" value="<?= htmlspecialchars($mobil['model']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="type">Tipe Mobil</label>
                    <select id="type" name="type" required>
                        <option value="SUV" <?= $mobil['type'] === 'SUV' ? 'selected' : '' ?>>SUV</option>
                        <option value="Sedan" <?= $mobil['type'] === 'Sedan' ? 'selected' : '' ?>>Sedan</option>
                        <option value="MPV" <?= $mobil['type'] === 'MPV' ? 'selected' : '' ?>>MPV</option>
                        <option value="Hatchback" <?= $mobil['type'] === 'Hatchback' ? 'selected' : '' ?>>Hatchback</option>
                        <option value="Sport" <?= $mobil['type'] === 'Sport' ? 'selected' : '' ?>>Sport</option>
                        <option value="LCGC" <?= $mobil['type'] === 'LCGC' ? 'selected' : '' ?>>LCGC</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="fuel_type">Jenis Bahan Bakar</label>
                    <select id="fuel_type" name="fuel_type" required>
                        <option value="Bensin" <?= $mobil['fuel_type'] === 'Bensin' ? 'selected' : '' ?>>Bensin</option>
                        <option value="Solar" <?= $mobil['fuel_type'] === 'Solar' ? 'selected' : '' ?>>Solar</option>
                        <option value="Listrik" <?= $mobil['fuel_type'] === 'Listrik' ? 'selected' : '' ?>>Listrik</option>
                        <option value="Hybrid" <?= $mobil['fuel_type'] === 'Hybrid' ? 'selected' : '' ?>>Hybrid</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="transmission">Transmisi</label>
                    <select id="transmission" name="transmission" required>
                        <option value="Automatic" <?= $mobil['transmission'] === 'Automatic' ? 'selected' : '' ?>>Automatic</option>
                        <option value="Manual" <?= $mobil['transmission'] === 'Manual' ? 'selected' : '' ?>>Manual</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="seats">Jumlah Kursi</label>
                    <input type="number" id="seats" name="seats" min="2" max="10" value="<?= htmlspecialchars($mobil['seats']) ?>" required>
                </div>
                
                
                
                <div class="form-group">
                    <label for="price_per_day">Harga Sewa per Hari (Rp)</label>
                    <input type="number" id="price_per_day" name="price_per_day" min="100000" value="<?= htmlspecialchars($mobil['price_per_day']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="available" <?= ($mobil['status'] === 'available' || $mobil['status'] === 'tersedia') ? 'selected' : '' ?>>Tersedia</option>
                        <option value="rented" <?= ($mobil['status'] === 'rented' || $mobil['status'] === 'disewa') ? 'selected' : '' ?>>Disewa</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">URL Gambar Mobil</label>
                    <input type="text" id="image" name="image" value="<?= htmlspecialchars($mobil['image']) ?>" required>
                </div>
                
                <div class="form-actions">
                    <a href="admin_mobil.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Batal
                    </a>
                    <button type="submit" class="btn-rent">
                        <i class="fas fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
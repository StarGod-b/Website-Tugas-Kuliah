<?php
session_start();
include 'config/Database.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$db = new Database();
$conn = $db->connect();

$error = '';
$success = '';

$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email_baru = trim($_POST['email_baru']);
    $password_sekarang = trim($_POST['password_sekarang']);
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    if (!password_verify($password_sekarang, $user['password'])) {
        $error = 'Password saat ini salah!';
    } elseif (!empty($email_baru) && !filter_var($email_baru, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid!';
    } elseif (!empty($password_baru) && $password_baru !== $konfirmasi_password) {
        $error = 'Password baru dan konfirmasi tidak cocok!';
    } elseif (!empty($password_baru) && strlen($password_baru) < 6) {
        $error = 'Password baru minimal 6 karakter!';
    } else {
        $email_update = !empty($email_baru) ? $email_baru : $user['email'];
        $password_update = !empty($password_baru)
            ? password_hash($password_baru, PASSWORD_DEFAULT)
            : $user['password'];

        $stmt = $conn->prepare("UPDATE users SET email = :email, password = :password WHERE id = :id");
        $stmt->bindParam(':email', $email_update);
        $stmt->bindParam(':password', $password_update);
        $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $success = 'Profil berhasil diperbarui!';
            // Refresh data user
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $error = 'Terjadi kesalahan saat memperbarui profil.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - Rental Mobil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        :root {
            --primary-blue: #1e3a8a;
            --secondary-blue: #3b82f6;
            --accent-orange: #f97316;
            --light-orange: #fdba74;
            --dark-gray: #1f2937;
            --light-gray: #f3f4f6;
            --white: #ffffff;
        }

        body {
            background: linear-gradient(135deg, var(--primary-blue), var(--secondary-blue));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background-color: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            flex-direction: column;
        }

        .header {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-orange));
            color: var(--white);
            padding: 25px 30px;
            text-align: center;
            position: relative;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            color: var(--white);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-50%) scale(1.05);
        }

        .content {
            display: flex;
            padding: 0;
        }

        .sidebar {
            background: linear-gradient(to bottom, var(--secondary-blue), var(--accent-orange));
            width: 250px;
            padding: 30px 20px;
            color: var(--white);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .user-info {
            text-align: center;
            margin-bottom: 30px;
        }

        .user-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background-color: var(--light-orange);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 40px;
            color: var(--primary-blue);
            border: 3px solid var(--white);
        }

        .user-info h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .user-info p {
            font-size: 14px;
            opacity: 0.9;
        }

        .role-badge {
            background-color: var(--accent-orange);
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 10px;
            display: inline-block;
        }

        .nav-links {
            width: 100%;
            margin-top: 20px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            margin-bottom: 8px;
            border-radius: 8px;
            color: var(--white);
            text-decoration: none;
            transition: all 0.3s;
        }

        .nav-link i {
            margin-right: 10px;
            font-size: 18px;
        }

        .nav-link:hover, .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .form-container {
            flex: 1;
            padding: 30px;
        }

        .form-header {
            margin-bottom: 25px;
        }

        .form-header h2 {
            font-size: 24px;
            color: var(--dark-gray);
            margin-bottom: 10px;
            position: relative;
            display: inline-block;
        }

        .form-header h2::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--secondary-blue), var(--accent-orange));
            border-radius: 3px;
        }

        .form-header p {
            color: #6b7280;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--dark-gray);
            font-size: 14px;
        }

        .input-with-icon {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--secondary-blue);
            font-size: 18px;
        }

        .form-control {
            width: 100%;
            padding: 14px 14px 14px 45px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--secondary-blue);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .btn-container {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .btn {
            padding: 12px 25px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--secondary-blue), var(--accent-orange));
            color: var(--white);
            flex: 1;
        }

        .btn-secondary {
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }

        .btn i {
            margin-right: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-primary:hover {
            background: linear-gradient(90deg, #2563eb, #ea580c);
        }

        .btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 15px;
            display: flex;
            align-items: center;
        }

        .alert i {
            margin-right: 10px;
            font-size: 20px;
        }

        .alert-error {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .password-info {
            background-color: var(--light-gray);
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            font-size: 14px;
            color: #4b5563;
        }

        .password-info ul {
            padding-left: 20px;
            margin-top: 8px;
        }

        .password-info li {
            margin-bottom: 5px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: var(--light-gray);
            color: #6b7280;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <button class="back-btn" onclick="window.location.href='dashboard.php'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1>Edit Profil Pengguna</h1>
            <p>Kelola informasi akun dan keamanan Anda</p>
        </div>
        
        <div class="content">
            <div class="sidebar">
                <div class="user-info">
                    <div class="user-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?= htmlspecialchars($user['name']) ?></h2>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                    <span class="role-badge"><?= htmlspecialchars($user['role']) ?></span>
                </div>
                
                <div class="nav-links">
                    <a href="#" class="nav-link active">
                        <i class="fas fa-user-edit"></i> Edit Profil
                    <a href="logout.php" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
            
            <div class="form-container">
                <div class="form-header">
                    <h2>Perbarui Informasi Akun</h2>
                    <p>Lengkapi formulir di bawah untuk memperbarui informasi profil Anda</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Email Saat Ini</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Email Baru</label>
                        <div class="input-with-icon">
                            <i class="fas fa-envelope-open input-icon"></i>
                            <input type="email" name="email_baru" class="form-control" placeholder="Masukkan email baru (kosongkan jika tidak ingin mengganti)">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password Saat Ini *</label>
                        <div class="input-with-icon">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" name="password_sekarang" class="form-control" placeholder="Masukkan password saat ini" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Password Baru</label>
                        <div class="input-with-icon">
                            <i class="fas fa-key input-icon"></i>
                            <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru (minimal 6 karakter)">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Konfirmasi Password Baru</label>
                        <div class="input-with-icon">
                            <i class="fas fa-key input-icon"></i>
                            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Konfirmasi password baru">
                        </div>
                    </div>
                    
                    <div class="password-info">
                        <strong>Tips Keamanan Password:</strong>
                        <ul>
                            <li>Gunakan minimal 6 karakter</li>
                            <li>Kombinasikan huruf besar, huruf kecil, dan angka</li>
                            <li>Hindari menggunakan informasi pribadi</li>
                            <li>Jangan gunakan password yang sama di beberapa akun</li>
                        </ul>
                    </div>
                    
                    <div class="btn-container">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='dashboard.php'">
                            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="footer">
            &copy; <?= date('Y') ?> Rental Mobil. Hak Cipta Dilindungi.
        </div>
    </div>
</body>
</html>
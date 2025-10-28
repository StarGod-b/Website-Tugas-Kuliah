<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

include 'config/Database.php';

$db = new Database();
$conn = $db->connect();

// Tambah admin baru
$success_message = "";
$error_message = "";

if (isset($_POST['add_admin'])) {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $role     = 'admin';
    $hash     = password_hash($password, PASSWORD_DEFAULT);

    try {
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hash, $role]);
        $success_message = "Admin berhasil ditambahkan!";
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            $error_message = "Email sudah terdaftar!";
        } else {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Hapus user jika ada permintaan delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'user_id'");
    $stmt->execute([$id]);
    header("Location: admin_users.php");
    exit;
}

// Ambil semua user (selain admin)
$stmt = $conn->prepare("SELECT * FROM users WHERE role = 'user'");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --olx-orange: #e67e22;
            --olx-orange-hover: #d35400;
            --olx-dark: #2c3e50;
            --olx-light: #ecf0f1;
        }
        
        .btn-primary {
            background-color: var(--olx-orange);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--olx-orange-hover);
            transform: translateY(-2px);
        }
        
        .card {
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .table-row {
            transition: all 0.2s ease;
        }
        
        .table-row:hover {
            background-color: #f9f9f9;
        }
        
        .input-focus:focus {
            border-color: var(--olx-orange);
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.2);
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadein {
            animation: fadeIn 0.4s ease-out forwards;
        }
        
        .action-btn {
            transition: all 0.2s ease;
        }
        
        .action-btn:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-users-cog text-orange-500 mr-2"></i> Kelola Data Pengguna
            </h1>
            <a href="dashboard.php" class="flex items-center text-orange-500 hover:text-orange-700">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Dashboard
            </a>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded animate-fadein">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <p><?= $success_message ?></p>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded animate-fadein">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <p><?= $error_message ?></p>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Admin Card -->
        <div class="card bg-white rounded-lg overflow-hidden mb-8 animate-fadein">
            <div class="bg-orange-500 text-white px-6 py-3">
                <h2 class="text-lg font-semibold">
                    <i class="fas fa-user-plus mr-2"></i> Tambah Admin Baru
                </h2>
            </div>
            <div class="p-6">
                <form method="POST">
                    <input type="hidden" name="add_admin" value="1">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700 mb-2">Nama Admin</label>
                            <input type="text" name="name" placeholder="Nama lengkap" required 
                                   class="input-focus w-full px-4 py-2 border rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" placeholder="Alamat email" required 
                                   class="input-focus w-full px-4 py-2 border rounded-lg focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-gray-700 mb-2">Password</label>
                            <input type="password" name="password" placeholder="Password" required 
                                   class="input-focus w-full px-4 py-2 border rounded-lg focus:outline-none">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-save mr-2"></i> Simpan Admin
                    </button>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card bg-white rounded-lg overflow-hidden animate-fadein">
            <div class="bg-orange-500 text-white px-6 py-3">
                <h2 class="text-lg font-semibold">
                    <i class="fas fa-users mr-2"></i> Daftar Pengguna
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($users as $user): ?>
                        <tr class="table-row">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= $user['id'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($user['name']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= htmlspecialchars($user['email']) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-3">
                                    <a href="#" class="action-btn text-blue-500 hover:text-blue-700" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?= $user['id'] ?>" 
                                       onclick="return confirm('Yakin ingin menghapus pengguna ini?')" 
                                       class="action-btn text-red-500 hover:text-red-700" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Empty State -->
        <?php if (empty($users)): ?>
        <div class="card bg-white rounded-lg p-8 text-center mt-6 animate-fadein">
            <i class="fas fa-users-slash text-4xl text-gray-400 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada pengguna terdaftar</h3>
            <p class="text-gray-500">Saat ini tidak ada pengguna yang terdaftar dalam sistem.</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        // Confirm before delete
        document.querySelectorAll('a[onclick]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Yakin ingin menghapus pengguna ini?')) {
                    e.preventDefault();
                }
            });
        });

        // Animate elements on scroll
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fadein');
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.card, [class*="message"]').forEach(el => {
            observer.observe(el);
        });
    </script>
</body>
</html>
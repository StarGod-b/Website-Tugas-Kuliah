<?php
class User {
    private $conn;
    public function __construct($db) {
        $this->conn = $db;
    }
    public function register($name, $email, $password) {
        // Cek apakah email sudah terdaftar
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            return "Email sudah terdaftar.";
        }
    
        // Hash password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
        // Simpan user baru
        $stmt = $this->conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        
        if ($stmt->execute([$name, $email, $hashedPassword])) {
            return true;
        } else {
            return "Gagal melakukan registrasi. Silakan coba lagi.";
        }
    }
    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            return true;
        }
        return false;
    }
}    
?>
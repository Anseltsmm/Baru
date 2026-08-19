<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

startSession();

// Redirect if already logged in
if (isLoggedIn()) {
    redirect(APP_URL . '/public/dashboard.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($token)) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $username = sanitize($_POST['username']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $full_name = sanitize($_POST['full_name']);
        $phone = sanitize($_POST['phone']);
        
        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($full_name) || empty($phone)) {
            $error = 'Semua field wajib diisi.';
        } elseif (strlen($username) < 4) {
            $error = 'Username minimal 4 karakter.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
        } elseif (strlen($password) < 6) {
            $error = 'Password minimal 6 karakter.';
        } elseif ($password !== $confirm_password) {
            $error = 'Konfirmasi password tidak cocok.';
        } else {
            $db = getDB();
            
            // Check if username or email already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetch()) {
                $error = 'Username atau email sudah digunakan.';
            } else {
                // Insert new user
                $hashed_password = hashPassword($password);
                $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, balance) VALUES (?, ?, ?, ?, ?, 0)");
                
                if ($stmt->execute([$username, $email, $hashed_password, $full_name, $phone])) {
                    $success = 'Registrasi berhasil! Silakan login.';
                } else {
                    $error = 'Terjadi kesalahan. Silakan coba lagi.';
                }
            }
        }
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= assetsPath('css/style.css') ?>">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1><?= APP_NAME ?></h1>
                <p>Daftar akun baru</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
                <p style="text-align: center; margin-top: 15px;">
                    <a href="<?= APP_URL ?>/public/login.php" class="btn btn-primary">Login Sekarang</a>
                </p>
            <?php else: ?>
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="form-group">
                    <label for="full_name">Nama Lengkap</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Masukkan nama lengkap" required value="<?= $_POST['full_name'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Pilih username" required value="<?= $_POST['username'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Masukkan email" required value="<?= $_POST['email'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">No. HP</label>
                    <input type="tel" id="phone" name="phone" placeholder="Contoh: 08123456789" required value="<?= $_POST['phone'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi password" required>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Daftar</button>
            </form>
            
            <div class="login-footer">
                <p>Sudah punya akun? <a href="<?= APP_URL ?>/public/login.php">Login di sini</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
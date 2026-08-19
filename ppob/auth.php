<?php
require 'db.php';

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['balance'] = $user['balance'];
            header("Location: dashboard.php");
            exit;
        } else {
            header("Location: login.php?error=1");
            exit;
        }
    }
    
    if(isset($_POST['register'])) {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, fullname, email, balance, role) VALUES (?, ?, ?, ?, 0, 'user')");
            $stmt->execute([$username, $password, $fullname, $email]);
            header("Location: login.php?success=1");
            exit;
        } catch(PDOException $e) {
            header("Location: login.php?error=register");
            exit;
        }
    }
}

if(isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>
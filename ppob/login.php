<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PPOB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h1>💰 PPOB System</h1>
            <p>Silakan login untuk melanjutkan</p>
            
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-error">Username atau password salah!</div>
            <?php endif; ?>
            
            <form method="POST" action="auth.php">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" required placeholder="Masukkan username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" required placeholder="Masukkan password">
                </div>
                <button type="submit" name="login" class="btn btn-primary">Login</button>
            </form>
            
            <div class="demo-info">
                <p><strong>Demo Account:</strong></p>
                <p>Username: <code>admin</code></p>
                <p>Password: <code>admin123</code></p>
            </div>
        </div>
    </div>
</body>
</html>
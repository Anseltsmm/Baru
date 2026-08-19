<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

startSession();
requireLogin();

$user = getUser($_SESSION['user_id']);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    
    if (!verifyCSRFToken($token)) {
        $error = 'Token keamanan tidak valid.';
    } else {
        $amount = floatval($_POST['amount']);
        $payment_method = sanitize($_POST['payment_method']);
        
        if ($amount < 10000) {
            $error = 'Minimal top up adalah Rp 10.000';
        } elseif ($amount > 10000000) {
            $error = 'Maksimal top up adalah Rp 10.000.000';
        } else {
            $admin_fee = 0;
            if ($payment_method === 'transfer') {
                $admin_fee = 2500;
            }
            $total = $amount + $admin_fee;
            
            $db = getDB();
            $stmt = $db->prepare("
                INSERT INTO topups (user_id, amount, payment_method, total, status, admin_fee)
                VALUES (?, ?, ?, ?, 'pending', ?)
            ");
            
            if ($stmt->execute([$_SESSION['user_id'], $amount, $payment_method, $total, $admin_fee])) {
                $success = 'Permintaan top up berhasil dibuat. Silakan lakukan pembayaran sebesar ' . formatCurrency($total) . ' dan upload bukti transfer.';
            } else {
                $error = 'Terjadi kesalahan. Silakan coba lagi.';
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
    <title>Top Up Saldo - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php"><?= APP_NAME ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link active" href="topup.php"><i class="bi bi-wallet2"></i> Top Up Saldo</a></li>
                    <li class="nav-item"><a class="nav-link" href="pulsa.php"><i class="bi bi-phone"></i> Pulsa & Data</a></li>
                    <li class="nav-item"><a class="nav-link" href="pln.php"><i class="bi bi-receipt"></i> Pembayaran</a></li>
                    <li class="nav-item"><a class="nav-link" href="riwayat.php"><i class="bi bi-clock-history"></i> Riwayat</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['full_name']) ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Top Up Saldo</li>
            </ol>
        </nav>

        <div class="row">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-wallet2"></i> Saldo Anda</h5>
                        <h2 class="text-primary"><?= formatCurrency($user['balance']) ?></h2>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Top Up</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Minimal: Rp 10.000</li>
                            <li class="mb-2"><i class="bi bi-check-circle text-success"></i> Maksimal: Rp 10.000.000</li>
                            <li class="mb-2"><i class="bi bi-info-circle text-info"></i> Admin fee: Rp 2.500 (Transfer)</li>
                            <li><i class="bi bi-clock text-warning"></i> Proses: 1-5 menit</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Top Up Saldo</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Jumlah Top Up</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" name="amount" placeholder="Masukkan jumlah" min="10000" max="10000000" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="">Pilih metode</option>
                                    <option value="transfer">Transfer Bank (Admin Rp 2.500)</option>
                                    <option value="qris">QRIS</option>
                                    <option value="ewallet">E-Wallet (GoPay/OVO/DANA)</option>
                                </select>
                            </div>

                            <div class="alert alert-info">
                                <strong>Total yang harus dibayar:</strong> <span id="total">Rp 0</span>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-send"></i> Buat Permintaan Top Up
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('input[name="amount"]').addEventListener('input', function() {
            const amount = parseFloat(this.value) || 0;
            const total = amount + 2500;
            document.getElementById('total').textContent = 'Rp ' + total.toLocaleString('id-ID');
        });
    </script>
</body>
</html>
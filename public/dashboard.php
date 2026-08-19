<?php
session_start();

// Simple auth check for demo
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Mock user data for demo
$user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'full_name' => $_SESSION['full_name'],
    'balance' => $_SESSION['balance'],
    'email' => 'user@example.com',
    'phone' => '081234567890'
];

// Mock transactions
$transactions = [
    [
        'id' => 1,
        'provider_name' => 'Telkomsel',
        'provider_type' => 'pulsa',
        'denom' => '50.000',
        'phone_number' => '081234567890',
        'amount' => 50000,
        'admin_fee' => 0,
        'total' => 50000,
        'status' => 'success',
        'created_at' => '2024-01-15 10:30:00'
    ],
    [
        'id' => 2,
        'provider_name' => 'PLN',
        'provider_type' => 'pln',
        'denom' => '100.000',
        'phone_number' => '123456789',
        'amount' => 100000,
        'admin_fee' => 2500,
        'total' => 102500,
        'status' => 'success',
        'created_at' => '2024-01-14 15:20:00'
    ],
    [
        'id' => 3,
        'provider_name' => 'Indosat',
        'provider_type' => 'pulsa',
        'denom' => '25.000',
        'phone_number' => '081234567891',
        'amount' => 25000,
        'admin_fee' => 0,
        'total' => 25000,
        'status' => 'failed',
        'created_at' => '2024-01-14 09:15:00'
    ]
];

// Mock stats
$stats = [
    'total_transactions' => 15,
    'total_spent' => 450000,
    'pending' => 2,
    'success' => 13
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= APP_NAME ?></title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="topup.php"><i class="bi bi-wallet2"></i> Top Up Saldo</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-phone"></i> Pulsa & Data
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pulsa.php">Beli Pulsa</a></li>
                            <li><a class="dropdown-item" href="data.php">Beli Paket Data</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-receipt"></i> Pembayaran
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="pln.php">Token PLN</a></li>
                            <li><a class="dropdown-item" href="pdam.php">PDAM</a></li>
                            <li><a class="dropdown-item" href="bpjs.php">BPJS</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="riwayat.php"><i class="bi bi-clock-history"></i> Riwayat</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="bi bi-person-circle"></i> <?= htmlspecialchars($user['full_name']) ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Balance Card -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-wallet2"></i> Saldo Anda</h5>
                        <h2 class="mb-0"><?= formatCurrency($user['balance']) ?></h2>
                        <a href="topup.php" class="btn btn-light btn-sm mt-2">Top Up Sekarang</a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="text-muted"><i class="bi bi-check-circle"></i> Transaksi Sukses</h6>
                                <h3 class="text-success"><?= $stats['success'] ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="text-muted"><i class="bi bi-clock"></i> Pending</h6>
                                <h3 class="text-warning"><?= $stats['pending'] ?></h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <h5><i class="bi bi-lightning"></i> Aksi Cepat</h5>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <a href="pulsa.php" class="btn btn-outline-primary w-100">
                    <i class="bi bi-phone"></i><br>Beli Pulsa
                </a>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <a href="data.php" class="btn btn-outline-success w-100">
                    <i class="bi bi-wifi"></i><br>Paket Data
                </a>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <a href="pln.php" class="btn btn-outline-warning w-100">
                    <i class="bi bi-lightbulb"></i><br>Token PLN
                </a>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <a href="topup.php" class="btn btn-outline-info w-100">
                    <i class="bi bi-plus-circle"></i><br>Top Up
                </a>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-clock-history"></i> Transaksi Terakhir</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Layanan</th>
                                <th>Nomor</th>
                                <th>Jumlah</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $trx): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></td>
                                <td>
                                    <span class="badge bg-<?= $trx['provider_type'] === 'pulsa' ? 'primary' : 'warning' ?>">
                                        <?= $trx['provider_name'] ?>
                                    </span>
                                    <?= $trx['denom'] ?>
                                </td>
                                <td><?= $trx['phone_number'] ?></td>
                                <td><?= formatCurrency($trx['total']) ?></td>
                                <td>
                                    <?php if ($trx['status'] === 'success'): ?>
                                        <span class="badge bg-success">Sukses</span>
                                    <?php elseif ($trx['status'] === 'pending'): ?>
                                        <span class="badge bg-warning">Pending</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Gagal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <a href="riwayat.php" class="btn btn-primary btn-sm">Lihat Semua Riwayat</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p class="mb-0">&copy; 2024 <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
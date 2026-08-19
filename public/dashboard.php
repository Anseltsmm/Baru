<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

startSession();
requireLogin();

$user = getUser(getCurrentUserId());
$stats = getStats();
$recent_transactions = getRecentTransactions(5);

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= APP_NAME ?></title>
    <link rel="stylesheet" href="<?= assetsPath('css/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-left">
                <h1 class="logo"><?= APP_NAME ?></h1>
            </div>
            <div class="header-right">
                <div class="user-menu">
                    <span class="user-name"><i class="fas fa-user"></i> <?= htmlspecialchars($user['full_name']) ?></span>
                    <span class="user-balance"><?= formatCurrency($user['balance']) ?></span>
                    <a href="<?= APP_URL ?>/api/auth/logout.php" class="btn btn-danger btn-sm">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="<?= APP_URL ?>/public/dashboard.php" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="<?= APP_URL ?>/public/topup.php"><i class="fas fa-wallet"></i> Top Up</a></li>
                <li><a href="<?= APP_URL ?>/public/pulsa.php"><i class="fas fa-mobile-alt"></i> Pulsa & Data</a></li>
                <li><a href="<?= APP_URL ?>/public/pembayaran.php"><i class="fas fa-file-invoice"></i> Pembayaran</a></li>
                <li><a href="<?= APP_URL ?>/public/riwayat.php"><i class="fas fa-history"></i> Riwayat</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <?php if ($flash): ?>
                <div class="alert alert-<?= $flash['type'] ?>"><?= $flash['message'] ?></div>
            <?php endif; ?>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon" style="background: #3498db;">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Saldo Saya</h3>
                        <p class="stat-value"><?= formatCurrency($user['balance']) ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #2ecc71;">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Transaksi</h3>
                        <p class="stat-value"><?= number_format($stats['total_transactions']) ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #e74c3c;">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Transaksi Hari Ini</h3>
                        <p class="stat-value"><?= number_format($stats['today_transactions']) ?></p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon" style="background: #9b59b6;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total Belanja</h3>
                        <p class="stat-value"><?= formatCurrency($user['total_spent'] ?? 0) ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
                <div class="action-grid">
                    <a href="<?= APP_URL ?>/public/topup.php" class="action-card">
                        <i class="fas fa-plus-circle"></i>
                        <span>Top Up Saldo</span>
                    </a>
                    <a href="<?= APP_URL ?>/public/pulsa.php" class="action-card">
                        <i class="fas fa-mobile-alt"></i>
                        <span>Beli Pulsa</span>
                    </a>
                    <a href="<?= APP_URL ?>/public/pembayaran.php" class="action-card">
                        <i class="fas fa-bolt"></i>
                        <span>Bayar PLN</span>
                    </a>
                    <a href="<?= APP_URL ?>/public/riwayat.php" class="action-card">
                        <i class="fas fa-list"></i>
                        <span>Riwayat</span>
                    </a>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="recent-transactions">
                <h2><i class="fas fa-history"></i> Transaksi Terakhir</h2>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Layanan</th>
                                <th>Nominal</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($recent_transactions)): ?>
                                <tr>
                                    <td colspan="5" style="text-align: center;">Belum ada transaksi</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($recent_transactions as $tx): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($tx['created_at'])) ?></td>
                                    <td><?= htmlspecialchars($tx['provider_name']) ?></td>
                                    <td><?= htmlspecialchars($tx['denom']) ?></td>
                                    <td><?= formatCurrency($tx['total']) ?></td>
                                    <td>
                                        <span class="badge badge-<?= $tx['status'] ?>">
                                            <?= ucfirst($tx['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="table-footer">
                    <a href="<?= APP_URL ?>/public/riwayat.php" class="btn btn-secondary btn-sm">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 <?= APP_NAME ?>. All rights reserved.</p>
        </div>
    </footer>

    <script src="<?= assetsPath('js/main.js') ?>"></script>
</body>
</html>
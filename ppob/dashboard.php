<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require 'db.php';

// Get stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_products = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 'active'")->fetchColumn();
$total_transactions = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
$total_revenue = $pdo->query("SELECT SUM(amount) FROM transactions WHERE status = 'success'")->fetchColumn();

// Recent transactions
$recent = $pdo->query("
    SELECT t.*, p.name as product_name, u.fullname 
    FROM transactions t 
    JOIN products p ON t.product_id = p.id 
    JOIN users u ON t.user_id = u.id 
    ORDER BY t.created_at DESC 
    LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - PPOB</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="wrapper">
        <aside class="sidebar">
            <div class="logo">💰 PPOB</div>
            <nav>
                <a href="dashboard.php" class="active">📊 Dashboard</a>
                <a href="produk.php">📦 Produk</a>
                <a href="transaksi.php">💳 Transaksi</a>
                <a href="riwayat.php">📋 Riwayat</a>
                <a href="auth.php?logout=1">🚪 Logout</a>
            </nav>
        </aside>
        
        <main class="main">
            <header>
                <h1>Dashboard</h1>
                <div class="user-info">
                    <span>👤 <?php echo $_SESSION['fullname']; ?></span>
                    <span>💵 Rp <?php echo number_format($_SESSION['balance'], 0, ',', '.'); ?></span>
                </div>
            </header>
            
            <div class="stats">
                <div class="stat-card">
                    <h3>Total User</h3>
                    <p><?php echo $total_users; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Produk</h3>
                    <p><?php echo $total_products; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Transaksi</h3>
                    <p><?php echo $total_transactions; ?></p>
                </div>
                <div class="stat-card">
                    <h3>Total Pendapatan</h3>
                    <p>Rp <?php echo number_format($total_revenue ?: 0, 0, ',', '.'); ?></p>
                </div>
            </div>
            
            <div class="card">
                <h2>Transaksi Terbaru</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>User</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent as $t): ?>
                        <tr>
                            <td><?php echo date('d/m/Y H:i', strtotime($t['created_at'])); ?></td>
                            <td><?php echo $t['fullname']; ?></td>
                            <td><?php echo $t['product_name']; ?></td>
                            <td>Rp <?php echo number_format($t['amount'], 0, ',', '.'); ?></td>
                            <td><span class="badge badge-<?php echo $t['status']; ?>"><?php echo ucfirst($t['status']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
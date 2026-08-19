<?php
/**
 * Helper Functions
 */

/**
 * Start session
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current admin ID
 */
function getCurrentAdminId() {
    return $_SESSION['admin_id'] ?? null;
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Sanitize input
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Format currency
 */
function formatCurrency($amount) {
    return CURRENCY_SYMBOL . ' ' . number_format($amount, 0, ',', '.');
}

/**
 * Generate unique transaction ID
 */
function generateTransactionId() {
    return 'TRX' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 6));
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Check if user has enough balance
 */
function checkBalance($userId, $amount) {
    $db = getDB();
    $stmt = $db->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    return $user['balance'] >= $amount;
}

/**
 * Deduct balance
 */
function deductBalance($userId, $amount) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET balance = balance - ? WHERE id = ? AND balance >= ?");
    return $stmt->execute([$amount, $userId, $amount]);
}

/**
 * Add balance
 */
function addBalance($userId, $amount) {
    $db = getDB();
    $stmt = $db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
    return $stmt->execute([$amount, $userId]);
}

/**
 * Get user data
 */
function getUser($userId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    return $stmt->fetch();
}

/**
 * Get provider list
 */
function getProviders($type = null) {
    $db = getDB();
    
    if ($type) {
        $stmt = $db->prepare("SELECT * FROM providers WHERE type = ? AND active = 1 ORDER BY name");
        $stmt->execute([$type]);
    } else {
        $stmt = $db->query("SELECT * FROM providers WHERE active = 1 ORDER BY type, name");
    }
    
    return $stmt->fetchAll();
}

/**
 * Get nominals by provider
 */
function getNominals($providerId) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM nominals WHERE provider_id = ? AND active = 1 ORDER BY price");
    $stmt->execute([$providerId]);
    return $stmt->fetchAll();
}

/**
 * Get transaction history
 */
function getTransactionHistory($userId, $limit = 20) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT t.*, p.name as provider_name, p.type as provider_type, n.denom
        FROM transactions t
        JOIN providers p ON t.provider_id = p.id
        JOIN nominals n ON t.nominal_id = n.id
        WHERE t.user_id = ?
        ORDER BY t.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$userId, $limit]);
    return $stmt->fetchAll();
}

/**
 * Get transaction by ID
 */
function getTransaction($transactionId) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT t.*, p.name as provider_name, p.type as provider_type, n.denom
        FROM transactions t
        JOIN providers p ON t.provider_id = p.id
        JOIN nominals n ON t.nominal_id = n.id
        WHERE t.id = ?
    ");
    $stmt->execute([$transactionId]);
    return $stmt->fetch();
}

/**
 * Create transaction
 */
function createTransaction($data) {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO transactions (user_id, provider_id, nominal_id, phone_number, amount, admin_fee, total, status, message)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([
        $data['user_id'],
        $data['provider_id'],
        $data['nominal_id'],
        $data['phone_number'],
        $data['amount'],
        $data['admin_fee'] ?? 0,
        $data['total'],
        $data['status'],
        $data['message'] ?? null
    ]);
}

/**
 * Update transaction status
 */
function updateTransactionStatus($transactionId, $status, $message = null) {
    $db = getDB();
    $stmt = $db->prepare("
        UPDATE transactions 
        SET status = ?, message = ? 
        WHERE id = ?
    ");
    return $stmt->execute([$status, $message, $transactionId]);
}

/**
 * Get recent transactions
 */
function getRecentTransactions($limit = 5) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT t.*, p.name as provider_name
        FROM transactions t
        JOIN providers p ON t.provider_id = p.id
        ORDER BY t.created_at DESC
        LIMIT ?
    ");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

/**
 * Get stats
 */
function getStats() {
    $db = getDB();
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as total FROM users");
    $totalUsers = $stmt->fetch()['total'];
    
    // Total transactions
    $stmt = $db->query("SELECT COUNT(*) as total FROM transactions");
    $totalTransactions = $stmt->fetch()['total'];
    
    // Total revenue (success transactions)
    $stmt = $db->query("SELECT SUM(total) as total FROM transactions WHERE status = 'success'");
    $totalRevenue = $stmt->fetch()['total'] ?? 0;
    
    // Today's transactions
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM transactions WHERE DATE(created_at) = CURDATE()");
    $stmt->execute();
    $todayTransactions = $stmt->fetch()['total'];
    
    return [
        'total_users' => $totalUsers,
        'total_transactions' => $totalTransactions,
        'total_revenue' => $totalRevenue,
        'today_transactions' => $todayTransactions
    ];
}

/**
 * Flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Require login
 */
function requireLogin() {
    startSession();
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Silakan login terlebih dahulu.');
        redirect(APP_URL . '/public/login.php');
    }
}

/**
 * Require admin login
 */
function requireAdminLogin() {
    startSession();
    if (!isAdminLoggedIn()) {
        setFlashMessage('error', 'Silakan login admin terlebih dahulu.');
        redirect(APP_URL . '/public/admin-login.php');
    }
}

/**
 * CSRF Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF Token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Validate phone number
 */
function validatePhoneNumber($phone) {
    // Remove leading 0 if present
    $phone = ltrim($phone, '0');
    
    // Check if starts with 62
    if (substr($phone, 0, 2) === '62') {
        return $phone;
    }
    
    // Add 62 prefix
    return '62' . $phone;
}

/**
 * Get provider by code
 */
function getProviderByCode($code) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM providers WHERE code = ? AND active = 1");
    $stmt->execute([$code]);
    return $stmt->fetch();
}
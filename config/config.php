<?php
/**
 * Database Configuration
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'ppob_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App Configuration
define('APP_NAME', 'PPOB System');
define('APP_URL', 'http://localhost/ppoB');
define('APP_VERSION', '1.0.0');

// Session Configuration
define('SESSION_LIFETIME', 3600); // 1 hour

// API Configuration
define('API_KEY', 'your-secret-api-key-here');
define('API_TIMEOUT', 30);

// Currency
define('CURRENCY', 'Rp');
define('CURRENCY_SYMBOL', 'Rp');

/**
 * Database Connection
 */
function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Database connection failed. Please try again later.");
        }
    }
    
    return $pdo;
}

/**
 * Get base path
 */
function basePath() {
    return dirname(__DIR__);
}

/**
 * Get public path
 */
function publicPath() {
    return basePath() . '/public';
}

/**
 * Get assets path
 */
function assetsPath($file) {
    return APP_URL . '/assets/' . $file;
}

/**
 * Get includes path
 */
function includesPath($file) {
    return basePath() . '/includes/' . $file;
}
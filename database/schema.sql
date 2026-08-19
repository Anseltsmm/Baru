-- PPOB Database Schema
-- Database: ppob_db

CREATE DATABASE IF NOT EXISTS ppob_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ppob_db;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    balance DECIMAL(15, 2) DEFAULT 0.00,
    status ENUM('active', 'suspended', 'blocked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Admin
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Providers (Provider PPOB)
CREATE TABLE IF NOT EXISTS providers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('pulsa', 'data', 'pln', 'pdam', 'bpjs', 'game', 'e_money') NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    symbol VARCHAR(20) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Nominal Pulsa/Data
CREATE TABLE IF NOT EXISTS nominals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    provider_id INT NOT NULL,
    denom VARCHAR(50) NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    sell_price DECIMAL(15, 2) NOT NULL,
    active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE
);

-- Tabel Transaksi
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    provider_id INT NOT NULL,
    nominal_id INT NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    reference_number VARCHAR(100),
    serial_number VARCHAR(100),
    amount DECIMAL(15, 2) NOT NULL,
    admin_fee DECIMAL(15, 2) DEFAULT 0.00,
    total DECIMAL(15, 2) NOT NULL,
    status ENUM('pending', 'success', 'failed', 'cancelled') DEFAULT 'pending',
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (provider_id) REFERENCES providers(id) ON DELETE CASCADE,
    FOREIGN KEY (nominal_id) REFERENCES nominals(id) ON DELETE CASCADE
);

-- Tabel Topup Saldo
CREATE TABLE IF NOT EXISTS topups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    payment_method VARCHAR(50),
    proof_image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    admin_fee DECIMAL(15, 2) DEFAULT 0.00,
    total DECIMAL(15, 2) NOT NULL,
    approved_by INT,
    approved_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES admins(id)
);

-- Tabel Log Transaksi
CREATE TABLE IF NOT EXISTS transaction_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    note TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE
);

-- Insert Default Admin
INSERT INTO admins (username, password, full_name) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert Default Users (password: password)
INSERT INTO users (username, email, password, full_name, phone, balance) VALUES
('user1', 'user1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Satu', '081234567890', 100000.00),
('user2', 'user2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'User Dua', '081234567891', 50000.00);

-- Insert Providers
INSERT INTO providers (name, type, code, symbol) VALUES
('Telkomsel', 'pulsa', 'TSEL', 'TL'),
('Indosat', 'pulsa', 'ISAT', 'IS'),
('XL', 'pulsa', 'XL', 'XL'),
('Tri', 'pulsa', 'TRI', '3'),
('Smartfren', 'pulsa', 'SFREN', 'SF'),
('PLN', 'pln', 'PLN', 'PLN'),
('PDAM', 'pdam', 'PDAM', 'PDAM'),
('BPJS', 'bpjs', 'BPJS', 'BPJS'),
('GoPay', 'e_money', 'GOPAY', 'GOPAY'),
('OVO', 'e_money', 'OVO', 'OVO'),
('DANA', 'e_money', 'DANA', 'DANA'),
('ML', 'game', 'ML', 'ML'),
('FF', 'game', 'FF', 'FF');

-- Insert Nominals Pulsa
INSERT INTO nominals (provider_id, denom, price, sell_price) VALUES
(1, '5.000', 6000, 6500),
(1, '10.000', 11000, 11500),
(1, '20.000', 20500, 21000),
(1, '25.000', 25500, 26000),
(1, '50.000', 49500, 50000),
(1, '100.000', 97000, 98000),
(2, '5.000', 5500, 6000),
(2, '10.000', 10500, 11000),
(2, "25.000", 25000, 25500),
(2, '50.000', 49000, 49500),
(3, '5.000', 5500, 6000),
(3, '10.000', 10500, 11000),
(3, '25.000', 25000, 25500),
(3, '50.000', 49000, 49500),
(4, '5.000', 5000, 5500),
(4, '10.000', 10000, 10500),
(4, '25.000', 24500, 25000),
(4, '50.000', 48500, 49000),
(5, '5.000', 5000, 5500),
(5, '10.000', 10000, 10500),
(5, '20.000', 19500, 20000),
(5, '25.000', 24500, 25000),
(5, '50.000', 48500, 49000),
(5, '100.000', 96000, 97000);

-- Insert Nominals Data
INSERT INTO nominals (provider_id, denom, price, sell_price) VALUES
(1, '1GB/30 Hari', 15000, 16000),
(1, '2GB/30 Hari', 25000, 26000),
(1, '5GB/30 Hari', 50000, 52000),
(1, '10GB/30 Hari', 85000, 88000),
(2, '1GB/30 Hari', 12000, 13000),
(2, '3GB/30 Hari', 25000, 26000),
(2, '8GB/30 Hari', 50000, 52000),
(3, '1GB/30 Hari', 12000, 13000),
(3, '3GB/30 Hari', 25000, 26000),
(3, '8GB/30 Hari', 50000, 52000);
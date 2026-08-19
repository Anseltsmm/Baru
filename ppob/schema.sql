-- Database PPOB
CREATE DATABASE IF NOT EXISTS ppob_db;
USE ppob_db;

-- Tabel Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    balance DECIMAL(15,2) DEFAULT 0.00,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    price DECIMAL(15,2) NOT NULL,
    seller_price DECIMAL(15,2) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Transaksi
CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    reference VARCHAR(100) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    status ENUM('pending', 'success', 'failed', 'cancelled') DEFAULT 'pending',
    sn VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Insert default admin
INSERT INTO users (username, password, fullname, email, balance, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@ppob.com', 999999999, 'admin');

-- Insert sample products
INSERT INTO products (category, name, code, price, seller_price, description) VALUES
('Pulsa', 'Pulsa Telkomsel 5K', 'TSEL5', 5500, 5000, 'Pulsa Telkomsel nominal 5.000'),
('Pulsa', 'Pulsa Telkomsel 10K', 'TSEL10', 10500, 10000, 'Pulsa Telkomsel nominal 10.000'),
('Pulsa', 'Pulsa XL 5K', 'XL5', 5400, 5000, 'Pulsa XL nominal 5.000'),
('Pulsa', 'Pulsa XL 10K', 'XL10', 10400, 10000, 'Pulsa XL nominal 10.000'),
('PLN', 'PLN 450 VA', 'PLN450', 15000, 15000, 'Token listrik PLN 450 VA'),
('PLN', 'PLN 900 VA', 'PLN900', 25000, 25000, 'Token listrik PLN 900 VA'),
('PLN', 'PLN 1300 VA', 'PLN1300', 50000, 50000, 'Token listrik PLN 1300 VA'),
('PDAM', 'PDAM Jakarta', 'PDAMJKT', 25000, 25000, 'Bayar tagihan PDAM Jakarta'),
('PDAM', 'PDAM Bandung', 'PDAMBDG', 25000, 25000, 'Bayar tagihan PDAM Bandung'),
('E-Money', 'OVO 20K', 'OVO20', 21000, 20000, 'Top up OVO 20.000'),
('E-Money', 'GoPay 20K', 'GOPAY20', 21000, 20000, 'Top up GoPay 20.000'),
('E-Money', 'DANA 20K', 'DANA20', 21000, 20000, 'Top up DANA 20.000');
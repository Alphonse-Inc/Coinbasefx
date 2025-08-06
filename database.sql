CREATE DATABASE IF NOT EXISTS crypto_trading;
USE crypto_trading;

-- Note: After running this script, you can optionally run test_data.sql
-- to add sample users for testing the application

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    bitcoin_address VARCHAR(100),
    usdt_address VARCHAR(100),
    litecoin_address VARCHAR(100),
    is_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_type ENUM('buy', 'sell') NOT NULL,
    coin_type ENUM('bitcoin', 'usdt', 'litecoin') NOT NULL,
    amount DECIMAL(20, 8) NOT NULL,
    price_per_coin DECIMAL(20, 8) NOT NULL,
    total_value DECIMAL(20, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE user_balances (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    bitcoin_balance DECIMAL(20, 8) DEFAULT 0.00000000,
    usdt_balance DECIMAL(20, 8) DEFAULT 0.00000000,
    litecoin_balance DECIMAL(20, 8) DEFAULT 0.00000000,
    usd_balance DECIMAL(20, 2) DEFAULT 0.00,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
-- Test data for Crypto Trading Platform
-- Run this after setting up the main database to add test users

USE crypto_trading;

-- Test User 1: Verified user ready for login
INSERT INTO users (
    username, 
    full_name, 
    email, 
    password, 
    bitcoin_address, 
    usdt_address, 
    litecoin_address, 
    is_verified, 
    verification_token,
    created_at
) VALUES (
    'testuser',
    'John Doe',
    'testuser@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    '1A1zP1eP5QGefi2DMPTfTL5SLmv7DivfNa',
    'TNzc4HjS7uRyaQw4xK1k9j3NXNqJzPgYfJ',
    'LdP8Qox1VAhCzLJGufkgTZDDqwgL3vF8Rv',
    1,
    NULL,
    NOW()
);

-- Test User 2: Admin user with higher privileges (for future features)
INSERT INTO users (
    username, 
    full_name, 
    email, 
    password, 
    bitcoin_address, 
    usdt_address, 
    litecoin_address, 
    is_verified, 
    verification_token,
    created_at
) VALUES (
    'admin',
    'Admin User',
    'admin@cryptotrading.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    '3J98t1WpEZ73CNmQviecrnyiWrnqRhWNLy',
    'TQNzc4HjS7uRyaQw4xK1k9j3NXNqJzPgYfJ',
    'MdP8Qox1VAhCzLJGufkgTZDDqwgL3vF8Rv',
    1,
    NULL,
    NOW()
);

-- Test User 3: Unverified user (to test email verification)
INSERT INTO users (
    username, 
    full_name, 
    email, 
    password, 
    bitcoin_address, 
    usdt_address, 
    litecoin_address, 
    is_verified, 
    verification_token,
    created_at
) VALUES (
    'unverified',
    'Jane Smith',
    'unverified@example.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    '1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2',
    'TR7NHqjeKQxGTCi6q8ZY4pL8otSzgjLj6t',
    'LbP8Qox1VAhCzLJGufkgTZDDqwgL3vF8Rv',
    0,
    'test_verification_token_123456789abcdef',
    NOW()
);

-- Add balances for verified users
INSERT INTO user_balances (user_id, bitcoin_balance, usdt_balance, litecoin_balance, usd_balance) VALUES
((SELECT id FROM users WHERE username = 'testuser'), 0.15000000, 500.00000000, 2.50000000, 1000.00),
((SELECT id FROM users WHERE username = 'admin'), 0.05000000, 1000.00000000, 5.00000000, 5000.00),
((SELECT id FROM users WHERE username = 'unverified'), 0.00000000, 0.00000000, 0.00000000, 0.00);

-- Add sample transactions for testuser
INSERT INTO transactions (user_id, transaction_type, coin_type, amount, price_per_coin, total_value, status, created_at) VALUES
((SELECT id FROM users WHERE username = 'testuser'), 'buy', 'bitcoin', 0.10000000, 45000.00, 4500.00, 'completed', DATE_SUB(NOW(), INTERVAL 5 DAY)),
((SELECT id FROM users WHERE username = 'testuser'), 'buy', 'usdt', 500.00000000, 1.00, 500.00, 'completed', DATE_SUB(NOW(), INTERVAL 3 DAY)),
((SELECT id FROM users WHERE username = 'testuser'), 'sell', 'bitcoin', 0.05000000, 46000.00, 2300.00, 'completed', DATE_SUB(NOW(), INTERVAL 1 DAY)),
((SELECT id FROM users WHERE username = 'testuser'), 'buy', 'litecoin', 2.50000000, 150.00, 375.00, 'completed', NOW());

-- Add sample transactions for admin
INSERT INTO transactions (user_id, transaction_type, coin_type, amount, price_per_coin, total_value, status, created_at) VALUES
((SELECT id FROM users WHERE username = 'admin'), 'buy', 'bitcoin', 0.05000000, 44000.00, 2200.00, 'completed', DATE_SUB(NOW(), INTERVAL 7 DAY)),
((SELECT id FROM users WHERE username = 'admin'), 'buy', 'usdt', 1000.00000000, 1.00, 1000.00, 'completed', DATE_SUB(NOW(), INTERVAL 6 DAY)),
((SELECT id FROM users WHERE username = 'admin'), 'buy', 'litecoin', 5.00000000, 148.00, 740.00, 'completed', DATE_SUB(NOW(), INTERVAL 2 DAY));
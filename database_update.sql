-- Database update script for existing installations
-- Run this if you already have the crypto_trading database and want to add forgot password functionality

USE crypto_trading;

-- Add password reset columns to users table
ALTER TABLE users 
ADD COLUMN reset_token VARCHAR(255) AFTER verification_token,
ADD COLUMN reset_token_expires DATETIME AFTER reset_token;

-- Add indexes for better performance
CREATE INDEX idx_reset_token ON users(reset_token);
CREATE INDEX idx_reset_token_expires ON users(reset_token_expires);
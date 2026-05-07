-- ============================================
-- Add password reset token columns to users table
-- Run this to enable password reset functionality
-- ============================================

USE appolios_db;

-- Add reset_token column (stores 64-char hex token)
ALTER TABLE users
ADD COLUMN reset_token VARCHAR(64) NULL DEFAULT NULL,
ADD COLUMN reset_token_expiry DATETIME NULL DEFAULT NULL;

-- Add index for faster token lookup
ALTER TABLE users
ADD INDEX idx_reset_token (reset_token);

-- Verify the columns were added
DESCRIBE users;

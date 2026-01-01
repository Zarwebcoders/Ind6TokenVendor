-- Simple migration to add platform_txn_id column
-- Run this in phpMyAdmin or MySQL command line

ALTER TABLE payment 
ADD COLUMN platform_txn_id VARCHAR(255) NULL AFTER id,
ADD INDEX idx_platform_txn_id (platform_txn_id);

-- Verify
DESCRIBE payment;

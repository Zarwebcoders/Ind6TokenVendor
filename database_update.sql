-- PayRaizen Integration - Database Update
-- Run this SQL after starting MySQL in XAMPP

USE ind6token_admin;

-- Add gateway_txn_id column to payments table
ALTER TABLE `payments` 
ADD COLUMN `gateway_txn_id` VARCHAR(100) NULL AFTER `txn_id`;

-- Verify the column was added
DESCRIBE payments;

-- Success message
SELECT 'Database updated successfully! gateway_txn_id column added.' AS Status;

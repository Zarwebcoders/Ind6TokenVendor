-- Verify payments table structure
DESCRIBE payments;

-- Check if all required columns exist
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'payments'
ORDER BY ORDINAL_POSITION;

-- Sample query to test
SELECT 
    id,
    platform_txn_id,
    vendor_id,
    amount,
    status,
    buyer_name,
    buyer_email,
    buyer_phone,
    payment_method,
    created_at,
    updated_at
FROM payments
LIMIT 5;

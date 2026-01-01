-- Check if vendors exist
SELECT * FROM vendors LIMIT 5;

-- If no vendors exist, create a test vendor
INSERT INTO vendors (business_name, upi_id, created_at, updated_at)
SELECT 'Test Merchant', 'test@upi', NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM vendors WHERE id = 1);

-- Verify vendor was created
SELECT * FROM vendors WHERE id = 1;

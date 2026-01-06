CREATE TABLE IF NOT EXISTS `payouts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `vendor_id` int(11) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `txn_id` varchar(100) NOT NULL,
  `gateway_order_id` varchar(100) DEFAULT NULL,
  `gateway_name` varchar(50) DEFAULT 'payraizen',
  `gateway_response` text DEFAULT NULL,
  `beneficiary_name` varchar(255) NOT NULL,
  `account_number` varchar(50) NOT NULL,
  `ifsc_code` varchar(20) NOT NULL,
  `bank_name` varchar(100) DEFAULT NULL,
  `utr` varchar(100) DEFAULT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `method` varchar(50) DEFAULT 'payraizen_payout',
  `failure_reason` text DEFAULT NULL,
  `verify_source` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txn_id` (`txn_id`),
  KEY `vendor_id` (`vendor_id`),
  KEY `gateway_order_id` (`gateway_order_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint (optional, if vendors table exists)
-- ALTER TABLE `payouts` 
--   ADD CONSTRAINT `payouts_vendor_fk` 
--   FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) 
--   ON DELETE CASCADE ON UPDATE CASCADE;

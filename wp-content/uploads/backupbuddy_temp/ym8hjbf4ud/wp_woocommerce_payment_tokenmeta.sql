CREATE TABLE `wp_woocommerce_payment_tokenmeta` (  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `payment_token_id` bigint(20) unsigned NOT NULL,  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,  PRIMARY KEY (`meta_id`),  KEY `payment_token_id` (`payment_token_id`),  KEY `meta_key` (`meta_key`(32))) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_woocommerce_payment_tokenmeta` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_woocommerce_payment_tokenmeta` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

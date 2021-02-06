CREATE TABLE `wp_woocommerce_payment_tokens` (  `token_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `gateway_id` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `token` text COLLATE utf8mb4_unicode_520_ci NOT NULL,  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',  `type` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `is_default` tinyint(1) NOT NULL DEFAULT '0',  PRIMARY KEY (`token_id`),  KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_woocommerce_payment_tokens` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_woocommerce_payment_tokens` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

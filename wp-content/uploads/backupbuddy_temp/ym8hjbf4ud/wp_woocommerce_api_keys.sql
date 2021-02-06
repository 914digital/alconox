CREATE TABLE `wp_woocommerce_api_keys` (  `key_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `description` varchar(200) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `permissions` varchar(10) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `consumer_key` char(64) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `consumer_secret` char(43) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `nonces` longtext COLLATE utf8mb4_unicode_520_ci,  `truncated_key` char(7) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `last_access` datetime DEFAULT NULL,  PRIMARY KEY (`key_id`),  KEY `consumer_key` (`consumer_key`),  KEY `consumer_secret` (`consumer_secret`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_woocommerce_api_keys` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_woocommerce_api_keys` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

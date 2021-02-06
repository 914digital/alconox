CREATE TABLE `wp_woocommerce_log` (  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `timestamp` datetime NOT NULL,  `level` smallint(4) NOT NULL,  `source` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `message` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,  `context` longtext COLLATE utf8mb4_unicode_520_ci,  PRIMARY KEY (`log_id`),  KEY `level` (`level`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_woocommerce_log` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_woocommerce_log` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

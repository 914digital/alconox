CREATE TABLE `wp_gf_entry_meta` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `form_id` mediumint(8) unsigned NOT NULL DEFAULT '0',  `entry_id` bigint(20) unsigned NOT NULL,  `meta_key` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `meta_value` longtext COLLATE utf8mb4_unicode_520_ci,  `item_index` varchar(60) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `meta_key` (`meta_key`(191)),  KEY `entry_id` (`entry_id`),  KEY `meta_value` (`meta_value`(191))) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_gf_entry_meta` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_gf_entry_meta` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

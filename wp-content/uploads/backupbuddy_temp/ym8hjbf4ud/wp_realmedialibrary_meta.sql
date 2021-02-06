CREATE TABLE `wp_realmedialibrary_meta` (  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `realmedialibrary_id` bigint(20) unsigned NOT NULL DEFAULT '0',  `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,  PRIMARY KEY (`meta_id`),  KEY `realmedialibrary_id` (`realmedialibrary_id`),  KEY `meta_key` (`meta_key`(250))) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_realmedialibrary_meta` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_realmedialibrary_meta` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

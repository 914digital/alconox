CREATE TABLE `wp_realmedialibrary_debug` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_realmedialibrary_debug` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_realmedialibrary_debug` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

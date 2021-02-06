CREATE TABLE `wp_tm_taskmeta` (  `meta_id` bigint(20) NOT NULL AUTO_INCREMENT,  `task_id` bigint(20) NOT NULL DEFAULT '0',  `meta_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `meta_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci,  PRIMARY KEY (`meta_id`),  KEY `meta_key` (`meta_key`(191)),  KEY `task_id` (`task_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_tm_taskmeta` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_tm_taskmeta` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

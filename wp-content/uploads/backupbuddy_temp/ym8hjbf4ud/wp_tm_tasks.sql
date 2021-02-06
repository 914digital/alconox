CREATE TABLE `wp_tm_tasks` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) NOT NULL,  `type` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,  `class_identifier` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT '0',  `attempts` int(11) DEFAULT '0',  `description` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,  `last_locked_at` bigint(20) DEFAULT '0',  `status` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `user_id` (`user_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_tm_tasks` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_tm_tasks` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

CREATE TABLE `wp_gf_entry_notes` (  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,  `entry_id` int(10) unsigned NOT NULL,  `user_name` varchar(250) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `user_id` bigint(20) DEFAULT NULL,  `date_created` datetime NOT NULL,  `value` longtext COLLATE utf8mb4_unicode_520_ci,  `note_type` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `sub_type` varchar(50) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `entry_id` (`entry_id`),  KEY `entry_user_key` (`entry_id`,`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_gf_entry_notes` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_gf_entry_notes` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

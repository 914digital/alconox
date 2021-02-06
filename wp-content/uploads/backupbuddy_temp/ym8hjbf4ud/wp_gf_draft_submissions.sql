CREATE TABLE `wp_gf_draft_submissions` (  `uuid` char(32) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `email` varchar(255) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `form_id` mediumint(8) unsigned NOT NULL,  `date_created` datetime NOT NULL,  `ip` varchar(39) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `source_url` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,  `submission` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,  PRIMARY KEY (`uuid`),  KEY `form_id` (`form_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_gf_draft_submissions` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_gf_draft_submissions` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

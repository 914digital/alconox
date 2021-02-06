CREATE TABLE `wp_gf_form_revisions` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `form_id` mediumint(8) unsigned NOT NULL,  `display_meta` longtext COLLATE utf8mb4_unicode_520_ci,  `date_created` datetime NOT NULL,  PRIMARY KEY (`id`),  KEY `date_created` (`date_created`),  KEY `form_id` (`form_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_gf_form_revisions` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_gf_form_revisions` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

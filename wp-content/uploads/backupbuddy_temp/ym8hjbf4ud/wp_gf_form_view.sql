CREATE TABLE `wp_gf_form_view` (  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `form_id` mediumint(8) unsigned NOT NULL,  `date_created` datetime NOT NULL,  `ip` char(15) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `count` mediumint(8) unsigned NOT NULL DEFAULT '1',  PRIMARY KEY (`id`),  KEY `date_created` (`date_created`),  KEY `form_id` (`form_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_gf_form_view` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_gf_form_view` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

CREATE TABLE `wp_gf_form` (  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,  `title` varchar(150) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `date_created` datetime NOT NULL,  `date_updated` datetime DEFAULT NULL,  `is_active` tinyint(1) NOT NULL DEFAULT '1',  `is_trash` tinyint(1) NOT NULL DEFAULT '0',  PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_gf_form` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_gf_form` VALUES('1', 'Register', '2020-01-15 05:10:26', NULL, '1', '0');
/*!40000 ALTER TABLE `wp_gf_form` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

CREATE TABLE `wp_redirection_groups` (  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `name` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `tracking` int(11) NOT NULL DEFAULT '1',  `module_id` int(11) unsigned NOT NULL DEFAULT '0',  `status` enum('enabled','disabled') COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT 'enabled',  `position` int(11) unsigned NOT NULL DEFAULT '0',  PRIMARY KEY (`id`),  KEY `module_id` (`module_id`),  KEY `status` (`status`)) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_redirection_groups` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_redirection_groups` VALUES('1', 'Redirections', '1', '1', 'enabled', '0');
INSERT INTO `wp_redirection_groups` VALUES('2', 'Modified Posts', '1', '1', 'enabled', '1');
/*!40000 ALTER TABLE `wp_redirection_groups` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

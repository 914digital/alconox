CREATE TABLE `wp_redirection_logs` (  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `created` datetime NOT NULL,  `url` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,  `sent_to` mediumtext COLLATE utf8mb4_unicode_520_ci,  `agent` mediumtext COLLATE utf8mb4_unicode_520_ci NOT NULL,  `referrer` mediumtext COLLATE utf8mb4_unicode_520_ci,  `redirection_id` int(11) unsigned DEFAULT NULL,  `ip` varchar(45) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,  `module_id` int(11) unsigned NOT NULL,  `group_id` int(11) unsigned DEFAULT NULL,  PRIMARY KEY (`id`),  KEY `created` (`created`),  KEY `redirection_id` (`redirection_id`),  KEY `ip` (`ip`),  KEY `group_id` (`group_id`),  KEY `module_id` (`module_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_redirection_logs` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_redirection_logs` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

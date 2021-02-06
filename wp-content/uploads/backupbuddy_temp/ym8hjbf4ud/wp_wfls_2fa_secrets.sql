CREATE TABLE `wp_wfls_2fa_secrets` (  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,  `user_id` bigint(20) unsigned NOT NULL,  `secret` tinyblob NOT NULL,  `recovery` blob NOT NULL,  `ctime` int(10) unsigned NOT NULL,  `vtime` int(10) unsigned NOT NULL,  `mode` enum('authenticator') NOT NULL DEFAULT 'authenticator',  PRIMARY KEY (`id`),  KEY `user_id` (`user_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `wp_wfls_2fa_secrets` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_wfls_2fa_secrets` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

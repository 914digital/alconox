CREATE TABLE `wp_pc_user_meta` (  `meta_id` mediumint(9) NOT NULL AUTO_INCREMENT,  `user_id` mediumint(9) unsigned NOT NULL,  `meta_key` varchar(255) NOT NULL DEFAULT '',  `meta_value` longtext NOT NULL,  UNIQUE KEY `meta_id` (`meta_id`)) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `wp_pc_user_meta` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_pc_user_meta` VALUES('3', '3', 'custom|||text', '');
/*!40000 ALTER TABLE `wp_pc_user_meta` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

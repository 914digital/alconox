CREATE TABLE `wp_pc_users` (  `id` mediumint(9) NOT NULL AUTO_INCREMENT,  `insert_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `name` varchar(150) NOT NULL DEFAULT '',  `surname` varchar(150) NOT NULL DEFAULT '',  `username` varchar(150) NOT NULL,  `psw` text NOT NULL,  `categories` text NOT NULL,  `email` varchar(255) NOT NULL,  `tel` varchar(20) NOT NULL,  `page_id` int(11) unsigned NOT NULL,  `wp_user_id` mediumint(9) unsigned NOT NULL,  `disable_pvt_page` smallint(1) unsigned NOT NULL,  `last_access` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `status` smallint(1) unsigned NOT NULL,  UNIQUE KEY `id` (`id`,`page_id`,`wp_user_id`)) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `wp_pc_users` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_pc_users` VALUES('3', '2016-07-13 09:11:42', '', '', 'carlos', 'YToyOntpOjA7czoxNjoiWm14aGJXVnVZMjgwTWpnPSI7aToxO3M6MzI6IjFiZDJmMDZkYmQyOWQxYTk2YzEwYTU0Y2NjMWY5OGRhIjt9', 'a:1:{i:0;s:2:\"43\";}', '', '323-304-9394', '6101', '0', '0', '2016-07-24 08:44:44', '1');
/*!40000 ALTER TABLE `wp_pc_users` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

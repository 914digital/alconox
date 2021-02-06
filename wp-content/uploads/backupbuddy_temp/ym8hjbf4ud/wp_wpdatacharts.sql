CREATE TABLE `wp_wpdatacharts` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `wpdatatable_id` int(11) NOT NULL,  `title` varchar(255) NOT NULL,  `engine` enum('google','highcharts','chartjs') NOT NULL,  `type` varchar(255) NOT NULL,  `json_render_data` text NOT NULL,  UNIQUE KEY `id` (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `wp_wpdatacharts` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_wpdatacharts` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

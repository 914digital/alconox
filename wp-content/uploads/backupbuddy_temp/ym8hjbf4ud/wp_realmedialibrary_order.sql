CREATE TABLE `wp_realmedialibrary_order` (  `attachment` bigint(20) NOT NULL,  `fid` mediumint(9) DEFAULT '-1',  `nr` bigint(20) DEFAULT NULL,  `oldCustomNr` bigint(20) DEFAULT NULL,  UNIQUE KEY `rmlorder` (`attachment`,`fid`)) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_realmedialibrary_order` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_realmedialibrary_order` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

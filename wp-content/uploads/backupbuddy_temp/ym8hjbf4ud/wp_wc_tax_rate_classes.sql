CREATE TABLE `wp_wc_tax_rate_classes` (  `tax_rate_class_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `name` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',  `slug` varchar(200) COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',  PRIMARY KEY (`tax_rate_class_id`),  UNIQUE KEY `slug` (`slug`(191))) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_wc_tax_rate_classes` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_wc_tax_rate_classes` VALUES('1', 'Reduced rate', 'reduced-rate');
INSERT INTO `wp_wc_tax_rate_classes` VALUES('2', 'Zero rate', 'zero-rate');
/*!40000 ALTER TABLE `wp_wc_tax_rate_classes` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

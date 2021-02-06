CREATE TABLE `wp_wc_category_lookup` (  `category_tree_id` bigint(20) unsigned NOT NULL,  `category_id` bigint(20) unsigned NOT NULL,  PRIMARY KEY (`category_tree_id`,`category_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_wc_category_lookup` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_wc_category_lookup` VALUES('86', '86');
/*!40000 ALTER TABLE `wp_wc_category_lookup` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

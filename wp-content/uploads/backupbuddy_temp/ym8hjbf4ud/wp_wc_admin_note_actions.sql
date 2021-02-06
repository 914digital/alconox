CREATE TABLE `wp_wc_admin_note_actions` (  `action_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `note_id` bigint(20) unsigned NOT NULL,  `name` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `label` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `query` longtext COLLATE utf8mb4_unicode_520_ci NOT NULL,  `status` varchar(255) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `is_primary` tinyint(1) NOT NULL DEFAULT '0',  PRIMARY KEY (`action_id`),  KEY `note_id` (`note_id`)) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_wc_admin_note_actions` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_wc_admin_note_actions` VALUES('1', '1', 'learn-more', 'Learn more', 'https://woocommerce.wordpress.com/', 'actioned', '0');
INSERT INTO `wp_wc_admin_note_actions` VALUES('2', '2', 'connect', 'Connect', '?page=wc-addons&section=helper', 'actioned', '0');
INSERT INTO `wp_wc_admin_note_actions` VALUES('3', '3', 'add-a-product', 'Add a product', 'https://alconox.914/wp-admin/post-new.php?post_type=product', 'actioned', '1');
INSERT INTO `wp_wc_admin_note_actions` VALUES('4', '4', 'learn-more', 'Learn more', 'https://woocommerce.com/mobile/', 'actioned', '0');
INSERT INTO `wp_wc_admin_note_actions` VALUES('5', '5', 'share-feedback', 'Review', 'https://wordpress.org/support/plugin/woocommerce-admin/reviews/?rate=5#new-post', 'actioned', '0');
/*!40000 ALTER TABLE `wp_wc_admin_note_actions` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

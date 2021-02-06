CREATE TABLE `wp_wc_order_coupon_lookup` (  `order_id` bigint(20) unsigned NOT NULL,  `coupon_id` bigint(20) unsigned NOT NULL,  `date_created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `discount_amount` double NOT NULL DEFAULT '0',  PRIMARY KEY (`order_id`,`coupon_id`),  KEY `coupon_id` (`coupon_id`),  KEY `date_created` (`date_created`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_wc_order_coupon_lookup` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_wc_order_coupon_lookup` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

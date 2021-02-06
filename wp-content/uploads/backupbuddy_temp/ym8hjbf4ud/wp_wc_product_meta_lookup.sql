CREATE TABLE `wp_wc_product_meta_lookup` (  `product_id` bigint(20) NOT NULL,  `sku` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT '',  `virtual` tinyint(1) DEFAULT '0',  `downloadable` tinyint(1) DEFAULT '0',  `min_price` decimal(10,2) DEFAULT NULL,  `max_price` decimal(10,2) DEFAULT NULL,  `onsale` tinyint(1) DEFAULT '0',  `stock_quantity` double DEFAULT NULL,  `stock_status` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT 'instock',  `rating_count` bigint(20) DEFAULT '0',  `average_rating` decimal(3,2) DEFAULT '0.00',  `total_sales` bigint(20) DEFAULT '0',  PRIMARY KEY (`product_id`),  KEY `virtual` (`virtual`),  KEY `downloadable` (`downloadable`),  KEY `stock_status` (`stock_status`),  KEY `stock_quantity` (`stock_quantity`),  KEY `onsale` (`onsale`),  KEY `min_max_price` (`min_price`,`max_price`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_wc_product_meta_lookup` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_wc_product_meta_lookup` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

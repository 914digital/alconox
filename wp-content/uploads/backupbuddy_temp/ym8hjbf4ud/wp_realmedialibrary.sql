CREATE TABLE `wp_realmedialibrary` (  `id` mediumint(9) NOT NULL AUTO_INCREMENT,  `parent` mediumint(9) NOT NULL DEFAULT '-1',  `name` tinytext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,  `slug` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,  `absolute` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL,  `bid` mediumint(10) NOT NULL DEFAULT '1',  `ord` mediumint(10) NOT NULL DEFAULT '0',  `type` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '0',  `restrictions` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci NOT NULL DEFAULT '',  `cnt` mediumint(10) DEFAULT NULL,  `contentCustomOrder` tinyint(1) NOT NULL DEFAULT '0',  UNIQUE KEY `id` (`id`)) ENGINE=MyISAM AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_realmedialibrary` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_realmedialibrary` VALUES('1', '-1', 'COA Files', 'coa-files', 'coa-files', '1', '0', '0', '', '638', '0');
INSERT INTO `wp_realmedialibrary` VALUES('24', '-1', 'Trace Analysis Certificates', 'trace-analysis-certificates', 'trace-analysis-certificates', '1', '1', '0', '', '13', '0');
INSERT INTO `wp_realmedialibrary` VALUES('33', '-1', 'White Papers', 'white-papers', 'white-papers', '1', '8', '0', '', '6', '0');
INSERT INTO `wp_realmedialibrary` VALUES('32', '-1', 'Aqueous Cleaning Handbook', 'aqueous-cleaning-handbook', 'aqueous-cleaning-handbook', '1', '8', '0', '', '1', '0');
INSERT INTO `wp_realmedialibrary` VALUES('31', '30', 'Products', 'products', 'product-gallery/products', '1', '8', '2', '', '2', '0');
INSERT INTO `wp_realmedialibrary` VALUES('30', '-1', 'Product Gallery', 'product-gallery', 'product-gallery', '1', '8', '1', '', '0', '0');
INSERT INTO `wp_realmedialibrary` VALUES('28', '-1', 'Product Image Downloads', 'product-image-downloads', 'product-image-downloads', '1', '7', '0', '', '13', '0');
INSERT INTO `wp_realmedialibrary` VALUES('22', '-1', 'SDS Sheets', 'sds-sheets', 'sds-sheets', '1', '2', '0', '', '14', '0');
INSERT INTO `wp_realmedialibrary` VALUES('23', '-1', 'Tech Bulletins', 'tech-bulletins', 'tech-bulletins', '1', '3', '0', '', '16', '0');
INSERT INTO `wp_realmedialibrary` VALUES('25', '-1', 'Inhibitory Residue Test', 'inhibitory-residue-test', 'inhibitory-residue-test', '1', '4', '0', '', '12', '0');
INSERT INTO `wp_realmedialibrary` VALUES('26', '-1', 'Pharmaceutical Cleaning Validation', 'pharmaceutical-cleaning-validation', 'pharmaceutical-cleaning-validation', '1', '5', '0', '', '1', '0');
INSERT INTO `wp_realmedialibrary` VALUES('27', '-1', 'Selling Guide', 'selling-guide', 'selling-guide', '1', '6', '0', '', '1', '0');
/*!40000 ALTER TABLE `wp_realmedialibrary` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
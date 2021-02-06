CREATE TABLE `wp_asp_synonyms` (  `id` int(11) NOT NULL AUTO_INCREMENT,  `keyword` varchar(50) COLLATE utf8mb4_unicode_520_ci NOT NULL,  `synonyms` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,  `lang` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,  PRIMARY KEY (`id`),  UNIQUE KEY `keyword` (`keyword`,`lang`),  KEY `keyword_lang` (`keyword`,`lang`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
/*!40000 ALTER TABLE `wp_asp_synonyms` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
/*!40000 ALTER TABLE `wp_asp_synonyms` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

CREATE TABLE `wp_ajaxsearchpro_priorities` (  `post_id` int(11) NOT NULL,  `blog_id` int(11) NOT NULL,  `priority` int(11) NOT NULL,  PRIMARY KEY (`post_id`,`blog_id`),  KEY `post_blog_id` (`post_id`,`blog_id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40000 ALTER TABLE `wp_ajaxsearchpro_priorities` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('11134', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('10474', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('10413', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('10411', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('10396', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('9989', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('9969', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('7547', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('7352', '1', '0');
INSERT INTO `wp_ajaxsearchpro_priorities` VALUES('7298', '1', '0');
/*!40000 ALTER TABLE `wp_ajaxsearchpro_priorities` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

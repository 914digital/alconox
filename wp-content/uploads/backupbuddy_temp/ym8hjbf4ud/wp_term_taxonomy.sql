CREATE TABLE `wp_term_taxonomy` (  `term_taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',  `taxonomy` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',  `description` longtext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,  `parent` bigint(20) unsigned NOT NULL DEFAULT '0',  `count` bigint(20) NOT NULL DEFAULT '0',  PRIMARY KEY (`term_taxonomy_id`),  UNIQUE KEY `term_id_taxonomy` (`term_id`,`taxonomy`),  KEY `taxonomy` (`taxonomy`)) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40000 ALTER TABLE `wp_term_taxonomy` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_term_taxonomy` VALUES('1', '1', 'category', '', '0', '3');
INSERT INTO `wp_term_taxonomy` VALUES('2', '2', 'link_category', '', '0', '7');
INSERT INTO `wp_term_taxonomy` VALUES('6', '6', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('7', '7', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('8', '8', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('9', '9', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('10', '10', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('11', '11', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('12', '12', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('13', '13', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('14', '14', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('15', '15', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('17', '17', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('18', '18', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('19', '19', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('20', '20', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('21', '21', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('22', '22', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('23', '23', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('88', '88', 'nav_menu', '', '0', '7');
INSERT INTO `wp_term_taxonomy` VALUES('89', '89', 'nav_menu', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('26', '26', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('27', '27', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('28', '28', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('29', '29', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('30', '30', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('31', '31', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('32', '32', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('33', '33', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('34', '34', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('35', '35', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('36', '36', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('37', '37', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('38', '38', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('42', '42', 'pc_reg_form', 'YTozOntzOjc6ImluY2x1ZGUiO2E6NDp7aTowO3M6ODoidXNlcm5hbWUiO2k6MTtzOjM6InBzdyI7aToyO3M6MTM6ImN1c3RvbXx8fHRleHQiO2k6MztzOjM6InRlbCI7fXM6NzoicmVxdWlyZSI7YTozOntpOjA7czo4OiJ1c2VybmFtZSI7aToxO3M6MzoicHN3IjtpOjI7czozOiJ0ZWwiO31zOjU6InRleHRzIjthOjE6e2k6MDtzOjA6IiI7fX0=', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('43', '43', 'pg_user_categories', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('48', '48', 'flamingo_inbound_channel', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('49', '49', 'flamingo_inbound_channel', '', '48', '1');
INSERT INTO `wp_term_taxonomy` VALUES('50', '50', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('51', '51', 'category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('54', '54', 'media_categories', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('57', '57', 'media_category', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('58', '58', 'category_media', '', '0', '42');
INSERT INTO `wp_term_taxonomy` VALUES('60', '60', 'category', '', '0', '4');
INSERT INTO `wp_term_taxonomy` VALUES('61', '61', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('62', '62', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('63', '63', 'category', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('64', '64', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('65', '65', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('66', '66', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('67', '67', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('68', '68', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('69', '69', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('70', '70', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('71', '71', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('72', '72', 'post_tag', '', '0', '1');
INSERT INTO `wp_term_taxonomy` VALUES('73', '73', 'product_type', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('74', '74', 'product_type', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('75', '75', 'product_type', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('76', '76', 'product_type', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('77', '77', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('78', '78', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('79', '79', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('80', '80', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('81', '81', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('82', '82', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('83', '83', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('84', '84', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('85', '85', 'product_visibility', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('86', '86', 'product_cat', '', '0', '0');
INSERT INTO `wp_term_taxonomy` VALUES('87', '87', 'action-group', '', '0', '25');
/*!40000 ALTER TABLE `wp_term_taxonomy` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;

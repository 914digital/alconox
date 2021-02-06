CREATE TABLE `wp_comments` (  `comment_ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,  `comment_post_ID` bigint(20) unsigned NOT NULL DEFAULT '0',  `comment_author` tinytext CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,  `comment_author_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',  `comment_author_url` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',  `comment_author_IP` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',  `comment_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `comment_date_gmt` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',  `comment_content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,  `comment_karma` int(11) NOT NULL DEFAULT '0',  `comment_approved` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '1',  `comment_agent` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',  `comment_type` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '',  `comment_parent` bigint(20) unsigned NOT NULL DEFAULT '0',  `user_id` bigint(20) unsigned NOT NULL DEFAULT '0',  PRIMARY KEY (`comment_ID`),  KEY `comment_post_ID` (`comment_post_ID`),  KEY `comment_approved_date_gmt` (`comment_approved`,`comment_date_gmt`),  KEY `comment_date_gmt` (`comment_date_gmt`),  KEY `comment_parent` (`comment_parent`),  KEY `comment_author_email` (`comment_author_email`(10)),  KEY `woo_idx_comment_type` (`comment_type`)) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40000 ALTER TABLE `wp_comments` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_comments` VALUES('3', '5399', 'Deanna', 'mariettabickersteth@bestmail.us', 'http://Julissa.blog.es', '66.252.211.212', '2016-12-10 13:46:19', '2016-12-10 13:46:19', 'I see interesting posts here. Your blog can go viral \r\neasily, you need some initial traffic only.\r\nHow to get it? Search for: ricusso\'s methods massive \r\ntraffic', '0', 'spam', 'Mozilla/5.0 (Windows NT 6.2; WOW64; rv:34.0) Gecko/20100101 Firefox/34.0', '', '0', '0');
INSERT INTO `wp_comments` VALUES('4', '5393', 'reverse commissions video on youtube', 'Piwowar@makemoneywithlindsey.com', 'https://m.youtube.com/watch?v=64CRiE8TlSs', '23.95.189.187', '2016-12-19 16:51:47', '2016-12-19 16:51:47', 'Emotional attention, self-control, approval, adhere to, patience but also security. These are typically among the issues that Tang Soo Can do, most of the Thai martial art attached to self defense purposes, can show buyers plus instilling in your soul the indicates not just to maintain along with your own eyes on the competency on the very very first real danger stains to cure confrontation all in all.', '0', 'spam', 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; en) Opera 8.50', '', '0', '0');
INSERT INTO `wp_comments` VALUES('5', '5393', '90Julia', 'pat_sappington@gmail.com', 'http://Reina.typepad.com', '177.83.240.218', '2016-12-20 02:01:08', '2016-12-20 02:01:08', 'Hello admin !! I read your content everyday and i must say you have \r\nhi quality content here. Your website deserves to go viral.\r\nYou need initial boost only. How to go viral fast?\r\nSearch for: forbesden\'s tools', '0', 'spam', 'Mozilla/5.0 (X11; CrOS x86_64 6310.68.0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/39.0.2171.96 Safari/537.36', '', '0', '0');
INSERT INTO `wp_comments` VALUES('6', '17258', 'ActionScheduler', '', '', '', '2020-01-15 04:48:03', '2020-01-15 04:48:03', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('7', '17258', 'ActionScheduler', '', '', '', '2020-01-15 04:48:27', '2020-01-15 04:48:27', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('8', '17258', 'ActionScheduler', '', '', '', '2020-01-15 04:48:27', '2020-01-15 04:48:27', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('9', '17259', 'ActionScheduler', '', '', '', '2020-01-15 04:48:27', '2020-01-15 04:48:27', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('10', '17259', 'ActionScheduler', '', '', '', '2020-01-15 05:48:45', '2020-01-15 05:48:45', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('11', '17259', 'ActionScheduler', '', '', '', '2020-01-15 05:48:45', '2020-01-15 05:48:45', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('12', '17261', 'ActionScheduler', '', '', '', '2020-01-15 05:48:45', '2020-01-15 05:48:45', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('13', '17261', 'ActionScheduler', '', '', '', '2020-01-15 06:50:15', '2020-01-15 06:50:15', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('14', '17261', 'ActionScheduler', '', '', '', '2020-01-15 06:50:15', '2020-01-15 06:50:15', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('15', '17262', 'ActionScheduler', '', '', '', '2020-01-15 06:50:15', '2020-01-15 06:50:15', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('16', '17262', 'ActionScheduler', '', '', '', '2020-01-15 07:50:38', '2020-01-15 07:50:38', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('17', '17262', 'ActionScheduler', '', '', '', '2020-01-15 07:50:38', '2020-01-15 07:50:38', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('18', '17275', 'ActionScheduler', '', '', '', '2020-01-15 07:50:38', '2020-01-15 07:50:38', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('19', '17275', 'ActionScheduler', '', '', '', '2020-01-15 08:51:59', '2020-01-15 08:51:59', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('20', '17275', 'ActionScheduler', '', '', '', '2020-01-15 08:51:59', '2020-01-15 08:51:59', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('21', '17279', 'ActionScheduler', '', '', '', '2020-01-15 08:51:59', '2020-01-15 08:51:59', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('22', '17279', 'ActionScheduler', '', '', '', '2020-01-16 04:07:37', '2020-01-16 04:07:37', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('23', '17279', 'ActionScheduler', '', '', '', '2020-01-16 04:07:37', '2020-01-16 04:07:37', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('24', '17280', 'ActionScheduler', '', '', '', '2020-01-16 04:07:37', '2020-01-16 04:07:37', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('25', '17280', 'ActionScheduler', '', '', '', '2020-01-16 21:24:40', '2020-01-16 21:24:40', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('26', '17280', 'ActionScheduler', '', '', '', '2020-01-16 21:24:40', '2020-01-16 21:24:40', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('27', '17296', 'ActionScheduler', '', '', '', '2020-01-16 21:24:40', '2020-01-16 21:24:40', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('28', '17296', 'ActionScheduler', '', '', '', '2020-01-16 22:25:29', '2020-01-16 22:25:29', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('29', '17296', 'ActionScheduler', '', '', '', '2020-01-16 22:25:29', '2020-01-16 22:25:29', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('30', '17297', 'ActionScheduler', '', '', '', '2020-01-16 22:25:30', '2020-01-16 22:25:30', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('31', '17297', 'ActionScheduler', '', '', '', '2020-01-17 22:35:11', '2020-01-17 22:35:11', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('32', '17297', 'ActionScheduler', '', '', '', '2020-01-17 22:35:11', '2020-01-17 22:35:11', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('33', '17298', 'ActionScheduler', '', '', '', '2020-01-17 22:35:11', '2020-01-17 22:35:11', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('34', '17298', 'ActionScheduler', '', '', '', '2020-01-17 23:56:02', '2020-01-17 23:56:02', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('35', '17298', 'ActionScheduler', '', '', '', '2020-01-17 23:56:03', '2020-01-17 23:56:03', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('36', '17299', 'ActionScheduler', '', '', '', '2020-01-17 23:56:03', '2020-01-17 23:56:03', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('37', '17299', 'ActionScheduler', '', '', '', '2020-01-18 01:09:53', '2020-01-18 01:09:53', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('38', '17299', 'ActionScheduler', '', '', '', '2020-01-18 01:09:53', '2020-01-18 01:09:53', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('39', '17300', 'ActionScheduler', '', '', '', '2020-01-18 01:09:53', '2020-01-18 01:09:53', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('40', '17300', 'ActionScheduler', '', '', '', '2020-01-18 04:04:20', '2020-01-18 04:04:20', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('41', '17300', 'ActionScheduler', '', '', '', '2020-01-18 04:04:20', '2020-01-18 04:04:20', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('42', '17301', 'ActionScheduler', '', '', '', '2020-01-18 04:04:20', '2020-01-18 04:04:20', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('43', '17301', 'ActionScheduler', '', '', '', '2020-01-18 06:43:48', '2020-01-18 06:43:48', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('44', '17301', 'ActionScheduler', '', '', '', '2020-01-18 06:43:48', '2020-01-18 06:43:48', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('45', '17302', 'ActionScheduler', '', '', '', '2020-01-18 06:43:48', '2020-01-18 06:43:48', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('46', '17302', 'ActionScheduler', '', '', '', '2020-01-18 10:52:23', '2020-01-18 10:52:23', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('47', '17302', 'ActionScheduler', '', '', '', '2020-01-18 10:52:23', '2020-01-18 10:52:23', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('48', '17303', 'ActionScheduler', '', '', '', '2020-01-18 10:52:23', '2020-01-18 10:52:23', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('49', '17303', 'ActionScheduler', '', '', '', '2020-01-18 11:52:27', '2020-01-18 11:52:27', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('50', '17303', 'ActionScheduler', '', '', '', '2020-01-18 11:52:27', '2020-01-18 11:52:27', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('51', '17304', 'ActionScheduler', '', '', '', '2020-01-18 11:52:27', '2020-01-18 11:52:27', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('52', '17304', 'ActionScheduler', '', '', '', '2020-01-18 19:27:27', '2020-01-18 19:27:27', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('53', '17304', 'ActionScheduler', '', '', '', '2020-01-18 19:27:27', '2020-01-18 19:27:27', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('54', '17305', 'ActionScheduler', '', '', '', '2020-01-18 19:27:27', '2020-01-18 19:27:27', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('55', '17305', 'ActionScheduler', '', '', '', '2020-01-18 20:39:39', '2020-01-18 20:39:39', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('56', '17305', 'ActionScheduler', '', '', '', '2020-01-18 20:39:39', '2020-01-18 20:39:39', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('57', '17306', 'ActionScheduler', '', '', '', '2020-01-18 20:39:39', '2020-01-18 20:39:39', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('58', '17306', 'ActionScheduler', '', '', '', '2020-01-18 23:29:47', '2020-01-18 23:29:47', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('59', '17306', 'ActionScheduler', '', '', '', '2020-01-18 23:29:47', '2020-01-18 23:29:47', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('60', '17307', 'ActionScheduler', '', '', '', '2020-01-18 23:29:47', '2020-01-18 23:29:47', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('61', '17307', 'ActionScheduler', '', '', '', '2020-01-19 22:06:10', '2020-01-19 22:06:10', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('62', '17307', 'ActionScheduler', '', '', '', '2020-01-19 22:06:10', '2020-01-19 22:06:10', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('63', '17308', 'ActionScheduler', '', '', '', '2020-01-19 22:06:11', '2020-01-19 22:06:11', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('64', '17308', 'ActionScheduler', '', '', '', '2020-01-20 02:36:49', '2020-01-20 02:36:49', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('65', '17308', 'ActionScheduler', '', '', '', '2020-01-20 02:36:49', '2020-01-20 02:36:49', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('66', '17309', 'ActionScheduler', '', '', '', '2020-01-20 02:36:49', '2020-01-20 02:36:49', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('67', '17309', 'ActionScheduler', '', '', '', '2020-01-20 03:38:34', '2020-01-20 03:38:34', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('68', '17309', 'ActionScheduler', '', '', '', '2020-01-20 03:38:34', '2020-01-20 03:38:34', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('69', '17310', 'ActionScheduler', '', '', '', '2020-01-20 03:38:34', '2020-01-20 03:38:34', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('70', '17310', 'ActionScheduler', '', '', '', '2020-01-20 04:58:38', '2020-01-20 04:58:38', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('71', '17310', 'ActionScheduler', '', '', '', '2020-01-20 04:58:38', '2020-01-20 04:58:38', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('72', '17312', 'ActionScheduler', '', '', '', '2020-01-20 04:58:38', '2020-01-20 04:58:38', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('73', '17312', 'ActionScheduler', '', '', '', '2020-01-20 13:27:10', '2020-01-20 13:27:10', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('74', '17312', 'ActionScheduler', '', '', '', '2020-01-20 13:27:10', '2020-01-20 13:27:10', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('75', '17313', 'ActionScheduler', '', '', '', '2020-01-20 13:27:10', '2020-01-20 13:27:10', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('76', '17313', 'ActionScheduler', '', '', '', '2020-01-21 01:12:10', '2020-01-21 01:12:10', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('77', '17313', 'ActionScheduler', '', '', '', '2020-01-21 01:12:10', '2020-01-21 01:12:10', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('78', '17314', 'ActionScheduler', '', '', '', '2020-01-21 01:12:10', '2020-01-21 01:12:10', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('79', '17314', 'ActionScheduler', '', '', '', '2020-01-21 10:20:54', '2020-01-21 10:20:54', 'action started', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('80', '17314', 'ActionScheduler', '', '', '', '2020-01-21 10:20:54', '2020-01-21 10:20:54', 'action complete', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
INSERT INTO `wp_comments` VALUES('81', '17315', 'ActionScheduler', '', '', '', '2020-01-21 10:20:55', '2020-01-21 10:20:55', 'action created', '0', '1', 'ActionScheduler', 'action_log', '0', '0');
/*!40000 ALTER TABLE `wp_comments` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
CREATE TABLE `wp_wfls_settings` (  `name` varchar(191) NOT NULL DEFAULT '',  `value` longblob,  `autoload` enum('no','yes') NOT NULL DEFAULT 'yes',  PRIMARY KEY (`name`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40000 ALTER TABLE `wp_wfls_settings` DISABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 0;
SET UNIQUE_CHECKS = 0;
INSERT INTO `wp_wfls_settings` VALUES('allow-xml-rpc', '1', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('captcha-stats', '{\"counts\":[0,0,0,0,0,0,0,0,0,0,0],\"avg\":0}', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('delete-deactivation', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('enable-auth-captcha', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('global-notices', '[]', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('ip-source', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('ip-trusted-proxies', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('last-secret-refresh', '1557879066', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('recaptcha-threshold', '0.5', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('remember-device', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('remember-device-duration', '2592000', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('require-2fa-grace-period-enabled', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('require-2fa.administrator', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('shared-hash-secret', '311fc5367638597ecf6d2da701a03f7c42e9c9e8673885506288b3d83b1fe9ef', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('shared-symmetric-secret', '03f100bf0c986d1b0ebd5689295416ce684e19b11162a2087eb59e37efd6b2d9', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('whitelisted', '', 'yes');
INSERT INTO `wp_wfls_settings` VALUES('xmlrpc-enabled', '1', 'yes');
/*!40000 ALTER TABLE `wp_wfls_settings` ENABLE KEYS */;
SET FOREIGN_KEY_CHECKS = 1;
SET UNIQUE_CHECKS = 1;
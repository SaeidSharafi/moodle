-- --------------------------------------------------------
-- Host:                         2.187.97.57
-- Server version:               5.7.32-0ubuntu0.18.04.1-log - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             11.0.0.5919
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table moodle.mdl_user
CREATE TABLE IF NOT EXISTS `mdl_user` (
  `id` bigint(10) NOT NULL AUTO_INCREMENT,
  `auth` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `confirmed` tinyint(1) NOT NULL DEFAULT '0',
  `policyagreed` tinyint(1) NOT NULL DEFAULT '0',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `suspended` tinyint(1) NOT NULL DEFAULT '0',
  `mnethostid` bigint(10) NOT NULL DEFAULT '0',
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `idnumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lastname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `emailstop` tinyint(1) NOT NULL DEFAULT '0',
  `icq` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `skype` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `yahoo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `aim` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `msn` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `phone1` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `phone2` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `institution` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `department` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `city` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `country` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `lang` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `calendartype` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'gregorian',
  `theme` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timezone` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '99',
  `firstaccess` bigint(10) NOT NULL DEFAULT '0',
  `lastaccess` bigint(10) NOT NULL DEFAULT '0',
  `lastlogin` bigint(10) NOT NULL DEFAULT '0',
  `currentlogin` bigint(10) NOT NULL DEFAULT '0',
  `lastip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `secret` varchar(15) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `picture` bigint(10) NOT NULL DEFAULT '0',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` longtext COLLATE utf8mb4_unicode_ci,
  `descriptionformat` tinyint(2) NOT NULL DEFAULT '1',
  `mailformat` tinyint(1) NOT NULL DEFAULT '1',
  `maildigest` tinyint(1) NOT NULL DEFAULT '0',
  `maildisplay` tinyint(2) NOT NULL DEFAULT '2',
  `autosubscribe` tinyint(1) NOT NULL DEFAULT '1',
  `trackforums` tinyint(1) NOT NULL DEFAULT '0',
  `timecreated` bigint(10) NOT NULL DEFAULT '0',
  `timemodified` bigint(10) NOT NULL DEFAULT '0',
  `trustbitmask` bigint(10) NOT NULL DEFAULT '0',
  `imagealt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastnamephonetic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firstnamephonetic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `middlename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alternatename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `moodlenetprofile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mdl_user_mneuse_uix` (`mnethostid`,`username`),
  KEY `mdl_user_del_ix` (`deleted`),
  KEY `mdl_user_con_ix` (`confirmed`),
  KEY `mdl_user_fir_ix` (`firstname`),
  KEY `mdl_user_las_ix` (`lastname`),
  KEY `mdl_user_cit_ix` (`city`),
  KEY `mdl_user_cou_ix` (`country`),
  KEY `mdl_user_las2_ix` (`lastaccess`),
  KEY `mdl_user_ema_ix` (`email`),
  KEY `mdl_user_aut_ix` (`auth`),
  KEY `mdl_user_idn_ix` (`idnumber`),
  KEY `mdl_user_fir2_ix` (`firstnamephonetic`),
  KEY `mdl_user_las3_ix` (`lastnamephonetic`),
  KEY `mdl_user_mid_ix` (`middlename`),
  KEY `mdl_user_alt_ix` (`alternatename`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=COMPRESSED COMMENT='One record for each person';

-- Dumping data for table moodle.mdl_user: ~13 rows (approximately)
DELETE FROM `mdl_user`;
/*!40000 ALTER TABLE `mdl_user` DISABLE KEYS */;
INSERT INTO `mdl_user` (`id`, `auth`, `confirmed`, `policyagreed`, `deleted`, `suspended`, `mnethostid`, `username`, `password`, `idnumber`, `firstname`, `lastname`, `email`, `emailstop`, `icq`, `skype`, `yahoo`, `aim`, `msn`, `phone1`, `phone2`, `institution`, `department`, `address`, `city`, `country`, `lang`, `calendartype`, `theme`, `timezone`, `firstaccess`, `lastaccess`, `lastlogin`, `currentlogin`, `lastip`, `secret`, `picture`, `url`, `description`, `descriptionformat`, `mailformat`, `maildigest`, `maildisplay`, `autosubscribe`, `trackforums`, `timecreated`, `timemodified`, `trustbitmask`, `imagealt`, `lastnamephonetic`, `firstnamephonetic`, `middlename`, `alternatename`, `moodlenetprofile`) VALUES
	(1, 'manual', 1, 0, 0, 0, 1, 'guest', '$2y$10$KkmmMpyHQ.X9D7qx4LzFxOcqUfGvBl20MdR1SxNb1ndZrZC1SdYBK', '', 'Guest user', ' ', 'root@localhost', 0, '', '', '', '', '', '', '', '', '', '', '', '', 'en', 'gregorian', '', '99', 0, 0, 0, 0, '', '', 0, '', 'This user is a special user that allows read-only access to some courses.', 1, 1, 0, 2, 1, 0, 0, 1596126048, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(2, 'manual', 1, 0, 0, 0, 1, 'admin', '$2y$10$TURs4y0DgwbWtEDLItyY.O20gRqqp5Sfls35r8AlIam88F8u4gX.S', '', 'Admin', 'User', 'moodledev@jdqazvin.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', 'en', 'jalali', '', '99', 1596127158, 1604050396, 1603797696, 1604050156, '151.240.7.204', '', 0, '', '', 1, 1, 0, 1, 1, 0, 0, 1596381815, 0, '', '', '', '', '', ''),
	(3, 'manual', 1, 0, 0, 0, 1, 'teacher1', '$2y$10$1TJyKAXSBWNF9rEiigGw2uYI5oH8..ABfgkeNfjqz.4L9kN9HZ1Xm', '', 'teacher1', 'Teacher', 'teacher1@mdldev.jdqazvin.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', 'en', 'gregorian', '', '99', 1596130073, 1596272855, 1596130073, 1596271916, '138.201.160.225', '', 0, '', '', 1, 1, 0, 2, 1, 0, 1596130021, 1596130021, 0, '', '', '', '', '', ''),
	(4, 'manual', 1, 0, 0, 0, 1, 'student1', '$2y$10$7trA62PApfg/Clnl9LecceQeFOBRn7e6enGPiGkwEBK9.ZMqHuqem', '', 'student1', 'Student', 'student1@mdldev.jdqazvin.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', 'en', 'jalali', '', '99', 1596134021, 1596134934, 0, 1596134021, '151.240.6.0', '', 0, '', '', 1, 1, 0, 2, 1, 0, 1596130049, 1596134939, 0, '', '', '', '', '', ''),
	(5, 'manual', 1, 0, 0, 0, 1, 'joomdle_connector', '$2y$10$SCW40Vz58xxPDvb3LZpxRegEc37WJjYue.m6uTQnZy6HMX/.rTZ7q', '', 'Joomdle', 'Connector', 'joomdle@donotdeletemeplease.com', 0, '', '', '', '', '', '', '', '', '', '', '', '', 'en', 'jalali', '', '99', 0, 0, 0, 0, '', '', 0, '', NULL, 1, 1, 0, 2, 1, 0, 1598778343, 1598778343, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(6, 'joomdle', 1, 0, 0, 0, 1, 'kuro13', '$2y$10$3DgGUkRE4/lt8fpLwWwoIezebLUmzMzkFYtjEYhpVOn04F384.r7.', '', 'سعید', 'شرفی', 'kuro@joomdle.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1598783457, 1598866159, 1598793487, 1598866159, '151.240.4.214', '', 3404, '', NULL, 1, 1, 0, 2, 1, 0, 1598783457, 1598783457, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(7, 'joomdle', 1, 0, 0, 0, 1, 'alireza', '$2y$10$iiI4jVgXUiL/Z.FkjI8.KO6/3YNaZMCv8V9gxb.1Vz2f/Q9f2IYfK', '', 'علیرضا', 'زمانی', 'azamani@asanlms.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1598867720, 1599383780, 1598934996, 1599380074, '192.168.2.1', '', 3368, '', NULL, 1, 1, 0, 2, 1, 0, 1598867720, 1598867720, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(8, 'joomdle', 1, 0, 0, 0, 1, 'student5', '', '', 'Ernest', 'Jackson jr', 'student5@local.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', 'fa', 'jalali', '', '99', 1599550916, 1603541166, 1599550936, 1603541166, '85.203.22.71', '', 0, '', NULL, 1, 1, 0, 2, 1, 0, 1599550916, 1599550916, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(9, 'joomdle', 1, 0, 1, 0, 1, '4310408648@asanlms.ir.1599927573', '$2y$10$Rd8NYL1fmYm9omqUTj24secAa95wah3t6QFMfDloVQjmyfrRCxgNS', '', 'سعید', 'شرفی', '5bf925b3b0126360ddb0291740ac32bb', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1599927444, 0, 0, 0, '192.168.2.1', '', 0, '', NULL, 1, 1, 0, 2, 1, 0, 1599927444, 1599927573, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(10, 'joomdle', 1, 0, 1, 0, 1, '4310408648@asanlms.ir.1599927710', '$2y$10$2Cg0Y.1gEedWzmlI4Qt7TeiqidZFL14hlgifZdZ7oKQBYruPAENiu', '', 'سعید', 'شرفی', '5bf925b3b0126360ddb0291740ac32bb', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1599927600, 0, 0, 0, '192.168.2.1', '', 0, '', NULL, 1, 1, 0, 2, 1, 0, 1599927600, 1599927710, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(11, 'joomdle', 1, 0, 0, 0, 1, '4310408648', '$2y$10$om9rlA9vun618JNC89HX3OH0jriXnuG3XC.rOdaQWaXZE09hxvPze', '', 'سعید', 'شرفی', '4310408648@asanlms.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1599927741, 1601730263, 1601061439, 1601730263, '151.240.7.234', '', 3422, '', NULL, 1, 1, 0, 2, 1, 0, 1599927741, 1599927742, 0, NULL, NULL, NULL, NULL, NULL, NULL),
	(12, 'joomdle', 1, 0, 0, 0, 1, 'synctest', '$2y$10$CLCaxDjSNPIMlvLbvSvrZ.P/TyXAzy2ygB3LbA7eCPnkRYYIZtCI6', '', 'joomdle', 'sync', 'sync@asanlms.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1600338377, 1600338377, 0, 1600338377, '151.240.6.77', '', 3396, '', '', 1, 1, 0, 2, 1, 0, 1600338302, 1600338304, 0, '', '', '', '', '', ''),
	(13, 'joomdle', 1, 0, 0, 0, 1, 'manager', '$2y$10$7HPI2Q27wSCP22TBzv4gheANKxiN68R/oKgYmzAIPCol9bbddrM6K', '', 'مدیر', 'سیستم', 'manager@moodle.asanlms.ir', 0, '', '', '', '', '', '', '', '', '', '', '', '', '', 'jalali', '', '', 1600346463, 0, 0, 0, '192.168.2.1', '', 3400, '', NULL, 1, 1, 0, 2, 1, 0, 1600346463, 1600346502, 0, NULL, NULL, NULL, NULL, NULL, NULL);
/*!40000 ALTER TABLE `mdl_user` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;

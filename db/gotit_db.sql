-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jan 28, 2018 at 08:48 PM
-- Server version: 5.7.19
-- PHP Version: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gotit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

DROP TABLE IF EXISTS `activity`;
CREATE TABLE IF NOT EXISTS `activity` (
  `activity_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `activity_type` varchar(20) NOT NULL,
  `time_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`activity_id`),
  KEY `UsersActivityForeign` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `activity`
--

INSERT INTO `activity` (`activity_id`, `user_id`, `activity_type`, `time_created`) VALUES
(49, 15, 'REGISTRATION', '2018-01-28 19:26:08'),
(50, 15, 'ITEM_CREATION', '2018-01-28 19:27:21'),
(51, 15, 'ITEM_CREATION', '2018-01-28 19:27:47'),
(52, 15, 'ITEM_CREATION', '2018-01-28 19:28:12'),
(53, 16, 'REGISTRATION', '2018-01-28 19:29:51'),
(54, 16, 'ITEM_CREATION', '2018-01-28 19:30:28'),
(55, 16, 'ITEM_CREATION', '2018-01-28 19:31:09'),
(56, 16, 'ITEM_CREATION', '2018-01-28 19:31:40'),
(57, 16, 'ITEM_DELETION', '2018-01-28 19:32:26'),
(58, 17, 'REGISTRATION', '2018-01-28 19:33:10'),
(59, 17, 'ITEM_CREATION', '2018-01-28 19:33:49'),
(60, 17, 'ITEM_CREATION', '2018-01-28 19:34:19'),
(61, 17, 'ITEM_CREATION', '2018-01-28 19:34:44'),
(62, 15, 'ITEM_CREATION', '2018-01-28 19:37:09'),
(63, 15, 'USER_REPORT', '2018-01-28 19:39:13'),
(64, 15, 'USER_REPORT', '2018-01-28 19:39:50');

-- --------------------------------------------------------

--
-- Table structure for table `end_user`
--

DROP TABLE IF EXISTS `end_user`;
CREATE TABLE IF NOT EXISTS `end_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `phone_number` varchar(13) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(13) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ROLE_USER',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `UNIQ_A3515A0DF85E0677` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `end_user`
--

INSERT INTO `end_user` (`user_id`, `username`, `password`, `first_name`, `last_name`, `phone_number`, `is_active`, `date_time`, `role`) VALUES
(1, 'marko@gmail.com', '$2y$13$rq6o9WRaUqRpzdsK2yy2y.z0ycg5eL9WFigddQj.5S4faE7baF/kK', 'Marko', 'Ognjenovic', '0603921320', 1, '2017-12-24 22:46:00', 'ROLE_ADMIN'),
(15, 'milos@gmail.com', '$2y$13$s3rTqykZ7V5VFtcVNDk7iOP6VQZtNfhMT6plRRl.DCnNj00/AZlSG', 'Milos', 'Milovanovic', '059483928', 1, '2018-01-28 19:26:08', 'ROLE_USER'),
(16, 'stefan@gmail.com', '$2y$13$MwI7hEDeBJEr7nSkewjv7uRomAb7ZeidqejgVf9M1lmtb1WMRw7R.', 'Stefan', 'Stefanovic', '0542938129', 1, '2018-01-28 19:29:50', 'ROLE_USER'),
(17, 'boban@gmail.com', '$2y$13$BAwZNI.0XPnSXsXXAODvsuLMJXSUlHfZgZBMWbAze.raQC6wqyf42', 'Boban', 'Bobanovic', '023948183', 1, '2018-01-28 19:33:09', 'ROLE_USER');

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

DROP TABLE IF EXISTS `item`;
CREATE TABLE IF NOT EXISTS `item` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `marker_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `description` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `image_url` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `IDX_1F1B251E474460EB` (`marker_id`),
  KEY `IDX_1F1B251EA76ED395` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=108 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`item_id`, `marker_id`, `user_id`, `description`, `image_url`, `type`, `date_time`, `deleted`) VALUES
(98, 82, 15, 'Neki kljucevi sa priveskom kucice', '05145d16c8b10d959c800f114c4d0a75.jpeg', 'keys', '2018-01-28 19:27:21', 0),
(99, 83, 15, 'Crni usb', NULL, 'usb', '2018-01-28 19:27:47', 0),
(100, 84, 15, 'Sivi Samsung s5', NULL, 'phone', '2018-01-28 19:28:12', 0),
(101, NULL, 16, '2 kljuci na privesku', NULL, 'keys', '2018-01-28 19:30:28', 1),
(102, 86, 16, 'Zuta gumica na kljucu', NULL, 'keys', '2018-01-28 19:31:09', 0),
(103, 87, 16, 'Zenska torba', NULL, 'other', '2018-01-28 19:31:39', 0),
(104, 88, 17, 'USB sa crvenim poklopcem', NULL, 'usb', '2018-01-28 19:33:49', 0),
(105, 89, 17, 'Sivi kljucevi od auta', NULL, 'keys', '2018-01-28 19:34:19', 0),
(106, 90, 17, 'Kljucevi sa 2 priveska', NULL, 'keys', '2018-01-28 19:34:44', 0),
(107, 91, 15, 'Crveni kljucevi', NULL, 'keys', '2018-01-28 19:37:09', 0);

-- --------------------------------------------------------

--
-- Table structure for table `item_history`
--

DROP TABLE IF EXISTS `item_history`;
CREATE TABLE IF NOT EXISTS `item_history` (
  `item_id` int(11) NOT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lng` decimal(10,6) NOT NULL,
  `time_deleted` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `reason` varchar(20) NOT NULL,
  PRIMARY KEY (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `item_history`
--

INSERT INTO `item_history` (`item_id`, `lat`, `lng`, `time_deleted`, `reason`) VALUES
(101, '44.812653', '20.463924', '2018-01-28 19:32:26', 'DONE');

-- --------------------------------------------------------

--
-- Table structure for table `item_type`
--

DROP TABLE IF EXISTS `item_type`;
CREATE TABLE IF NOT EXISTS `item_type` (
  `type_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `item_type`
--

INSERT INTO `item_type` (`type_id`) VALUES
('documents'),
('glasses'),
('jewelry'),
('keys'),
('other'),
('pet'),
('phone'),
('plus1'),
('usb'),
('wallet'),
('watch');

-- --------------------------------------------------------

--
-- Table structure for table `marker`
--

DROP TABLE IF EXISTS `marker`;
CREATE TABLE IF NOT EXISTS `marker` (
  `marker_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lng` decimal(10,6) NOT NULL,
  `num_of_items` int(11) NOT NULL,
  `type` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`marker_id`),
  KEY `IDX_82CF20FEA76ED395` (`user_id`),
  KEY `type` (`type`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `marker`
--

INSERT INTO `marker` (`marker_id`, `user_id`, `lat`, `lng`, `num_of_items`, `type`, `date_time`) VALUES
(82, 15, '44.777327', '20.458603', 1, 'keys', '2018-01-28 19:27:21'),
(83, 15, '44.792556', '20.477829', 1, 'usb', '2018-01-28 19:27:47'),
(84, 15, '44.784028', '20.490532', 1, 'phone', '2018-01-28 19:28:12'),
(86, 16, '44.805224', '20.426502', 1, 'keys', '2018-01-28 19:31:09'),
(87, 16, '44.820447', '20.429935', 1, 'other', '2018-01-28 19:31:39'),
(88, 17, '44.818377', '20.427361', 1, 'usb', '2018-01-28 19:33:49'),
(89, 17, '45.772792', '19.125824', 1, 'keys', '2018-01-28 19:34:19'),
(90, 17, '45.764709', '19.113550', 1, 'keys', '2018-01-28 19:34:44'),
(91, 15, '44.813019', '20.416031', 1, 'keys', '2018-01-28 19:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

DROP TABLE IF EXISTS `report`;
CREATE TABLE IF NOT EXISTS `report` (
  `report_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `solved` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`report_id`),
  KEY `UserReportingForeign` (`user_id`),
  KEY `ItemReportedForeign` (`item_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `report`
--

INSERT INTO `report` (`report_id`, `user_id`, `item_id`, `description`, `solved`) VALUES
(5, 15, 104, 'Krsi uslove zbog...', 0),
(6, 15, 106, 'Nije ok zbog...', 0);

-- --------------------------------------------------------

--
-- Table structure for table `statistics`
--

DROP TABLE IF EXISTS `statistics`;
CREATE TABLE IF NOT EXISTS `statistics` (
  `statistics_id` varchar(15) NOT NULL,
  `num_of_users` int(11) NOT NULL,
  `num_of_items` int(11) NOT NULL,
  `num_of_found` int(11) NOT NULL,
  `num_of_banned` int(11) NOT NULL,
  PRIMARY KEY (`statistics_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statistics`
--

INSERT INTO `statistics` (`statistics_id`, `num_of_users`, `num_of_items`, `num_of_found`, `num_of_banned`) VALUES
('24.01.2018.', 10, 4, 1, 1),
('25.01.2018.', 3, 1, 0, 2),
('26.01.2018.', 7, 4, 2, 1),
('27.01.2018.', 5, 3, 1, 0),
('28.01.2018', 3, 10, 1, 2),
('MAIN', 28, 22, 5, 6);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity`
--
ALTER TABLE `activity`
  ADD CONSTRAINT `UsersActivityForeign` FOREIGN KEY (`user_id`) REFERENCES `end_user` (`user_id`);

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `FK_1F1B251EA76ED395` FOREIGN KEY (`user_id`) REFERENCES `end_user` (`user_id`),
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`type`) REFERENCES `item_type` (`type_id`),
  ADD CONSTRAINT `marker_ibfk_1` FOREIGN KEY (`marker_id`) REFERENCES `marker` (`marker_id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Constraints for table `item_history`
--
ALTER TABLE `item_history`
  ADD CONSTRAINT `OriginalItemForeign` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `marker`
--
ALTER TABLE `marker`
  ADD CONSTRAINT `FK_82CF20FEA76ED395` FOREIGN KEY (`user_id`) REFERENCES `end_user` (`user_id`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `ItemReportedForeign` FOREIGN KEY (`item_id`) REFERENCES `item` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `UserReportingForeign` FOREIGN KEY (`user_id`) REFERENCES `end_user` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

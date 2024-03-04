# ************************************************************
# Sequel Ace SQL dump
# Version 20062
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 5.7.39)
# Database: nfl
# Generation Time: 2024-03-04 17:28:50 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table athletes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `athletes`;

CREATE TABLE `athletes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(16) DEFAULT NULL,
  `first_name` varchar(64) DEFAULT NULL,
  `last_name` varchar(64) DEFAULT NULL,
  `full_name` varchar(128) DEFAULT NULL,
  `short_name` varchar(64) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `debut_year` int(11) DEFAULT NULL,
  `birth_city` varchar(32) DEFAULT NULL,
  `birth_state` varchar(32) DEFAULT NULL,
  `birth_country` varchar(32) DEFAULT NULL,
  `jersey` int(11) DEFAULT NULL,
  `position` varchar(32) DEFAULT NULL,
  `team` int(11) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `status` varchar(16) DEFAULT NULL,
  `race_asian` float DEFAULT NULL,
  `race_indian` float DEFAULT NULL,
  `race_black` float DEFAULT NULL,
  `race_white` float DEFAULT NULL,
  `race_middle_eastern` float DEFAULT NULL,
  `race_latino_hispanic` float DEFAULT NULL,
  `dominant_race` varchar(16) DEFAULT NULL,
  `face_confidence` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table teams
# ------------------------------------------------------------

DROP TABLE IF EXISTS `teams`;

CREATE TABLE `teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `location` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `short_name` varchar(32) DEFAULT NULL,
  `color` varchar(8) DEFAULT NULL,
  `alternate_color` varchar(8) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL,
  `is_all_star` tinyint(1) DEFAULT NULL,
  `venue_name` varchar(64) DEFAULT NULL,
  `venue_city` varchar(64) DEFAULT NULL,
  `venue_state` varchar(64) DEFAULT NULL,
  `venue_zip_code` int(11) DEFAULT NULL,
  `venue_capacity` int(11) DEFAULT NULL,
  `venue_grass` tinyint(1) DEFAULT NULL,
  `venue_indoor` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

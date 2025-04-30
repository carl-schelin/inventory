-- MySQL dump 10.14  Distrib 5.5.68-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: inventory
-- ------------------------------------------------------
-- Server version	5.5.68-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `inv_users`
--

DROP TABLE IF EXISTS `inv_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_users` (
  `usr_id` int(8) NOT NULL AUTO_INCREMENT,
  `usr_level` int(1) NOT NULL DEFAULT '2',
  `usr_disabled` int(1) NOT NULL DEFAULT '0',
  `usr_name` varchar(120) NOT NULL DEFAULT '',
  `usr_first` varchar(255) NOT NULL DEFAULT '',
  `usr_last` varchar(255) NOT NULL DEFAULT '',
  `usr_email` varchar(255) NOT NULL DEFAULT '',
  `usr_manager` int(10) NOT NULL DEFAULT '0',
  `usr_title` int(10) NOT NULL DEFAULT '0',
  `usr_passwd` varchar(32) NOT NULL DEFAULT '',
  `usr_reset` int(1) NOT NULL DEFAULT '0',
  `usr_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usr_group` int(1) unsigned NOT NULL DEFAULT '0',
  `usr_notify` int(10) NOT NULL DEFAULT '-1',
  `usr_freq` int(10) NOT NULL DEFAULT '-1',
  `usr_countdown` int(10) NOT NULL DEFAULT '0',
  `usr_phone` char(15) NOT NULL DEFAULT '000-000-0000',
  `usr_theme` int(10) NOT NULL DEFAULT '7',
  `usr_verified` int(10) NOT NULL DEFAULT '0',
  `usr_magickey` char(60) NOT NULL DEFAULT '',
  `usr_linkexpire` int(20) NOT NULL DEFAULT '0',
  `usr_checkin` date NOT NULL DEFAULT '1971-01-01',
  `usr_ipaddr` char(20) NOT NULL DEFAULT '0.0.0.0',
  PRIMARY KEY (`usr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:02:02

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
-- Table structure for table `inv_groups`
--

DROP TABLE IF EXISTS `inv_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_groups` (
  `grp_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `grp_disabled` int(1) NOT NULL DEFAULT '0',
  `grp_name` char(60) NOT NULL DEFAULT '',
  `grp_manager` int(10) NOT NULL DEFAULT '0',
  `grp_department` int(10) NOT NULL DEFAULT '0',
  `grp_email` char(255) NOT NULL DEFAULT '',
  `grp_page` varchar(255) NOT NULL DEFAULT '',
  `grp_changedby` int(10) NOT NULL DEFAULT '0',
  `grp_status` int(10) NOT NULL DEFAULT '0',
  `grp_server` int(10) NOT NULL DEFAULT '0',
  `grp_import` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`grp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:30

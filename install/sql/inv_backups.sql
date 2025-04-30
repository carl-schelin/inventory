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
-- Table structure for table `inv_backups`
--

DROP TABLE IF EXISTS `inv_backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_backups` (
  `bu_id` int(10) NOT NULL AUTO_INCREMENT,
  `bu_companyid` int(10) NOT NULL DEFAULT '0',
  `bu_start` char(15) NOT NULL DEFAULT '1971-01-01',
  `bu_include` int(10) NOT NULL DEFAULT '0',
  `bu_retention` int(10) NOT NULL DEFAULT '0',
  `bu_sunday` int(10) NOT NULL DEFAULT '0',
  `bu_monday` int(10) NOT NULL DEFAULT '0',
  `bu_tuesday` int(10) NOT NULL DEFAULT '0',
  `bu_wednesday` int(10) NOT NULL DEFAULT '0',
  `bu_thursday` int(10) NOT NULL DEFAULT '0',
  `bu_friday` int(10) NOT NULL DEFAULT '0',
  `bu_saturday` int(10) NOT NULL DEFAULT '0',
  `bu_suntime` char(10) NOT NULL DEFAULT '00:00',
  `bu_montime` char(10) NOT NULL DEFAULT '00:00',
  `bu_tuetime` char(10) NOT NULL DEFAULT '00:00',
  `bu_wedtime` char(10) NOT NULL DEFAULT '00:00',
  `bu_thutime` char(10) NOT NULL DEFAULT '00:00',
  `bu_fritime` char(10) NOT NULL DEFAULT '00:00',
  `bu_sattime` char(10) NOT NULL DEFAULT '00:00',
  `bu_changedby` int(10) NOT NULL DEFAULT '0',
  `bu_fromdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `bu_todate` timestamp NOT NULL DEFAULT '1971-01-01 07:00:00',
  `bu_notes` text NOT NULL,
  PRIMARY KEY (`bu_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:29

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
-- Table structure for table `inv_models`
--

DROP TABLE IF EXISTS `inv_models`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_models` (
  `mod_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mod_vendor` int(10) NOT NULL DEFAULT '0',
  `mod_name` char(100) NOT NULL DEFAULT '',
  `mod_type` int(10) NOT NULL DEFAULT '0',
  `mod_primary` int(10) NOT NULL DEFAULT '0',
  `mod_size` char(100) NOT NULL DEFAULT '',
  `mod_speed` char(20) NOT NULL DEFAULT '',
  `mod_eopur` date NOT NULL DEFAULT '1971-01-01',
  `mod_eoship` date NOT NULL DEFAULT '1971-01-01',
  `mod_eol` date NOT NULL DEFAULT '1971-01-01',
  `mod_plugs` int(10) NOT NULL DEFAULT '0',
  `mod_plugtype` int(10) NOT NULL DEFAULT '0',
  `mod_volts` int(10) NOT NULL DEFAULT '0',
  `mod_draw` char(20) NOT NULL DEFAULT '',
  `mod_start` char(20) NOT NULL DEFAULT '',
  `mod_btu` char(30) NOT NULL DEFAULT '',
  `mod_virtual` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mod_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:58

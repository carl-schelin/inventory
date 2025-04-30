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
-- Table structure for table `inv_locations`
--

DROP TABLE IF EXISTS `inv_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_locations` (
  `loc_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loc_type` int(10) NOT NULL DEFAULT '0',
  `loc_name` char(60) NOT NULL DEFAULT '',
  `loc_addr1` char(60) NOT NULL DEFAULT '',
  `loc_addr2` char(60) NOT NULL DEFAULT '',
  `loc_suite` char(60) NOT NULL DEFAULT '',
  `loc_city` int(10) NOT NULL DEFAULT '0',
  `loc_county` char(60) NOT NULL DEFAULT '',
  `loc_state` char(60) NOT NULL DEFAULT '',
  `loc_zipcode` char(60) NOT NULL DEFAULT '',
  `loc_country` char(60) NOT NULL DEFAULT '',
  `loc_contact1` char(255) NOT NULL DEFAULT '',
  `loc_contact2` char(255) NOT NULL DEFAULT '',
  `loc_details` char(150) NOT NULL DEFAULT '',
  `loc_default` int(10) NOT NULL DEFAULT '0',
  `loc_convention` char(20) NOT NULL DEFAULT '',
  `loc_instance` int(10) NOT NULL DEFAULT '0',
  `loc_xpoint` int(10) NOT NULL DEFAULT '0',
  `loc_ypoint` int(10) NOT NULL DEFAULT '0',
  `loc_xlen` int(10) NOT NULL DEFAULT '0',
  `loc_ylen` int(10) NOT NULL DEFAULT '0',
  `loc_identity` char(10) DEFAULT NULL,
  `loc_environment` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`loc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:55

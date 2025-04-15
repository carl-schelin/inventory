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
-- Table structure for table `inv_licenses`
--

DROP TABLE IF EXISTS `inv_licenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_licenses` (
  `lic_id` int(10) NOT NULL AUTO_INCREMENT,
  `lic_vendor` char(30) NOT NULL DEFAULT '',
  `lic_product` char(32) NOT NULL DEFAULT '',
  `lic_date` date NOT NULL DEFAULT '1971-01-01',
  `lic_vendorpo` char(20) NOT NULL DEFAULT '',
  `lic_po` char(20) NOT NULL DEFAULT '',
  `lic_project` int(10) NOT NULL DEFAULT '0',
  `lic_quantity` int(10) NOT NULL DEFAULT '0',
  `lic_key` char(128) NOT NULL DEFAULT '',
  `lic_serial` char(128) NOT NULL DEFAULT '',
  `lic_domain` char(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`lic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:55

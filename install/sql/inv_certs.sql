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
-- Table structure for table `inv_certs`
--

DROP TABLE IF EXISTS `inv_certs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_certs` (
  `cert_id` int(10) NOT NULL AUTO_INCREMENT,
  `cert_desc` char(80) NOT NULL DEFAULT '',
  `cert_url` char(80) NOT NULL DEFAULT '',
  `cert_filename` char(80) NOT NULL DEFAULT '',
  `cert_expire` date NOT NULL DEFAULT '1971-01-01',
  `cert_authority` char(60) NOT NULL DEFAULT '',
  `cert_subject` char(60) NOT NULL DEFAULT '',
  `cert_group` int(10) NOT NULL DEFAULT '0',
  `cert_ca` int(10) NOT NULL DEFAULT '0',
  `cert_memo` text NOT NULL,
  `cert_isca` int(10) NOT NULL DEFAULT '0',
  `cert_top` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cert_id`)
) ENGINE=MyISAM AUTO_INCREMENT=117 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:29

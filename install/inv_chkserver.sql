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
-- Table structure for table `inv_chkserver`
--

DROP TABLE IF EXISTS `inv_chkserver`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_chkserver` (
  `chk_id` int(10) NOT NULL AUTO_INCREMENT,
  `chk_companyid` int(10) NOT NULL DEFAULT '0',
  `chk_errorid` int(10) NOT NULL DEFAULT '0',
  `chk_userid` int(10) NOT NULL DEFAULT '0',
  `chk_status` int(10) NOT NULL DEFAULT '0',
  `chk_priority` int(10) NOT NULL DEFAULT '0',
  `chk_opened` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `chk_closed` timestamp NOT NULL DEFAULT '1971-01-01 07:00:00',
  `chk_text` text NOT NULL,
  `chk_import` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`chk_id`)
) ENGINE=MyISAM AUTO_INCREMENT=215827 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:29

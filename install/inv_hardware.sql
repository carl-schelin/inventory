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
-- Table structure for table `inv_hardware`
--

DROP TABLE IF EXISTS `inv_hardware`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_hardware` (
  `hw_id` int(8) NOT NULL AUTO_INCREMENT,
  `hw_companyid` int(8) NOT NULL DEFAULT '0',
  `hw_hw_id` int(10) NOT NULL DEFAULT '0',
  `hw_hd_id` int(10) NOT NULL DEFAULT '0',
  `hw_type` int(10) NOT NULL DEFAULT '0',
  `hw_serial` char(100) NOT NULL DEFAULT '',
  `hw_asset` char(20) NOT NULL DEFAULT '',
  `hw_service` char(20) NOT NULL DEFAULT '',
  `hw_vendorid` int(8) NOT NULL DEFAULT '0',
  `hw_projectid` int(10) NOT NULL DEFAULT '0',
  `hw_product` int(10) NOT NULL DEFAULT '0',
  `hw_group` int(10) NOT NULL DEFAULT '0',
  `hw_poid` int(10) NOT NULL DEFAULT '0',
  `hw_purchased` date NOT NULL DEFAULT '1971-01-01',
  `hw_built` date NOT NULL DEFAULT '1971-01-01',
  `hw_active` date NOT NULL DEFAULT '1971-01-01',
  `hw_retired` date NOT NULL DEFAULT '1971-01-01',
  `hw_reused` date NOT NULL DEFAULT '1971-01-01',
  `hw_eolticket` char(30) NOT NULL DEFAULT '',
  `hw_supportid` int(10) NOT NULL DEFAULT '0',
  `hw_response` int(10) NOT NULL DEFAULT '0',
  `hw_supid_verified` int(10) NOT NULL DEFAULT '0',
  `hw_primary` int(10) NOT NULL DEFAULT '0',
  `hw_verified` int(10) NOT NULL DEFAULT '0',
  `hw_deleted` int(10) NOT NULL DEFAULT '0',
  `hw_note` char(255) NOT NULL DEFAULT '',
  `hw_rma` char(50) NOT NULL DEFAULT '',
  `hw_user` int(10) NOT NULL DEFAULT '0',
  `hw_update` date NOT NULL DEFAULT '1971-01-01',
  `hw_supportstart` date NOT NULL DEFAULT '1971-01-01',
  `hw_supportend` date NOT NULL DEFAULT '1971-01-01',
  `hw_custodian` int(10) NOT NULL DEFAULT '0',
  `hw_buc` int(10) NOT NULL DEFAULT '0',
  `hw_business` int(10) NOT NULL DEFAULT '0',
  `hw_dept` int(10) NOT NULL DEFAULT '0',
  `hw_expense` int(10) NOT NULL DEFAULT '0',
  `hw_customer` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`hw_id`),
  KEY `hw_companyid_idx` (`hw_companyid`)
) ENGINE=MyISAM AUTO_INCREMENT=308058 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:30

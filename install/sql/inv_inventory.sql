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
-- Table structure for table `inv_inventory`
--

DROP TABLE IF EXISTS `inv_inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_inventory` (
  `inv_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `inv_name` char(60) NOT NULL DEFAULT '',
  `inv_companyid` int(10) NOT NULL DEFAULT '0',
  `inv_fqdn` char(100) NOT NULL DEFAULT '',
  `inv_vmname` char(60) NOT NULL DEFAULT '',
  `inv_uuid` char(100) NOT NULL DEFAULT '',
  `inv_satuuid` char(100) NOT NULL DEFAULT '',
  `inv_function` char(255) NOT NULL DEFAULT '',
  `inv_document` char(255) NOT NULL DEFAULT '',
  `inv_callpath` int(10) NOT NULL DEFAULT '0',
  `inv_class` int(10) NOT NULL DEFAULT '0',
  `inv_power` char(30) NOT NULL DEFAULT '',
  `inv_location` int(10) NOT NULL DEFAULT '0',
  `inv_rack` char(30) NOT NULL DEFAULT '',
  `inv_row` char(30) NOT NULL DEFAULT '',
  `inv_unit` int(10) NOT NULL DEFAULT '0',
  `inv_notes` char(255) NOT NULL DEFAULT '',
  `inv_status` int(10) unsigned DEFAULT '0',
  `inv_zone` int(10) NOT NULL DEFAULT '0',
  `inv_manager` int(10) NOT NULL DEFAULT '0',
  `inv_appadmin` int(10) NOT NULL DEFAULT '0',
  `inv_front` int(10) NOT NULL DEFAULT '0',
  `inv_rear` int(10) NOT NULL DEFAULT '0',
  `inv_ssh` int(1) NOT NULL DEFAULT '0',
  `inv_ansible` int(10) NOT NULL DEFAULT '0',
  `inv_product` int(10) NOT NULL DEFAULT '0',
  `inv_project` int(10) NOT NULL,
  `inv_image` int(10) NOT NULL DEFAULT '0',
  `inv_virtual` int(10) NOT NULL DEFAULT '0',
  `inv_response` int(10) NOT NULL DEFAULT '0',
  `inv_department` int(10) NOT NULL DEFAULT '0',
  `inv_maint` int(10) NOT NULL DEFAULT '1',
  `inv_mstart` int(10) NOT NULL DEFAULT '0',
  `inv_mend` int(10) NOT NULL DEFAULT '0',
  `inv_mdow` int(10) NOT NULL DEFAULT '0',
  `inv_minterval` int(10) NOT NULL DEFAULT '0',
  `inv_kernel` date NOT NULL DEFAULT '1971-01-01',
  `inv_patchid` int(10) NOT NULL DEFAULT '0',
  `inv_patched` date NOT NULL DEFAULT '1971-01-01',
  `inv_svcnow` int(10) NOT NULL DEFAULT '0',
  `inv_env` int(10) NOT NULL DEFAULT '0',
  `inv_appliance` int(10) NOT NULL DEFAULT '0',
  `inv_ticket` char(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`inv_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13645 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:55

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
-- Table structure for table `inv_interface`
--

DROP TABLE IF EXISTS `inv_interface`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inv_interface` (
  `int_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `int_server` char(60) NOT NULL DEFAULT '',
  `int_domain` char(100) NOT NULL DEFAULT '',
  `int_companyid` int(10) NOT NULL DEFAULT '0',
  `int_ipaddressid` int(10) NOT NULL DEFAULT '0',
  `int_face` char(20) NOT NULL DEFAULT '',
  `int_int_id` int(10) NOT NULL DEFAULT '0',
  `int_ip6` char(1) NOT NULL DEFAULT '0',
  `int_addr` char(100) NOT NULL DEFAULT '',
  `int_vaddr` int(10) NOT NULL DEFAULT '0',
  `int_netbios` char(100) NOT NULL DEFAULT '',
  `int_eth` char(20) NOT NULL DEFAULT '',
  `int_veth` int(10) NOT NULL DEFAULT '0',
  `int_network` int(10) NOT NULL DEFAULT '0',
  `int_mask` char(50) NOT NULL DEFAULT '',
  `int_gate` char(100) NOT NULL DEFAULT '',
  `int_vgate` int(10) NOT NULL DEFAULT '0',
  `int_note` char(255) NOT NULL DEFAULT '',
  `int_verified` char(1) NOT NULL DEFAULT '0',
  `int_switch` char(50) NOT NULL DEFAULT '',
  `int_port` char(50) NOT NULL DEFAULT '',
  `int_sysport` char(50) NOT NULL DEFAULT '',
  `int_primary` int(8) NOT NULL DEFAULT '0',
  `int_type` int(10) NOT NULL DEFAULT '0',
  `int_vlan` char(10) NOT NULL DEFAULT '',
  `int_media` int(10) NOT NULL DEFAULT '0',
  `int_speed` int(10) NOT NULL DEFAULT '0',
  `int_duplex` int(10) NOT NULL DEFAULT '0',
  `int_role` int(10) NOT NULL DEFAULT '0',
  `int_redundancy` int(10) NOT NULL DEFAULT '0',
  `int_groupname` char(20) NOT NULL DEFAULT '',
  `int_virtual` int(10) NOT NULL DEFAULT '0',
  `int_zone` int(10) NOT NULL DEFAULT '0',
  `int_user` int(10) NOT NULL DEFAULT '0',
  `int_update` date NOT NULL DEFAULT '1971-01-01',
  `int_openview` int(10) NOT NULL DEFAULT '0',
  `int_nagios` int(10) NOT NULL DEFAULT '0',
  `int_backup` int(10) NOT NULL DEFAULT '0',
  `int_management` int(10) NOT NULL DEFAULT '0',
  `int_login` int(10) NOT NULL DEFAULT '0',
  `int_ping` int(10) NOT NULL DEFAULT '0',
  `int_ssh` int(10) NOT NULL DEFAULT '0',
  `int_http` int(10) NOT NULL DEFAULT '0',
  `int_ftp` int(10) NOT NULL DEFAULT '0',
  `int_smtp` int(10) NOT NULL DEFAULT '0',
  `int_snmp` int(10) NOT NULL DEFAULT '0',
  `int_load` int(10) NOT NULL DEFAULT '0',
  `int_uptime` int(10) NOT NULL DEFAULT '0',
  `int_cpu` int(10) NOT NULL DEFAULT '0',
  `int_swap` int(10) NOT NULL DEFAULT '0',
  `int_memory` int(10) NOT NULL DEFAULT '0',
  `int_cfg2html` int(10) NOT NULL DEFAULT '1',
  `int_notify` int(10) NOT NULL DEFAULT '0',
  `int_hours` int(10) NOT NULL DEFAULT '0',
  `int_hostname` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`int_id`),
  KEY `int_companyid_idx` (`int_companyid`),
  KEY `int_addr_idx` (`int_addr`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-02-23  1:01:30

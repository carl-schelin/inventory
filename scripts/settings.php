<?php

# add a space at the end as the company will be inserted into strings.
$Sitecompany		= 'Hobgoblin Consulting Services, LLC ';
$Companyemail           = 'internal.pri';

# by default, enable debugging in case we missed a server entry. ALL means full on screen debugging
$Sitedebug		= 'ALL';

# Set the environment here so other places in the code can be tested without changing code.
$hostname = php_uname('n');

# inventory is the new home but it's a VirtualHost so hostname is lnmt1cuomtool10. This "fixes" this.
#if ($hostname == 'lnmt1cuomtool11.internal.pri') {
#  $hostname = "inventory.internal.pri";
#}

#############################################################
# Development servers
#############################################################

if ($hostname == "bldr0cuomdev1.internal.pri") {
  $Siteenv              = "DEV";
  $Sitedebug		= "NO"; # no error logging
  $Sitedebug		= "ALL"; # log errors to file _and_ to the screen
  $Sitedebug		= "YES"; # log errors just to a file

# Set site specific variables
  $Sitehttp             = "bldr0cuomdev1.internal.pri";
  $Siteurl              = "http://" . $Sitehttp;
  $Statushttp		= "bldr0cuomdev1.internal.pri";
  $Statusurl		= "http://" . $Statushttp;
  $Nagioshttp		= "lnmt1cuomjs1.internal.pri";
  $Nagiosurl		= "http://" . $Nagioshttp;
  $Wikihttp		= "lnmt1cuomwk1.internal.pri";
  $Wikiurl		= "http://" . $Wikihttp;

# Changelog location (home directories)
  $Changehome           = "/home";

# Header graphic
  $Siteheader           = "devtitlegraphic.gif";

# Path details
  $Sitedir              = "/var/www/html";
  $Siteinstall          = "/inventory";
  $Statusdir		= "/var/www/html";
  $Statusinstall	= "/status";
  $Nagiosdir		= "/usr/local/httpd/htsecure";
  $Nagiosinstall	= "/nagios";
  $Wikidir		= "/usr/local/httpd/htsecure";
  $Wikiinstall		= "/makers";

# Who to contact
  $Siteadmins           = ",cschelin@internal.pri";
  $Sitedev              = "cschelin@internal.pri";
  $EmergencyContact     = "cschelin@internal.pri";

# MySQL specific settings
  $DBtype               = "mysql";
  $DBserver             = "localhost";
  $DBname               = "inventory";
  $DBuser               = "invuser";
  $DBpassword           = "this4now!!";
  $DBprefix             = "";
}

#############################################################

#############################################################
# QA servers
#############################################################

#############################################################
# Production servers
#############################################################

#############################################################

if ($hostname == 'inventory.internal.pri') {
  $Siteenv		= "PROD";
  $Sitedebug		= "YES";
  $Sitedebug		= "NO";

# Set site specific variables
  $Sitehttp		= "inventory.internal.pri";
  $Siteurl		= "https://" . $Sitehttp;
  $Statushttp		= "status.internal.pri";
  $Statusurl		= "https://" . $Statushttp;
  $Nagioshttp		= "status.internal.pri";
  $Nagiosurl		= "http://" . $Nagioshttp;
  $Wikihttp		= "lnmt1cuomwiki1.internal.pri";
  $Wikiurl		= "https://" . $Wikihttp;

# Changelog location (home directories)
  $Changehome		= "/home";

# Header graphic
  $Siteheader		= "intrado-logo.png";

# Path details
  $Sitedir		= "/var/www/html";
  $Siteinstall		= "/inventory";
  $Statusdir		= "/var/www/html";
  $Statusinstall	= "/status";
  $Nagiosdir		= "/usr/local/httpd/htsecure";
  $Nagiosinstall	= "/nagios";
  $Wikidir		= "/usr/local/httpd/htsecure";
  $Wikiinstall		= "/makers";

# Who to contact
  $Siteadmins		= ",cschelin@internal.pri";
  $Sitedev		= "cschelin@internal.pri";
  $EmergencyContact	= "cschelin@internal.pri";

# MySQL specific settings
  $DBtype		= "mysql";
  $DBserver		= "localhost";
  $DBname		= "inventory";
  $DBuser		= "invuser";
  $DBpassword		= "this4now!!";
  $DBprefix		= "";
}

#############################################################

if ($hostname == 'lnmt1cuomtool11.internal.pri') {
  $Siteenv		= "PROD";
  $Sitedebug		= "YES";
  $Sitedebug		= "NO";

# Set site specific variables
  $Sitehttp		= "lnmt1cuomtool11.internal.pri";
  $Siteurl		= "https://" . $Sitehttp;
  $Statushttp		= "status.internal.pri";
  $Statusurl		= "https://" . $Statushttp;
  $Nagioshttp		= "status.internal.pri";
  $Nagiosurl		= "http://" . $Nagioshttp;
  $Wikihttp		= "lnmt1cuomwiki1.internal.pri";
  $Wikiurl		= "https://" . $Wikihttp;

# Changelog location (home directories)
  $Changehome		= "/home";

# Header graphic
  $Siteheader		= "intrado-logo.png";

# Path details
  $Sitedir		= "/var/www/html";
  $Siteinstall		= "/inventory";
  $Statusdir		= "/var/www/html";
  $Statusinstall	= "/status";
  $Nagiosdir		= "/usr/local/httpd/htsecure";
  $Nagiosinstall	= "/nagios";
  $Wikidir		= "/usr/local/httpd/htsecure";
  $Wikiinstall		= "/makers";

# Who to contact
  $Siteadmins		= ",cschelin@internal.pri";
  $Sitedev		= "cschelin@internal.pri";
  $EmergencyContact	= "cschelin@internal.pri";

# MySQL specific settings
  $DBtype		= "mysql";
  $DBserver		= "inventory.internal.pri";
  $DBname		= "inventory";
  $DBuser		= "invuser";
  $DBpassword		= "this4now!!";
  $DBprefix		= "";
}

#############################################################

# enable debugging

if ( $Sitedebug == 'YES' || $Sitedebug == 'ALL' ) {
# set ini variables to manage error handling
  ini_set('error_reporting', E_ALL | E_STRICT);
  if ($Sitedebug == 'ALL') {
    ini_set('display_errors', 'on');
  } else {
    ini_set('display_errors', 'off');
  }
  ini_set('log_errors', 'On');
  ini_set('error_log', '/var/tmp/inventory.log');
}


# site details
$Sitename		= "Inventory Database";
$Sitefooter		= "";


# Root directory for the Inventory Program
$Sitepath		= $Sitedir . $Siteinstall;
$Siteroot		= $Siteurl . $Siteinstall;

# Status Management app variables
$Statuspath		= $Statusdir . $Statusinstall;
$Statusroot		= $Statusurl . $Statusinstall;

# Nagios app variables
$Nagiospath		= $Nagiosdir . $Nagiosinstall;
$Nagiosroot		= $Nagiosurl . $Nagiosinstall;

# Wiki app variables
$Wikipath		= $Wikidir . $Wikiinstall;
$Wikiroot		= $Wikiurl . $Wikiinstall;


#######
## Service Class Definitions Location
#######

$Serviceclass		= "http://intradonet/sites/database/Shared%20Documents/Service_Class/Service_Class_Definition.doc";

#######
## Email address for specific functions
#######

# for the Magic ticketing system
$Magicdev 		= "svc_MagicAdminDev@intrado.com";
$Magicprod 		= "svc_magicprodemail@intrado.com";

$Magicdevemail		= "svc_MagicAdminDev@intrado.com"; # Testing to Dev
$Magicprodemail		= "svc_magicprodemail@intrado.com"; # Production

# for the Remedy ticketing system
$Remedydev8		= "remedy.helpdesk.dev@intrado.com";
$Remedydevsvr8		= "LMV08-REMAPPQA.corp.intrado.pri";
$Remedydev9		= "remedy.helpdesk.dev.safetyservices@regmail.west.com";
$Remedydevsvr9		= "LNMT0CWASRMAP00";

$Remedyprod8		= "remedy.helpdesk@intrado.com";
$Remedyprodsvr8		= "LMV08-REMAR01.corp.intrado.pri";
$Remedyprod9		= "Remedy91HelpdeskProd@intrado.com";
$Remedyprodsvr9		= "LNMT1CWASRMAP01.corp.intrado.pri";

$Remedyqa9		= "Remedy91HelpdeskQA@intrado.com";
$Remedyqasvr9		= "lnmt0cwasrmap10.corp.intrado.pri";

#######
##  Application and Utility specific locations
##  Sitepath is the prefix for OS level files such as include() or fopen()
##  Siteroot is the prefix for URL based files
#######


## Account Management path
$Userspath		= $Sitepath . "/accounts";
$Usersroot		= $Siteroot . "/accounts";

## Admin scripts (db modifiers)
$Adminpath		= $Sitepath . "/admin";
$Adminroot		= $Siteroot . "/admin";

## Articles
$Articlepath		= $Sitepath . "/articles";
$Articleroot		= $Siteroot . "/articles";

## Bug Tracking Manager scripts
$Bugpath		= $Sitepath . "/bugs";
$Bugroot		= $Siteroot . "/bugs";

## Certificate Manager scripts
$Certspath		= $Sitepath . "/certs";
$Certsroot		= $Siteroot . "/certs";

## Data Center path
$DCpath			= $Sitepath . "/datacenter";
$DCroot			= $Siteroot . "/datacenter";

## Edit path
$Editpath		= $Sitepath . "/edit";
$Editroot		= $Siteroot . "/edit";

## FAQ Manager scripts
$FAQpath		= $Sitepath . "/faq";
$FAQroot		= $Siteroot . "/faq";

## Feature Tracking Manager scripts
$Featurepath		= $Sitepath . "/features";
$Featureroot		= $Siteroot . "/features";

## Hardware scripts
$Hardwarepath		= $Sitepath . "/hardware";
$Hardwareroot		= $Siteroot . "/hardware";

## Misc Images
$Imgspath		= $Sitepath . "/imgs";
$Imgsroot		= $Siteroot . "/imgs";

## Image Manager scripts
$Imagepath		= $Sitepath . "/image";
$Imageroot		= $Siteroot . "/image";

## Tabbed Inventory scripts
$Invpath		= $Sitepath . "/inventory";
$Invroot		= $Siteroot . "/inventory";

## IPAM
$IPAMpath		= $Sitepath . "/ipam";
$IPAMroot		= $Siteroot . "/ipam";

## Issue Tracker scripts
$Issuepath		= $Sitepath . "/issue";
$Issueroot		= $Siteroot . "/issue";

## License Manager scripts
$Licensepath		= $Sitepath . "/license";
$Licenseroot		= $Siteroot . "/license";

## Login
$Loginpath		= $Sitepath . "/login";
$Loginroot		= $Siteroot . "/login";

## Manage server errors
$Managepath		= $Sitepath . "/manage";
$Manageroot		= $Siteroot . "/manage";

## Server Monitoring errors
$Monitorpath		= $Sitepath . "/monitoring";
$Monitorroot		= $Siteroot . "/monitoring";

## Pictures
$Picturepath		= $Sitepath . "/pictures";
$Pictureroot		= $Siteroot . "/pictures";

## Report path
$Reportpath		= $Sitepath . "/reports";
$Reportroot		= $Siteroot . "/reports";

## Show path
$Showpath		= $Sitepath . "/show";
$Showroot		= $Siteroot . "/show";


# disable access to the site and print a maintenance message
$Sitemaintenance	= "1";
$Sitecopyright		= "";

# Default variable to determine whether a popup alert is presented or a full login page
$called			= 'no';

# Group settings. Hate to hard code numbers
$GRP_Unix		= 1;
$GRP_WebApps		= 25;
$GRP_ICLAdmins		= 26;
$GRP_SCM		= 27;
$GRP_SysEng		= 29;
$GRP_InfoSec		= 31;
$GRP_Shipping		= 40;
$GRP_DataCenter		= 44;
$GRP_ALIMAdmin		= 55;
$GRP_IENV		= 87;

# Access levels
$AL_Admin		= 1;
$AL_Edit		= 2;
$AL_ReadOnly		= 3;
$AL_Guest		= 4;

# Set a default theme for users not logged in.
if (!isset($_SESSION['theme'])) {
  $_SESSION['theme']	= 'sunny';
}

?>

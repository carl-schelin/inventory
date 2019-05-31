<?php

# add a space at the end as the company will be inserted into strings.
$Sitecompany		= 'Company ';
# by default, enable debugging in case we missed a server entry. ALL means full on screen debugging
$Sitedebug		= 'ALL';

# Set the environment here so other places in the code can be tested without changing code.
$hostname = php_uname('n');


#############################################################
# Server Descriptions
#############################################################

if ($hostname == "[hostname]") {
  $Siteenv		= "DEV";
  $Sitedebug		= "NO";
  $Sitedebug		= "YES";

# Set site specific variables
  $Sitehttp		= "[hostname]";
  $Siteurl		= "http://" . $Sitehttp;
  $Statushttp		= "status";
  $Statusurl		= "http://" . $Statushttp;
  $Nagioshttp		= "status";
  $Nagiosurl		= "http://" . $Nagioshttp;
  $Wikihttp		= "incowk01";
  $Wikiurl		= "http://" . $Wikihttp;

# Changelog location (home directories)
  $Changehome		= "/home";

# Header graphic
  $Siteheader		= "devtitlegraphic.gif";

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
  $Siteadmins		= ",[your email]";
  $Sitedev		= "[your email]";
  $EmergencyContact	= "[your email]";

# MySQL specific settings
  $DBtype		= "mysql";
  $DBserver		= "localhost";
  $DBname		= "inventory";
  $DBuser		= "[inventory username]";
  $DBpassword		= "[password]";
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
##  Application and Utility specific locations
##  Sitepath is the prefix for OS level files such as include() or fopen()
##  Siteroot is the prefix for URL based files
#######


## Admin scripts (db modifiers)
$Adminpath		= $Sitepath . "/admin";
$Adminroot		= $Siteroot . "/admin";

## Articles
$Articlepath		= $Sitepath . "/articles";
$Articleroot		= $Siteroot . "/articles";

## Bulk Uploads
$Assetpath		= $Sitepath . "/assets";
$Assetroot		= $Siteroot . "/assets";

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

## Handoff scripts
$Handoffpath		= $Sitepath . "/handoff";
$Handoffroot		= $Siteroot . "/handoff";

## Hardware scripts
$Hardwarepath		= $Sitepath . "/hardware";
$Hardwareroot		= $Siteroot . "/hardware";

## Misc Images
$Imgspath		= $Sitepath . "/imgs";
$Imgsroot		= $Siteroot . "/imgs";

## Image Manager scripts
$Imagepath		= $Sitepath . "/image";
$Imageroot		= $Siteroot . "/image";

## Issue Tracker scripts
$Issuepath		= $Sitepath . "/issue";
$Issueroot		= $Siteroot . "/issue";

## License Manager scripts
$Licensepath		= $Sitepath . "/license";
$Licenseroot		= $Siteroot . "/license";

## Server Listing Manager scripts
$Listingpath		= $Sitepath . "/listings";
$Listingroot		= $Siteroot . "/listings";

## Login
$Loginpath		= $Sitepath . "/login";
$Loginroot		= $Siteroot . "/login";

## Monitoring
$Monitorpath		= $Sitepath . "/monitor";
$Monitorroot		= $Siteroot . "/monitor";

## Morning Report
$Morningpath		= $Sitepath . "/morning";
$Morningroot		= $Siteroot . "/morning";

## Pictures
$Picturepath		= $Sitepath . "/pictures";
$Pictureroot		= $Siteroot . "/pictures";

## PSAPs
$PSAPpath		= $Sitepath . "/psaps";
$PSAProot		= $Siteroot . "/psaps";

## Request path
$Requestpath		= $Sitepath . "/requests";
$Requestroot		= $Siteroot . "/requests";

## Report path
$Reportpath		= $Sitepath . "/reports";
$Reportroot		= $Siteroot . "/reports";

## RSDP path
$RSDPpath		= $Sitepath . "/rsdp";
$RSDProot		= $Siteroot . "/rsdp";

## Sanity Report path
$Sanitypath		= $Sitepath . "/sanity";
$Sanityroot		= $Siteroot . "/sanity";

## Security path
$Securitypath		= $Sitepath . "/security";
$Securityroot		= $Siteroot . "/security";

## Show path
$Showpath		= $Sitepath . "/show";
$Showroot		= $Siteroot . "/show";

## Upload directory
$Uploadpath		= $Sitepath . "/uploads";
$Uploadroot		= $Siteroot . "/uploads";

## Account Management path
$Userspath		= $Sitepath . "/accounts";
$Usersroot		= $Siteroot . "/accounts";


# disable access to the site and print a maintenance message
$Sitemaintenance	= "1";
$Sitecopyright		= "";

# Default variable to determine whether a popup alert is presented or a full login page
$called			= 'no';

# Group settings. Hate to hard code numbers
$GRP_Unix		= 1;
$GRP_VoiceNetwork	= 2;
$GRP_Virtualization	= 4;
$GRP_Windows		= 5;
$GRP_TSS		= 7;
$GRP_DBAdmins		= 8;
$GRP_SAN		= 9;
$GRP_Backups		= 9;
$GRP_Monitoring		= 10;
$GRP_Networking		= 12;
$GRP_TechOps		= 14;
$GRP_Tandem		= 15;
$GRP_Mobility		= 19;
$GRP_WebApps		= 25;
$GRP_ICLAdmins		= 26;
$GRP_SCM		= 27;
$GRP_SysEng		= 29;
$GRP_InfoSec		= 31;
$GRP_Shipping		= 40;
$GRP_DataCenter		= 44;
$GRP_ALIMAdmin		= 55;
$GRP_IENV		= 87;
$GRP_OpsEng		= 96;
$GRP_i3			= 99;

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

<?php

# add a space at the end as the company will be inserted into strings.
$Sitecompany		= 'Hobgoblin Consulting Services, LLC ';
# by default, enable debugging in case we missed a server entry. ALL means full on screen debugging
$Sitedebug		= 'ALL';

# Set the environment here so other places in the code can be tested without changing code.
$hostname = php_uname('n');


#############################################################
# Development servers
#############################################################

if ($hostname == "[hostname]") {
  $Siteenv		= "PROD";
  $Siteenv		= "DEV";
  $Sitedebug		= "NO"; # no error logging
  $Sitedebug		= "ALL"; # log errors to file _and_ to the screen
  $Sitedebug		= "YES"; # just log errors to the file

# Set site specific variables
  $Sitehttp		= "[hostname]";
  $Siteurl		= "http://" . $Sitehttp;
  $Statushttp		= "[status]";
  $Statusurl		= "http://" . $Statushttp;
  $Nagioshttp		= "[nagios]]";
  $Nagiosurl		= "http://" . $Nagioshttp;
  $Wikihttp		= "[wiki]]";
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
  $Siteadmins		= ","; # lead with a comma as this is added to other possible email addresses
  $Sitedev		= "";
  $EmergencyContact	= "";

# MySQL specific settings
  $DBtype		= "mysql";
  $DBserver		= "localhost";
  $DBname		= "inventory";
  $DBuser		= "invuser";
  $DBpassword		= "";   # enter in the password for the user
  $DBprefix		= "";
}

#############################################################
# QA servers
#############################################################

#############################################################
# Production servers
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

## message exclude path
$Excludepath		= $Sitepath . "/exclude";
$Excluderoot		= $Siteroot . "/exclude";

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
$IPAMpath               = $Sitepath . "/ipam";
$IPAMroot               = $Siteroot . "/ipam";

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
$Monitorpath            = $Sitepath . "/monitoring";
$Monitorroot            = $Siteroot . "/monitoring";

## Pictures
$Picturepath		= $Sitepath . "/pictures";
$Pictureroot		= $Siteroot . "/pictures";

## Report path
$Reportpath		= $Sitepath . "/reports";
$Reportroot		= $Siteroot . "/reports";

## Show path
$Showpath		= $Sitepath . "/show";
$Showroot		= $Siteroot . "/show";

## Account Management path
$Userspath		= $Sitepath . "/accounts";
$Usersroot		= $Siteroot . "/accounts";


# disable access to the site and print a maintenance message
$Sitemaintenance	= "1";
$Sitecopyright		= "";

# Default variable to determine whether a popup alert is presented or a full login page
$called			= 'no';

# Group settings. Hate to hard code numbers. Need to add this to the database vs here.
$GRP_Unix		= 1;
$GRP_DBAdmins		= 8;
$GRP_Monitoring		= 10;
$GRP_Networking		= 12;

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

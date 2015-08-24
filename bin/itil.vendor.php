#!/usr/local/bin/php
<?php
# Script: itil.vendor.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve the company information from the company table 
# for the conversion to Remedy.
# Requires:
# Company Name
# Company Type == 'Customer'
# Description
# Hot Line
# EMail
# Web Page
# Deletion Flag

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company,Company Type,Description,Phone,Email,Webpage,Deletion Flag\n";

  $q_string  = "select com_name,typ_name,com_description,com_phone,com_email,com_webpage,com_disabled ";
  $q_string .= "from company ";
  $q_string .= "left join loc_types on loc_types.typ_id = company.com_type ";
  $q_string .= "order by com_name ";
  $q_company = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_company = mysql_fetch_array($q_company)) {

    if ($a_company['com_disabled']) {
      $disabled = 'Yes';
    } else {
      $disabled = 'No';
    }

    print "\"" . $a_company['com_name'] . "\",\"" . $a_company['typ_name'] . "\",\"" . $a_company['com_description'] . "\",\"" . $a_company['com_phone'] . "\",\"" . $a_company['com_email'] . "\",\"" . $a_company['com_webpage'] . "\",\"" . $disabled . "\"\n";
  }

?>

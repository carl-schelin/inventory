#!/usr/local/bin/php
<?php
# Script: itil.vendor.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
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
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company,Company Type,Description,Hot Line,Email,Webpage,Deletion Flag\n";

  $q_string  = "select com_id,com_name,typ_name,com_description,com_phone,com_email,com_webpage,com_disabled ";
  $q_string .= "from company ";
  $q_string .= "left join loc_types on loc_types.typ_id = company.com_type ";
  $q_string .= "order by com_name ";
  $q_company = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_company = mysqli_fetch_array($q_company)) {

    if ($a_company['com_disabled']) {
      $disabled = 'Yes';
    } else {
      $disabled = 'No';
    }

    print "\"" . $a_company['com_name'] . "\",\"" . $a_company['typ_name'] . "\",\"" . $a_company['com_description'] . "\",\"" . $a_company['com_phone'] . "\",\"" . $a_company['com_email'] . "\",\"" . $a_company['com_webpage'] . "\",\"" . $disabled . "\"\n";
  }

  $q_string  = "select loc_id,loc_type,loc_name ";
  $q_string .= "from locations ";
  $q_string .= "left join loc_types on loc_types.typ_id = locations.loc_type ";
  $q_string .= "order by loc_name ";
  $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_locations = mysqli_fetch_array($q_locations)) {

    $type = "Other";
    $subtype = '';
    if ($a_locations['loc_type'] == 1) {
      $type = "Service Provider";
      $subtype = "Data Center";
    }
    if ($a_locations['loc_type'] == 2) {
      $type = "Customer";
      $subtype = "PSAP";
    }
    if ($a_locations['loc_type'] == 3) {
      $type = "Vendor";
      $subtype = "NOC";
    }

    print "\"" . $a_locations['loc_name'] . "\",\"" . $type . "\",\"" . $subtype . "\",\"\",\"\",\"\",\"No\"\n";
  }

  mysqli_free_request($db);

?>

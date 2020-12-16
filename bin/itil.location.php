#!/usr/local/bin/php
<?php
# Script: itil.location.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Retrieve the company information from the Location table 
# for the conversion to Remedy.
# Requires:
# Company Name
# Type
# Region
# Site Group
# Site Name
# Street
# Country
# State/Province
# City
# Zip/Postal Code

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company,Region,Site Group,Site Name,Description,Street,Country,State/Province,City,Zip/Postal Code,Deletion Flag\n";

  $q_string  = "select loc_name,loc_type,loc_addr1,loc_addr2,cn_country,st_state,ct_city,loc_zipcode ";
  $q_string .= "from locations ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join states on states.st_id = cities.ct_state ";
  $q_string .= "left join country on country.cn_id = states.st_country ";
  $q_string .= "order by loc_name ";
  $q_locations = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_locations = mysqli_fetch_array($q_locations)) {

    $type = "Other";
    if ($a_locations['loc_type'] == 1) {
      $type = "Service Provider";
    }
    if ($a_locations['loc_type'] == 2) {
      $type = "Customer";
    }
    if ($a_locations['loc_type'] == 3) {
      $type = "Vendor";
    }

    if (strlen($a_locations['loc_addr2']) > 0) {
      $a_locations['loc_addr1'] .= ", " . $a_locations['loc_addr2'];
    }

    print "\"" . $a_locations['loc_name'] . "\",\"\",\"\",\"" . $a_locations['loc_name'] . "\",\"\",\"" . $a_locations['loc_addr1'] . "\",\"" . $a_locations['cn_country'] . "\",\"" . $a_locations['st_state'] . "\",\"" . $a_locations['ct_city'] . "\",\"" . $a_locations['loc_zipcode'] . "\",\"No\"\n";
  }

  mysqli_free_result($db);

?>

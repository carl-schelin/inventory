#!/usr/local/bin/php
<?php
# Script: itil.company.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve the company information from the Location table 
# for the conversion to Remedy.
# Requires:
# Company Name
# Company Type:
#   Vendor
#   Manufacturer
#   Supplier
#   Leasing
#   Customer
#   OutSourcer
#   Maintenance
#   Other
#   Generic Contact
#   Operating Company
#   Service Provider
# Note: There is a sub-category that we can edit
#   Service Provider - Data Center
#   Customer - PSAP
#   Vendor - NOC

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Company ID,Company,Type,Subtype\n";

  $q_string  = "select loc_id,loc_type,loc_name ";
  $q_string .= "from locations ";
  $q_string .= "left join loc_types on loc_types.typ_id = locations.loc_type ";
  $q_string .= "order by loc_name ";
  $q_locations = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_locations = mysql_fetch_array($q_locations)) {

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

    print $a_locations['loc_id'] . ",\"" . $a_locations['loc_name'] . "\"," . $type . "," . $subtype . "\n";
  }

?>

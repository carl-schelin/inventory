#!/usr/local/bin/php
<?php
# Script: ekenner.spreadsheet.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: From email:
# I know I have ask about this before however I need to be able to pull a list of all the servers 
# and equipment from inventory based on age. I need this for all Dev, TST, SQA, CIl and Prod 
# systems and hardware. I will need to be able to pull this list weekly and sort it based on age.
# It is also critical that we have all the fields populated for make model serial number asset tag 
# and application owners. I need age for every piece of hardware. 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "\"Server Name\",\"Function\",\"Custodian\",\"Application Owner\",\"Vendor\",\"Device\",\"Serial Number\",\"Service Tag\",\"Asset Tag\",\"Build Date\"\n";

  $q_string  = "select inv_name,inv_function,grp_name,inv_appadmin,mod_vendor,mod_name,hw_serial,hw_service,hw_asset,hw_built ";
  $q_string .= "from inventory ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where inv_status = 0 and hw_primary = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . $a_inventory['grp_name'] . "\",";
    print "\"" . $a_groups['grp_name'] . "\",";
    print "\"" . $a_inventory['mod_vendor'] . "\",";
    print "\"" . $a_inventory['mod_name'] . "\",";
    print "\"" . $a_inventory['hw_serial'] . "\",";
    print "\"" . $a_inventory['hw_service'] . "\",";
    print "\"" . $a_inventory['hw_asset'] . "\",";
    print "\"" . $a_inventory['hw_built'] . "\"\n";

  }

?>

#!/usr/local/bin/php
<?php
# Script: gadler.spreadsheet.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# requesting:

# External ID (Asset)
# Resource Class (Asset) - 
# Model
# Serial # (Asset)
# Primary Application (Asset)
# Status (Asset) - Active, Disposed of
# Title (Cost Center)
# Name (Location) - CAL02, WDC09, DEN13, DEN15, TOR11 
# IP Address (Asset)
# Owner (Asset)
# Support Group (Asset)
# Oper. System (*Computer)
# Name (Asset Supplier)
# PO Number (Asset)

  print "\"External ID (Asset)\",";
  print "\"Resource Class (Asset)\",";
  print "\"Model\",";
  print "\"Serial # (Asset)\",";
  print "\"Primary Application (Asset)\",";
  print "\"Status (Asset)\",";
  print "\"Title (Cost Center)\",";
  print "\"Name (Location)\",";
  print "\"IP Address (Asset)\",";
  print "\"Owner (Asset)\",";
  print "\"Support Group (Asset)\",";
  print "\"Oper. System (*Computer)\",";
  print "\"Name (Asset Supplier)\",";
  print "\"PO Number (Asset)\",";
  print "\n";

  $q_string  = "select inv_id,inv_name,inv_function,grp_name,inv_appadmin,int_addr,sw_software,hw_serial,mod_name,prod_name,loc_west,inv_status ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "where inv_virtual = 0 and int_primary = 1 and hw_primary = 1 and sw_type = 'OS' and (loc_west = \"CAL02\" or loc_west = \"WDC09\" or loc_west = \"DEN13\" or loc_west = \"DEN15\" or loc_west = \"TOR11\") ";
  $q_string .= "group by loc_west,inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    if ($a_inventory['inv_status'] == 0) {
      $status = "Active";
    } else {
      $status = "Disposed of";
    }

    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . return_System($a_inventory['inv_id']) . "\",";
    print "\"" . $a_inventory['mod_name'] . "\",";
    print "\"" . $a_inventory['hw_serial'] . "\",";
    print "\"" . $a_inventory['prod_name'] . "\",";
    print "\"" . $status . "\",";
    print "\"\",";
    print "\"" . $a_inventory['loc_west'] . "\",";
    print "\"" . $a_inventory['int_addr'] . "\",";
    print "\"" . $a_groups['grp_name'] . "\",";
    print "\"" . $a_inventory['grp_name'] . "\",";
    print "\"" . $a_inventory['sw_software'] . "\",";
    print "\"\",";
    print "\"\"";
    print "\n";

  }

?>

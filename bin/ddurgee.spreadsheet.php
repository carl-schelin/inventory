#!/usr/local/bin/php
<?php
# Script: ddurgee.spreadsheet.php
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

  print "\"Server ID\",\"Server Name\",\"Operating System\"\n";

  $q_string  = "select inv_id,inv_name,inv_function,sw_software ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "where inv_status = 0 and sw_type = 'OS' and inv_manager = 5 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    print "\"" . $a_inventory['inv_id'] . "\",";
    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . $a_inventory['sw_software'] . "\"\n";

  }

?>

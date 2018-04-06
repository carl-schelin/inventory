#!/usr/local/bin/php
<?php
# Script: openview.path.php
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

  $date = date('Y-m-d', strtodate('-5 days'));

  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "where inv_status = 0 and inv_ssh = 1 and int_openview = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_inventory) > 0) {
    while ($a_inventory = mysql_fetch_array($q_inventory)) {

      $q_string  = "select alarm_id ";
      $q_string .= "from alarms ";
      $q_string .= "where alarm_timestamp > '" . $date . "' and alarm_companyid = " . $a_inventory['inv_id'] . " ";
      $q_alarms = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      if (mysql_num_rows($q_alarms) == 0) {

        print $a_inventory['inv_name'] . "\n";
      }
    }
  }

?>

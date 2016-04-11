#!/usr/local/bin/php
<?php
# Script: servers.net.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $q_string  = "select inv_id,inv_name,inv_fqdn,zone_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $GRP_Networking . " ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ( $a_inventory = mysql_fetch_array($q_inventory) ) {

    $os = '';

# Convert all to lowercase
    $value[0]                 = strtolower($value[0]);
    $value[1]                 = strtolower($value[1]);
    $os                       = strtolower(return_System($a_inventory['inv_id']));
    $a_inventory['zone_name'] = strtolower($a_inventory['zone_name']);

    print $a_inventory['inv_name'] . ":" . $a_inventory['inv_fqdn'] . ":$os:" . $a_inventory['zone_name'] . ":" . $a_inventory['inv_id'] . "\n";

  }

# add application for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_Networking . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . "::::0\n";

  }

#  print "$value[0]:$value[1]:$os:" . $a_inventory['inv_zone'] . ":::" . $a_inventory['inv_id'] . "\n";

?>

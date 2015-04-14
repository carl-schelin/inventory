#!/usr/local/bin/php
<?php
# Script: servers.vtt.php
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

  $q_string  = "select inv_id,inv_name,zone_name,hw_service,inv_notes ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $GRP_Virtualization . " and hw_primary = 1 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $os = '';

    $value = explode("/", $a_inventory['inv_name']);
    if (!isset($value[1])) {
      $value[1] = '';
    }

# Convert all to lowercase
    $value[0]                 = strtolower($value[0]);
    $value[1]                 = strtolower($value[1]);
    $os                       = strtolower(return_System($a_inventory['inv_id']));
    $a_inventory['zone_name'] = strtolower($a_inventory['zone_name']);
    $a_inventory['inv_notes'] = strtolower($a_inventory['inv_notes']);

    print "$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . "::" . $a_inventory['inv_notes'] . ":" . $a_inventory['hw_service'] . "\n";

  }

# add the centrify application for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_Virtualization . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . ":::::::\n";

  }

?>

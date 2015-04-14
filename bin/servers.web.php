#!/usr/local/bin/php
<?php
# Script: servers.web.php
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

  $q_string  = "select inv_id,inv_name,zone_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where (inv_manager = " . $GRP_WebApps . " or sw_group = " . $GRP_WebApps . ") and inv_status = 0 ";
  $q_string .= "group by inv_name ";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

# determine operating system
    $os = "";
    $os = return_System($a_inventory['inv_id']);

    $value = explode("/", $a_inventory['inv_name']);
    if (!isset($value[1])) {
      $value[1] = '';
    }

    print "$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":" . $a_inventory['inv_id'] . "\n";

  }

# add applications for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_WebApps . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . ":::::0\n";

  }

?>

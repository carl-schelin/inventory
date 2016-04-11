#!/usr/local/bin/php
<?php
# Script: servers.lab.php
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

  $q_string = "select inv_id,inv_name,inv_fqdn,zone_name,inv_ssh ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where inv_manager = " . $GRP_ICLAdmins . " and inv_status = 0 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $os = "";
    $pre = "";
    $note = "";
    $peering = "";

    $os = return_System($a_inventory['inv_id']);

    if ($a_inventory['inv_ssh'] == 0) {
      $pre = '#';
    }

    print "$pre" . $a_inventory['inv_name'] . ":" . $a_inventory['inv_fqdn'] . ":$os:" . $a_inventory['zone_name'] . "::$note:" . $a_inventory['inv_id'] . "\n";

  }

# add application for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_ICLAdmins . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print "#" . $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0\n";

  }

?>

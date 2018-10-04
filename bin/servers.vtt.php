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

  $package     = "servers.vtt.php";
  $mygroup     = $GRP_Virtualization;

# add a header with settings and email target
  $q_string  = "select grp_email,grp_status,grp_server,grp_import ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $mygroup . " ";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  $a_groups = mysql_fetch_array($q_groups);

  $chkstatus = 'No';
  if ($a_groups['grp_status']) {
    $chkstatus = 'Yes';
  }
  $chkserver = 'No';
  if ($a_groups['grp_server']) {
    $chkserver = 'Yes';
  }
  $import = 'No';
  if ($a_groups['grp_import']) {
    $import = 'Yes';
  }

  print "#email: " . $a_groups['grp_email'] . " chkstatus: " . $chkstatus . " chkserver: " . $chkserver . " import: " . $import . "\n";

  $q_string  = "select inv_id,inv_name,inv_fqdn,zone_name,hw_serial,inv_notes ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "where inv_status = 0 and inv_manager = " . $mygroup . " and hw_primary = 1 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $os = '';
    $tags = "";

    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'];
    $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_tags = mysql_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ", ";
    }

# Convert all to lowercase
    $value[0]                 = strtolower($value[0]);
    $value[1]                 = strtolower($value[1]);
    $os                       = strtolower(return_System($a_inventory['inv_id']));
    $a_inventory['zone_name'] = strtolower($a_inventory['zone_name']);
    $a_inventory['inv_notes'] = strtolower($a_inventory['inv_notes']);

    print $a_inventory['inv_name'] . ":" . $a_inventory['inv_fqdn'] . ":$os:" . $a_inventory['zone_name'] . ":" . $tags . ":" . $a_inventory['inv_notes'] . ":" . $a_inventory['hw_serial'] . "\n";

  }

# add the centrify application for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $mygroup . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . ":::::::\n";

  }

?>

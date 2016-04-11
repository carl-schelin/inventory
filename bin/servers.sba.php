#!/usr/local/bin/php
<?php
# Script: servers.sba.php
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
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "inner join tags on tags.tag_inv_id = inventory.inv_id " ;
  $q_string .= "where (inv_manager = " . $GRP_Backups . " or sw_group = " . $GRP_Backups . ") and tag_group = " . $GRP_Backups . " and inv_status = 0 ";
  $q_string .= "group by inv_id ";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $os = "";
    $note = "";

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'] . " and tag_group = " . $GRP_Backups . " ";
    $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_tags = mysql_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

# The SBA group (Scott) requested a listing of systems in their changelog that have a group tag for the SBA group.
# this way the server changelog listing is much shorter as the storage and backup group have backup software on all the servers.
    if (strlen($tags) > 0) {
# determine operating system
      $os = return_System($a_inventory['inv_id']);

      print $a_inventory['inv_name'] . ":" . $a_inventory['inv_fqdn'] . ":$os:" . $a_inventory['zone_name'] . ":$tags:$note:" . $a_inventory['inv_id'] . "\n";

    }
  }

# add applications for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_Backups . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0\n";

  }

#  print "$pre$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$note:" . $a_inventory['inv_id'] . "\n";

?>

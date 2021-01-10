#!/usr/local/bin/php
<?php
# Script: servers.sba.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $package      = "servers.sba.php";
  $mygroup      = $GRP_Backups;

# add a header with settings and email target
  $q_string  = "select grp_email,grp_status,grp_server,grp_import ";
  $q_string .= "from a_groups ";
  $q_string .= "where grp_id = " . $mygroup . " ";
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_groups = mysqli_fetch_array($q_groups);

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

  $q_string  = "select inv_id,inv_name,inv_fqdn,zone_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "inner join tags on tags.tag_companyid = inventory.inv_id " ;
  $q_string .= "where (inv_manager = " . $mygroup . " or sw_group = " . $mygroup . ") and tag_group = " . $mygroup . " and inv_status = 0 ";
  $q_string .= "group by inv_id ";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $os = "";
    $note = "";

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_companyid = " . $a_inventory['inv_id'] . " and tag_group = " . $mygroup . " ";
    $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_tags = mysqli_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

# The SBA group (Scott) requested a listing of systems in their changelog that have a group tag for the SBA group.
# this way the server changelog listing is much shorter as the storage and backup group have backup software on all the servers.
    if (strlen($tags) > 0) {
# determine operating system
      $os = return_System($db, $a_inventory['inv_id']);

      print $a_inventory['inv_name'] . ":" . $a_inventory['inv_fqdn'] . ":$os:" . $a_inventory['zone_name'] . ":$tags:$note:" . $a_inventory['inv_id'] . "\n";

    }
  }

# add applications for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $mygroup . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_changelog = mysqli_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0\n";

  }

#  print "$pre$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$note:" . $a_inventory['inv_id'] . "\n";

  mysqli_close($db);

?>

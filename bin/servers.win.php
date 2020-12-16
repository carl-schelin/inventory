#!/usr/local/bin/php
<?php
# Script: servers.win.php
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

  $package            = "servers.win.php";
  $mygroup            = $GRP_Windows;

# add a header with settings and email target
  $q_string  = "select grp_email,grp_status,grp_server,grp_import ";
  $q_string .= "from groups ";
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

  $q_string  = "select inv_id,inv_name,inv_fqdn,zone_name,inv_ssh ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where (inv_manager = " . $mygroup . " or inv_appadmin = " . $mygroup . " or sw_group = " . $mygroup . ") and inv_status = 0 ";
  $q_string .= "group by inv_name ";
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

# determine operating system
    $os = "";
    $os = return_System($db, $a_software['inv_id']);

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
    $pre = "";
    if ($a_software['inv_ssh'] == 0) {
      $pre = '#';
    }

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_companyid = " . $a_software['inv_id'];
    $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_tags = mysqli_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

    $interfaces = '';
    $q_string  = "select int_server ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_interface = mysqli_fetch_array($q_interface)) {
      $interfaces .= "," . $a_interface['int_server'] . ",";
    }

    $output = $pre . $a_software['inv_name'] . ":" . $a_software['inv_fqdn'] . ":$os:" . $a_software['zone_name'] . ":$tags:$interfaces:" . $a_software['inv_id'] . "\n";
    print $output;

  }

# add applications for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $mygroup . " and cl_delete = 0 ";
  $q_string .= "group by cl_name ";
  $q_changelog = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_changelog = mysqli_fetch_array($q_changelog)) {

    $output = '#' . $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0\n";
    print $output;

  }

  mysqli_close($db);

?>

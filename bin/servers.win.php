#!/usr/local/bin/php
<?php
# Script: servers.win.php
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

  $package            = "servers.win.php";
  $mygroup            = $GRP_Windows;

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

  $q_string  = "select inv_id,inv_name,inv_fqdn,zone_name,inv_ssh ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where (inv_manager = " . $mygroup . " or inv_appadmin = " . $mygroup . " or sw_group = " . $mygroup . ") and inv_status = 0 ";
  $q_string .= "group by inv_name ";
  $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_software = mysql_fetch_array($q_software)) {

# determine operating system
    $os = "";
    $os = return_System($a_software['inv_id']);

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
    $pre = "";
    if ($a_software['inv_ssh'] == 0) {
      $pre = '#';
    }

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_inv_id = " . $a_software['inv_id'];
    $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_tags = mysql_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

    $interfaces = '';
    $q_string  = "select int_server ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_software['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
    $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_interface = mysql_fetch_array($q_interface)) {
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
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    $output = '#' . $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0\n";
    print $output;

  }

?>

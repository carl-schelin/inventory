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

# only a few zones so load the up into an array
  $q_string  = "select zone_id,zone_name ";
  $q_string .= "from zones ";
  $q_zones = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_zones = mysql_fetch_array($q_zones)) {
    $zonename[$a_zones['zone_id']] = $a_zones['zone_name'];
  }

  $q_string  = "select inv_id,inv_name,inv_fqdn,inv_zone,inv_ssh ";
  $q_string .= "from software ";
  $q_string .= "left join inventory on inventory.inv_id = software.sw_companyid ";
  $q_string .= "where (inv_manager = " . $GRP_WebApps . " or inv_appadmin = " . $GRP_WebApps . " or sw_group = " . $GRP_WebApps . ") and inv_status = 0 ";
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

    print "$pre" . $a_software['inv_name'] . ":" . $a_software['inv_fqdn'] . ":" . $zonename[$a_software['inv_zone']] . ":" . $tags . ":" . $interfaces . ":" . $a_inventory['inv_id'] . "\n";

  }

# add applications for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_WebApps . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print "#" . $a_changelog['cl_name'] . ":::::0\n";

  }

?>

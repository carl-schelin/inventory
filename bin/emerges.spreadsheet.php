#!/usr/local/bin/php
<?php
# Script: emerges.spreadsheet.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Provide a list of all active equipment as a csv
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "\"Asset Family\",\"Asset Class\",\"Asset Name\",\"Cost Center\",\"Model\",\"Location (TZ)\",\"IP Address\",\"Primary Application\",\"Support Group\",\"Oper Sys\",\"Security Risk Rating\",\"App Owner?\",\"Serial\"\n";

  $q_string  = "select inv_id,inv_name,mod_vendor,mod_name,ct_city,inv_zone,inv_function,grp_name,inv_appadmin,part_name,inv_virtual,hw_serial,zone_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join cities on cities.ct_id = locations.loc_city ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join parts on parts.part_id = hardware.hw_type ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "where inv_status = 0 and hw_primary = 1 ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $q_string  = "select grp_name ";
    $q_string .= "from groups ";
    $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'] . " ";
    $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_groups = mysql_fetch_array($q_groups);

    $interface = "";
    $console = "";
    $q_string = "select int_face,int_addr,int_type,itp_acronym,int_ip6 ";
    $q_string .= "from interface ";
    $q_string .= "left join inttype on inttype.itp_id = interface.int_type ";
    $q_string .= "where int_companyid = \"" . $a_inventory['inv_id'] . "\" and int_type != 7 and int_addr != '' and int_ip6 = 0 ";
    $q_string .= "order by int_face";
    $q_interface = mysql_query($q_string) or die(mysql_error());
    while ($a_interface = mysql_fetch_array($q_interface)) {

# if a console or LOM interface type
      if ($a_interface['int_type'] == 4 || $a_interface['int_type'] == 6) {
        $console .= $a_interface['int_face'] . "=" . $a_interface['int_addr'];
      } else {
        $interface .= $a_interface['itp_acronym'] . "=" . $a_interface['int_addr'] . " ";
      }
    }

    $q_string  = "select sw_software ";
    $q_string .= "from software ";
    $q_string .= "where sw_companyid = " . $a_inventory['inv_id'] . " and sw_type = 'OS' ";
    $q_software = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    $a_software = mysql_fetch_array($q_software);

    $serial = '';
    if (strlen($a_inventory['hw_serial']) > 0) {
      $serial = $a_inventory['hw_serial'];
    }

    if (strlen($a_inventory['zone_name']) > 0) {
      $zone = $a_inventory['ct_city'] . " (" . $a_inventory['zone_name'] . ")";
    } else {
      $zone = $a_inventory['ct_city'];
    }
# get linux vm vs windows vm for type otherwise hwtype for server;

    print "\"\",";
    print "\"" . $a_inventory['part_name'] . "\",";
    print "\"" . $a_inventory['inv_name'] . "\",";
    print "\"\",";
    print "\"" . $a_inventory['mod_vendor'] . " " . $a_inventory['mod_name'] . "\",";
    print "\"" . $zone . "\",";
    print "\"" . $interface . $console . "\",";
    print "\"" . $a_inventory['inv_function'] . "\",";
    print "\"" . $a_inventory['grp_name'] . "\",";
    print "\"" . $a_software['sw_software'] . "\",";
    print "\"\",";
    print "\"" . $a_groups['grp_name'] . "\",";
    print "\"" . $serial . "\",";
    print "\n";

  }

?>

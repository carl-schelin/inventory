#!/usr/local/bin/php
<?php
# Script: fingerprint.php
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

# pass server names
  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the server name.\n";
    exit(1);
  } else {
    $server = $argv[1];
  }

  $q_string  = "select inv_id,inv_name,inv_fqdn,inv_ssh,zone_name,prod_name,prj_name,loc_west,grp_name,inv_appadmin,inv_appliance ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "where inv_name = \"" . $server . "\" and inv_status = 0 and inv_ssh = 1 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  $a_inventory = mysql_fetch_array($q_inventory);

  $os = "";
  $tags = "";

  $os = return_System($a_inventory['inv_id']);

  $tags = '';
  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_companyid = " . $a_inventory['inv_id'];
  $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_tags = mysql_fetch_array($q_tags)) {
    $tags .= "," . $a_tags['tag_name'] . ",";
  }

  $appadmin = 'Unassigned';
  $q_string  = "select grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_groups) > 0) {
    $a_groups = mysql_fetch_array($q_groups);
    $appadmin = $a_groups['grp_name'];
  }

  $interfaces = '';
  $zone = '';
  $q_string  = "select int_server,zone_zone ";
  $q_string .= "from interface ";
  $q_string .= "left join ip_zones on ip_zones.zone_id = interface.int_zone ";
  $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
  $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_interface = mysql_fetch_array($q_interface)) {
    if ($a_interface['zone_zone'] != '') {
      $zone = $a_interface['zone_zone'];
    }
    
    $interfaces .= "," . $a_interface['int_server'] . ",";
  }
  if ($zone == '') {
    $zone = 'Unknown';
  }

  $product = str_replace(" ", "_", $a_inventory['prod_name']);
  if ($product == '') {
    $product = "Unassigned";
  }

  $project = str_replace(" ", "_", $a_inventory['prj_name']);
  if ($project == '') {
    $project = "Unassigned";
  }

  $appliance = 'No';
  if ($a_inventory['inv_appliance']) {
    $appliance = 'Yes';
  }

  $status = "Active";
  $q_string  = "select hw_active ";
  $q_string .= "from hardware ";
  $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and hw_active = '0000-00-00' ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_hardware) > 0) {
    $status = "Build";
  }

  $hardware = "Unset";
  $software = "Unset";
  $q_string  = "select hw_eol,sw_eol,hw_supportid ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id  ";
  $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and sw_type = \"OS\" ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_hardware) > 0) {
    $a_hardware = mysql_fetch_array($q_hardware);

    $hardware = $a_hardware['hw_eol'];
    $software = $a_hardware['sw_eol'];

  }

# since mod_virtual checks for not virtual, then all VMs should be N/A.
  $supported = "N/A";
  $q_string  = "select hw_supportid ";
  $q_string .= "from hardware ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and mod_virtual = 0 ";
  $q_hardware = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_hardware) > 0) {
    $a_hardware = mysql_fetch_array($q_hardware);

    if ($a_hardware['hw_supportid'] > 0) {
      $supported = "Yes";
    } else {
      $supported = "No";
    }
  }

  print "Hostname: " . $a_inventory['inv_name'] . "\n";
  print "Domain: " . $a_inventory['inv_fqdn'] . "\n";
  print "OS: " . $os . "\n";
  print "Location: " . $a_inventory['loc_west'] . "\n";
  print "Timezone: " . $a_inventory['zone_name'] . "\n";
  print "Zone: " . $zone . "\n";
  print "Tags: " . $tags . "\n";
  print "System Custodian: " . $a_inventory['grp_name'] . "\n";
  print "Primary Application Custodian: " . $appadmin . "\n";
  print "Appliance: " . $appliance . "\n";
  print "Interfaces: " . $interfaces . "\n";
  print "InventoryID: " . $a_inventory['inv_id'] . "\n";
  print "Product: " . $product . "\n";
  print "Project: " . $project . "\n";
  print "Hardware EOL: " . $hardware . "\n";
  print "Software EOL: " . $software . "\n";
  print "Support Contract: " . $supported . "\n";
  print "Status: " . $status . "\n";

?>

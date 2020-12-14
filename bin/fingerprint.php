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
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
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

  $q_string  = "select inv_id,inv_name,inv_fqdn,inv_ssh,svc_acronym,inv_callpath,zone_name,prod_name,prj_name,loc_west,grp_name,inv_appadmin,inv_appliance,inv_maint,inv_managebigfix ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join service on service.svc_id = inventory.inv_class ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
  $q_string .= "left join groups on groups.grp_id = inventory.inv_manager ";
  $q_string .= "where inv_name = \"" . $server . "\" and inv_status = 0 and inv_ssh = 1 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  $a_inventory = mysqli_fetch_array($q_inventory);

  $managebigfix = 'No';
  if ($a_inventory['inv_managebigfix']) {
    $managebigfix = 'Yes';
  }
  $callpath = 'No';
  if ($a_inventory['inv_callpath']) {
    $callpath = 'Yes';
  }

  $os = "";
  $tags = "";

  $os = return_System($db, $a_inventory['inv_id']);

  $tags = '';
  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_companyid = " . $a_inventory['inv_id'];
  $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_tags = mysqli_fetch_array($q_tags)) {
    $tags .= "," . $a_tags['tag_name'] . ",";
  }

  $appadmin = 'Unassigned';
  $q_string  = "select grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_id = " . $a_inventory['inv_appadmin'];
  $q_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_groups) > 0) {
    $a_groups = mysqli_fetch_array($q_groups);
    $appadmin = $a_groups['grp_name'];
  }

  $interfaces = '';
  $zone = '';
  $q_string  = "select int_server,zone_zone ";
  $q_string .= "from interface ";
  $q_string .= "left join ip_zones on ip_zones.zone_id = interface.int_zone ";
  $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
  $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_interface = mysqli_fetch_array($q_interface)) {
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
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_hardware) > 0) {
    $status = "Build";
  }

  $hardware = "Unset";
  $software = "Unset";
  $q_string  = "select hw_eol,sw_eol,hw_supportid ";
  $q_string .= "from hardware ";
  $q_string .= "left join inventory on inventory.inv_id = hardware.hw_companyid ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id  ";
  $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and sw_type = \"OS\" ";
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_hardware) > 0) {
    $a_hardware = mysqli_fetch_array($q_hardware);

    $hardware = $a_hardware['hw_eol'];
    $software = $a_hardware['sw_eol'];

  }

# since mod_virtual checks for not virtual, then all VMs should be N/A.
  $supported = "N/A";
  $q_string  = "select hw_supportid ";
  $q_string .= "from hardware ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and mod_virtual = 0 ";
  $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_hardware) > 0) {
    $a_hardware = mysqli_fetch_array($q_hardware);

    if ($a_hardware['hw_supportid'] > 0) {
      $supported = "Yes";
    } else {
      $supported = "No";
    }
  }

# the goal here is to populate the maintenance bits when it's time for the server to be updated
# and also to let you individually identify servers that should be disabling the bigfix agent
# once patching is done.

# first off, get the bigfix bit from the inventory
# second, get the timezone the server is using
# then calculate the window, 11pm Central to 5am Central
# the set the maintenance start and end times


  $dayname = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
  $maintday = $dayname['0'];
  $maintstart = 0;
  $maintend = 0;
  $q_string  = "select win_day,win_start,win_end ";
  $q_string .= "from window ";
  $q_string .= "where win_id = " . $a_inventory['inv_maint'] . " ";
  $q_window = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_window) > 0) {
    $a_window = mysqli_fetch_array($q_window);

# getting the day for the maintenance window, not today's day
    $maintday = $dayname[$a_window['win_day']];

# while we know the central time zone for the patching, we need to match it up with 
# the system's designated timezone, which we do pull from the server and the inventory.
# we want to convert the current time to Central Time in order for the maint start and end 
# numbers to be correct.

    $maintstart = $a_window['win_start'];
    $maintstop = $a_window['win_end'];

  }


  print "Hostname: " . $a_inventory['inv_name'] . "\n";
  print "Domain: " . $a_inventory['inv_fqdn'] . "\n";
  print "OS: " . $os . "\n";
  print "Location: " . $a_inventory['loc_west'] . "\n";
  print "Timezone: " . $a_inventory['zone_name'] . "\n";
  print "Service Class: " . $a_inventory['svc_acronym'] . "\n";
  print "911 Call Path: " . $callpath . "\n";
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
  print "Manage BigFix: " . $managebigfix . "\n";
  print "Maintenance Day: " . $maintday . "\n";
  print "Maintenance Start: " . $maintstart . "\n";
  print "Maintenance Stop: " . $maintstop . "\n";

  mysqli_free_result($db);

?>

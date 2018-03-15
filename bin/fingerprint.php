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

  $q_string  = "select inv_id,inv_name,inv_fqdn,inv_ssh,zone_name,prod_name,prj_name,loc_west ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "left join locations on locations.loc_id = inventory.inv_location ";
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
  $q_string .= "where tag_inv_id = " . $a_inventory['inv_id'];
  $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_tags = mysql_fetch_array($q_tags)) {
    $tags .= "," . $a_tags['tag_name'] . ",";
  }

  $interfaces = '';
  $q_string  = "select int_server ";
  $q_string .= "from interface ";
  $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
  $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_interface = mysql_fetch_array($q_interface)) {
    $interfaces .= "," . $a_interface['int_server'] . ",";
  }

  $product = str_replace(" ", "_", $a_inventory['prod_name']);
  if ($product == '') {
    $product = "Unassigned";
  }

  $project = str_replace(" ", "_", $a_inventory['prj_name']);
  if ($project == '') {
    $project = "Unassigned";
  }

  print "Hostname: " . $a_inventory['inv_name'] . "\n";
  print "Domain: " . $a_inventory['inv_fqdn'] . "\n";
  print "OS: " . $os . "\n";
  print "Location: " . $a_inventory['loc_west'] . "\n";
  print "Timezone: " . $a_inventory['zone_name'] . "\n";
  print "Tags: " . $tags . "\n";
  print "Interfaces: " . $interfaces . "\n";
  print "InventoryID: " . $a_inventory['inv_id'] . "\n";
  print "Product: " . $product . "\n";
  print "Project: " . $project . "\n";

?>

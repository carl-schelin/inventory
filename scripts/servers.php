#!/usr/local/bin/php
<?php
# Script: servers.php
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

# want to loop through the group table to see which group is active, pull 
# pull the necessary information and then build the lists.
# in the mean time, it works fine for one group

  $package        = "servers.php";
  $mygroup        = $GRP_Unix;

  print "#Server Name(1):FQDN(2):Operating System(3):Time Zone(4):,Tag,(5):,Interface Name,(6):Inventory ID(7):Product Name(8):Project(9):Status(10)\n";

# add a header with settings and email target
  $q_string  = "select grp_email,grp_status,grp_server,grp_import ";
  $q_string .= "from inv_groups ";
  $q_string .= "where grp_id = " . $mygroup . " ";
  $q_inv_groups = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  $a_inv_groups = mysqli_fetch_array($q_inv_groups);

  $chkstatus = 'No';
  if ($a_inv_groups['grp_status']) {
    $chkstatus = 'Yes';
  }
  $chkserver = 'No';
  if ($a_inv_groups['grp_server']) {
    $chkserver = 'Yes';
  }
  $import = 'No';
  if ($a_inv_groups['grp_import']) {
    $import = 'Yes';
  }

  print "#email: " . $a_inv_groups['grp_email'] . " chkstatus: " . $chkstatus . " chkserver: " . $chkserver . " import: " . $import . "\n";

  $q_string  = "select inv_id,inv_name,inv_fqdn,inv_ssh,zone_name,prod_name,prj_name ";
  $q_string .= "from inv_inventory ";
  $q_string .= "left join inv_timezones on inv_timezones.zone_id = inv_inventory.inv_zone ";
  $q_string .= "left join inv_products  on inv_products.prod_id  = inv_inventory.inv_product ";
  $q_string .= "left join inv_projects  on inv_projects.prj_id   = inv_inventory.inv_project ";
  $q_string .= "where inv_manager = " . $mygroup . " and inv_status = 0 ";
  $q_string .= "order by inv_name";
  $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {

    $os = "";
    $pre = "";
    $tags = "";

# add a comment character to the server list for live servers but not ssh'able.
    if ($a_inv_inventory['inv_ssh'] == 0) {
      $pre = '#';
    }

    $os = return_System($db, $a_inv_inventory['inv_id']);

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from inv_tags ";
    $q_string .= "where tag_companyid = " . $a_inv_inventory['inv_id'];
    $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {
      $tags .= "," . $a_inv_tags['tag_name'] . ",";
    }

    $interfaces = '';
    $q_string  = "select int_server,int_domain,int_management ";
    $q_string .= "from inv_interface ";
    $q_string .= "where int_companyid = " . $a_inv_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
    $q_inv_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_inv_interface = mysqli_fetch_array($q_inv_interface)) {
      $interfaces .= "," . $a_inv_interface['int_server'] . ",";

# if the management checkbox is checked, then use this interface and not the main interface.
      if ($a_inv_interface['int_management']) {
        $a_inv_inventory['inv_name'] = $a_inv_interface['int_server'];
        $a_inv_inventory['inv_fqdn'] = $a_inv_interface['int_domain'];
      }

    }

    $product = str_replace(" ", "_", $a_inv_inventory['prod_name']);
    if ($product == '') {
      $product = "Unassigned";
    }

    $project = str_replace(" ", "_", $a_inv_inventory['prj_name']);
    if ($project == '') {
      $project = "Unassigned";
    }

    $status = "Active";
    $q_string  = "select hw_active ";
    $q_string .= "from inv_hardware ";
    $q_string .= "where hw_companyid = " . $a_inv_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and hw_active = '1971-01-01' ";
    $q_inv_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inv_hardware) > 0) {
      $status = "Build";
    }

    print "$pre" . $a_inv_inventory['inv_name'] . ":" . $a_inv_inventory['inv_fqdn'] . ":$os:" . $a_inv_inventory['zone_name'] . ":$tags:$interfaces:" . $a_inv_inventory['inv_id'] . ":" . $product . ":" . $project . ":" . $status . "\n";

  }

  mysqli_close($db);

?>

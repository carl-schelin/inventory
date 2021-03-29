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

  $package        = "servers.unix.php";
  $mygroup        = $GRP_Unix;

  print "#Server Name(1):FQDN(2):Operating System(3):Time Zone(4):,Tag,(5):,Interface Name,(6):Inventory ID(7):Product Name(8):Project(9):Status(10)\n";

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

  $q_string  = "select inv_id,inv_name,inv_fqdn,inv_ssh,zone_name,prod_name,prj_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where inv_manager = " . $mygroup . " and inv_status = 0 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
  while ($a_inventory = mysqli_fetch_array($q_inventory)) {

    $os = "";
    $pre = "";
    $tags = "";

# add a comment character to the server list for live servers but not ssh'able.
# scripts use the "^#" part to make sure commented servers are able to use the changelog process
    if ($a_inventory['inv_ssh'] == 0) {
      $pre = '#';
    }

    $os = return_System($db, $a_inventory['inv_id']);

    $tags = '';
    $q_string  = "select tag_name ";
    $q_string .= "from tags ";
    $q_string .= "where tag_companyid = " . $a_inventory['inv_id'];
    $q_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_tags = mysqli_fetch_array($q_tags)) {
      $tags .= "," . $a_tags['tag_name'] . ",";
    }

    $interfaces = '';
    $q_string  = "select int_server,int_domain,int_management ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_ip6 = 0 and (int_type = 1 || int_type = 2 || int_type = 6)";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    while ($a_interface = mysqli_fetch_array($q_interface)) {
      $interfaces .= "," . $a_interface['int_server'] . ",";

# if the management checkbox is checked, then use this interface and not the main interface.
      if ($a_interface['int_management']) {
        $a_inventory['inv_name'] = $a_interface['int_server'];
        $a_inventory['inv_fqdn'] = $a_interface['int_domain'];
      }

    }

    $product = str_replace(" ", "_", $a_inventory['prod_name']);
    if ($product == '') {
      $product = "Unassigned";
    }

    $project = str_replace(" ", "_", $a_inventory['prj_name']);
    if ($project == '') {
      $project = "Unassigned";
    }

    $status = "Active";
    $q_string  = "select hw_active ";
    $q_string .= "from hardware ";
    $q_string .= "where hw_companyid = " . $a_inventory['inv_id'] . " and hw_deleted = 0 and hw_primary = 1 and hw_active = '1971-01-01' ";
    $q_hardware = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_hardware) > 0) {
      $status = "Build";
    }

    print "$pre" . $a_inventory['inv_name'] . ":" . $a_inventory['inv_fqdn'] . ":$os:" . $a_inventory['zone_name'] . ":$tags:$interfaces:" . $a_inventory['inv_id'] . ":" . $product . ":" . $project . ":" . $status . "\n";

  }

# add the centrify application for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $mygroup . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_changelog = mysqli_fetch_array($q_changelog)) {

    print "#" . $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0:" . $a_changelog['cl_name'] . "\n";

  }

  mysqli_close($db);

?>

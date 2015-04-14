#!/usr/local/bin/php
<?php
# Script: servers.dba.php
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

  $q_string  = "select inv_id,inv_name,inv_ssh,zone_name,prod_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join software on software.sw_companyid = inventory.inv_id ";
  $q_string .= "left join zones on zones.zone_id = inventory.inv_zone ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "where (inv_manager = " . $GRP_DBAdmins . " or sw_group = " . $GRP_DBAdmins . ") and inv_status = 0 ";
  $q_string .= "order by inv_name";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

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

    $value = explode("/", $a_inventory['inv_name']);
    if (!isset($value[1])) {
      $value[1] = '';
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

    print "$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$interfaces:" . $a_inventory['inv_id'] . ":" . $product . "\n";

  }

# add the centrify application for changelog work
  $q_string  = "select cl_name ";
  $q_string .= "from changelog ";
  $q_string .= "where cl_group = " . $GRP_DBAdmins . " and cl_delete = 0 ";
  $q_string .= "order by cl_name";
  $q_changelog = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_changelog = mysql_fetch_array($q_changelog)) {

    print $a_changelog['cl_name'] . ":::::," . $a_changelog['cl_name'] . ",:0:" . $a_changelog['cl_name'] . "\n";

  }

#  print "$pre$value[0]:$value[1]:$os:" . $a_inventory['zone_name'] . ":$tags:$interfaces:" . $a_inventory['inv_id'] . "\n";

?>

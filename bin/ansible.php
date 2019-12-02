#!/bin/php
<?php
# Script: ansible.php
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

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

# by products
  $q_string  = "select prod_id,prod_name ";
  $q_string .= "from products ";
  $q_string .= "order by prod_name ";
  $q_product = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_product = mysql_fetch_array($q_product)) {

    $q_string  = "select int_server ";
    $q_string .= "from inventory ";
    $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
    $q_string .= "where inv_manager = " . $manager . " and inv_status = 0 and inv_ssh = 1 and inv_product = " . $a_product['prod_id'] . " and inv_ansible = 1 and int_management = 1 ";
    $q_string .= "order by inv_name ";
    $q_inventory = mysql_query($q_string) or die(mysql_error());
    if (mysql_num_rows($q_inventory) > 0) {
      print "[" . str_replace(" ", "_", str_replace("/", "_", $a_product['prod_name'])) . "]\n";

      while ($a_inventory = mysql_fetch_array($q_inventory)) {
        print $a_inventory['int_server'] . "\n";
      }
      print "\n";
    }
  }

# find tags with 0 which apply to all servers
# ignore any server with a '-[tag]'
# 


# by tags
  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_group = " . $manager . " ";
  $q_string .= "group by tag_name ";
  $q_tags = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_tags = mysql_fetch_array($q_tags)) {

    $q_string  = "select int_server ";
    $q_string .= "from inventory ";
    $q_string .= "left join tags on tags.tag_companyid = inventory.inv_id ";
    $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
    $q_string .= "where inv_status = 0 and inv_ssh = 1 and tag_name = \"" . $a_tags['tag_name'] . "\" and inv_ansible = 1 and int_management = 1 ";
    $q_string .= "order by inv_name ";
    $q_inventory = mysql_query($q_string) or die(mysql_error());
    if (mysql_num_rows($q_inventory) > 0) {
      print "[" . str_replace(" ", "_", str_replace("/", "_", $a_tags['tag_name'])) . "]\n";

      while ($a_inventory = mysql_fetch_array($q_inventory)) {
        print $a_inventory['int_server'] . "\n";
      }
      print "\n";
    }
  }

# nagios
  $q_string  = "select int_server ";
  $q_string .= "from inventory ";
  $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
  $q_string .= "where inv_status = 0 and inv_ssh = 1 and int_nagios = 1 and inv_ansible = 1 and int_management = 1 ";
  $q_string .= "group by int_server ";
  $q_inventory = mysql_query($q_string) or die(mysql_error());
  if (mysql_num_rows($q_inventory) > 0) {
    print "[nagios]\n";

    while ($a_inventory = mysql_fetch_array($q_inventory)) {
      print $a_inventory['int_server'] . "\n";
    }
    print "\n";
  }

?>

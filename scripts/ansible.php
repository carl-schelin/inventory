#!/bin/php
<?php
# Script: ansible.php
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

# crontab entry:
# create the ansible inventory
#* * * * * /usr/local/bin/php /var/www/html/inventory/scripts/ansible.php > /usr/local/admin/etc/hosts 2>&1

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

# by tags
  $q_string  = "select tag_name ";
  $q_string .= "from tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 1 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_tags) > 0) {
    while ($a_tags = mysqli_fetch_array($q_tags)) {

      $q_string  = "select int_server ";
      $q_string .= "from inventory ";
      $q_string .= "left join tags on tags.tag_companyid = inventory.inv_id ";
      $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and tag_name = \"" . $a_tags['tag_name'] . "\" and inv_ansible = 1 and int_management = 1 ";
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inventory) > 0) {
        print "[" . str_replace(" ", "_", str_replace("/", "_", $a_tags['tag_name'])) . "]\n";

        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          print $a_inventory['int_server'] . "\n";
        }
        print "\n";
      }
    }
  }


# by location tags. type == 2
# loop through tags where type == 2, get the loc_id and list any server that is at that location.

  $q_string  = "select tag_name,tag_companyid ";
  $q_string .= "from tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 2 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_tags) > 0) {
    while ($a_tags = mysqli_fetch_array($q_tags)) {

      $q_string  = "select int_server ";
      $q_string .= "from inventory ";
      $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_location = " . $a_tags['tag_companyid'] . " and inv_ansible = 1 and int_management = 1 ";
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inventory) > 0) {
        print "[" . str_replace(" ", "_", str_replace("/", "_", $a_tags['tag_name'])) . "]\n";
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          print $a_inventory['int_server'] . "\n";
        }
        print "\n";
      }

    }
  }


# by product tags, type == 3

  $q_string  = "select tag_name,tag_companyid ";
  $q_string .= "from tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 3 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_tags = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_tags) > 0) {
    while ($a_tags = mysqli_fetch_array($q_tags)) {

      $q_string  = "select int_server ";
      $q_string .= "from inventory ";
      $q_string .= "left join interface on interface.int_companyid = inventory.inv_id ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_product = " . $a_tags['tag_companyid'] . " and inv_ansible = 1 and int_management = 1 ";
      $q_string .= "order by inv_name ";
      $q_inventory = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
      if (mysqli_num_rows($q_inventory) > 0) {
        print "[" . str_replace(" ", "_", str_replace("/", "_", $a_tags['tag_name'])) . "]\n";
        while ($a_inventory = mysqli_fetch_array($q_inventory)) {
          print $a_inventory['int_server'] . "\n";
        }
        print "\n";
      }

    }
  }


# by software tags


# by hardare tags





  mysqli_close($db);

?>

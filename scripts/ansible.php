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
  $q_string .= "from inv_tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 1 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_tags) > 0) {
    while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {

      $q_string  = "select int_server ";
      $q_string .= "from inv_inventory ";
      $q_string .= "left join inv_tags      on inv_tags.tag_companyid      = inv_inventory.inv_id ";
      $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and tag_name = \"" . $a_inv_tags['tag_name'] . "\" and inv_ansible = 1 and int_management = 1 ";
      $q_string .= "order by inv_name ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) > 0) {
        print "[" . str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name'])) . "]\n";

        while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
          print $a_inv_inventory['int_server'] . "\n";
        }
        print "\n";
      }
    }
  }


# by location tags. type == 2
# loop through tags where type == 2, get the loc_id and list any server that is at that location.

  $q_string  = "select tag_name,tag_companyid ";
  $q_string .= "from inv_tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 2 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_tags) > 0) {
    while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {

      $q_string  = "select int_server ";
      $q_string .= "from inv_inventory ";
      $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_location = " . $a_inv_tags['tag_companyid'] . " and inv_ansible = 1 and int_management = 1 ";
      $q_string .= "order by inv_name ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) > 0) {
        print "[" . str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name'])) . "]\n";
        while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
          print $a_inv_inventory['int_server'] . "\n";
        }
        print "\n";
      }

    }
  }


# by product tags, type == 3

  $q_string  = "select tag_name,tag_companyid ";
  $q_string .= "from inv_tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 3 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_tags) > 0) {
    while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {

      $q_string  = "select int_server ";
      $q_string .= "from inv_inventory ";
      $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_product = " . $a_inv_tags['tag_companyid'] . " and inv_ansible = 1 and int_management = 1 ";
      $q_string .= "order by inv_name ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) > 0) {
        print "[" . str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name'])) . "]\n";
        while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
          print $a_inv_inventory['int_server'] . "\n";
        }
        print "\n";
      }

    }
  }


# by software tags
# Get a list of all the type 4 tags, type 4 being software tags
# the tag_companyid field points to the software_id in the software table.
# from there retrieve a list of all servers that have that software

  $q_string  = "select tag_name ";
  $q_string .= "from inv_tags ";
  $q_string .= "where tag_type = 4 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_tags) > 0) {
    while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {

      print "[" . str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name'])) . "]\n";

      $q_string  = "select tag_companyid ";
      $q_string .= "from inv_tags ";
      $q_string .= "where tag_type = 4 and tag_name = \"" . $a_inv_tags['tag_name'] . "\" ";
      $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_software) > 0) {
        while ($a_software = mysqli_fetch_array($q_software)) {

          $q_string  = "select int_server ";
          $q_string .= "from inv_svr_software ";
          $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_svr_software.svr_companyid ";
          $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_svr_software.svr_companyid ";
          $q_string .= "where svr_softwareid = " . $a_software['tag_companyid'] . " and inv_ssh = 1 and inv_ansible = 1 and int_management = 1 ";
          $q_inv_svr_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_svr_software) > 0) {
            while ($a_inv_svr_software = mysqli_fetch_array($q_inv_svr_software)) {

              print $a_inv_svr_software['int_server'] . "\n";

            }
          }
        }
        print "\n";
      }
    }
  }


# by hardare tags





  mysqli_close($db);

?>

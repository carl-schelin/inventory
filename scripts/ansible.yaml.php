#!/bin/php
<?php
# Script: ansible.yaml.php
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
#* * * * * /usr/local/bin/php /var/www/html/inventory/scripts/ansible.yaml.php > /usr/local/admin/etc/hosts.yaml 2>&1

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

# if we're passing in the environment we want to run against, 
# we need to lookup the name being passed in the inv_environment 
# table.
# if not found, exit with an error
# if found, get the env_name for the header and the id in order to
# only get the servers with that location that matches everything else

  if ($argc > 1) {
    $environment = $argv[1];
  } else {
    print "You need to pass the environment this script runs against.\n";
    exit(1);
  }

  $q_string  = "select env_id,env_name ";
  $q_string .= "from inv_environment ";
  $q_string .= "where env_abb = \"" . $environment . "\" ";
  $q_inv_environment = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_environment) > 0) {
    $a_inv_environment = mysqli_fetch_array($q_inv_environment);
  } else {
    print "You need to pass the environment this script runs against.\n";
    exit(1);
  }

  $manager = 1;

#      1 | Development | Dev     |
#      2 | Stage       | Stage   |
#      3 | Production  | Prod    |
#      4 | QA          | QA      |
#      5 | Home        | Home    |

# print the header information of the file
  print "# Inventory file for " . strtolower($a_inv_environment['env_name']) . " environment\n";
  print "all:\n";
  print "  children:\n";
  print "    " . strtolower($a_inv_environment['env_name']) . ":\n";
  print "      vars:\n";
  print "        environment: " . strtolower($a_inv_environment['env_name']) . "\n";
  print "      children:\n";


# by tags
  $q_string  = "select tag_name ";
  $q_string .= "from inv_tags ";
  $q_string .= "where tag_group = " . $manager . " and tag_type = 1 ";
  $q_string .= "group by tag_name ";
  $q_string .= "order by tag_name ";
  $q_inv_tags = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_tags) > 0) {
    while ($a_inv_tags = mysqli_fetch_array($q_inv_tags)) {

      $serverlisting = array();
      $q_string  = "select int_server ";
      $q_string .= "from inv_inventory ";
      $q_string .= "left join inv_tags      on inv_tags.tag_companyid      = inv_inventory.inv_id ";
      $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
      $q_string .= "left join inv_locations on inv_locations.loc_id        = inv_inventory.inv_location ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and tag_name = \"" . $a_inv_tags['tag_name'] . "\" and inv_ansible = 1 and int_management = 1 and loc_environment = " . $a_inv_environment['env_id'] . " ";
      $q_string .= "group by int_server ";
      $q_string .= "order by int_server ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) > 0) {
        $header = str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name']));

        $index = 0;
        while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
          $serverlisting[$index++] = "            " . $a_inv_inventory['int_server'] . ":\n";
        }

        if ($index > 0) {
          print "        " . $header . ":\n";
          print "          vars:\n";
          print "            deployment: " . $header . "\n";
          print "          hosts:\n";
          foreach ($serverlisting as $listing) {
            print $listing;
          }
          reset($serverlisting);
        }
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

      $serverlisting = array();
      $q_string  = "select int_server ";
      $q_string .= "from inv_inventory ";
      $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
      $q_string .= "left join inv_locations on inv_locations.loc_id        = inv_inventory.inv_location ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_location = " . $a_inv_tags['tag_companyid'] . " and inv_ansible = 1 and int_management = 1 and loc_environment = " . $a_inv_environment['env_id'] . " ";
      $q_string .= "order by inv_name ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) > 0) {
        $header = str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name']));

        $index = 0;
        while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
          $serverlisting[$index++] = "            " . $a_inv_inventory['int_server'] . ":\n";
        }

        if ($index > 0) {
          print "        " . $header . ":\n";
          print "          vars:\n";
          print "            deployment: " . $header . "\n";
          print "          hosts:\n";
          foreach ($serverlisting as $listing) {
            print $listing;
          }
          reset($serverlisting);
        }
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

      $serverlisting = array();
      $q_string  = "select int_server ";
      $q_string .= "from inv_inventory ";
      $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_inventory.inv_id ";
      $q_string .= "left join inv_locations on inv_locations.loc_id        = inv_inventory.inv_location ";
      $q_string .= "where inv_status = 0 and inv_ssh = 1 and inv_product = " . $a_inv_tags['tag_companyid'] . " and inv_ansible = 1 and int_management = 1 and loc_environment = " . $a_inv_environment['env_id'] . " ";
      $q_string .= "group by int_server ";
      $q_string .= "order by int_server ";
      $q_inv_inventory = mysqli_query($db, $q_string) or die(mysqli_error($db));
      if (mysqli_num_rows($q_inv_inventory) > 0) {
        $header = str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name']));

        $index = 0;
        while ($a_inv_inventory = mysqli_fetch_array($q_inv_inventory)) {
          $serverlisting[$index] = "            " . $a_inv_inventory['int_server'] . ":\n";
        }

        if ($index > 0) {
          print "        " . $header . ":\n";
          print "          vars:\n";
          print "            deployment: " . $header . "\n";
          print "          hosts:\n";
          foreach ($serverlisting as $listing) {
            print $listing;
          }
          reset($serverlisting);
        }
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

      $serverlisting = array();
      $q_string  = "select tag_companyid ";
      $q_string .= "from inv_tags ";
      $q_string .= "where tag_type = 4 and tag_name = \"" . $a_inv_tags['tag_name'] . "\" ";
      $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_software) > 0) {
        $header = str_replace(" ", "_", str_replace("/", "_", $a_inv_tags['tag_name']));

        $index = 0;
        while ($a_software = mysqli_fetch_array($q_software)) {
          $q_string  = "select int_server ";
          $q_string .= "from inv_svr_software ";
          $q_string .= "left join inv_interface on inv_interface.int_companyid = inv_svr_software.svr_companyid ";
          $q_string .= "left join inv_inventory on inv_inventory.inv_id        = inv_svr_software.svr_companyid ";
          $q_string .= "left join inv_locations on inv_locations.loc_id        = inv_inventory.inv_location ";
          $q_string .= "where svr_softwareid = " . $a_software['tag_companyid'] . " and inv_ssh = 1 and inv_ansible = 1 and int_management = 1 and loc_environment = " . $a_inv_environment['env_id'] . " ";
          $q_string .= "group by int_server ";
          $q_string .= "order by int_server ";
          $q_inv_svr_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          if (mysqli_num_rows($q_inv_svr_software) > 0) {

            while ($a_inv_svr_software = mysqli_fetch_array($q_inv_svr_software)) {
              $serverlisting[$index++] = "            " . $a_inv_svr_software['int_server'] . ":\n";
            }

          }
        }
        if ($index > 0) {
          print "        " . $header . ":\n";
          print "          vars:\n";
          print "            deployment: " . $header . "\n";
          print "          hosts:\n";

          $final = array_unique($serverlisting);
          sort($final);

          foreach ($final as $listing) {
            print $listing;
          }
          reset($serverlisting);
        }
      }
    }
  }


# by hardware tags

  mysqli_close($db);

?>

#!/usr/local/bin/php
<?php
# Script: initialize.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: This script creates a skeleton entry in the inventory 
# to be ready for the core import. it's assumed a wrapper script will 
# validate

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the server to be initialized.\n";
    exit(1);
  } else {
    $server = $argv[1];
  }

  if ($server[0] == '-') {
    print "This script is called by the inventory shell script. To use this stand alone, pass the name of the new server on the command line.\n";
    exit(1);
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

# so first, get the server names from the inventory table to identify the server id

  $date = date('Y-m-d');

  print "Checking inventory to see if $server exists.\n";

  $q_string  = "select inv_id,inv_manager,inv_product ";
  $q_string .= "from inventory ";
  $q_string .= "where inv_status = 0 and inv_name = '" . $server . "'";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {

    echo "ERROR: $server already exists in the inventory.\n";

    exit(1);
  } else {

    print "Checking the interface table to see if $server has been associated with an existing server.\n";

    $q_string  = "select int_id,inv_name ";
    $q_string .= "from interface ";
    $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
    $q_string .= "where inv_status = 0 and int_server = '" . $server . "'";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_interface) > 0) {
      $a_interface = mysqli_fetch_array($q_interface);

      echo "ERROR: $server is identified as being an interface for " . $a_interface['inv_name'] . ".\n";

      exit(1);
    } else {

      echo "Adding $server to the inventory...\n";

      $q_string = "insert into inventory set inv_id = null,inv_name = \"" . $server . "\",inv_manager=1,inv_status=0,inv_function=\"Server Initialized\",inv_ssh=1";

      if ($debug == 'yes') {
        print $q_string . "\n";
        $serverid = 0;
      } else {
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

        $query = "select last_insert_id($db)";
        $q_result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
        $a_result = mysqli_fetch_array($q_result);

        $serverid = $a_result['last_insert_id($db)'];
      }

      echo "Adding a $server interface...\n";

      $q_string = "insert into interface set int_id=null,int_server = \"" . $server . "\",int_companyid=" . $serverid;

      if ($debug == 'yes') {
        print $q_string . "\n";
      } else {
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      echo "Adding a virtual machine for $server...\n";

      $q_string = "insert into hardware set hw_id=null,hw_type=45,hw_companyid=" . $serverid . ",hw_vendorid=45,hw_group=1,hw_built=\"" . $date . "\",hw_primary=1";

      if ($debug == 'yes') {
        print $q_string . "\n";
      } else {
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }
    }
  }

  mysqli_free_result($db);

?>

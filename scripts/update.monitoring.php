#!/usr/local/bin/php
<?php
# Script: update.monitoring.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: From email:
# I know I have ask about this before however I need to be able to pull a list of all the servers 
# and equipment from inventory based on age. I need this for all Dev, TST, SQA, CIl and Prod 
# systems and hardware. I will need to be able to pull this list weekly and sort it based on age.
# It is also critical that we have all the fields populated for make model serial number asset tag 
# and application owners. I need age for every piece of hardware. 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Updating all interfaces setting openview, nagios, ping, and ssh off\n";

# turn off openview, nagios, ping, and ssh monitoring for all systems for now.
  $q_string  = "update interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "set ";
  $q_string .= "int_openview = 0,int_nagios = 0,int_ping = 0,int_ssh = 0 ";
  $q_string .= "where inv_manager = 1 ";

  $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  print "Identify every system and update if HP Openview is installed.\n";

# get all software owned by monitoring and with a vendor of HP
# it's an assuption but not a bad one.
  $q_string  = "select sw_companyid ";
  $q_string .= "from software ";
  $q_string .= "where sw_vendor = 'HP' and sw_group = 10 ";
  $q_software = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_software = mysqli_fetch_array($q_software)) {

    $q_string  = "update interface set ";
    $q_string .= "int_openview = 1,int_nagios = 0,int_ping = 1 ";
    $q_string .= "where int_companyid = " . $a_software['sw_companyid'] . " and int_type = 1 ";

    $insert = mysqli_query($db, $q_string);
  }

  print "Update every console to enable nagios and ping since openview isn't generally enabled to ping\n";
# okay, all the systems managed by openview are identified. now ping loms where possible
  $q_string  = "update interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "set ";
  $q_string .= "int_nagios = 1,int_ping = 1 ";
  $q_string .= "where (int_type = 4 or int_type = 6) and inv_manager = 1 ";

  $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  print "Update every management interface to enable nagios and ping but not ssh\n";
# okay, all the systems managed by openview are identified. now update nagios but all loms as well.
  $q_string  = "update interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "set ";
  $q_string .= "int_nagios = 1,int_ping = 1 ";
  $q_string .= "where int_openview = 0 and (int_type = 1 or int_type = 4 or int_type = 6) and inv_manager = 1 ";

  $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  print "Update every management interface that's accessible via ssh (inv_ssh = 1) to enable sshing to the system.\n";
# okay, enable ssh checks for all systems mgt interfaces with ssh
  $q_string  = "update interface ";
  $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
  $q_string .= "set ";
  $q_string .= "int_nagios = 1,int_ping = 1,int_ssh = 1 ";
  $q_string .= "where int_openview = 0 and int_type = 1 and inv_manager = 1 and inv_ssh = 1 ";

  $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

  mysqli_close($db);

?>

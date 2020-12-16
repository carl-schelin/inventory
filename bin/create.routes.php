#!/usr/local/bin/php
<?php
# Script: create.routes.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'no';
  $debug = 'yes';

# basically pull the information from the routing table where static == 1 and write it out as a yaml file.


  $q_string  = "select inv_id,inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join routing on routing.route_companyid = inventory.inv_id ";
  $q_string .= "where inv_manager = " . $GRP_Unix . " and inv_status = 0 and route_static = 1 ";
  $q_string .= "group by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {

    print "# automatically generated vars file\n";
    print "---\n\n";

    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

# get the hostname for the server set when the int_hostname flag == 1
      $q_string  = "select int_server ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_hostname = 1 ";
      $q_string .= "limit 1 ";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_interface = mysqli_fetch_array($q_interface);

      print $a_interface['int_server']  .":\n";

      $q_string  = "select route_address,route_mask,route_gateway,route_interface,route_source ";
      $q_string .= "from routing ";
      $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " and route_static = 1 ";
      $q_routing = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_routing = mysqli_fetch_array($q_routing)) {

        $q_string  = "select int_face ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $a_routing['route_interface'] . " ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_interface = mysqli_fetch_array($q_interface);

        print "  - { ";
        print "address: \"" . $a_routing['route_address']   . "\", ";
        print "mask: \"" . $a_routing['route_mask']   . "\", ";
        print "gateway: \"" . $a_routing['route_gateway']   . "\", ";
        print "device: \""  . $a_interface['int_face']      . "\", ";
        print "source: \""  . $a_routing['route_source']    . "\"";
        print "}\n";

      }

      print "\n\n";
    }
  }

  mysqli_close($db);

?>

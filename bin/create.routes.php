#!/usr/local/bin/php
<?php
# Script: create.routes.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
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
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_inventory) > 0) {

    print "# automatically generated vars file\n";
    print "---\n\n";

    while ($a_inventory = mysql_fetch_array($q_inventory)) {

# get the hostname for the server set when the int_hostname flag == 1
      $q_string  = "select int_server ";
      $q_string .= "from interface ";
      $q_string .= "where int_companyid = " . $a_inventory['inv_id'] . " and int_hostname = 1 ";
      $q_string .= "limit 1 ";
      $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      $a_interface = mysql_fetch_array($q_interface);

      print $a_interface['int_server']  .":\n";

      $q_string  = "select route_address,route_mask,route_gateway,route_interface,route_source ";
      $q_string .= "from routing ";
      $q_string .= "where route_companyid = " . $a_inventory['inv_id'] . " and route_static = 1 ";
      $q_routing = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_routing = mysql_fetch_array($q_routing)) {

        $q_string  = "select int_face ";
        $q_string .= "from interface ";
        $q_string .= "where int_id = " . $a_routing['route_interface'] . " ";
        $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        $a_interface = mysql_fetch_array($q_interface);

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

?>

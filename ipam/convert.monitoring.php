#!/usr/local/bin/php
<?php
# Script: convert.ipam.php
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

  $package = "convert.ipam.php";

  $debug = 'no';
  if ($argv[$argc - 1] == 'debug') {
    $debug = 'yes';
  }

# need to get the IP address from all interface tables and then determine the network for each.

  $q_string  = "select int_id,int_addr,int_mask,int_server,int_domain ";
  $q_string .= "from interface ";
  $q_string .= "where int_ip6 = 0 ";
  $q_string .= "order by int_addr ";
  $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&called=" . $called . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));
  if (mysqli_num_rows($q_interface) > 0) {
    while ($a_interface = mysqli_fetch_array($q_interface)) {

#      print "IP: " . $a_interface['int_addr'] . "/" . $a_interface['int_mask'] . "\n";

$ipaddr = ip2long($a_interface['int_addr']);
$mask = -1 << ( 32 - $a_interface['int_mask'] );


      if (is_numeric($a_interface['int_mask']) && $a_interface['int_mask'] < 32) {
#        $subnet = long2ip( ip2long( $a_interface['int_addr'] ) & ip2long( $a_interface['int_mask']) );
#        $broadcast = long2ip( ip2long( $a_interface['int_addr'] ) | ip2long( $a_interface['int_mask']) );
#        $wildcard = long2ip( ~ip2long( $a_interface['int_mask'] ) );
        $subnet = long2ip( $ipaddr & $mask );
        $broadcast = long2ip( $ipaddr | $_mask );
        $wildcard = long2ip( ~$mask );


        print "Network: $subnet/" . $a_interface['int_mask'] . ": " . $a_interface['int_server'] . "." . $a_interface['int_domain'] . "\n";
      }
    }

  } else {
    print "None found\n";
  }

?>

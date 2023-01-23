#!/usr/local/bin/php
<?php
# Script: ipaddress.import.php
# By: Carl Schelin
# read in the ip address information

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# read in a comma delimited file of information.
# ipv4 address, ipv6 address, hostname,    domain,  network id, subzone id, type id, user id, description
#   ip_ipv4,      ip_ipv6,  ip_hostname, ip_domain, ip_netowrk, ip_subzone, ip_type, ip_user, ip_description.
#     20            50         100          255         int        int        int      int        50
#    value[0],   value[1],   value[2]     value[3]    value[4]    value[5],  value[6]  value[7], value[8]
# if ipv4 address is blank, import ipv6
# if ipv6 address is blank, import ipv4
# we don't want to add an address if it already exists so search for that and skip if true

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Need to pass the import file name.\n";
    exit(1);
  } else {
    $filename = $argv[1];
  }

# if $debug is yes, only print the output. if no, then update the database
  $debug = 'yes';
  $debug = 'no';

  $file = fopen($filename, "r") or die;
  while(!feof($file)) {
    $process = trim(fgets($file));

    $value = explode(",", $process);

# first, get the ip address from the table
    $q_string  = "select ip_ipv4,ip_ipv6 ";
    $q_string .= "from inv_ipaddress ";
    if (strlen($value[0]) > 0) {
      $q_string .= "where ip_ipv4 = \"" . $value[0] . "\" ";
    } else {
      $q_string .= "where ip_ipv6 = \"" . $value[1] . "\" ";
    }
    $q_inv_ipaddress = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db) . "\n");
    if (mysqli_num_rows($q_inv_ipaddress) == 0) {

      $q_string = 
        "ip_ipv4            = \"" . $value[0] . "\"," . 
        "ip_ipv6            = \"" . $value[1] . "\"," . 
        "ip_hostname        = \"" . $value[2] . "\"," . 
        "ip_domain          = \"" . $value[3] . "\"," . 
        "ip_network         =   " . $value[4] . "," . 
        "ip_subzone         =   " . $value[5] . "," . 
        "ip_type            =   " . $value[6] . "," . 
        "ip_user            =   " . $value[7] . "," . 
        "ip_description     = \"" . $value[8] . "\"";

      $q_string = "insert into inv_ipaddress set ip_id = null," . $q_string;

      if ($debug == 'yes') {
        print $q_string . "\n";
      } else {
        $q_result = mysqli_query($db, $q_string) or die($q_string . ":  " . mysqli_error($db));
      }
    }
  }

  mysqli_close($db);

?>

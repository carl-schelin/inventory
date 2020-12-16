<?php
# Script: upload.nnmireport.php
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

  $debug = 'yes';
  $debug = 'no';

  $file = 'nnmiServerReport.current.csv';
  $date = date('Y-m-d');

# Report from the monitoring team
# servername/ip address - ignore really as the last field is the list of IPs monitored. only use if the last column is blank
# status - CRITICAL MINOR NORMAL NOSTATUS UNKNOWN
# service status - MANAGED NOTMANAGED OUTOFSERVICE
# list of space separated IPs.

# ignore the first two lines as they're description and header
$count = 0;
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    if ($count++ > 1) {
      $data[0] = clean($data[0], 50);  # server/ip
      $data[1] = clean($data[1], 24);  # status
      $data[2] = clean($data[2], 40);  # service
      $data[3] = clean($data[3], 255);  # ip listing

      if ($debug == 'yes') {
        print "0: " . $data[0] . ", 1: " . $data[1] . ", 2: " . $data[2] . ", 3: " . $data[3] . "\n";
      }

# need to split up the ip listing then search for the ip. when found, update the inventory record with that info.
      $iplist = explode(" ", $data[3]);

      foreach ($iplist as &$address) {

        $q_string  = "select int_id ";
        $q_string .= "from interface ";
        $q_string .= "where int_addr = \"" . $address . "\" ";
        $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        if (mysqli_num_rows($q_interface) > 0) {
          while ($a_interface = mysqli_fetch_array($q_interface)) {

            $q_string  = 
              "int_monstatus   = \"" . $data[1] . "\"," . 
              "int_monservice  = \"" . $data[2] . "\"," .
              "int_mondate     = \"" . $date    . "\"";

            $query = "update interface set " . $q_string . " where int_id = " . $a_interface['int_id'] . " ";
            if ($debug == 'yes') {
              print $query . "\n";
            } else {
              $result = mysqli_query($db, $query) or die($query . ": " . mysqli_error($db));
            }
            print "IP found: $data[0],$data[1],$data[2],$address\n";
          }
        } else {
          print "Not found: $data[0],$data[1],$data[2],$address\n";
        }
      }
    }
  }
}

?>

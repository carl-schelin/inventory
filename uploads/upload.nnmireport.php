<?php
# Script: upload.nnmireport.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $debug = 'yes';
  $debug = 'no';

  $file = 'nnmiServerReport.current.csv';
  $date = date('Y-m-d');

# legend
  print "Usage:\n";
  print "php upload.support.php\n";
  print "f - Found the hardware\n";
  print ". - Found the Serial Number but did not find the hardware.\n";

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
        $q_interface = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        if (mysql_num_rows($q_interface) > 0) {
          while ($a_interface = mysql_fetch_array($q_interface)) {

            $q_string  = 
              "int_monstatus   = \"" . $data[1] . "\"," . 
              "int_monservice  = \"" . $data[2] . "\"," .
              "int_mondate     = \"" . $date    . "\"";

            $query = "update interface set " . $q_string . " where int_id = " . $a_interface['int_id'] . " ";
            if ($debug == 'yes') {
              print $query . "\n";
            } else {
              $result = mysql_query($query) or die($query . ": " . mysql_error());
            }
          }
        }
      }
    }
  }
}

?>

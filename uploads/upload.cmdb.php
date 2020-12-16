<?php
# Script: upload.cmdb.php
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

# only need the first column in order to locate the right server
# then the x column to get the type of environment

  $file = "cmdb-import.current.csv";

$lineno = 0;
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    $q_string  = "select inv_id ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = \"" . $data[0] . "\" ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    if (mysqli_num_rows($q_inventory) > 0) {
      $a_inventory = mysqli_fetch_array($q_inventory);

      $inv_env = 0;
      if ($data[7] == 'App Dev') {
        $inv_env = 1;
      }
      if ($data[7] == 'Pre Production') {
        $inv_env = 2;
      }
      if ($data[7] == 'Production') {
        $inv_env = 3;
      }
      if ($data[7] == 'QA') {
        $inv_env = 4;
      }
      if ($data[7] == 'Sys Dev') {
        $inv_env = 5;
      }

      $q_string  = "update ";
      $q_string .= "inventory ";
      $q_string .= "set inv_env = " . $inv_env . " ";
      $q_string .= "where inv_id = " . $a_inventory['inv_id'] . " ";

      if ($debug == 'yes') {
#        print "Server: " . $data[0] . ", Environment: " . $data[7] . "\n";
#        print $q_string . "\n";
      } else {
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }
    } else {
      print "Error: Unable to locate " . $data[0] . " in the inventory: line: $lineno \n";
    }
    $lineno++;
  }
  fclose($handle);
} else {
  print "Unable to open $file \n";
}

?>

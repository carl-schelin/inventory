<?php
# Script: upload.family.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

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

# read in the uploaded file
# uploads are in $Uploadpath

# need to check the family to see if it exists, if so, get the id otherwise create it and get the new id.
# column is: Plugin(0), Plugin Name(1), Family(2), Severity(3), IP Address(4), NetBIOS Name(5), DNS Name(6), MAC Address(7), Repository(8)
# need: Plugin Name, Family, Severity, IP Address, DNS Name, MAC Address

$row = 1;
if (($handle = fopen("vulnerability-sorted.csv", "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

    $q_string  = "select fam_name ";
    $q_string .= "from family ";
    $q_string .= "where fam_name = \"" . $data[2] . "\" ";
    $q_family = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    if (mysqli_num_rows($q_family) == 0) {
      $q_string  = "insert ";
      $q_string .= "into family ";
      $q_string .= "set fam_id = null,";
      $q_string .= "fam_name = \"" . $data[2] . "\"";

      if ($debug == 'yes') {
        print $q_string . "\n";
      } else {
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }
    }
  }
  fclose($handle);
}

?>

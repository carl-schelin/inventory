<?php
# Script: upload.security.php
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

# read in the uploaded file
# uploads are in $Uploadpath

# need to check the family to see if it exists, if so, get the id otherwise create it and get the new id.
# column is: Plugin(0), Plugin Name(1), Family(2), Severity(3), IP Address(4), NetBIOS Name(5), DNS Name(6), MAC Address(7), Repository(8)
# need: Plugin Name, Family, Severity, IP Address, DNS Name, MAC Address

# get the existing security information; sec_id,sec_name
# 


  $row = 1;
  if (($handle = fopen("vulnerability-sorted.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

      $q_string  = "select fam_id ";
      $q_string .= "from family ";
      $q_string .= "where fam_name = '" . $data[2] . "'";
      $q_family = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_family = mysqli_fetch_array($q_family);

      $q_string  = "select sev_id ";
      $q_string .= "from severity ";
      $q_string .= "where sev_name = '" . $data[3] . "'";
      $q_severity = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      $a_severity = mysqli_fetch_array($q_severity);

      $q_string  = "select sec_id,sec_severity ";
      $q_string .= "from security ";
      $q_string .= "where sec_id = " . $data[0];
      $q_security = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_security) == 0) {

# if sec_id is not set,
#   add the entry
        $q_string  = "insert ";
        $q_string .= "into security set ";
        $q_string .= "sec_id       =  " . $data[0]                           . ",";
        $q_string .= "sec_name     = '" . mysqli_real_escape_string($db, $data[1]) . "',";
        $q_string .= "sec_family   =  " . $a_family['fam_id']                . ",";
        $q_string .= "sec_severity =  " . $a_severity['sev_id']              . " ";
        $insert = mysqli_query($db, $q_string) or die("\nQuery: " . $q_string . "\nFamily: " . $data[2] . "\nSeverity: " . $data[3] . "\n" . mysqli_error($db) . "\n");
        print "+";
      } else {
        $a_security = mysqli_fetch_array($q_security);

# if the severity changes, update the record
        if ($a_security['sec_severity'] != $a_severity['sev_id']) {
          $q_string  = "update ";
          $q_string .= "security ";
          $q_string .= "set ";
          $q_string .= "sec_name     = \"" . mysqli_real_escape_string($db, $data[1]) . "\",";
          $q_string .= "sec_severity =   " . $a_severity['sev_id'] . " ";
          $q_string .= "where sec_id = " . $a_security['sec_id'];
          $update = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          print "^";
        } else {
          print ".";
        }
      }
    }
    fclose($handle);
  }

?>

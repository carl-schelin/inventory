<?php
# Script: upload.security.php
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

  if ($argv[1] == '') {
    print "Need to pass the filename to be imported\n";
    exit(1);
  }

  if (file_exists($Uploadpath . "/" . $argv[1]) == false) {
    print "File " . $Uploadpath . "/" . $argv[1] . " doesn't exist.\n";
    exit(1);
  }

# read in the uploaded file
# uploads are in $Uploadpath

# need to check the family to see if it exists, if so, get the id otherwise create it and get the new id.
# column is: Plugin(0), Plugin Name(1), Family(2), Severity(3), IP Address(4), NetBIOS Name(5), DNS Name(6), MAC Address(7), Repository(8)
# need: Plugin Name, Family, Severity, IP Address, DNS Name, MAC Address

# get the existing security information; sec_id,sec_name
# 


  $row = 1;
  if (($handle = fopen($Uploadpath . "/" . $argv[1], "r")) !== FALSE) {
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
      $q_string .= "from inv_security ";
      $q_string .= "where sec_id = " . $data[0];
      $q_inv_security = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_inv_security) == 0) {

# if sec_id is not set,
#   add the entry
        $q_string  = "insert ";
        $q_string .= "into inv_security set ";
        $q_string .= "sec_id       =  " . $data[0]                           . ",";
        $q_string .= "sec_name     = '" . mysqli_real_escape_string($db, $data[1]) . "',";
        $q_string .= "sec_family   =  " . $a_family['fam_id']                . ",";
        $q_string .= "sec_severity =  " . $a_severity['sev_id']              . " ";
        $insert = mysqli_query($db, $q_string) or die("\nQuery: " . $q_string . "\nFamily: " . $data[2] . "\nSeverity: " . $data[3] . "\n" . mysqli_error($db) . "\n");
        print "+";
      } else {
        $a_inv_security = mysqli_fetch_array($q_inv_security);

# if the severity changes, update the record
        if ($a_inv_security['sec_severity'] != $a_severity['sev_id']) {
          $q_string  = "update ";
          $q_string .= "inv_security ";
          $q_string .= "set ";
          $q_string .= "sec_name     = \"" . mysqli_real_escape_string($db, $data[1]) . "\",";
          $q_string .= "sec_severity =   " . $a_severity['sev_id'] . " ";
          $q_string .= "where sec_id = " . $a_inv_security['sec_id'];
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

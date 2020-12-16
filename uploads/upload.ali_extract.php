<?php
# Script: upload.ali_extract.php
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

  $extract = 'ali_total.csv';
# retiring for separate load
  $centurylink = 'CTL_Non_ALI_PSAP_IDs.csv';

  $debug = 'yes';
  $debug = 'no';

# set all the delete flags to 1 prior to running the import
  $q_string  = "update ";
  $q_string .= "psaps ";
  $q_string .= "set psap_delete = 1 ";
  $q_string .= "where psap_customerid = 1319 ";
  $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));


# ali_id, ali_name, psap_id, descrption,     lport, circuit_id, 
# N1,     hplgmtn,  1,      IEN DEFAULT PSAP,28001, NULL,      CTL WEST
# N2,     hpmiamn,  1,      IEN DEFAULT PSAP,28001, NULL,      CTL WEST
#  0            1   2                      3     4     5              6

if (($handle = fopen($extract, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

# the manually added data has a 'null' instead of a number for the lport.
    if ($data[4] == 'NULL') {
      $data[4] = 0;
    }

# initial check, find IP address in database and associate the plugin id with the ip in the vulnerability table.
# group is by default, the inv_manager id
# 
# ignore not in the system for now

    $q_string  = "select inv_id,inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $data[1] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) == 0) {

      print "no such server as " . $data[1] . "\n";

    } else {
      $a_inventory = mysqli_fetch_array($q_inventory);

      $query  = 
        "psap_customerid  =  " . "1319"                                        . "," . 
        "psap_ali_id      = \"" . mysqli_real_escape_string($db, clean($data[0],10))  . "\"," .
        "psap_companyid   =  " . $a_inventory['inv_id']                        . "," .
        "psap_psap_id     = \"" . mysqli_real_escape_string($db, clean($data[2],255)) . "\"," .
        "psap_description = \"" . mysqli_real_escape_string($db, clean($data[3],255)) . "\"," .
        "psap_lport       =  " . $data[4]                                      . "," .
        "psap_circuit_id  = \"" . mysqli_real_escape_string($db, clean($data[5],255)) . "\"," . 
        "psap_updated     = \"" . date('Y-m-d')                                 . "\"," . 
        "psap_delete      =   " . "0";

      $q_string  = "select psap_id "; 
      $q_string .= "from psaps ";
      $q_string .= "where psap_ali_id = '" . trim($data[0]) . "' and psap_companyid = " . $a_inventory['inv_id'] . " and psap_psap_id = " . $data[2] . " ";
      $q_psaps = mysqli_query($db, $q_string) or print $q_string . "\n";
      if (mysqli_num_rows($q_psaps) == 0) {
        $q_string  = "insert into psaps set psap_id = null," . $query;
        print "i";
      } else {
        $a_psaps = mysqli_fetch_array($q_psaps);
        $q_string  = "update psaps set " . $query . " where psap_id = " . $a_psaps['psap_id'];
        print "u";
      }

      if ($debug == 'no') {
        $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      } else {
        print $q_string . "\n";
      }

    }
  }

  fclose($handle);
}

# these are all the Default psap information so they'll all be 10114
#if (($handle = fopen($centurylink, "r")) !== FALSE) {
#  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
#
## the manually added data has a 'null' instead of a number for the lport.
#    if ($data[4] == 'NULL') {
#      $data[4] = 0;
#    }
#
#    $query  = 
#      "psap_customerid  =   " . "41"                                          . "," . 
#      "psap_ali_id      = \"" . mysqli_real_escape_string($db, clean($data[0],10))  . "\"," .
#      "psap_companyid   =   " . "10114"                                       . "," .
#      "psap_psap_id     = \"" . mysqli_real_escape_string($db, clean($data[2],255)) . "\"," .
#      "psap_description = \"" . mysqli_real_escape_string($db, clean($data[3],255)) . "\"," .
#      "psap_lport       =   " . $data[4]                                      . "," .
#      "psap_circuit_id  = \"" . mysqli_real_escape_string($db, clean($data[5],255)) . "\"," . 
#      "psap_updated     = \"" . date('Y-m-d')                                 . "\"," . 
#      "psap_delete      =   " . "0";
#
#    $q_string  = "select psap_id "; 
#    $q_string .= "from psaps ";
#    $q_string .= "where psap_ali_id = '" . trim($data[0]) . "' and psap_companyid = 10114 and psap_psap_id = " . $data[2] . " ";
#    if ($debug == 'yes') {
#      print $q_string . "\n";
#    }
#    $q_psaps = mysqli_query($db, $q_string) or print $q_string . "\n";
#    if (mysqli_num_rows($q_psaps) == 0) {
#      $q_string  = "insert into psaps set psap_id = null," . $query;
#      print "i";
#    } else {
#      $a_psaps = mysqli_fetch_array($q_psaps);
#      $q_string  = "update psaps set " . $query . " where psap_id = " . $a_psaps['psap_id'];
#      print "u";
#    }
#
#    if ($debug == 'no') {
#      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
#    } else {
#      print $q_string . "\n";
#    }
#
#  }
#
#  fclose($handle);
#}

?>

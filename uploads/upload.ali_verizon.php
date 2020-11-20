<?php
# Script: upload.ali_verizon.php
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

  $debug = 'yes';
  $debug = 'no';

  $file = 'verizon.ienvload.current.csv';

# set all the delete flags to 1 prior to running the import
  $q_string  = "update ";
  $q_string .= "psaps ";
  $q_string .= "set psap_delete = 1 ";
  $q_string .= "where psap_customerid = 1319 and (psap_companyid = 10372 or psap_companyid = 10373) ";
  $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# ALI ID,ALI Name,PSAP ID,PSAP Name,lport,Circuit ID,LEC
# V1,VZIENVA,549,BEALUSAFCAPDRTA,9999,Default,Verizon IENV
# V1,VZIENVA,571,BUTTCOCASDRTA,9999,Default,Verizon IENV

$row = 1;
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

# clear out spaces
    $data[0] = clean($data[0], 10);

    $q_string  = "select inv_id,inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $data[1] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) == 0) {

      print "no such server as " . $data[1] . "\n";

    } else {
      $a_inventory = mysqli_fetch_array($q_inventory);

      $query  = 
        "psap_customerid  =   " . "1319"                                        . "," . 
        "psap_ali_id      = \"" . mysqli_real_escape_string(clean($data[0],10))  . "\"," .
        "psap_companyid   =   " . $a_inventory['inv_id']                        . "," .
        "psap_psap_id     = \"" . mysqli_real_escape_string(clean($data[2],255)) . "\"," .
        "psap_description = \"" . mysqli_real_escape_string(clean($data[3],255)) . "\"," .
        "psap_lport       =   " . $data[4]                                      . "," .
        "psap_circuit_id  = \"" . mysqli_real_escape_string(clean($data[5],255)) . "\"," . 
        "psap_lec         = \"" . $data[6]                                      . "\"," . 
        "psap_updated     = \"" . date('Y-m-d')                                 . "\"," . 
        "psap_delete      =   " . "0";

      $q_string  = "select psap_id "; 
      $q_string .= "from psaps ";
      $q_string .= "where psap_ali_id = '" . $data[0] . "' and psap_companyid = " . $a_inventory['inv_id'] . " and psap_psap_id = " . $data[2] . " ";
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

?>

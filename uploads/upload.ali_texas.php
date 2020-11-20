<?php
# Script: upload.ali_texas.php
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

  $extract = 'texas.csec.current.csv';

# old data
#
#        "psap_customerid  =  " . "1319"                             . "," .
#        "psap_ali_id      = '" . mysqli_real_escape_string($data[0]) . "'," .
#        "psap_companyid   =  " . $a_inventory['inv_id']             . "," .
#        "psap_psap_id     = '" . mysqli_real_escape_string($data[2]) . "'," .
#        "psap_description = '" . mysqli_real_escape_string($data[3]) . "'," .
#        "psap_lport       =  " . $data[4]                           . "," .
#        "psap_circuit_id  = '" . mysqli_real_escape_string($data[5]) . "'";
#
#0     1     2      3                4   5     6
#A4,dalalic2,2,TX-ENNIS POLICE DEPT,9042,NULL,AT&T Southwest
#
# new data
#
#        "psap_customerid  =  " . "1319"                             . "," .
#        "psap_ali_id      = '" . mysqli_real_escape_string($data[1]) . "'," .
#        "psap_companyid   =  " . $a_inventory['inv_id']             . "," .
#        "psap_psap_id     = '" . mysqli_real_escape_string($data[0]) . "'," .
#        "psap_description = '" . mysqli_real_escape_string($data[2]) . "'," .
#        "psap_lport       =  " . $data[3]                           . "," .
#        "psap_circuit_id  = '" . mysqli_real_escape_string($data[4]) . "'";
#
# 0  1        2           3  4   5    # current columns
#512,N1,TEXAS TEST PSAP,4011,,hplgmtn
# 2  0        3           4  5   1    # old data columns
#update psaps set psap_customerid  =  1319,psap_ali_id      = 'TX-VICTORIA PD',psap_companyid   =  1379,psap_psap_id     = '4374',psap_description = 'VCTATXPD1RTA',psap_lport       =  hplgmtn ,psap_circuit_id  = 'N1' where psap_id = 26081


$row = 1;
if (($handle = fopen($extract, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

# initial check, find IP address in database and associate the plugin id with the ip in the vulnerability table.
# group is by default, the inv_manager id
# 
# ignore not in the system for now

    $q_string  = "select inv_id,inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $data[5] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) == 0) {

      print "no such server as " . $data[5] . "\n";

    } else {
      $a_inventory = mysqli_fetch_array($q_inventory);

      $query  = 
        "psap_customerid  =  " . "1319"                             . "," . 
        "psap_ali_id      = '" . mysqli_real_escape_string($data[1]) . "'," .
        "psap_companyid   =  " . $a_inventory['inv_id']             . "," .
        "psap_psap_id     = '" . mysqli_real_escape_string($data[0]) . "'," .
        "psap_description = '" . mysqli_real_escape_string($data[2]) . "'," .
        "psap_lport       =  " . $data[3]                           . "," .
        "psap_circuit_id  = '" . mysqli_real_escape_string($data[4]) . "'";

      $q_string  = "select psap_id "; 
      $q_string .= "from psaps ";
      $q_string .= "where psap_ali_id = '" . $data[1] . "' and psap_companyid = " . $a_inventory['inv_id'] . " and psap_psap_id = " . $data[0] . " ";
      $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_psaps) == 0) {
        $q_string  = "insert into psaps set psap_id = null," . $query;
        print "i";
      } else {
        $a_psaps = mysqli_fetch_array($q_psaps);
        $q_string  = "update psaps set psap_texas = 1 where psap_id = " . $a_psaps['psap_id'];

        if ($debug == 'no') {
          $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
          print "u";
        } else {
          print $q_string . "\n";
        }
      }
    }
  }

  fclose($handle);
}

?>

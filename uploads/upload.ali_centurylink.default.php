<?php
# Script: upload.ali_centurylink.default.php
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

# clear out old data first. Only customerid = 41 and parentid = 0 (default entries)
  $q_string  = "delete ";
  $q_string .= "from psaps ";
  $q_string .= "where psap_customerid = 41 and psap_parentid = 0";
  $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

# This script adds the Centurylink data to the psaps table. This is intended to fill in the Partners fields in the output .csv file.
# As such the psap_partnerid equals the Intrado ALI ID.
# Output is if the psap_partnerid == 0, then the PSAP is an Intrado PSAP otherwise it's a Parter PSAP.
# 
# New fields per the new spreadsheet:
# psap_parentid - Stores the psap_id of the Intrado PSAP where ALI ID and Intrado PSAP ID match
# psap_pseudo_cid - Stores the 'New Circuit ID', column 6/G
# psap_lec - Stores the 'LEC', column 7/H
#
# Existing fields
# psap_id
# psap_ali_id - ALI ID
# psap_companyid - ALI ID - points to server name
# psap_psap_id - PSAP ID
# psap_description - PSAP NAME
# psap_lport - LPORT
# psap_circuit_id _ CIRCUIT ID

  $file = "centurylink.default.current.csv";

$row = 1;
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

# initially get the intrado psap information: column 0, 1, 3, 4
# match it with what's in the system already and get the intrado psap_id. This becomes the centurylink psap_companyid
# then see if the centurylink data inlcuding the psap_companyid and psap_parentid (which equals psap_id of intrado psap) exists




# initial check, find IP address in database and associate the plugin id with the ip in the vulnerability table.
# group is by default, the inv_manager id
# 
# ignore not in the system for now

#
# new centurylink data 1/29
# "Q1" |hpstlew |          03318         |     607       |"UT JUAB COUNTY" | "64LGGZ134187  172.20.50.124"| 64/LGGZ/134187/MS|CTL West |         JUAB COUNTY             |UT/EVXR/318/MS     |
# 0A       1B                2C                 3D                4E                     5F                        6G              7H                 8I                        9J
#ALI-ID,ALI Name,CTL-ID - Partner PSAP ID,Intrado PSAP ID,Intrado PSAP Name,        Circuit ID -          , NEW CircuitID,      LEC,    CTL-PSAP Name - Partner PSAP Name,CTL-PSAP Circuit ID,
#
# 0        1           2                   3             4     5     6      7                 8
#ALI-ID,ALI Name,Intrado PSAP ID,Intrado PSAP Name,Circuit ID,LEC,CTL-ID,CTL-PSAP Name,CTL-PSAP Circuit ID,
# 
# old centurylink data 12/11 - Ignore this...
# 0        1           2                        3                   4            5            6          7                 8                      9
#ALI-ID,ALI Name,CTL-ID - Partner PSAP ID,Intrado PSAP ID,Intrado PSAP Name,Circuit ID - ,NEW CircuitID,LEC,CTL-PSAP Name - Partner PSAP Name,CTL-PSAP Circuit ID

# clear out spaces
    $data[0] = clean($data[0], 10);

# get the server name that matches the second column
    $q_string  = "select inv_id,inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $data[1] . "' ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) == 0) {

      print "no such server as " . $data[1] . "\n";

    } else {
      $a_inventory = mysqli_fetch_array($q_inventory);

# duplicates:
#select psap_id from psaps where psap_ali_id = 'Q4' and psap_companyid = 1372 and psap_psap_id = '880' and psap_description = 'nc fayetteville com hosted viper 1';
# returns 
#select psap_id from psaps where psap_ali_id = 'Q3' and psap_companyid = 1373 and psap_psap_id = '880' and psap_description = 'nc fayetteville com hosted viper 1';
# returns 
#select psap_id from psaps where psap_ali_id = 'Q4' and psap_companyid = 1372 and psap_psap_id = '881' and psap_description = 'nc fayetteville com hosted viper 2';
# returns 
#select psap_id from psaps where psap_ali_id = 'Q3' and psap_companyid = 1373 and psap_psap_id = '881' and psap_description = 'nc fayetteville com hosted viper 2';
# returns 

# get the matching intrado psap id when data0, data1, data3, and data4 match
      $q_string  = "select psap_id ";
      $q_string .= "from psaps ";
      $q_string .= "where ";
      $q_string .= "  psap_ali_id      = '" . mysqli_real_escape_string(clean($data[0],10)) . "' and ";
      $q_string .= "  psap_companyid   =  " . $a_inventory['inv_id']             . "  and ";
      $q_string .= "  psap_psap_id     = '" . $data[3]                           . "' and ";
      $q_string .= "  psap_description = '" . mysqli_real_escape_string(clean($data[4],255)) . "' ";
      $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_psaps) == 0) {
        $a_psaps['psap_id'] = 0;
      } else {
        $a_psaps = mysqli_fetch_array($q_psaps);
      }

# add or update the centurylink (customerid 41) data.
# parentid == intrado psap_id == 0 if Default
# change from data[9] to data[6] per converstion with mahesh and jerry. 2/1/16
      $query  = 
        "psap_customerid  =  " . "41"                                          . "," . 
        "psap_parentid    =  " . $a_psaps['psap_id']                           . "," . 
        "psap_ali_id      = '" . mysqli_real_escape_string(clean($data[0],10))  . "'," .
        "psap_companyid   =  " . $a_inventory['inv_id']                        . "," .
        "psap_psap_id     = '" . mysqli_real_escape_string(clean($data[2],255)) . "'," .
        "psap_description = '" . mysqli_real_escape_string(clean($data[8],255)) . "'," .
        "psap_lport       =  " . 0                                             . "," . 
        "psap_circuit_id  = '" . mysqli_real_escape_string(clean($data[5],255)) . "'," . 
        "psap_pseudo_cid  = '" . mysqli_real_escape_string(clean($data[6],255)) . "'," . 
        "psap_lec         = '" . mysqli_real_escape_string(clean($data[7],255)) . "'," .
        "psap_updated     = '" . date('Y-m-d')                                 . "' ";


# now that we have the centurylink data (data0, data2, data8, data5, data6, data7) plus parentid, see if it already exists
      $q_string  = "select psap_id "; 
      $q_string .= "from psaps ";
      $q_string .= "where ";
      $q_string .= "  psap_customerid  =  " . "41"                                          . "  and ";
      $q_string .= "  psap_parentid    =  " . "0"                                           . "  and ";
      $q_string .= "  psap_ali_id      = '" . mysqli_real_escape_string(clean($data[0],10))  . "' and ";
      $q_string .= "  psap_companyid   =  " . $a_inventory['inv_id']                        . "  and ";
      $q_string .= "  psap_psap_id     =  " . mysqli_real_escape_string(clean($data[2],255)) . "  and ";
      $q_string .= "  psap_description = '" . mysqli_real_escape_string(clean($data[8],255)) . "' and ";
      $q_string .= "  psap_circuit_id  = '" . mysqli_real_escape_string(clean($data[5],255)) . "' and ";
      $q_string .= "  psap_pseudo_cid  = '" . mysqli_real_escape_string(clean($data[6],255)) . "' and ";
      $q_string .= "  psap_lec         = '" . mysqli_real_escape_string(clean($data[7],255)) . "' ";
      $q_psaps = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_psaps) == 0) {
        $q_string  = "insert into psaps set psap_id = null," . $query;
        if ($debug == 'no') {
          print "i";
        }
      } else {
        $a_psaps = mysqli_fetch_array($q_psaps);
        $q_string  = "update psaps set " . $query . " where psap_id = " . $a_psaps['psap_id'];
        if ($debug == 'no') {
          print "u";
          print "\nDuplicate record: " . $q_string . "\n";
        }
      }

      if ($debug == 'no') {
        $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      } else {
        print $q_string . "\n";
      }

    }
  }

  fclose($handle);

  print "\n";
}

?>

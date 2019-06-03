<?php
# Script: upload.ali_centurylink.php
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

  $file = "centurylink.current.csv";

# mark old centurylink info as deleted first, then unmark when found
  $q_string  = "update psaps ";
  $q_string .= "set psap_delete = 1 ";
  $q_string .= "where psap_companyid = 41 ";
  $result = mysql_query($q_string) or die($q_string . ": " . mysql_error());

# load the data
if (($handle = fopen($file, "r")) !== FALSE) {
  while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

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

    $q_string  = "select inv_id,inv_name ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $data[1] . "' ";
    $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_inventory) == 0) {

      print "no such server as " . $data[1] . "\n";

    } else {
      $a_inventory = mysql_fetch_array($q_inventory);

# get the matching Intrado psap information from the database
      $q_string  = "select psap_id "; 
      $q_string .= "from psaps ";
      $q_string .= "where psap_ali_id = '" . $data[0] . "' and psap_companyid = " . $a_inventory['inv_id'] . " and psap_psap_id = " . $data[3] . " ";
      $q_psaps = mysql_query($q_string) or die($q_string . "\n: " . mysql_error() . "\n");
      if (mysql_num_rows($q_psaps) == 0) {
# error, there should be an association with an existing psap id.
        print "Error: No association with an existing Intrado PSAP: Query: ALI ID = " . $data[0] . " and ALI Name = " . $a_inventory['inv_name'] . " and PSAP ID = " . $data[3] . "\n";

        $q_string  = "select psap_ali_id,inv_name "; 
        $q_string .= "from psaps ";
        $q_string .= "left join inventory on inventory.inventory.inv_id = inventory.psaps.psap_companyid ";
        $q_string .= "where psap_psap_id = " . $data[3] . " and psap_ali_id like \"Q%\" ";
        $q_string .= "order by psap_ali_id,inv_name ";
        $q_psaps = mysql_query($q_string) or die($q_string . "\n: " . mysql_error() . "\n");
        while ($a_psaps = mysql_fetch_array($q_psaps)) {
          print "--Found: ALI ID = " . $a_psaps['psap_ali_id'] . " and ALI Name = " . $a_psaps['inv_name'] . " and PSAP ID = " . $data[3] . "\n";
        }

      } else {
        $a_psaps = mysql_fetch_array($q_psaps);

# change from data[9] to data[6] per converstion with mahesh and jerry. 2/1/16
        $query  = 
          "psap_customerid  =  " . "41"                                          . "," . 
          "psap_parentid    =  " . $a_psaps['psap_id']                           . "," . 
          "psap_ali_id      = '" . mysql_real_escape_string(clean($data[0],10))  . "'," .
          "psap_companyid   =  " . $a_inventory['inv_id']                        . "," .
          "psap_psap_id     = '" . mysql_real_escape_string(clean($data[2],255)) . "'," .
          "psap_description = '" . mysql_real_escape_string(clean($data[8],255)) . "'," .
          "psap_lport       =  " . 0                                             . "," . 
          "psap_circuit_id  = '" . mysql_real_escape_string(clean($data[5],255)) . "'," . 
          "psap_pseudo_cid  = '" . mysql_real_escape_string(clean($data[6],255)) . "'," . 
          "psap_lec         = '" . mysql_real_escape_string(clean($data[7],255)) . "'," .
          "psap_delete      =  " . 0                                             . "," . 
          "psap_updated     = '" . date('Y-m-d')                                 . "'";

        $q_string  = "select psap_id "; 
        $q_string .= "from psaps ";
        $q_string .= "where psap_customerid = 41 and psap_ali_id = '" . $data[0] . "' and psap_companyid = " . $a_inventory['inv_id'] . " and psap_psap_id = " . $data[2] . " ";
        $q_psaps = mysql_query($q_string) or die($query . "\n:" . $q_string . "\n: " . mysql_error());
        if (mysql_num_rows($q_psaps) == 0) {
          $q_string  = "insert into psaps set psap_id = null," . $query;
          if ($debug == 'no') {
#            print "i";
            print "";
          }
        } else {
          $a_psaps = mysql_fetch_array($q_psaps);
          $q_string  = "update psaps set " . $query . " where psap_id = " . $a_psaps['psap_id'];
          if ($debug == 'no') {
#            print "u";
            print "";
          }
        }

        if ($debug == 'no') {
          $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());
        } else {
          print $q_string . "\n";
        }

      }
    }
  }

  fclose($handle);
}

?>

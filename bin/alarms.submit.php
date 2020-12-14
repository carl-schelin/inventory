#!/usr/local/bin/php
<?php
# Script: alarms.submit.php
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


  $file = fopen('alarms.output', "r");

  while(!feof($file)) {
    $process = trim(fgets($file));

    $value = explode("\t", $process);

    $formVars['alarm_level'] = 0;
    if ($value[2] == "critical") {
      $formVars['alarm_level'] = 5;
    }
    if ($value[2] == "major") {
      $formVars['alarm_level'] = 4;
    }
    if ($value[2] == "minor") {
      $formVars['alarm_level'] = 3;
    }
    if ($value[2] == "warning") {
      $formVars['alarm_level'] = 2;
    }
    if ($value[2] == "normal") {
      $formVars['alarm_level'] = 1;
    }

# need to convert $value[0] to 0000-00-00 00:00:00 from Mon Day Year HH:MM:SS.
    $formVars['alarm_timestamp'] = date("Y-m-d H:i:s", strtotime($value[0]));

    $formVars['alarm_text'] = $value[3];
    if (isset($value[4])) {
      $formVars['alarm_text'] .= " " . $value[4];
    }

    $q_string = "select inv_id ";
    $q_string .= "from inventory ";
    $q_string .= "where inv_name = '" . $value[1] . "' and inv_status = 0 ";
    $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inventory) > 0) {
      $a_inventory = mysqli_fetch_array($q_inventory);

      $q_string = 
        "alarm_companyid  =   " . $a_inventory['inv_id']       . "," . 
        "alarm_timestamp  = \"" . $formVars['alarm_timestamp'] . "\"," . 
        "alarm_level      =   " . $formVars['alarm_level']     . "," . 
        "alarm_text       = \"" . $formVars['alarm_text']      . "\"";

      $query = "insert into alarms set alarm_id = null," . $q_string;
      mysqli_query($db, $query);

      print $formVars['alarm_timestamp'] . "\n";
    } else {

      $q_string = "select int_companyid ";
      $q_string .= "from interface ";
      $q_string .= "left join inventory on inventory.inv_id = interface.int_companyid ";
      $q_string .= "where int_server = '" . $value[1] . "' and inv_status = 0 ";
      $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_interface) > 0) {
        $a_interface = mysqli_fetch_array($q_interface);

        $q_string = 
          "alarm_companyid  =   " . $a_interface['int_companyid']  . "," . 
          "alarm_timestamp  = \"" . $formVars['alarm_timestamp']   . "\"," . 
          "alarm_level      =   " . $formVars['alarm_level']       . "," . 
          "alarm_text       = \"" . mysqli_real_escape_string($formVars['alarm_text']) . "\"";

        $query = "insert into alarms set alarm_id = null," . $q_string;
        mysqli_query($db, $query);

        print $formVars['alarm_timestamp'] . "\n";
      } else {
        print "Error: Unable to locate $value[1].\n";
      }
    }
  }

  fclose($file);

?>

#!/usr/local/bin/php
<?php
# Script: openview.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 
# 

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# list the last alarm received for every server
# then check the interface listing to see if there's an interface marked for openview

  $q_string  = "select inv_id,alarm_companyid,inv_name ";
  $q_string .= "from alarms ";
  $q_string .= "left join inventory on inventory.inv_id = alarms.alarm_companyid ";
  $q_string .= "where inv_status = 0 ";
  $q_string .= "order by inv_name,alarm_timestamp desc ";
  $q_string .= "limit 1 ";
  $q_alarms = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_alarms = mysqli_fetch_array($q_alarms)) {

    $q_string  = "select int_id ";
    $q_string .= "from interface ";
    $q_string .= "where int_companyid = " . $a_alarms['alarm_companyid'] . " and int_openview = 1 ";
    $q_interface = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

    if (mysqli_num_rows($q_interface) == 0) {
      print "\"" . $a_alarms['inv_id'] . "\",";
      print "\"" . $a_alarms['inv_name'] . "\"\n";
    }

  }

  mysqli_free_result($db);

?>

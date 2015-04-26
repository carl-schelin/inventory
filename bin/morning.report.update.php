<?php
# Script: morning.report.update.php
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

# set the date so the report knows which data to retrieve
  $today = date('Y-m-d');

# if today is Monday, get the updates from Friday and not from Sunday.
  if (date('w') == 1) {
    $previous = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 3, date('Y')));
  } else {
    $previous = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
  }

  $q_string  = "select grp_id,grp_name ";
  $q_string .= "from groups ";
  $q_string .= "where grp_report != 0 ";
  $q_string .= "order by grp_report";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {

    $q_string  = "select rep_id,rep_issue,rep_user,rep_group,rep_status,rep_task,rep_timestamp ";
    $q_string .= "from report ";
    $q_string .= "where rep_group = " . $a_groups['grp_id'] . " and rep_date = '" . $previous . "'";
    $q_report = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_report = mysql_fetch_array($q_report)) {

      $issues = strtolower(substr($a_report['rep_task'], 0, 9));
      if ($issues == 'no issues') {
        $issues = '';
      }

      if ($a_report['rep_status'] > 1 && $issues != '') {
        $q_string = "insert into report set " .
          "rep_id        =   " . "NULL"                      . "," . 
          "rep_user      =   " . $a_report['rep_user']       . "," . 
          "rep_issue     =   " . $a_report['rep_issue']      . "," . 
          "rep_timestamp = \"" . $a_report['rep_timestamp']  . "\"," . 
          "rep_group     =   " . $a_report['rep_group']      . "," . 
          "rep_date      = \"" . $today                      . "\"," . 
          "rep_status    =   " . $a_report['rep_status']     . "," . 
          "rep_task      = \"" . $a_report['rep_task']       . "\"";

        mysql_query($q_string) or die($q_string . ": " . mysql_error());

      }

    }
  }

?>

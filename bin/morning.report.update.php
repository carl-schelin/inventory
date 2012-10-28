<?php
  include('/usr/local/httpd/htsecure/status/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','status','root','this4now!!');

  $today = date('Y-m-d');

# if today is Monday, get the updates from Friday and not from Sunday.
  if (date('w') == 1) {
    $previous = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 3, date('Y')));
  } else {
    $previous = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
  }

  $q_string = "select grp_id,grp_name from groups where grp_report != 0 order by grp_report";
  $q_groups = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_groups = mysql_fetch_array($q_groups)) {

    $q_string = "select rep_id,rep_user,rep_group,rep_status,rep_task,rep_date from report where rep_group = " . $a_groups['grp_id'] . " and rep_date = '" . $previous . "'";
    $q_report = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    while ($a_report = mysql_fetch_array($q_report)) {

      if ($a_report['rep_status'] > 1) {
        $q_string = "insert into report set " .
          "rep_id     =   " . "NULL"                  . "," . 
          "rep_user   =   " . $a_report['rep_user']   . "," . 
          "rep_group  =   " . $a_report['rep_group']  . "," . 
          "rep_date   = \"" . $a_report['rep_date']   . "\"," . 
          "rep_status =   " . $a_report['rep_status'] . "," . 
          "rep_task   = \"" . $a_report['rep_task']   . "\"";

        mysql_query($q_string) or die($q_string . ": " . mysql_error());

      }

    }
  }

?>

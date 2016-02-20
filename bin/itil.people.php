#!/usr/local/bin/php
<?php
# Script: itil.people.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Retrieve the personnel information
# for the conversion to Remedy.
# Requires:
# Last Name
# First Name
# Job Title (Optional)
# 1

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "Last Name,First Name,Job Title,Profile Status\n";

  $q_string  = "select usr_last,usr_first,tit_name ";
  $q_string .= "from users ";
  $q_string .= "left join titles on titles.tit_id = users.usr_title ";
  $q_string .= "where usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {

    print "\"" . $a_users['usr_last'] . ",\"" . $a_users['usr_first'] . "\",\"" . $a_users['tit_name'] . "\",1\n";
  }

?>

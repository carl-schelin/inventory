#!/usr/local/bin/php
<?php
# Script: morning.report.userlist.php
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

  $q_string  = "select usr_email ";
  $q_string .= "from users ";
  $q_string .= "where usr_id != 1 ";
  $q_string .= "and usr_disabled = 0 ";
  $q_string .= "and usr_email like '%@intrado.com' ";
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print $a_users['usr_email'] . "\n";
  }

?>

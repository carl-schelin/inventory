<?php
include('settings.php');
include('config.php');

// Important Configuration Option
// e.g. dbconn('localhost','your_database','your_login','your_pass');

$db  = dbconn( 'localhost', $DBname, $DBuser, $DBpassword);

// No need to edit below this line.

// Connect and select database.

function dbconn($server,$database,$user,$pass){
  $db = mysql_connect($server,$user,$pass);
  $db_select = mysql_select_db($database,$db);
  return $db;
}

?>

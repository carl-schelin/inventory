<?php
include('settings.php');
include('config.php');

// Important Configuration Option
// e.g. dbconn('localhost','your_database','your_login','your_pass');

$db  = dbconn( 'localhost', $DBname, $DBuser, $DBpassword);

// No need to edit below this line.

// Connect and select database.

function dbconn($server,$database,$user,$pass){

  $db = mysqli_connect($server,$user,$pass, $database);

  $db_select = mysqli_select_db($db, $database);

  return $db;
}

?>

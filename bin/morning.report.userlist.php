#!/usr/bin/php
<?php
  include('/usr/local/httpd/htsecure/status/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','status','root','this4now!!');

  $q_string = "select usr_email from users where usr_id != 1 and usr_disabled = 0";
  $q_users = mysql_query($q_string, $db) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {
    print $a_users['usr_email'] . "\n";
  }

?>

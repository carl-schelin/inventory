<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $q_string  = "select usr_email,usr_altemail ";
  $q_string .= "from users ";
  $q_string .= "where usr_id != 1 and usr_disabled = 0 ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string, $db) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {

    print $a_users['usr_email'] . "\n";

    if (strlen($a_users['usr_altemail']) > 0) {
      $emails = preg_split("/[\s,]+/", $a_users['usr_altemail']);

      for ($i = 0; $i < count($emails); $i++) {
        print $emails[$i] . "\n";
      }
    }
  }

  mysqli_close($db);

?>

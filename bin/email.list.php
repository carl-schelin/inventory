<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $q_string  = "select usr_id,usr_email,grp_email,grp_clserver ";
  $q_string .= "from users ";
  $q_string .= "left join groups on groups.grp_id = users.usr_group ";
  $q_string .= "where (usr_email like '%@intrado.com' or usr_email like '%west.com') and usr_disabled = 0 and grp_clserver != '' ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_users = mysqli_fetch_array($q_users)) {

    print $a_users['usr_email'] . ":" . $a_users['grp_email'] . ":" . $a_users['grp_clserver'] . ":" . $a_users['usr_id'] . "\n";

  }

  mysqli_close($db);

?>

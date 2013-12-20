<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn('localhost','inventory','root','this4now!!');

  $q_string  = "select usr_id,usr_email,grp_email,grp_clserver ";
  $q_string .= "from users ";
  $q_string .= "left join groups on groups.grp_id = users.usr_group ";
  $q_string .= "where usr_email like '%@intrado.com' and usr_disabled = 0 and grp_clserver != '' ";
  $q_string .= "order by usr_last,usr_first ";
  $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_users = mysql_fetch_array($q_users)) {

    print $a_users['usr_email'] . ":" . $a_users['grp_email'] . ":" . $a_users['grp_clserver'] . ":" . $a_users['usr_id'] . "\n";

  }

?>

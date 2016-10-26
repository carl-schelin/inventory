<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

  $month = date('m') - 1;
  $date = date('Y-' . $month . '-01');

  if ($argc > 2) {
    $date = $argv[2];
  }

  $debug = 'yes';
  $debug = 'no';

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $body = "The following servers have been decommissioned since " . $date . ". Please review the listing.\n\n";

  $q_string  = "select inv_name,hw_retired ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "where hw_retired > '" . $date . "' and inv_manager = " . $manager . " ";
  $q_string .= "order by hw_retired,inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_inventory = mysql_fetch_array($q_inventory)) {

    $servers .= $a_inventory['inv_name'] . " - " . $a_inventory['hw_retired'] . "\n";

  }

  if (strlen($servers) > 0) {
    $body .= $servers;

    if ($debug == 'yes') {
      print $body;
    } else {

      $q_string  = "select usr_email ";
      $q_string .= "from users ";
      $q_string .= "left join grouplist on grouplist.gpl_user = users.usr_id ";
      $q_string .= "where gpl_group = " . $manager . " ";
      $q_users = mysql_query($q_string) or die($q_string . ": " . mysql_error());
      while ($a_users = mysql_fetch_array($q_users)) {
        if ($a_users['usr_email'] != '') {
          mail($a_users['usr_email'], $date . " Retirements", $body, $headers);
        }
      }
    }
  }


?>

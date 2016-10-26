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
# for testing; mail me only
  $email = 'carl.schelin@intrado.com';
  $email = '';

  $headers  = "From: Inventory Management <inventory@incojs01.scc911.com>\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

  $hw_retired = "<p>The following servers have been decommissioned since " . $date . ". Please review the listing.</p>\n";

  $q_string  = "select inv_name,hw_retired ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "where hw_retired > '" . $date . "' and inv_manager = " . $manager . " ";
  $q_string .= "order by hw_retired,inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_inventory) > 0) {
    $retired = "<table>\n";
    $retired .= "<tr>\n";
    $retired .= "  <th>Server</th>\n";
    $retired .= "  <th>Retired</th>\n";
    $retired .= "</tr>\n";
    while ($a_inventory = mysql_fetch_array($q_inventory)) {

      $retired .= "<tr>\n";
      $retired .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $retired .= "  <td>" . $a_inventory['hw_retired'] . "</td>\n";
      $retired .= "</tr>\n";
    }
    $retired .= "</table>\n";
  }

  $hw_built = "<p>The following servers are identified as still being built (no Go Live date). Please review the listing and if the server is <b>in use</b>, please mark the server as appropriate. You can set the actual date when editing the server hardware (primary device).</p>\n";

  $q_string  = "select inv_name,hw_built ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "where hw_active = '0000-00-00' and hw_primary = 1 and inv_status = 0 and inv_manager = " . $manager . " ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  if (mysql_num_rows($q_inventory) > 0) {
    $built = "<table>\n";
    $built .= "<tr>\n";
    $built .= "  <th>Server</th>\n";
    $built .= "  <th>Build Date</th>\n";
    $built .= "</tr>\n";
    while ($a_inventory = mysql_fetch_array($q_inventory)) {

      $built .= "<tr>\n";
      $built .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $built .= "  <td>" . $a_inventory['hw_built'] . "</td>\n";
      $built .= "</tr>\n";
    }
    $built .= "</table>\n";
  }

  $body = '';
  if (strlen($retired) > 0) {
    $body .= $hw_retired . $retired;
  }

  if (strlen($built) > 0) {
    $body .= $hw_built . $built;
  }

  if (strlen($body) > 0) {
    if ($debug == 'yes') {
      print $body;
    } else {

      if ($email == '') {
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
      } else {
        mail($email, $date . " Retirements", $body, $headers);
      }
    }
  }

?>

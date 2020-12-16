<?php
# Script: inventory.review.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: send out a list of all retired and rsdp systems

# root.cron: # Monthly review of retired systems
# root.cron: 0 5 1 * * /usr/local/bin/php /usr/local/httpd/bin/inventory.review.php 1
# root.cron: 0 5 1 * * /usr/local/bin/php /usr/local/httpd/bin/inventory.review.php 26

  include('settings.php');
  include($Sitepath . '/function.php');
  date_default_timezone_set('UTC') ;

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  $manager = 1;

  if ($argc > 1) {
    $manager = $argv[1];
  }

  $month = date('m') - 1;
  $year = date('Y');
  if ($month == 0) {
    $month = 12;
    $year--;
  }
  $date = date($year . '-' . $month . '-01');

  if ($argc > 2) {
    $date = $argv[2];
  }

  $debug = 'yes';
  $debug = 'no';
# for testing; mail me only
  $email = 'carl.schelin@intrado.com';
  $email = '';

  $headers  = "From: Inventory Management <inventory@" . $hostname . ">\r\n";
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";


  $hw_retired = "<p>The following servers have been decommissioned since " . $date . ". Please review the listing.</p>\n";

  $q_string  = "select inv_name,hw_retired ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "where hw_retired > '" . $date . "' and inv_manager = " . $manager . " ";
  $q_string .= "order by hw_retired,inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    $retired = "<table border=\"1\">\n";
    $retired .= "<tr>\n";
    $retired .= "  <th>Server</th>\n";
    $retired .= "  <th>Retired</th>\n";
    $retired .= "</tr>\n";
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

      $retired .= "<tr>\n";
      $retired .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $retired .= "  <td>" . $a_inventory['hw_retired'] . "</td>\n";
      $retired .= "</tr>\n";
    }
    $retired .= "</table>\n";
  }


  $hw_built  = "<p>The following servers are identified as still being built (no Go Live date).</p>";
  $hw_built .= "<p>Please review the listing and if the server is <b>in use</b>, please mark the server as appropriate. You can set the actual date when editing the server hardware (primary device).</p>\n";
  $hw_built .= "<p>Please review the listing.</p>\n";

  $q_string  = "select inv_name,inv_rsdp,hw_built,prod_name,prj_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join products on products.prod_id = inventory.inv_product ";
  $q_string .= "left join projects on projects.prj_id = inventory.inv_project ";
  $q_string .= "where hw_active = '0000-00-00' and hw_primary = 1 and inv_status = 0 and inv_manager = " . $manager . " ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    $built = "<table border=\"1\">\n";
    $built .= "<tr>\n";
    $built .= "  <th>Server</th>\n";
    $built .= "  <th>Build Date</th>\n";
    $built .= "  <th>Product</th>\n";
    $built .= "  <th>Project</th>\n";
    $built .= "  <th>RSDP Requester</th>\n";
    $built .= "</tr>\n";
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

      $user = '';
      if ($a_inventory['inv_rsdp'] > 0) {
        $q_string  = "select usr_first,usr_last ";
        $q_string .= "from rsdp_server ";
        $q_string .= "left join users on  users.usr_id = rsdp_server.rsdp_requestor ";
        $q_string .= "where rsdp_id = " . $a_inventory['inv_rsdp'] . " ";
        $q_rsdp_server = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        $a_rsdp_server = mysqli_fetch_array($q_rsdp_server);

        $user = $a_rsdp_server['usr_last'] . ", " . $a_rsdp_server['usr_first'];
      }

      $built .= "<tr>\n";
      $built .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $built .= "  <td>" . $a_inventory['hw_built'] . "</td>\n";
      $built .= "  <td>" . $a_inventory['prod_name'] . "</td>\n";
      $built .= "  <td>" . $a_inventory['prj_name'] . "</td>\n";
      $built .= "  <td>" . $user . "</td>\n";
      $built .= "</tr>\n";
    }
    $built .= "</table>\n";
  }


  $hw_ssh  = "<p>The following servers are identified as a Physical or Virtual Server but don't have the 'SSH' flag enabled.</p>";
  $hw_ssh .= "<p>Enabling SSH lets the UnixSvc account retrieve information from the server to keep the Inventory accurate and other scripts use the flag to indicate the server is accessible via ssh.</p>";
  $hw_ssh .= "<p>Please review the listing.</p>\n";

  $q_string  = "select inv_name ";
  $q_string .= "from inventory ";
  $q_string .= "left join hardware on hardware.hw_companyid = inventory.inv_id ";
  $q_string .= "left join models on models.mod_id = hardware.hw_vendorid ";
  $q_string .= "where inv_status = 0 and inv_ssh = 0 and inv_manager = " . $manager . " and (mod_id = 15 or mod_id = 45) ";
  $q_string .= "order by inv_name ";
  $q_inventory = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inventory) > 0) {
    $ssh = "<table border=\"1\">\n";
    $ssh .= "<tr>\n";
    $ssh .= "  <th>Server</th>\n";
    $ssh .= "</tr>\n";
    while ($a_inventory = mysqli_fetch_array($q_inventory)) {

      $ssh .= "<tr>\n";
      $ssh .= "  <td>" . $a_inventory['inv_name'] . "</td>\n";
      $ssh .= "</tr>\n";
    }
    $ssh .= "</table>\n";
  }


#  now build the email message. If blank, then ignore.
  $body = '';
  if (strlen($retired) > 0) {
    $body .= $hw_retired . $retired;
  }

  if (strlen($built) > 0) {
    $body .= $hw_built . $built;
  }

  if (strlen($ssh) > 0) {
    $body .= $hw_ssh . $ssh;
  }

# and send the email out
  if (strlen($body) > 0) {
    if ($debug == 'yes') {
      print $body;
    } else {

      if ($email == '') {
        $q_string  = "select usr_email ";
        $q_string .= "from users ";
        $q_string .= "left join grouplist on grouplist.gpl_user = users.usr_id ";
        $q_string .= "where gpl_group = " . $manager . " ";
        $q_users = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
        while ($a_users = mysqli_fetch_array($q_users)) {
          if ($a_users['usr_email'] != '') {
            mail($a_users['usr_email'], $date . " Inventory Review", $body, $headers);
          }
        }
      } else {
        mail($email, "Testing: " . $date . " Inventory Review", $body, $headers);
      }
    }
  }

  mysqli_close($db);

?>

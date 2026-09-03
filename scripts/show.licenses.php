<?php
  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# this script retrieves the product parent, children, then checks 
# the lic_expire date against today's date.

# pass in one of two options:
# warning - licenses that expire in 90 days
# error - licenses that expire in 30 days
# critical - licenses that have expired

  if ($argc == 1) {
    print "ERROR: invalid command line parameters. Pass in one of the following:\n\n";
    print "Warning - Display all licenses that expire between 90 days and 30 days.\n";
    print "Error - Display all licenses that expire between 30 days and today.\n";
    print "Critical - Display all licenses that have expired.\n\n";
    exit(1);
  } else {
    $compare = $argv[1];
  }

# get today's date to compare with the license expiration dates
  $today = date('Y-m-d');

# initialize output
  $output = '';
  $product = '';
  if ($compare == 'warning') {
    $product = "The following licenses are expiring in the next 90 days:\n";
  }
  if ($compare == 'error') {
    $product = "The following licenses are expiring in the next 30 days:\n";
  }
  if ($compare == 'critical') {
    $product = "The following licenses have expired:\n";
  }

# get the parent license titles first
  $q_string  = "select lic_id,ven_name,lic_product,lic_parent ";
  $q_string .= "from inv_licenses ";
  $q_string .= "left join inv_vendors on inv_vendors.ven_id = inv_licenses.lic_vendor ";
  $q_string .= "where lic_parent = 0 and lic_status = 0 ";
  $q_string .= "order by lic_product ";
  $q_inv_licenses = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  if (mysqli_num_rows($q_inv_licenses) > 0) {
    while ($a_inv_licenses = mysqli_fetch_array($q_inv_licenses)) {

      $output = 'no';
      $product .= $a_inv_licenses['ven_name'] . ": " . $a_inv_licenses['lic_product'] . "\n";

      $q_string  = "select lic_product,lic_expire ";
      $q_string .= "from inv_licenses ";
      $q_string .= "where lic_parent = " . $a_inv_licenses['lic_id'] . " and lic_status = 0 ";
      $q_string .= "order by lic_product ";
      $q_child = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      if (mysqli_num_rows($q_child) > 0) {
        while ($a_child = mysqli_fetch_array($q_child)) {

# if the expiration date -90 days in that window, change the color.
# let's do 90 days for highlight
          $date = new DateTime($a_child['lic_expire']);
          $days = 90;
          $interval = new DateInterval("P{$days}D");
          $date->sub($interval);
          $highlight_date = $date->format("Y-m-d");

# and 30 days for expiring soon!!!
          $date = new DateTime($a_child['lic_expire']);
          $days = 30;
          $interval = new DateInterval("P{$days}D");
          $date->sub($interval);
          $error_date = $date->format("Y-m-d");

# and critical date 
          $critical_date = $a_child['lic_expire'];

          if ($compare == "warning") {
            if ($today > $highlight_date && $today < $error_date) {
              $output = "yes";
              $product .= "> " . $a_child['lic_expire'] . " - " . $a_child['lic_product'] . "\n";
            }
          }

          if ($compare == "error") {
            if ($today > $error_date && $today < $critical_date) {
              $output = "yes";
              $product .= "> " . $a_child['lic_expire'] . " - " . $a_child['lic_product'] . "\n";
            }
          }

          if ($compare == "critical") {
            if ($today > $critical_date) {
              $output = "yes";
              $product .= "> " . $a_child['lic_expire'] . " - " . $a_child['lic_product'] . "\n";
            }
          }
        }
        $product .= "\n";
        if ($output == 'yes') {
          print $product;
        }
        $product = '';
      }
    }
  }

  mysqli_close($db);

?>

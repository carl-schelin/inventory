<?php
# Script: lockuser.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysqli_connect($server,$user,$pass,$database);
    $db_select = mysqli_select_db($db,$database);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

# crontab entry:
# users that shouldn't be on the servers any more
#0 16 * * * /usr/local/bin/php /var/www/html/inventory/scripts/lockuser.php > /usr/local/admin/install/unixsuite/etc/lockuser.dat

  print "# This data file is managed by the inventory and is rebuilt daily.\n";
  print "# Do not update this file manually.\n\n";

  $q_string  = "select mu_username,mu_email,mu_ticket,mu_comment ";
  $q_string .= "from inv_manageusers ";
  $q_string .= "where mu_account = 0 and mu_locked = 1 ";
  $q_string .= "order by mu_username ";
  $q_inv_manageusers = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_inv_manageusers = mysqli_fetch_array($q_inv_manageusers)) {

# only list if the account exists on a live server
    $q_string  = "select pwd_user ";
    $q_string .= "from inv_syspwd ";
    $q_string .= "left join inv_inventory on inv_inventory.inv_id = inv_syspwd.pwd_companyid ";
    $q_string .= "where pwd_user = \"" . $a_inv_manageusers['mu_username'] . "\" and inv_status = 0 ";
    $q_inv_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inv_syspwd) > 0) {
#      if ($a_inv_manageusers['mu_comment'] != '') {
#        print "# " . $a_inv_manageusers['mu_comment'] . "\n";
#      }
#      if ($a_inv_manageusers['mu_ticket'] != '') {
#        print "# " . $a_inv_manageusers['mu_ticket'] . "\n";
#      }
      print $a_inv_manageusers['mu_username'] . ":" . $a_inv_manageusers['mu_email'] . "\n";
    }
  }

  mysqli_close($db);

?>

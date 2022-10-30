<?php
# Script: valid.email.php
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
# the proper gecos information for users
#0 16 * * * /usr/local/bin/php /var/www/html/inventory/scripts/valid.email.php > /usr/local/admin/install/unixsuite/etc/valid.email

  print "# This data file is managed by the inventory and is rebuilt daily.\n";
  print "# Do not update this file manually.\n\n";

  $q_string  = "select mu_username,mu_name,mu_email,mu_comment ";
  $q_string .= "from manageusers ";
  $q_string .= "where (mu_account = 0 or mu_account = 2) and mu_name != '' and mu_email != '' ";
  $q_string .= "order by mu_username ";
  $q_manageusers = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
  while ($a_manageusers = mysqli_fetch_array($q_manageusers)) {

# only list if the account exists on a live server
    $q_string  = "select pwd_user ";
    $q_string .= "from inv_syspwd ";
    $q_string .= "left join inventory on inventory.inv_id = inv_syspwd.pwd_companyid ";
    $q_string .= "where pwd_user = \"" . $a_manageusers['mu_username'] . "\" and inv_status = 0 ";
    $q_inv_syspwd = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
    if (mysqli_num_rows($q_inv_syspwd) > 0) {
#      if ($a_manageusers['mu_comment'] != '') {
#        print "# " . $a_manageusers['mu_comment'] . "\n";
#      }
      print $a_manageusers['mu_username'] . ":" . $a_manageusers['mu_name'] . "," . $a_manageusers['mu_email'] . "\n";
    }
  }

  mysqli_close($db);

?>

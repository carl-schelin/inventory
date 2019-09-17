<?php
# Script: lockuser.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description:

  include('settings.php');
  include($Sitepath . '/function.php');

  function dbconn($server,$database,$user,$pass){
    $db = mysql_connect($server,$user,$pass);
    $db_select = mysql_select_db($database,$db);
    return $db;
  }

  $db = dbconn($DBserver, $DBname, $DBuser, $DBpassword);

  print "# This data file is managed by the inventory and is rebuilt daily.\n";
  print "# Do not update this file manually.\n\n";

  $q_string  = "select mu_username,mu_email,mu_ticket,mu_comment ";
  $q_string .= "from manageusers ";
  $q_string .= "where mu_account = 0 and mu_locked = 1 ";
  $q_string .= "order by mu_username ";
  $q_manageusers = mysql_query($q_string) or die($q_string . ": " . mysql_error());
  while ($a_manageusers = mysql_fetch_array($q_manageusers)) {

# only list if the account exists on a live server
    $q_string  = "select pwd_user ";
    $q_string .= "from syspwd ";
    $q_string .= "left join inventory on inventory.inv_id = syspwd.pwd_companyid ";
    $q_string .= "where pwd_user = \"" . $a_manageusers['mu_username'] . "\" and inv_status = 0 ";
    $q_syspwd = mysql_query($q_string) or die($q_string . ": " . mysql_error());
    if (mysql_num_rows($q_syspwd) > 0) {
#      if ($a_manageusers['mu_comment'] != '') {
#        print "# " . $a_manageusers['mu_comment'] . "\n";
#      }
#      if ($a_manageusers['mu_ticket'] != '') {
#        print "# " . $a_manageusers['mu_ticket'] . "\n";
#      }
      print $a_manageusers['mu_username'] . ":" . $a_manageusers['mu_email'] . "\n";
    }
  }

?>

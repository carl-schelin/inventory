<?php
# Script: system.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "system.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel(2)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from operatingsystems");

      $q_string  = "update operatingsystem ";
      $q_string .= "set os_delete = 1,os_user = " . $_SESSION['uid'] . " ";
      $q_string .= "where os_id = " . $formVars['id'];
      $update = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      print "alert('Operating System deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>

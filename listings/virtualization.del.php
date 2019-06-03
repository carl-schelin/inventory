<?php
# Script: virtualization.del.php
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
    $package = "virtualization.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from changelog");

      $q_string  = "update ";
      $q_string .= "changelog ";
      $q_string .= "set cl_delete = 1,cl_whodel = " . $_SESSION['uid'] . " ";
      $q_string .= "where cl_id = " . $formVars['id'];
      $insert = mysql_query($q_string) or die($q_string . ": " . mysql_error());

      print "alert('Listing deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>

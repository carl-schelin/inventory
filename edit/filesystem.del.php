<?php
# Script: filesystem.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Delete filesystems

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "filesystem.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from filesystem");

      $q_string  = "delete ";
      $q_string .= "from filesystem ";
      $q_string .= "where fs_id = " . $formVars['id'];
      $insert = mysql_query($q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysql_error()));

      print "alert('Filesystem deleted.');\n";
    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>

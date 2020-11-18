<?php
# Script: network.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# See: https://incowk01/makers/index.php/Coding_Standards
# Description: Delete a specified record from the database

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "network.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($AL_Edit)) {
      logaccess($_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from interface");

      $q_string  = "delete ";
      $q_string .= "from interface ";
      $q_string .= "where int_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      print "alert('Interface deleted.');\n";
# Check all interfaces with this int_int_id and revert to 0 so they aren't lost
      $q_string  = "update ";
      $q_string .= "interface ";
      $q_string .= "set int_int_id = 0 ";
      $q_string .= "where int_int_id = " . $formVars['id'];
      $q_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

    } else {
      logaccess($_SESSION['uid'], $package, "Access denied");
    }
  }
?>

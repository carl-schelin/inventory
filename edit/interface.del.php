<?php
# Script: interface.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Delete a specified record from the database

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "interface.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from inv_interface");

      $q_string  = "delete ";
      $q_string .= "from inv_interface ";
      $q_string .= "where int_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

# Check all child interfaces of this interface (if any) with this int_int_id and revert to 0 so they aren't lost
      $q_string  = "update ";
      $q_string .= "inv_interface ";
      $q_string .= "set int_int_id = 0 ";
      $q_string .= "where int_int_id = " . $formVars['id'];
      $q_inv_interface = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>

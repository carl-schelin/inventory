<?php
# Script: software.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Delete a record from the database

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "software.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from software");

      $q_string  = "delete ";
      $q_string .= "from software ";
      $q_string .= "where sw_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      print "alert('Software deleted.');\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>

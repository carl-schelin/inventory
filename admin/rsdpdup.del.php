<?php
# Script: rsdpdup.del.php
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
    $package = "rsdpdup.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }
    $formVars['select'] = '';
    if (isset($_GET['select'])) {
      $formVars['select'] = clean($_GET['select'], 20);
    }
    $formVars['table'] = '';
    if (isset($_GET['table'])) {
      $formVars['table'] = clean($_GET['table'], 30);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from " . $formVars['table']);

      $q_string  = "delete ";
      $q_string .= "from " . $formVars['table'] . " ";
      $q_string .= "where " . $formVars['select'] . " = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die(header("Location: " . $Siteroot . "/error.php?script=" . $package . "&error=" . $q_string . "&mysql=" . mysqli_error($db)));

      print "alert('Record deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>

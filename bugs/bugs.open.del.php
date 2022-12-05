<?php
# Script: bugs.open.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: 

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "bugs.open.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Guest)) {
      $q_string  = "select bug_id ";
      $q_string .= "from inv_bugs_detail ";
      $q_string .= "where bug_bug_id = " . $formVars['id'];
      $q_inv_bugs_detail = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      while ($a_inv_bugs_detail = mysqli_fetch_array($q_inv_bugs_detail)) {

        logaccess($db, $_SESSION['uid'], $package, "Deleting " . $a_inv_bugs_detail['bug_id'] . " from inv_bugs_detail");

        $q_string  = "delete ";
        $q_string .= "from inv_bugs_detail ";
        $q_string .= "where bug_id = " . $a_inv_bugs_detail['bug_id'];
        $result = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));
      }

      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from inv_bugs");

      $q_string  = "delete ";
      $q_string .= "from inv_bugs ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "alert('Bug deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>

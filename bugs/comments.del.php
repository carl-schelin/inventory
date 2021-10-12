<?php
# Script: comments.del.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Delete a detail record

  header('Content-Type: text/javascript');

  include('settings.php');
  $called = 'yes';
  include($Loginpath . '/check.php');
  include($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "comments.del.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Deleting " . $formVars['id'] . " from bugs_detail");

      $q_string  = "delete ";
      $q_string .= "from bugs_detail ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $insert = mysqli_query($db, $q_string) or die($q_string . ": " . mysqli_error($db));

      print "alert('Comment deleted.');\n";

      print "clear_fields();\n";
    } else {
      logaccess($db, $_SESSION['uid'], $package, "Access denied");
    }
  }
?>

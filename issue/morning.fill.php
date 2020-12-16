<?php
# Script: morning.fill.php
# Owner: Carl Schelin
# Coding Standard 3.0 Applied
# Description: Fill in the table for editing.
#

  header('Content-Type: text/javascript');

  include ('settings.php');
  $called = 'yes';
  include ($Loginpath . '/check.php');
  include ($Sitepath . '/function.php');

  if (isset($_SESSION['username'])) {
    $package = "morning.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from issue_morning");

      $q_string  = "select morn_text,morn_timestamp,morn_user,morn_status ";
      $q_string .= "from issue_morning ";
      $q_string .= "where morn_id = " . $formVars['id'];
      $q_issue_morning = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_issue_morning = mysqli_fetch_array($q_issue_morning);
      mysqli_free_result($q_issue_morning);

      $selected = return_Index($db, $a_issue_morning['morn_user'], "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.start.morn_text.value = '"      . mysqli_real_escape_string($db, $a_issue_morning['morn_text'])      . "';\n";
      print "document.start.morn_timestamp.value = '" . mysqli_real_escape_string($db, $a_issue_morning['morn_timestamp']) . "';\n";

      print "document.start.morn_user['"   . $selected                       . "'].selected = true;\n";
      print "document.start.morn_status['" . $a_issue_morning['morn_status'] . "'].selected = true;\n";

      print "document.start.morning_id.value = " . $formVars['id'] . ";\n";

      print "document.start.mornupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>

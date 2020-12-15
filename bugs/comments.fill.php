<?php
# Script: comments.fill.php
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
    $package = "comments.fill.php";
    $formVars['id'] = 0;
    if (isset($_GET['id'])) {
      $formVars['id'] = clean($_GET['id'], 10);
    }

    if (check_userlevel($db, $AL_Edit)) {
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from bugs_detail");

      $q_string  = "select bug_text,bug_timestamp,bug_user ";
      $q_string .= "from bugs_detail ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $q_bugs_detail = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_bugs_detail = mysqli_fetch_array($q_bugs_detail);
      mysqli_free_result($q_bugs_detail);

      $selected = return_Index($db, $a_bugs_detail['bug_user'],       "select usr_id from users where usr_disabled = 0 order by usr_last,usr_first");

      print "document.start.bug_text.value = '"      . mysqli_real_escape_string($a_bugs_detail['bug_text'])      . "';\n";
      print "document.start.bug_timestamp.value = '" . mysqli_real_escape_string($a_bugs_detail['bug_timestamp']) . "';\n";

      print "document.start.bug_user['" . $selected . "'].selected = true;\n";

      print "document.start.bug_id.value = " . $formVars['id'] . ";\n";

      print "document.start.bugupdate.disabled = false;\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>

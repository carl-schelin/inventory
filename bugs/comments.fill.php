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
      logaccess($db, $_SESSION['uid'], $package, "Requesting record " . $formVars['id'] . " from inv_bugs_detail");

      $q_string  = "select bug_text,bug_timestamp,bug_user ";
      $q_string .= "from inv_bugs_detail ";
      $q_string .= "where bug_id = " . $formVars['id'];
      $q_inv_bugs_detail = mysqli_query($db, $q_string) or die (mysqli_error($db));
      $a_inv_bugs_detail = mysqli_fetch_array($q_inv_bugs_detail);
      mysqli_free_result($q_inv_bugs_detail);

      $selected = return_Index($db, $a_inv_bugs_detail['bug_user'],       "select usr_id from inv_users where usr_disabled = 0 order by usr_last,usr_first") + 1;

      print "document.formUpdate.bug_text.value = '"      . mysqli_real_escape_string($db, $a_inv_bugs_detail['bug_text'])      . "';\n";
      print "document.formUpdate.bug_timestamp.value = '" . mysqli_real_escape_string($db, $a_inv_bugs_detail['bug_timestamp']) . "';\n";

      print "document.formUpdate.bug_user['" . $selected . "'].selected = true;\n";

      print "document.formUpdate.bug_id.value = " . $formVars['id'] . ";\n";

    } else {
      logaccess($db, $_SESSION['uid'], $package, "Unauthorized access.");
    }
  }
?>
